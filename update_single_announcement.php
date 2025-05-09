<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Connection failed']));
}

$data = json_decode(file_get_contents('php://input'), true);

$sql = "UPDATE announcement SET title = ?, updateType = ?, description = ? WHERE aID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $data['title'], $data['updateType'], $data['description'], $data['aID']);

$result = $stmt->execute();

echo json_encode(['success' => $result]);

$stmt->close();
$conn->close();
?> 