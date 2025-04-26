<?php
include 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'] ?? null;
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $place_of_birth = $_POST['place_of_birth'] ?? null;
    $mobile_number = $_POST['mobile_number'] ?? null;
    $email_address = $_POST['email_address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $street_barangay = $_POST['street_barangay'] ?? null;
    $town_city_municipality = $_POST['town_city_municipality'] ?? null;
    $province = $_POST['province'] ?? null;
    $zip_code = $_POST['zip_code'] ?? null;
    $school_attended = $_POST['school_attended'] ?? null;
    $school_id_number = $_POST['school_id_number'] ?? null;
    $grade_level = $_POST['grade_level'] ?? null;
    $course = $_POST['course'] ?? null;
    $type_of_disability = $_POST['type_of_disability'] ?? null;

    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    // Insert into the database
    $stmt = $conn->prepare("
        INSERT INTO applicants (
            first_name, middle_name, last_name, birthdate, place_of_birth, 
            mobile_number, email_address, password, profile_picture, 
            street_barangay, town_city_municipality, province, zip_code, 
            school_attended, school_id_number, grade_level, course, type_of_disability
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'ssssssssbsssssssss',
        $first_name, $middle_name, $last_name, $birthdate, $place_of_birth,
        $mobile_number, $email_address, $password, $profile_picture,
        $street_barangay, $town_city_municipality, $province, $zip_code,
        $school_attended, $school_id_number, $grade_level, $course, $type_of_disability
    );

    if ($stmt->execute()) {
        echo "<script>alert('Successfully created!'); window.location.href = 'apply1.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>