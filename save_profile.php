<?php
include 'db.php';
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit the form.");
}

$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['first_name']) || empty($_POST['scholarship_id'])) {
        die("Required fields are missing.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $religion = $_POST['religion'];
    $nationality = $_POST['nationality'];
    $current_school = $_POST['current_school'];
    $current_school_address = $_POST['current_school_address'];
    $grade_level = $_POST['grade_level'];
    $student_id = $_POST['student_id'];

    // Update the `users` table
    $updateUsersQuery = "
        UPDATE users 
        SET first_name = ?, middle_name = ?, last_name = ?
        WHERE user_id = ?";
    $stmt = $conn->prepare($updateUsersQuery);
    $stmt->bind_param("sssi", $first_name, $middle_name, $last_name, $user_id);

    if (!$stmt->execute()) {
        echo "Error updating users table: " . $stmt->error;
        exit();
    }

    // Check if the user already has a profile in `user_profiles`
    $checkQuery = "SELECT user_profile_id FROM user_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (isset($_FILES['profile_avatar']) && $_FILES['profile_avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // Directory to save the uploaded file
        $uploadFile = $uploadDir . basename($_FILES['profile_avatar']['name']);
    
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_avatar']['tmp_name'], $uploadFile)) {
            // Save the file path to the database
            $profileAvatarPath = $uploadFile;
            $updateQuery = "UPDATE user_profiles SET profile_avatar = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("si", $profileAvatarPath, $user_id);
            $stmt->execute();
        } else {
            echo "Error uploading the file.";
        }
    }
    
    if ($result->num_rows > 0) {
        // Update existing profile
        $updateQuery = "
            UPDATE user_profiles 
            SET Address = ?, Age = ?, Gender = ?, Religion = ?, Nationality = ?, 
                Current_School = ?, Current_School_address = ?, Grade_level = ?, Student_id = ?
            WHERE user_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param(
            "sisssssssi",
            $address, $age, $gender, $religion, $nationality,
            $current_school, $current_school_address, $grade_level, $student_id,
            $user_id
        );
    } else {
        // Insert new profile
        $insertQuery = "
            INSERT INTO user_profiles (
                user_id, Address, Age, Gender, Religion, Nationality, 
                Current_School, Current_School_address, Grade_level, Student_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param(
            "isissssssi",
            $user_id, $address, $age, $gender, $religion, $nationality,
            $current_school, $current_school_address, $grade_level, $student_id
        );
    }

    // Execute the query
    if ($stmt->execute()) {
        echo "Profile saved successfully.";
        header("Location: tests.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $user_profile_id = $_POST['user_profile_id']; // Assuming this is passed in the form
    $scholarship_id = $_POST['scholarship_id'];

    // Insert into scholarships_applications table
    $applicationQuery = "INSERT INTO scholarships_applications (user_profile_id, scholarship_id, user_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($applicationQuery);
    $stmt->bind_param("iii", $user_profile_id, $scholarship_id, $user_id);
    $stmt->execute();
    $application_id = $stmt->insert_id;

    // Insert into user_application_requirements table
    $requirementsQuery = "INSERT INTO user_application_requirements (
        Scholarship_Application_form_1,
        Scholarship_Application_form_2,
        Report_Card_form_138,
        Enrollment_form,
        Indigency_certification,
        Supporting_documents,
        user_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($requirementsQuery);
    $stmt->bind_param(
        "ssssssi",
        $_FILES['scholarship_application_form_1']['name'],
        $_FILES['scholarship_application_form_2']['name'],
        $_FILES['report_card_form_138']['name'],
        $_FILES['enrollment_form']['name'],
        $_FILES['indigency_certification']['name'],
        $_FILES['supporting_documents']['name'],
        $user_id
    );
    $stmt->execute();

    // Handle file uploads
    $uploadDir = 'uploads/';
    foreach ($_FILES as $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            move_uploaded_file($file['tmp_name'], $uploadDir . basename($file['name']));
        }
    }

    // Redirect or display success message
    header("Location: success.php");
    exit();
}
?>





