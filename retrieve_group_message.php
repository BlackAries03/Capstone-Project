<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed.");
}

$groupID = $_POST["groupID"];
$messageID = $_POST["mID"]; 

$sql = "DELETE FROM groupmessage WHERE gID = ? AND mID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "error" => $conn->error]);
    exit();
}

$stmt->bind_param("ii", $groupID, $messageID); 

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

// Close the connection
$conn->close();
