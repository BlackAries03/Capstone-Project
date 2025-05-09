<?php
session_start();
include 'getName.php';


$response = ['success' => false, 'error' => ''];

try {
    if (!isset($_SESSION['UID'])) {
        throw new Exception('User not logged in');
    }

    if (!isset($_FILES['story_image'])) {
        throw new Exception('No image uploaded');
    }

    $file = $_FILES['story_image'];
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetDir = 'stories/';
    $targetPath = $targetDir . $fileName;

    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type');
    }

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $stmt = $conn->prepare("INSERT INTO stories (UID, img, postTime, expiryTime) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 24 HOUR))");
        $stmt->bind_param("is", $_SESSION['UID'], $targetPath);
        
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            throw new Exception('Database error');
        }
    } else {
        throw new Exception('Failed to move uploaded file');
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?> 