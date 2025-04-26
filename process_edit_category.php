<?php
include 'db.php'; // Replace with your actual database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (!isset($_POST['scholarship_id']) || empty($_POST['scholarship_id'])) {
        die("Scholarship ID is missing.");
    }
    if (!isset($_POST['scholarship_category_id']) || empty($_POST['scholarship_category_id'])) {
        die("Category ID is missing.");
    }

    $scholarshipId = intval($_POST['scholarship_id']);
    $scholarshipCategoryId = intval($_POST['scholarship_category_id']);

    // Update the scholarship_category_id in the scholarships table
    $updateSql = "UPDATE scholarships SET scholarship_category_id = ? WHERE scholarship_id = ?";
    $stmt = $conn->prepare($updateSql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param('ii', $scholarshipCategoryId, $scholarshipId);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Category updated successfully!</p>";
        header("Location: edit_scholarship.php?scholarship_id=$scholarshipId"); // Redirect back to the edit page
        exit;
    } else {
        echo "<p style='color: red;'>Failed to update category: " . $stmt->error . "</p>";
    }
}
?>