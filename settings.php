<?php
$currentPage = 'settings';
include 'header_sidebar.html';

// Include database connection
include 'db.php';
session_start();

// Initialize a success message variable
$successMessage = "";

// Fetch current settings from the database
$query = "SELECT * FROM settings";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve submitted data
    $siteName = mysqli_real_escape_string($conn, $_POST['siteName']);
    $siteUrl = mysqli_real_escape_string($conn, $_POST['siteUrl']);
    $themeColor = mysqli_real_escape_string($conn, $_POST['themeColor']);
    $adminEmail = mysqli_real_escape_string($conn, $_POST['adminEmail']);

    // Update the settings in the database
    $updateQuery = "UPDATE settings SET 
        siteName = '$siteName', 
        siteUrl = '$siteUrl', 
        themeColor = '$themeColor', 
        adminEmail = '$adminEmail'";
        
    if (mysqli_query($conn, $updateQuery)) {
        $successMessage = "Settings updated successfully!";
        // Refresh the settings data
        $settings = [
            'siteName' => $siteName,
            'siteUrl' => $siteUrl,
            'themeColor' => $themeColor,
            'adminEmail' => $adminEmail
        ];
    } else {
        $successMessage = "Error updating settings: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            
            background-color: rgb(231, 231, 231);
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 330px;
            margin-right: 5%;
            margin-top: 40px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color:rgb(70, 128, 48);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .success-message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #dff0d8;
            color: #3c763d;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Settings</h1>
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
            <script>
                setTimeout(() => {
                    document.querySelector('.success-message').style.display = 'none';
                }, 3000);
            </script>
        <?php endif; ?>
        <form method="POST" action="settings.php">
            <div class="form-group">
                <label for="siteName">Site Name:</label>
                <input type="text" id="siteName" name="siteName" value="<?php echo htmlspecialchars($settings['siteName']); ?>">
            </div>
            <div class="form-group">
                <label for="siteUrl">Site URL:</label>
                <input type="text" id="siteUrl" name="siteUrl" value="<?php echo htmlspecialchars($settings['siteUrl']); ?>">
            </div>
            <div class="form-group">
                <label for="themeColor">Theme Color:</label>
                <input type="text" id="themeColor" name="themeColor" value="<?php echo htmlspecialchars($settings['themeColor']); ?>">
            </div>
            <div class="form-group">
                <label for="adminEmail">Admin Email:</label>
                <input type="email" id="adminEmail" name="adminEmail" value="<?php echo htmlspecialchars($settings['adminEmail']); ?>">
            </div>
            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
