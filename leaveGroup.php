<?php
session_start();
$conn = new mysqli("localhost", "root", "", "socialmedia");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed"]));
}

if (!isset($_SESSION["userID"])) {
    die(json_encode(["success" => false, "error" => "User not logged in"]));
}

$userID = $_SESSION["userID"];
$groupID = $_POST["groupID"] ?? null;

if (!$groupID) {
    die(json_encode(["success" => false, "error" => "Missing group ID"]));
}

// Remove the user from the group
$stmt = $conn->prepare("DELETE FROM groupmember WHERE gID = ? AND UID = ?");
$stmt->bind_param("ii", $groupID, $userID);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to leave group"]);
}

$stmt->close();
$conn->close();
