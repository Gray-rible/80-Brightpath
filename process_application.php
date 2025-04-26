<?php
// Start the session (if needed) and include database connection
session_start();
include 'db_connection.php'; // Replace with your actual database connection file

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect applicant personal details from POST
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $place_of_birth = $_POST['place_of_birth'];
    $mobile_number = $_POST['mobile_number'];
    $email_address = $_POST['email_address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    
    // Address fields
    $street_barangay = $_POST['street_barangay'];
    $town_city_municipality = $_POST['town_city_municipality'];
    $province = $_POST['province'];
    $zip_code = $_POST['zip_code'];
    
    // School fields
    $school_attended = $_POST['school_attended'];
    $school_id_number = $_POST['school_id_number'];
    
    // Optional fields
    $type_of_disability = isset($_POST['type_of_disability']) ? $_POST['type_of_disability'] : null;
    
    // File uploads (e.g., profile picture and documents)
    $profile_picture = addslashes(file_get_contents($_FILES['profile_picture']['tmp_name'])); // Convert file to binary
    $application_letter = addslashes(file_get_contents($_FILES['application_letter']['tmp_name']));
    $seniorhigh_grading_report = addslashes(file_get_contents($_FILES['seniorhigh_grading_report']['tmp_name']));
    $grades_photocopy = addslashes(file_get_contents($_FILES['grades_photocopy']['tmp_name']));
    $registration_form = addslashes(file_get_contents($_FILES['registration_form']['tmp_name']));
    $supporting_documents = addslashes(file_get_contents($_FILES['supporting_documents']['tmp_name']));
    $medical_certificate = addslashes(file_get_contents($_FILES['medical_certificate']['tmp_name']));
    $scholarship_application_form_1 = addslashes(file_get_contents($_FILES['scholarship_application_form_1']['tmp_name']));
    $scholarship_application_form_2 = addslashes(file_get_contents($_FILES['scholarship_application_form_2']['tmp_name']));
    $birth_certificate = addslashes(file_get_contents($_FILES['birth_certificate']['tmp_name']));
    
    // Prepare SQL queries to insert applicant data into database
    $sql_applicant = "INSERT INTO Applicants (
        first_name, middle_name, last_name, birthdate, place_of_birth, mobile_number, email_address, password,
        profile_picture, street_barangay, town_city_municipality, province, zip_code, 
        school_attended, school_id_number, type_of_disability
    ) VALUES (
        '$first_name', '$middle_name', '$last_name', '$birthdate', '$place_of_birth', '$mobile_number', '$email_address', '$password',
        '$profile_picture', '$street_barangay', '$town_city_municipality', '$province', '$zip_code',
        '$school_attended', '$school_id_number', '$type_of_disability'
    )";
    
    // Insert the data into the Applicants table
    if (mysqli_query($conn, $sql_applicant)) {
        $applicant_id = mysqli_insert_id($conn); // Get the ID of the inserted applicant record
        
        // Insert file uploads into Applicant Requirements table
        $sql_requirements = "INSERT INTO Applicant_Requirements (
            applicant_id, scholarship_requirement_id, document_path, status
        ) VALUES
            ($applicant_id, 1, '$application_letter', 'Submitted'),
            ($applicant_id, 2, '$seniorhigh_grading_report', 'Submitted'),
            ($applicant_id, 3, '$grades_photocopy', 'Submitted'),
            ($applicant_id, 4, '$registration_form', 'Submitted'),
            ($applicant_id, 5, '$supporting_documents', 'Submitted'),
            ($applicant_id, 6, '$medical_certificate', 'Submitted'),
            ($applicant_id, 7, '$scholarship_application_form_1', 'Submitted'),
            ($applicant_id, 8, '$scholarship_application_form_2', 'Submitted'),
            ($applicant_id, 9, '$birth_certificate', 'Submitted')";
        
        if (mysqli_query($conn, $sql_requirements)) {
            echo "Application submitted successfully!";
        } else {
            echo "Error submitting requirements: " . mysqli_error($conn);
        }
    } else {
        echo "Error inserting applicant data: " . mysqli_error($conn);
    }
    
    // Close the database connection
    mysqli_close($conn);
}
?>
