<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit the form.");
}

$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];

    // Update the `users` table
    $updateUsersQuery = "
        UPDATE users 
        SET first_name = ?, middle_name = ?, last_name = ?
        WHERE user_id = ?";
    $stmt = $conn->prepare($updateUsersQuery);
    $stmt->bind_param("sssi", $first_name, $middle_name, $last_name, $user_id);

    if ($stmt->execute()) {
        echo "User information updated successfully.";
        header("Location: manage_applications.php");
        exit();
    } else {
        echo "Error updating users table: " . $stmt->error;
    }
}
?>
