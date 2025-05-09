<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database connection failed."]));
}

$groupID = $_POST["groupID"];
$userID = $_POST["userID"];
$loggedInUserID = $_SESSION["userID"];

$stmt = $conn->prepare("SELECT role FROM groupmember WHERE gID = ? AND UID = ?");
$stmt->bind_param("ii", $groupID, $loggedInUserID);
$stmt->execute();
$result = $stmt->get_result();
$roleRow = $result->fetch_assoc();
$stmt->close();

if ($roleRow["role"] !== "creator") {
    die(json_encode(["success" => false, "error" => "Only the group creator can transfer the role."]));
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("UPDATE groupmember SET role = 'creator' WHERE gID = ? AND UID = ?");
    $stmt->bind_param("ii", $groupID, $userID);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE groupmember SET role = 'member' WHERE gID = ? AND UID = ?");
    $stmt->bind_param("ii", $groupID, $loggedInUserID);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
$conn->close();
