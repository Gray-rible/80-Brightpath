<?php
include 'db.php';
session_start();
$currentPage = 'profile';

// Verify session user ID
if (!isset($_SESSION['user_id'])) {
    die("User is not logged in. Please log in to access your profile.");
}
$user_id = $_SESSION['user_id'];

// Initialize success message
$successMessage = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);

    // Handle profile avatar upload
    $profile_avatar = null;
    if (isset($_FILES['profile_avatar']) && $_FILES['profile_avatar']['error'] === UPLOAD_ERR_OK) {
        $profile_avatar = file_get_contents($_FILES['profile_avatar']['tmp_name']);
    }

    // Update query for the `users` table
    $updateQuery = "
        UPDATE users 
        SET first_name = ?, 
            middle_name = ?, 
            last_name = ?, 
            email = ?, 
            job_title = ?, 
            profile_avatar = ?
        WHERE user_id = ?
    ";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssssi", $first_name, $middle_name, $last_name, $email, $job_title, $profile_avatar, $user_id);
    if (!$stmt->execute()) {
        $successMessage = "Error updating profile: " . $stmt->error;
    } else {
        $successMessage = "Profile updated successfully!";
    }
}

// Fetch user data including profile avatar
$query = "
    SELECT 
        first_name, 
        middle_name, 
        last_name, 
        email, 
        job_title, 
        profile_avatar 
    FROM users 
    WHERE user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Error: No user data found for the given user ID.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>
<?php include 'header_sidebar.html'; ?>
<!-- Success Message -->
<?php if (!empty($successMessage)): ?>
    <div id="successMessage"><?php echo $successMessage; ?></div>
    <script>
        // Show the success message
        document.getElementById("successMessage").style.display = "block";

        // Automatically hide the success message after 3 seconds
        setTimeout(() => {
            document.getElementById("successMessage").style.display = "none";
        }, 3000);
    </script>
<?php endif; ?>

<div class="fullcontainer">
    <p style="font-size: 24px; font-weight: bold;">MY PROFILE</p>

    <form id="profileForm" method="POST" action="" enctype="multipart/form-data">
        <div class="innertt">
            <!-- Picture Upload Section -->
            <div class="firsttt">
                <div class="picture-upload">
                    <label for="profile_avatar" class="picture-label">Upload 2x2 Photo</label>
                    <!-- Image preview -->
                    <img id="avatar-preview" 
                         src="<?php echo !empty($row['profile_avatar']) ? 'data:image/jpeg;base64,' . base64_encode($row['profile_avatar']) : 'default_avatar.png'; ?>" 
                         alt="Avatar Preview" 
                         class="avatar-preview">
                    <!-- Custom file input -->
                    <label for="profile_avatar" class="custom-file-label">
                        <?php echo empty($row['profile_avatar']) ? 'Upload New Photo' : 'Change Avatar'; ?>
                    </label>
                    <input type="file" id="profile_avatar" name="profile_avatar" accept="image/*" class="file-input" onchange="previewImage(event)">
                </div>
            </div>

            <div class="secondtt">
                <label for="firstn">First Name:</label>
                <input type="text" id="firstn" name="first_name" value="<?php echo htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="detectChange()">
                <label for="midln">Middle Name:</label>
                <input type="text" id="midln" name="middle_name" value="<?php echo htmlspecialchars($row['middle_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="detectChange()">
                <label for="lastn">Last Name:</label>
                <input type="text" id="lastn" name="last_name" value="<?php echo htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8'); ?>" onchange="detectChange()">
                <label for="emailn">Email:</label>
                <input type="email" id="emailn" name="email" value="<?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?>" onchange="detectChange()">
            </div>
            <div class="thirdtt">
                <label for="jobtitle">Job Title:</label>
                <input type="text" id="jobtitle" name="job_title" value="<?php echo htmlspecialchars($row['job_title'], ENT_QUOTES, 'UTF-8'); ?>" onchange="detectChange()">
            </div>
        </div>
        <button type="submit" id="updateButton" class="button" style="display: none;">Update Changes</button>
    </form>
</div>
<script>
    function previewImage(event) {
        const file = event.target.files[0]; // Get the selected file
        const preview = document.getElementById('avatar-preview'); // Get the preview image element
        const label = document.querySelector('.custom-file-label'); // Get the custom file label

        if (file) {
            const reader = new FileReader(); // Create a FileReader to read the file
            reader.onload = function(e) {
                preview.src = e.target.result; // Set the preview image source to the selected file
                label.textContent = 'Change Photo'; // Update the label text
                label.style.backgroundColor = 'rgb(145, 51, 51)'; // Change background color on file selection
            };
            reader.readAsDataURL(file); // Read the file as a data URL
        }
    }

    // Detect changes and show the Update Changes button
    function detectChange() {
        const updateButton = document.getElementById('updateButton');
        updateButton.style.display = 'block'; // Show Update Changes button
    }
</script>
</body>
</html>