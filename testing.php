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

// Fetch all scholarships for the filter dropdown
$scholarshipQuery = "SELECT scholarship_id, scholarship_name FROM scholarships";
$scholarshipResult = $conn->query($scholarshipQuery);

// Get the selected scholarship ID from the filter (if any)
$selectedScholarshipId = isset($_GET['scholarship_id']) ? intval($_GET['scholarship_id']) : null;

// Fetch scholarship applications for the logged-in user
$applicationsQuery = "
    SELECT sa.applications_id, sa.created_at, sa.updated_at, 
           s.scholarship_name, up.Address, up.Grade_level, 
           u.first_name, u.middle_name, u.last_name
    FROM scholarships_applications sa
    JOIN scholarships s ON sa.scholarship_id = s.scholarship_id
    JOIN user_profiles up ON sa.user_profile_id = up.user_profile_id
    JOIN users u ON sa.user_id = u.user_id
    WHERE sa.user_id = ?";

if ($selectedScholarshipId) {
    $applicationsQuery .= " AND sa.scholarship_id = ?";
}

$stmt = $conn->prepare($applicationsQuery);
if ($selectedScholarshipId) {
    $stmt->bind_param("ii", $user_id, $selectedScholarshipId);
} else {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$applicationsResult = $stmt->get_result();
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
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        main {
            padding: 20px;
            margin-left: 330px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        .btn-container {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .filter-container {
            margin-bottom: 20px;
        }

        .filter-container select {
            padding: 5px;
            font-size: 16px;
        }

        .filter-container button {
            padding: 5px 10px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .filter-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <main>
        <h1>Scholarship Applications</h1>
        <button onclick="showApplicationForm()">Send an Application</button>

        <!-- Filter Section -->
        <div class="filter-container">
            <form method="GET" action="">
                <label for="scholarship_id">Filter by Scholarship:</label>
                <select name="scholarship_id" id="scholarship_id">
                    <option value="">All Scholarships</option>
                    <?php while ($scholarship = $scholarshipResult->fetch_assoc()) { ?>
                        <option value="<?php echo $scholarship['scholarship_id']; ?>" 
                            <?php echo $selectedScholarshipId == $scholarship['scholarship_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($scholarship['scholarship_name']); ?>
                        </option>
                    <?php } ?>
                </select>
                <button type="submit">Filter</button>
            </form>
        </div>

        <!-- Scholarship Applications Table -->
        <h2>List of Applications</h2>
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Scholarship Name</th>
                    <th>Grade Level</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $applicationsResult->fetch_assoc()) { 
                    // Determine the status dynamically
                    $status = 'Pending'; // Default status
                    $submittedAt = strtotime($row['created_at']);
                    $currentTime = time();
                    $timeDifference = $currentTime - $submittedAt;

                    if ($timeDifference > 604800) { // 7 days in seconds
                        $status = 'Granted';
                    } elseif ($timeDifference > 1209600) { // 14 days in seconds
                        $status = 'Rejected';
                    }

                    // Combine first name, middle name, and last name into full name
                    $fullName = htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
                ?>
                    <tr>
                        <td><?php echo $fullName; ?></td>
                        <td><?php echo htmlspecialchars($row['scholarship_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Grade_level']); ?></td>
                        <td><?php echo htmlspecialchars($row['Address']); ?></td>
                        <td><?php echo htmlspecialchars($status); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                        <td>
                            <a href="view_application_details.php?application_id=<?php echo $row['applications_id']; ?>" class="btn">View Details</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Application Form -->
        <div class="application-form-container" id="applicationForm">
            <h2>Application Form</h2>
            <form method="POST" action="submit_application.php" enctype="multipart/form-data">
                <!-- Form fields here (already provided in your existing code) -->
                <div class="btn-container">
                    <button type="submit">Submit Application</button>
                    <button type="button" onclick="hideApplicationForm()">Cancel</button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function showApplicationForm() {
            document.getElementById('applicationForm').style.display = 'block';
        }

        function hideApplicationForm() {
            document.getElementById('applicationForm').style.display = 'none';
        }
    </script>
</body>

</html>