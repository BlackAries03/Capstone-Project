<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);
$uid = $data['uid'];
$username = $data['username'];

// Check if user is banned first
$checkSql = "SELECT * FROM banneduser WHERE UID = ? AND userName = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param('is', $uid, $username);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Cannot unban user that is not banned.']);
    $checkStmt->close();
    $conn->close();
    exit;
}

// Remove user from banneduser table
$sql = "DELETE FROM banneduser WHERE UID = ? AND userName = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $uid, $username);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$checkStmt->close();
$conn->close();
?>
