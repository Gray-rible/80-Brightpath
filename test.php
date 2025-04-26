<?php
include 'db.php'; // Replace with your actual database connection file
session_start();
$currentPage = 'test';
include 'header_sidebar.html';

// Query to fetch all necessary records from application_requirements
// Query to fetch the required data
$query = "
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

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Applicant Scholarship Records</title>
    <style>
        main{
            margin-left: 320px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .action-icon {
            cursor: pointer;
            color: blue;
            text-decoration: underline;
        }
    </style>
</head>
<body>
   <main>
        <h1>Applicant Scholarship Records</h1>
        <p>Welcome to the Applicant Scholarship Records page. Here you can view all the records related to scholarship applications.</p>
    <h2>Applicant Scholarship Records</h2>
    <table>
        <thead>
            <tr>
                <th>Record ID</th>
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
        </main>
    <script>
        function viewRecord(recordId) {
            // Redirect to a page to view the record details
            window.location.href = `view_record.php?record_id=${recordId}`;
        }
    </script>
</body>
</html>