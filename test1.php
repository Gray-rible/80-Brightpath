<?php
$currentPage = 'manage_applications';
include 'header_sidebar.html';
include 'db.php';


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data (assuming user_id is stored in session)
session_start();
$user_id = $_SESSION['user_id'];
$user_data = null;

if ($user_id) {
    $query = "SELECT first_name, middle_name, last_name FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Scholarship Applications</title>
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
        .circle-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color:rgb(105, 105, 105);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 24px;
            text-align: center;
        }

        /* Form styling */
        #application-form {
            display: none;
            margin-top: 20px;
            background-color: white;
            max-width: ;width: 800px;
            padding: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
     <script>
        // Show form on button click
        function showForm() {
            document.getElementById('application-form').style.display = 'block';
        }
    </script>
</head>

<body>
    <main>
     <!-- Circular button -->
     Make an Application
    <button class="circle-btn" onclick="showForm()">+</button>

<!-- Application form -->
<form id="application-form" method="POST" action="save_application.php">
    <div class="form-group">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>" required>
    </div>
    <div class="form-group">
        <label for="middle_name">Middle Name</label>
        <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user_data['middle_name'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>" required>
    </div>
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <button type="submit" class="submit-btn">Submit</button>
</form>
    </main>

</body>

</html>