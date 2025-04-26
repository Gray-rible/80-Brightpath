<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit an application.");
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Check if the form is submitted
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
    $scholarship_id = $_POST['scholarship_id'];
    

    // Handle file uploads
    $uploadedFiles = [
        'scholarship_application_form_1' => null,
        'scholarship_application_form_2' => null,
        'report_card_form_138' => null,
        'enrollment_form' => null,
        'indigency_certification' => null,
        'supporting_documents' => null,
    ];

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES as $key => $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $filePath = $uploadDir . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $filePath);
            $uploadedFiles[$key] = $filePath;
        }
    }

    // Update users table
    $userQuery = "UPDATE users SET first_name = ?, middle_name = ?, last_name = ? WHERE user_id = ?";
    $stmt = $conn->prepare($userQuery);
    $stmt->bind_param("sssi", $first_name, $middle_name, $last_name, $user_id);
    if (!$stmt->execute()) {
        die("Error updating users table: " . $stmt->error);
    }

    // Update or insert into user_profiles table
    $profileQuery = "SELECT user_profile_id FROM user_profiles WHERE user_id = ?";
    $stmt = $conn->prepare($profileQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userProfile = $result->fetch_assoc();

    if ($userProfile) {
        $user_profile_id = $userProfile['user_profile_id'];
        $updateProfileQuery = "UPDATE user_profiles SET Address = ?, Age = ?, Gender = ?, Religion = ?, Nationality = ?, Current_School = ?, Current_School_address = ?, Grade_level = ?, Student_id = ?, Father_last_name = ?, Father_first_name = ?, Father_middle_name = ?, Father_birthday = ?, Father_Occupation = ?, Father_monthly_salary = ?, Mother_last_name = ?, Mother_first_name = ?, Mother_middle_name = ?, Mother_birthday = ?, Mother_occupation = ?, Mother_monthly_salary = ? WHERE user_profile_id = ?";
        $stmt = $conn->prepare($updateProfileQuery);
        $stmt->bind_param("sisssssssssssdsssssssi", $address, $age, $gender, $religion, $nationality, $current_school, $current_school_address, $grade_level, $student_id, $father_last_name, $father_first_name, $father_middle_name, $father_birthday, $father_occupation, $father_monthly_salary, $mother_last_name, $mother_first_name, $mother_middle_name, $mother_birthday, $mother_occupation, $mother_monthly_salary, $user_profile_id);
        if (!$stmt->execute()) {
            die("Error updating user_profiles table: " . $stmt->error);
        }
    } else {
        $insertProfileQuery = "INSERT INTO user_profiles (user_id, Address, Age, Gender, Religion, Nationality, Current_School, Current_School_address, Grade_level, Student_id, Father_last_name, Father_first_name, Father_middle_name, Father_birthday, Father_Occupation, Father_monthly_salary, Mother_last_name, Mother_first_name, Mother_middle_name, Mother_birthday, Mother_occupation, Mother_monthly_salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertProfileQuery);
        $stmt->bind_param("isisssssssssssdssssss", $user_id, $address, $age, $gender, $religion, $nationality, $current_school, $current_school_address, $grade_level, $student_id, $father_last_name, $father_first_name, $father_middle_name, $father_birthday, $father_occupation, $father_monthly_salary, $mother_last_name, $mother_first_name, $mother_middle_name, $mother_birthday, $mother_occupation, $mother_monthly_salary);
        if (!$stmt->execute()) {
            die("Error inserting into user_profiles table: " . $stmt->error);
        }
        $user_profile_id = $stmt->insert_id;
    }

    // Insert or update user_application_requirements table
    $checkQuery = "SELECT User_application_requirements_id FROM user_application_requirements WHERE user_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingRequirements = $result->fetch_assoc();

    if ($existingRequirements) {
        $user_application_requirements_id = $existingRequirements['User_application_requirements_id'];
        $updateQuery = "UPDATE user_application_requirements SET Scholarship_Application_form_1 = ?, Scholarship_Application_form_2 = ?, Report_Card_form_138 = ?, Enrollment_form = ?, Indigency_certification = ?, Supporting_documents = ?, updated_at = NOW() WHERE User_application_requirements_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssssi", $uploadedFiles['scholarship_application_form_1'], $uploadedFiles['scholarship_application_form_2'], $uploadedFiles['report_card_form_138'], $uploadedFiles['enrollment_form'], $uploadedFiles['indigency_certification'], $uploadedFiles['supporting_documents'], $user_application_requirements_id);
        if (!$stmt->execute()) {
            die("Error updating user_application_requirements table: " . $stmt->error);
        }
    } else {
        $insertQuery = "INSERT INTO user_application_requirements (user_id, Scholarship_Application_form_1, Scholarship_Application_form_2, Report_Card_form_138, Enrollment_form, Indigency_certification, Supporting_documents, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("issssss", $user_id, $uploadedFiles['scholarship_application_form_1'], $uploadedFiles['scholarship_application_form_2'], $uploadedFiles['report_card_form_138'], $uploadedFiles['enrollment_form'], $uploadedFiles['indigency_certification'], $uploadedFiles['supporting_documents']);
        if (!$stmt->execute()) {
            die("Error inserting into user_application_requirements table: " . $stmt->error);
        }
        $user_application_requirements_id = $stmt->insert_id;
    }

    // Insert into scholarships_applications table
    $insertApplicationQuery = "INSERT INTO scholarships_applications (user_profile_id, User_application_requirements_id, scholarship_id, user_id, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($insertApplicationQuery);
    $stmt->bind_param("iiii", $user_profile_id, $user_application_requirements_id, $scholarship_id, $user_id);
    if (!$stmt->execute()) {
        die("Error inserting into scholarships_applications table: " . $stmt->error);
    }

    echo "<script>alert('Application submitted successfully!'); window.location.href = 'manage_applications.php';</script>";
}
?>
