<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to submit an application.");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $applicant_id = intval($_POST['applicant_id']);
    $scholarship_id = intval($_POST['scholarship_id']);

        // Debugging messages
        if ($applicant_id === null || $scholarship_id === null) {
            die("Error: Missing applicant_id or scholarship_id. Applicant ID: " . var_export($applicant_id, true) . ", Scholarship ID: " . var_export($scholarship_id, true));
        } else {
            echo "Debug: Applicant ID: $applicant_id, Scholarship ID: $scholarship_id<br>";
        }

        
    // Insert into applicant_scholarship_records
    $stmt = $conn->prepare("
        INSERT INTO applicant_scholarship_records (applicant_id, scholarship_id, status, submission_date)
        VALUES (?, ?, 'pending', NOW())
    ");
    $stmt->bind_param('ii', $applicant_id, $scholarship_id);
    if (!$stmt->execute()) {
        die("Error inserting into applicant_scholarship_records: " . $stmt->error);
    }
    $record_id = $stmt->insert_id;

    // Handle file uploads for requirements
    foreach ($_FILES as $key => $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $scholarship_requirement_id = intval(str_replace('requirement_', '', $key));
            $fileContent = file_get_contents($file['tmp_name']);

            $stmt = $conn->prepare("
                INSERT INTO applicant_requirements (record_id, applicant_id, scholarship_requirements_id, uploaded_file_path, status, submission_date)
                VALUES (?, ?, ?, ?, 'submitted', NOW())
            ");
            $stmt->bind_param('iiib', $record_id, $applicant_id, $scholarship_requirement_id, $fileContent);
            if (!$stmt->execute()) {
                die("Error inserting into applicant_requirements: " . $stmt->error);
            }
        }
    }

    echo "<script>alert('Application submitted successfully!'); window.location.href = 'manage_applications.php';</script>";
}
?>