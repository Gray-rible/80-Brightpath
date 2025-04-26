<?php
include 'db.php';

if (isset($_GET['scholarship_id'])) {
    $scholarshipId = intval($_GET['scholarship_id']);

    $query = "
        SELECT sr.scholarship_requirement_id, sr.label, r.requirement_name
        FROM scholarship_requirements sr
        INNER JOIN requirements r ON sr.requirement_id = r.requirement_id
        WHERE sr.scholarship_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $scholarshipId);
    $stmt->execute();
    $result = $stmt->get_result();

    $requirements = [];
    while ($row = $result->fetch_assoc()) {
        $requirements[] = $row;
    }

    echo json_encode($requirements);
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>