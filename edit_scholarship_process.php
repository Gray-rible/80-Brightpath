<?php
include 'db.php'; // Replace with your actual database connection file
session_start();

if (!isset($_GET['scholarship_id']) || empty($_GET['scholarship_id'])) {
    die("Error: Scholarship ID is missing or invalid.");
}

$scholarshipId = intval($_GET['scholarship_id']);

// Fetch scholarship details
$sql = "SELECT 
    s.scholarship_name, 
    s.Scholarship_general_description_s1, 
    s.Scholarship_selection_criteria, 
    s.status_id AS status_id, 
    st.status_name, 
    s.image_data, 
    s.user_id, 
    s.slots_available, 
    s.Scholarship_education_details_s2, 
    s.Scholarship_financial_assistance_details_s3, 
    s.Scholarship_maintaing_s4, 
    s.Scholarship_effects_for_others_s5, 
    s.forfeiture_of_benefit, 
    s.note_for_submission, 
    s.scholarship_category_id,
    CONCAT(u.first_name, ' ', u.middle_name, ' ', u.last_name) AS admin_name,
    c.category_name
FROM scholarships s
LEFT JOIN users u ON s.user_id = u.user_id
LEFT JOIN scholarship_categories c ON s.scholarship_category_id = c.scholarship_category_id
LEFT JOIN status st ON s.status_id = st.status_id
WHERE s.scholarship_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $scholarshipId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Scholarship not found.");
}

$scholarship = $result->fetch_assoc();

// Update scholarship details
if (isset($_POST['update_scholarship'])) {
    // Add the update logic here (see above)
}

// Update requirements
if (isset($_POST['update_labels'])) {
    foreach ($_POST['labels'] as $scholarshipRequirementId => $label) {
        $updateSql = "UPDATE scholarship_requirements SET label = ? WHERE scholarship_requirement_id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param('si', $label, $scholarshipRequirementId);
        $stmt->execute();
    }
    echo "<p style='color: green;'>Labels updated successfully!</p>";
}

// Add new requirements
if (isset($_POST['add_requirement'])) {
    $requirementName = $_POST['requirement_name'];
    $requirementDescription = $_POST['requirement_description'];

    $insertRequirementSql = "INSERT INTO requirements (requirement_name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($insertRequirementSql);
    $stmt->bind_param('ss', $requirementName, $requirementDescription);
    $stmt->execute();

    $newRequirementId = $conn->insert_id; // Get new requirement ID
    $linkRequirementSql = "INSERT INTO scholarship_requirements (scholarship_id, requirement_id) VALUES (?, ?)";
    $stmt = $conn->prepare($linkRequirementSql);
    $stmt->bind_param('ii', $scholarshipId, $newRequirementId);
    $stmt->execute();
    echo "<p style='color: green;'>New requirement added successfully!</p>";
}

// Delete requirements
if (isset($_POST['delete_requirement'])) {
    $scholarshipRequirementId = $_POST['scholarship_requirement_id'];
    $deleteSql = "DELETE FROM scholarship_requirements WHERE scholarship_requirement_id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param('i', $scholarshipRequirementId);
    $stmt->execute();
    echo "<p style='color: green;'>Requirement removed successfully!</p>";
}

// Fetch linked requirements
$sqlRequirements = "SELECT sr.scholarship_requirement_id, sr.label, r.requirement_name, r.description
                    FROM scholarship_requirements sr
                    JOIN requirements r ON sr.requirement_id = r.requirement_id
                    WHERE sr.scholarship_id = ?";
$stmt = $conn->prepare($sqlRequirements);
$stmt->bind_param('i', $scholarshipId);
$stmt->execute();
$requirementsResult = $stmt->get_result();
$requirements = [];
while ($row = $requirementsResult->fetch_assoc()) {
    $requirements[] = $row;
}
?>