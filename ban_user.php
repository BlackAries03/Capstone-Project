<?php
header('Content-Type: application/json');

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);
$uid = $input['uid'];
$username = $input['username'];

// Database connection
$servername = "localhost";
$db_username = "root";  // Changed variable name to avoid conflict
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $db_username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Update the banneduser table
$sql = "INSERT INTO banneduser (UID, username) VALUES ('$uid', '$username')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
}

$conn->close();
?>
