<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['userID'])) {
    die("User not logged in.");
}

$userID = $_SESSION['userID']; // Logged-in user ID

// Check if file is uploaded
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === 0) {
    $file = $_FILES['profilePic'];
    $targetDir = "picture/";
    $fileName = basename($file["name"]);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];

    // Create uploads directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Validate file type
    if (!in_array($fileType, $allowedTypes)) {
        die("Invalid file type.");
    }

    // Restrict file size (Max: 2MB)
    $maxFileSize = 10 * 1024 * 1024;
    if ($file["size"] > $maxFileSize) {
        die("File size exceeds the 10MB limit.");
    }

    // Generate a unique filename
    $newFileName = "profile_" . $userID . "." . $fileType;
    $targetFilePath = $targetDir . $newFileName;

    // Move the file to the uploads directory
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        // Store file path in the database
        $stmt = $conn->prepare("UPDATE udata SET profilePic = ? WHERE UID = ?");
        $stmt->bind_param("si", $targetFilePath, $userID);

        if ($stmt->execute()) {
            echo "Profile picture updated successfully.";
            $_SESSION['profilePic'] = $targetFilePath; // Update session
            echo "<script>setTimeout(function(){ window.location.href='_accountManagement.php'; }, 200);</script>";
        } else {
            echo "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "File upload failed.";
    }
} else {
    echo "No file uploaded.";
}

$conn->close();
