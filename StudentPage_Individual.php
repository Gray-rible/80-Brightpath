<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 80%;
            max-width: 600px;
            text-align: left;
        }
        h1 {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        p {
            margin: 10px 0;
            line-height: 1.6;
        }
        strong {
            color:rgb(37, 93, 153);
        }
        .not-found {
            text-align: center;
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Details</h1>
        <?php
        include 'database.php';

        if (isset($_GET['id'])) {
            $studentId = mysqli_real_escape_string($conn, $_GET['id']);
            
            $query = "SELECT s.*, c.courseName 
                      FROM StudentRecordTB s 
                      INNER JOIN CourseTB c ON s.courseId = c.courseId 
                      WHERE s.studentId = $studentId";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                echo "<p><strong>Student ID:</strong> {$row['studentId']}</p>";
                echo "<p><strong>Full Name:</strong> {$row['firstName']} {$row['lastName']}</p>";
                echo "<p><strong>Address:</strong> {$row['houseNo']}, {$row['brgyName']}, {$row['municipality']}, {$row['province']}, {$row['region']}, {$row['country']}</p>";
                echo "<p><strong>Contact Number:</strong> {$row['studContactNo']}</p>";
                echo "<p><strong>Email Address:</strong> {$row['emailAddress']}</p>";
                echo "<p><strong>Guardian Name:</strong> {$row['guardianFirstName']} {$row['guardianLastName']}</p>";
                echo "<p><strong>Hobbies:</strong> {$row['hobbies']}</p>";
                echo "<p><strong>Nickname:</strong> {$row['nickname']}</p>";
                echo "<p><strong>Course:</strong> {$row['courseName']}</p>"; 
                echo "<p><strong>Year Level:</strong> {$row['yearId']}</p>";
            } else {
                echo "<p class='not-found'>Student not found.</p>";
            }
        } else {
            echo "<p class='not-found'>No student selected.</p>";
        }
        ?>
    </div>
</body>
</html>
