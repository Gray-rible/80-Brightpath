<?php
$currentPage = 'manage_scholarships';
include 'header_sidebar.html';

// Start the session and include database connection
session_start();
include 'db.php'; // Replace with your actual database connection file

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get the logged-in user's ID
$loggedInUserId = $_SESSION['user_id'];

// Fetch scholarships and their categories from the database
$sql = "SELECT 
            s.scholarship_id,
            s.scholarship_name, 
            c.category_name, 
            s.Scholarship_general_description_s1, 
            s.scholarship_selection_criteria, 
            CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) AS admin_name,
            st.status_name, -- Fetch the status name
            s.slots_available,
            s.image_data,
            s.user_id
        FROM scholarships s
        LEFT JOIN scholarship_categories c ON s.scholarship_category_id = c.scholarship_category_id
        LEFT JOIN users u ON s.user_id = u.user_id
        LEFT JOIN status st ON s.status_id = st.status_id"; 
        

$result = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarships Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        main {
            margin-left: 320px;
            padding: 20px;
        }
        h1 {
            color: #007bff;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .scholarship-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 250px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .scholarship-card.managed {
            background: linear-gradient(to bottom, rgb(255, 255, 255), rgb(186, 255, 202)); /* Light to green gradient */
            border: solid green 3px;
            color: white;
        }
        .scholarship-card.managed h3 {
            color: black;
        }
        .scholarship-card.managed p {
            color: rgb(133, 133, 133);
        }
        .scholarship-card.managed .status {
            color: rgb(36, 122, 40);
        }
        .scholarship-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .scholarship-card h3 {
            font-size: 18px;
            margin: 10px 0;
            color: #333;
        }
        .scholarship-card p {
            margin: 5px 0;
            color: #555;
        }
        .scholarship-card .status {
            font-weight: bold;
            color: #28a745;
        }
        .scholarship-card .status.inactive {
            color: #dc3545;
        }
        .scholarship-card .buttons {
            margin-top: 10px;
        }
        .scholarship-card .buttons a {
            display: inline-block;
            margin: 5px 5px 0 0;
            padding: 5px 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .scholarship-card .buttons a.edit {
            background-color: #28a745;
        }
        .scholarship-card .buttons a:hover {
            opacity: 0.9;
        }
        .add-card {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f4f4f4;
            border: 2px dashed #007bff;
            border-radius: 8px;
            width: 250px;
            height: 400px;
            text-align: center;
            cursor: pointer;
        }
        .add-card a {
            text-decoration: none;
            color: #007bff;
            font-size: 18px;
            font-weight: bold;
        }
        .add-card:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>
    <main>
        <h1>Scholarships Management</h1>
        <div class="gallery">
            <?php
             // Check if results are returned
             if (mysqli_num_rows($result) > 0) {
                // Display scholarships in a gallery
                while ($row = mysqli_fetch_assoc($result)) {
                    $imageSrc = !empty($row['image_data']) 
                        ? "data:image/jpeg;base64," . base64_encode($row['image_data']) 
                        : 'default-image.jpg'; // Default image if none is provided

                    // Highlight scholarships managed by the logged-in user
                    $cardClass = $row['user_id'] == $loggedInUserId ? 'scholarship-card managed' : 'scholarship-card';

                    // Define the status class
                    

                    echo "<div class='$cardClass' id='scholarship-{$row['scholarship_id']}'>";
                    echo "<img src='$imageSrc' alt='Scholarship Image' id='image-{$row['scholarship_id']}'>";
                    echo "<h3>" . htmlspecialchars($row['scholarship_name']) . "</h3>
                          <p>Category: " . htmlspecialchars($row['category_name'] ?? 'Uncategorized') . "</p>
                          <p>Managed By: " . htmlspecialchars($row['admin_name']) . "</p>
                         <p class='status'>" . htmlspecialchars($row['status_name']) . "</p>
                          <p>Slots Available: " . htmlspecialchars($row['slots_available']) . "</p>";

                    // Buttons
                    echo "<div class='buttons'>";
                    echo "<a href='view_scholarship.php?scholarship_id=" . $row['scholarship_id'] . "' class='view'>View</a>";
                    if ($row['user_id'] == $loggedInUserId) {
                        echo "<a href='edit_scholarship.php?scholarship_id=" . $row['scholarship_id'] . "' class='edit'>Edit</a>";
                    }
                    echo "</div>";

                    echo "</div>";
                }
            } else {
                echo "<p>No scholarships found</p>";
            }
            ?>
            <!-- Add Scholarship Card -->
            <div class="add-card">
                <a href="add_scps.php">+ Add Scholarship</a>
            </div>
        </div>
    </main>
</body>
</html>