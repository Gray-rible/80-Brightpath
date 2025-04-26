<?php
$currentPage = 'manage_applications';
include 'header_sidebar.html';
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Get the search name from the GET request
$searchName = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';

// Fetch all scholarships for the filter section
$scholarshipQuery = "SELECT scholarship_id, scholarship_name FROM scholarships";
$scholarshipResult = $conn->query($scholarshipQuery);

// Get the selected scholarship IDs from the filter (if any)
$selectedScholarshipIds = isset($_GET['scholarship_ids']) ? $_GET['scholarship_ids'] : [];

// Base query to fetch the required data
$sql = "
    SELECT 
        asr.record_id, 
        CONCAT(a.first_name, ' ', a.middle_name, ' ', a.last_name) AS full_name, 
        s.scholarship_name, 
        asr.status, 
        asr.submission_date
    FROM 
        applicant_scholarship_records asr
    INNER JOIN 
        applicants a ON asr.applicant_id = a.applicant_id
    INNER JOIN 
        scholarships s ON asr.scholarship_id = s.scholarship_id
";

// Add filter condition if scholarships are selected
if (!empty($selectedScholarshipIds)) {
    $selectedScholarshipIds = array_map('intval', $selectedScholarshipIds); // Sanitize input
    $sql .= " WHERE asr.scholarship_id IN (" . implode(',', $selectedScholarshipIds) . ")";
}

// Add search condition if a search name is provided
if (!empty($searchName)) {
    $searchName = $conn->real_escape_string($searchName); // Sanitize input
    $sql .= (strpos($sql, 'WHERE') !== false ? ' AND' : ' WHERE') . " CONCAT(a.first_name, ' ', a.middle_name, ' ', a.last_name) LIKE '%$searchName%'";
}

// Execute the query
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
           
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px;
            margin-left: 300px; /* Adjust to match the sidebar width */
           border-radius: 5px;
    background: linear-gradient(135deg, rgb(255, 255, 255), rgb(255, 255, 255));
            min-height: 650px;
            padding-top: 30px;
           
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .content-container {
    display: flex;
    gap: 20px;
    justify-content: space-between; /* Adjust spacing */
    align-items: flex-start; /* Align items at the top */
}

table {
    width: 95%; /* Matches the inquiries table width */
    border-collapse: collapse;
    margin-inline: 20px; /* Centers the table horizontally */
    margin-top: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Adds a subtle shadow */
    
}

table th,
table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd; /* Adds a light border between rows */
}

table th {
    background-color: #f2f2f2; /* Light gray background for header */
    font-weight: bold;
    color: black; /* Black text for header */
}

table td {
    color: #333; /* Dark gray text for table cells */
}

table tr {
    transition: background-color 0.3s ease; /* Smooth transition for background color */
}

table tr:hover {
    background-color: rgb(204, 234, 255); 
}

table td.address-cell {
    max-width: 150px; /* Set a maximum width for the cell */
    overflow: hidden; /* Hide overflowing content */
    text-overflow: ellipsis; /* Add ellipsis (...) for overflowing text */
    white-space: nowrap; /* Prevent text from wrapping */
    cursor: pointer; /* Change cursor to pointer to indicate interactivity */
}

table td.address-cell:hover {
    overflow: visible; /* Show full content on hover */
    white-space: normal; /* Allow text to wrap */
    position: relative; /* Ensure the content appears above other elements */
    z-index: 1; /* Bring the hovered cell to the front */}

.view-button {
    background-color: #17a2b8;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.delete-button {
    background-color: #dc3545;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

        .filter-container {
    width: 30%;
    max-width: 1000px; /* Optional for size limits */
    color: rgb(45, 37, 46);
    padding: 10px;
    border-radius: 8px;
    background-Color:rgb(255, 255, 255);
}



        .filter-container h3 {
           
            margin-top: 0;
        }

        .filter-container div {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .filter-container input[type="checkbox"] {
            margin-right: 10px;
        }

        .filter-container label {
            margin: 0;
        }

        .filter-container .reset-button {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            background-color:rgb(85, 39, 39);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
        }

        .filter-container .reset-button:hover {
            background-color:rgb(145, 29, 29);
        }
        h2.listof{
            font-weight: bold;
            color: rgb(46, 114, 63);
            text-align: center;
            font-family: sans-serif;
            background-color:rgb(255, 255, 255);
            font-size: 18px;
            padding: 17px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            margin-top: 0;
           box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .applications-table {
            border-radius: 13px;
            background-color: rgb(236, 236, 236);
            box-shadow: 2 2 10px rgba(241, 5, 5, 0.6);
            border: 3px solid  #28a745;

        }
        h3{
            color:rgb(27, 121, 155);
            border-bottom: 2px solid rgb(192, 192, 192);
            font-family: sans-serif;
            padding-bottom: 10px;
        }
        .addapplicant {
           margin-top: 20px;
           margin-left: 220px;
            background-color: rgb(255, 255, 255);
            
            padding-block: 10px ;
            width: 70PX;
            height: 70px;
            text-align: center;
            border: none;
            border-radius: 15px;
            font-size: 24px;
            cursor: pointer;
            color: rgb(68, 36, 143);
            font-size: 30px;
            border: dashed 1px rgb(68, 36, 143);
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .search-bar {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    gap: 10px;
}

.search-bar input[type="text"] {
    flex: 1;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

.search-bar .search-button {
    padding: 8px 15px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.search-bar .search-button:hover {
    background-color: #218838;
}
    </style>
</head>

<body>
    <main>
        <div class="content-container">
            <!-- Scholarship Applications Table -->
            <div class="applications-table">
                <h2 class="listof">List of Applications</h2>
                <table>
        <thead>
            <tr>
                <th>no.</th>
                <th>Applicant Name</th>
                <th>Scholarship Name</th>
                <th>Status</th>
                <th>Submission Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through the result and display rows in the table
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['record_id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['scholarship_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['submission_date']) . "</td>";
                echo "<td><span class='action-icon' onclick=\"viewRecord(" . $row['record_id'] . ")\">View</span></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
            </div>

            <!-- Filter Section -->
            <div class="filter-container">
    <h3>Find Applicant</h3>
    <form method="GET" action="">
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" name="search_name" placeholder="Search Name" value="<?php echo isset($_GET['search_name']) ? htmlspecialchars($_GET['search_name']) : ''; ?>">
            <button type="submit" class="search-button">Search</button>
        </div>
          <br>
        <h3>Filter by Scholarships</h3>
        <?php while ($scholarship = $scholarshipResult->fetch_assoc()): ?>
            <div>
                <input type="checkbox" name="scholarship_ids[]" value="<?php echo $scholarship['scholarship_id']; ?>" 
                    <?php echo in_array($scholarship['scholarship_id'], $selectedScholarshipIds) ? 'checked' : ''; ?>>
                <label><?php echo htmlspecialchars($scholarship['scholarship_name']); ?></label>
            </div>
        <?php endwhile; ?>
        <a href="manage_applications.php" class="reset-button">Reset</a><br>
        <button class="addapplicant" onclick="event.preventDefault(); window.location.href='apply1.php';">+</button>
    </form>
</div>
         
    </main>

    <script>
        // Automatically submit the form when a checkbox is clicked
        document.querySelectorAll('input[name="scholarship_ids[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                checkbox.closest('form').submit();
            });
        });
    </script>
</body>

</html>

<?php
// Close the connection
$conn->close();
?>*/