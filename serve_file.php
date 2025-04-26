<?php
if (isset($_GET['file'])) {
    $filePath = urldecode($_GET['file']); // Decode the file path

    // Validate the file path to prevent directory traversal attacks
    $baseDir = __DIR__ . '/uploads/'; // Adjust this to your uploads directory
    $realPath = realpath($baseDir . $filePath);

    if ($realPath && strpos($realPath, $baseDir) === 0 && file_exists($realPath)) {
        // Serve the file with appropriate headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($realPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($realPath));
        readfile($realPath);
        exit;
    } else {
        echo "File not found or access denied.";
    }
} else {
    echo "No file specified.";
}
?>