<?php
// Start session and include database connection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db.php'; // Replace with your actual database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect scholarship data
    $scholarship_name = $_POST['scholarship_name'] ?? null;
    $scholarship_category_id = $_POST['scholarship_category_id'] ?? null;
    $slots_available = $_POST['slots_available'] ?? 0;
    $Scholarship_general_description_s1 = $_POST['Scholarship_general_description_s1'] ?? null;
    $scholarship_selection_criteria = $_POST['scholarship_selection_criteria'] ?? null;
    $scholarship_education_details_s2 = $_POST['scholarship_education_details_s2'] ?? null;
    $scholarship_financial_assistance_details_s3 = $_POST['scholarship_financial_assistance_details_s3'] ?? null;
    $scholarship_maintaing_s4 = $_POST['scholarship_maintaing_s4'] ?? null;
    $scholarship_effects_for_others_s5 = $_POST['scholarship_effects_for_others_s5'] ?? null;
    $forfeiture_of_benefit = $_POST['forfeiture_of_benefit'] ?? null;
    $note_for_submission = $_POST['note_for_submission'] ?? null;
    $status_id = $_POST['status_id'] ?? null; // Get the status_id from the form
    $user_id = $_SESSION['user_id']; // Logged-in user's ID

    // Debugging: Check if status_id is being received
    if (!$status_id) {
        die("Error: Status field is not being submitted.");
    }

    // Validate the status_id field
    if (!in_array($status_id, ['1', '2', '3', '4', '5'])) {
        die("Invalid status value.");
    }

    // Handle image upload
    $imageData = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
    }

    // Prepare the SQL statement for inserting the scholarship
    $sql = "INSERT INTO scholarships (
        scholarship_name, scholarship_category_id, slots_available, Scholarship_general_description_s1,
        scholarship_selection_criteria, scholarship_education_details_s2,
        scholarship_financial_assistance_details_s3, scholarship_maintaing_s4, scholarship_effects_for_others_s5,
        forfeiture_of_benefit, note_for_submission, user_id, status_id, image_data
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param(
        "sissssssssssis",
        $scholarship_name, $scholarship_category_id, $slots_available, $Scholarship_general_description_s1,
        $scholarship_selection_criteria, $scholarship_education_details_s2,
        $scholarship_financial_assistance_details_s3, $scholarship_maintaing_s4, $scholarship_effects_for_others_s5,
        $forfeiture_of_benefit, $note_for_submission, $user_id, $status_id, $imageData
    );

    // Execute the query and check for errors
    if ($stmt->execute()) {
        $scholarship_id = $conn->insert_id; // Get the ID of the newly created scholarship

        // Process selected requirements
        if (isset($_POST['requirements']) && is_array($_POST['requirements'])) {
            foreach ($_POST['requirements'] as $requirement_id) {
                $label = $_POST['labels'][$requirement_id] ?? null;
                $requirement_sql = "INSERT INTO scholarship_requirements (scholarship_id, requirement_id, label)
                                    VALUES (?, ?, ?)";
                $requirement_stmt = $conn->prepare($requirement_sql);
                $requirement_stmt->bind_param('iis', $scholarship_id, $requirement_id, $label);
                $requirement_stmt->execute();
            }
        }

        // Process new requirements and labels
        if (isset($_POST['new_requirements']) && is_array($_POST['new_requirements'])) {
            foreach ($_POST['new_requirements'] as $index => $requirement_name) {
                $requirement_description = $_POST['new_descriptions'][$index] ?? null;
                $label = $_POST['new_labels'][$index] ?? null;

                // Skip empty fields
                if (empty($requirement_name) || empty($requirement_description)) {
                    continue; // Skip this iteration if fields are empty
                }

                // Insert new requirement
                $new_requirement_sql = "INSERT INTO requirements (requirement_name, description) VALUES (?, ?)";
                $new_requirement_stmt = $conn->prepare($new_requirement_sql);
                $new_requirement_stmt->bind_param('ss', $requirement_name, $requirement_description);
                $new_requirement_stmt->execute();
                $new_requirement_id = $conn->insert_id;

                // Link new requirement to scholarship
                $link_sql = "INSERT INTO scholarship_requirements (scholarship_id, requirement_id, label)
                             VALUES (?, ?, ?)";
                $link_stmt = $conn->prepare($link_sql);
                $link_stmt->bind_param('iis', $scholarship_id, $new_requirement_id, $label);
                $link_stmt->execute();
            }
        }

        echo "Scholarship and requirements added successfully!";
    } else {
        // Debugging SQL errors
        echo "Error executing query: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>