<?php
include 'db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit;
}

$action = $_POST["action"] ?? '';

switch ($action) {
    case "add_member":
        $groupID = $_POST["groupID"] ?? null;
        $memberID = $_POST["memberID"] ?? null;

        if (!$groupID || !$memberID) {
            echo json_encode(["success" => false, "error" => "Missing parameters"]);
            exit;
        }

        // Check if user is already in the group
        $checkStmt = $conn->prepare("SELECT * FROM groupmember WHERE gID = ? AND UID = ?");
        $checkStmt->bind_param("ii", $groupID, $memberID);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["success" => false, "error" => "User already in group"]);
            exit;
        }

        // Add user to the group
        $stmt = $conn->prepare("INSERT INTO groupmember (gID, UID, role) VALUES (?, ?, 'member')");
        $stmt->bind_param("ii", $groupID, $memberID);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to add member"]);
        }

        $stmt->close();
        $checkStmt->close();
        $conn->close();
        exit;

    default:
        echo json_encode(["success" => false, "error" => "Invalid action"]);
        exit;
}
