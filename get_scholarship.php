<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT scholarship_name, scholarship_category_id, scholarship_description 
              FROM scholarships 
              WHERE scholarship_id = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo json_encode(mysqli_fetch_assoc($result));
    } else {
        echo json_encode(['error' => 'Scholarship not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>