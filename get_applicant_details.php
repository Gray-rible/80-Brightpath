<?php
include 'db.php';

if (!isset($_GET['applicant_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing applicant_id']);
    exit;
}

$applicant_id = intval($_GET['applicant_id']);

$query = "SELECT first_name, middle_name, last_name, birthdate, place_of_birth, mobile_number, email_address, street_barangay, town_city_municipality, province, zip_code, school_attended, school_id_number, grade_level, course, type_of_disability, profile_picture FROM applicants WHERE applicant_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $applicant = $result->fetch_assoc();

    // Encode the profile_picture as Base64
    if (!empty($applicant['profile_picture'])) {
        $applicant['profile_picture'] = base64_encode($applicant['profile_picture']);
    }

    echo json_encode($applicant);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Applicant not found']);
}
?>