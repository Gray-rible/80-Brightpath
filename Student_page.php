<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Page</title>
    <style>
        table /* update this*/ {
            width: 70%;
            border-collapse: collapse;
            margin-inline: 10%;
        }
        h1  /* add this */{
            margin-left:10%;
            padding-top:10%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .view-btn {
            background-color:rgb(37, 93, 153);
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Student Page</h1>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'database.php';
            $query = "SELECT studentId, CONCAT(firstName, ' ', lastName) AS fullName FROM StudentRecordTB";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['studentId']}</td>
                        <td>{$row['fullName']}</td>
                        <td><a href='StudentPage_Individual.php?id={$row['studentId']}' class='view-btn'>VIEW</a></td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
