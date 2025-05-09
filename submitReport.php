<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the data from the POST request
$title = $_POST['title'];
$content = $_POST['content'];
$reason = $_POST['reason'];
$fid = $_POST['fid'];

// Validate FID
if (!isset($fid) || empty($fid)) {
    die("Error: FID is required");
}

// Prepare the SQL statement
$sql = "INSERT INTO reportpost (FID, Title, Content, Reason) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $fid, $title, $content, $reason);

if ($stmt->execute()) {
    echo "New report created successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
