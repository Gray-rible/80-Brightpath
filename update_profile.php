<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id']; // Ensure user_id exists

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title']);

    // Update query
    $query = "UPDATE users SET 
        first_name = '$first_name', 
        middle_name = '$middle_name', 
        last_name = '$last_name', 
        email = '$email', 
        job_title = '$job_title' 
    WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        // Redirect or show success message
        header('Location: admin_profile.php'); // Replace with your profile page
        exit();
    } else {
        echo "Error updating profile: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
