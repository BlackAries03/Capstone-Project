<?php
session_start();
header("Content-Type: application/json"); // Always return JSON

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed."]);
    exit();
}

if (!isset($_SESSION["userID"]) || !isset($_POST["receiverID"]) || !isset($_POST["message"])) {
    echo json_encode(["success" => false, "error" => "Unauthorized access."]);
    exit();
}

$userID = intval($_SESSION["userID"]);
$receiverID = intval($_POST["receiverID"]);
$message = trim($_POST["message"]);

if (empty($message)) {
    echo json_encode(["success" => false, "error" => "Message cannot be empty."]);
    exit();
}

$encodedMessage = base64_encode($message);

$sql = "INSERT INTO chathistory (senderID, receiverID, message, time) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $userID, $receiverID, $encodedMessage);
$success = $stmt->execute();

if ($success) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to send message."]);
}

$stmt->close();
$conn->close();
