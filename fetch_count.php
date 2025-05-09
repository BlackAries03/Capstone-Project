<?php
include 'getName.php';

$conn = new mysqli("localhost", "root", "", "socialmedia");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION["userID"] ?? null;
$query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE userid = ? AND status = 'n'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['unread_count' => $row['unread_count']]);
$stmt->close();
$conn->close();
?>
