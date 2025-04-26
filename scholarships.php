<?php
// Start the session and include database connection
session_start();
include 'db.php'; // Replace with your actual database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarships Management</title>
</head>
<body>
    <h1>Scholarships Management</h1>

    <!-- List of Scholarships -->
    <h2>Scholarships List</h2>
    <table border="1">
        <tr>
            <th>Scholarship Name</th>
            <th>Category</th>
            <th>Description</th>
            <th>Selection Criteria</th>
            <th>User Managing</th>
        </tr>
        <?php
        // Fetch scholarships and their categories from the database
        $sql = "SELECT 
                    s.scholarship_name, 
                    c.category_name, 
                    s.scholarship_description, 
                    s.scholarship_selection_criteria, 
                    CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) AS admin_name
                FROM Scholarships s
                JOIN Scholarship_Categories c ON s.scholarship_category_id = c.scholarship_category_id
                JOIN Users u ON s.user_id = u.user_id";
        $result = mysqli_query($conn, $sql);

        // Check if results are returned
        if (mysqli_num_rows($result) > 0) {
            // Display scholarships in a table
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['scholarship_name']}</td>
                        <td>{$row['category_name']}</td>
                        <td>{$row['scholarship_description']}</td>
                        <td>{$row['scholarship_selection_criteria']}</td>
                        <td>{$row['admin_name']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No scholarships found</td></tr>";
        }
        ?>
    </table>

    <!-- Add New Scholarship -->
    <h2>Add a New Scholarship</h2>
    <form action="add_scholarship.php" method="POST">
        <label for="scholarship_name">Scholarship Name:</label>
        <input type="text" id="scholarship_name" name="scholarship_name" required><br>

        <label for="scholarship_category">Category:</label>
        <select id="scholarship_category" name="scholarship_category_id" required>
            <?php
            // Fetch pre-inserted categories
            $category_sql = "SELECT * FROM Scholarship_Categories";
            $category_result = mysqli_query($conn, $category_sql);

            while ($category_row = mysqli_fetch_assoc($category_result)) {
                echo "<option value='{$category_row['scholarship_category_id']}'>{$category_row['category_name']}</option>";
            }
            ?>
        </select><br>

        <label for="scholarship_description">Description:</label>
        <textarea id="scholarship_description" name="scholarship_description" required></textarea><br>

        <label for="scholarship_details">Details:</label>
        <textarea id="scholarship_details" name="scholarship_details"></textarea><br>

        <label for="scholarship_selection_criteria">Selection Criteria:</label>
        <textarea id="scholarship_selection_criteria" name="scholarship_selection_criteria" required></textarea><br>

        <label for="requirements">Requirements:</label><br>
        <?php
        // Fetch pre-inserted requirements
        $requirement_sql = "SELECT * FROM Requirements";
        $requirement_result = mysqli_query($conn, $requirement_sql);

        while ($requirement_row = mysqli_fetch_assoc($requirement_result)) {
            echo "<input type='checkbox' name='requirements[]' value='{$requirement_row['requirement_id']}'> {$requirement_row['requirement_name']}<br>";
        }
        ?>
        
        <input type="submit" value="Add Scholarship">
    </form>

</body>
</html>
