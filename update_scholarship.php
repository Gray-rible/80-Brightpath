<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['scholarshipId']);
    $name = mysqli_real_escape_string($conn, $_POST['scholarshipName']);
    $categoryId = mysqli_real_escape_string($conn, $_POST['scholarshipCategoryId']);
    $description = mysqli_real_escape_string($conn, $_POST['scholarshipDescription']);

    $query = "UPDATE scholarships SET 
              scholarship_name = '$name',
              scholarship_category_id = '$categoryId',
              scholarship_description = '$description'
              WHERE scholarship_id = $id";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>