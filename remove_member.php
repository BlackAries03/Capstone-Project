<?php
header('Content-Type: application/json');
error_reporting(0);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$response = array();

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    $groupID = $_POST["groupID"];
    $userID = $_POST["userID"];

    $stmt = $conn->prepare("SELECT role FROM groupmember WHERE gID = ? AND UID = ?");
    $stmt->bind_param("ii", $groupID, $_SESSION["userID"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $roleRow = $result->fetch_assoc();
    $stmt->close();

    if ($roleRow["role"] !== "creator") {
        throw new Exception("Only the group creator can remove members.");
    }

    $stmt = $conn->prepare("DELETE FROM groupmember WHERE gID = ? AND UID = ?");
    $stmt->bind_param("ii", $groupID, $userID);
    
    if ($stmt->execute()) {
        // Get userName and groupName
        $name_stmt = $conn->prepare("SELECT u.userName, g.gName FROM udata u, g WHERE u.UID = ? AND g.gID = ?");
        $name_stmt->bind_param("ii", $_SESSION["userID"], $groupID);
        $name_stmt->execute();
        $name_result = $name_stmt->get_result();
        $names = $name_result->fetch_assoc();
        $name_stmt->close();

        if ($names) {
            // Add notification
            $notif_sql = "INSERT INTO notifications (userID, username, message, status) VALUES (?, ?, ?, 'n')";
            $notif_stmt = $conn->prepare($notif_sql);
            $message = $names['userName'] . " removed you from " . $names['gName'];
            $notif_stmt->bind_param("iss", $userID, $names['userName'], $message);
            $notif_stmt->execute();
            $notif_stmt->close();
        }

        $response["success"] = true;
    } else {
        throw new Exception("Failed to remove member");
    }
    $stmt->close();

} catch (Exception $e) {
    $response["success"] = false;
    $response["error"] = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    echo json_encode($response);
}
