<?php
include 'db.php'; // Replace with your actual database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the uploaded file
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        if ($imageData === false) {
            echo json_encode(['success' => false, 'error' => 'Failed to read the uploaded file.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No image uploaded or upload error.']);
        exit;
    }

    // Validate scholarship_id
    if (!isset($_POST['scholarship_id']) || empty($_POST['scholarship_id'])) {
        echo json_encode(['success' => false, 'error' => 'Scholarship ID is missing.']);
        exit;
    }

    $scholarshipId = intval($_POST['scholarship_id']);

    // Prepare the SQL query to update the image_data column
    $sql = "UPDATE scholarships SET image_data = ? WHERE scholarship_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->send_long_data(0, $imageData); // Send binary data for the first parameter
        $stmt->bind_param('i', $scholarshipId); // Bind the second parameter (scholarship_id)

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'newImageSrc' => "data:image/jpeg;base64," . base64_encode($imageData)
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update the database: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare the SQL statement.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

$conn->close();
?>