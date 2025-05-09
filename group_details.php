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
$userID = $_SESSION["userID"];

$stmt = $conn->prepare("SELECT role FROM groupmember WHERE gID = ? AND UID = ?");
$stmt->bind_param("ii", $groupID, $userID);
$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc()["role"] ?? null;

if ($role !== "creator") {
    die(json_encode(["success" => false, "error" => "Unauthorized access"]));
}

$action = $_POST["action"];

switch ($action) {
    case "update_name":
        $newName = trim($_POST["newName"]);
        if (empty($newName)) {
            echo json_encode(["success" => false, "error" => "Group name cannot be empty"]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE g SET gName = ? WHERE gID = ?");
        $stmt->bind_param("si", $newName, $groupID);
        break;

    case "update_image":
        if (!isset($_FILES["groupImage"])) {
            echo json_encode(["success" => false, "error" => "No file uploaded"]);
            exit;
        }

        $targetDir = "picture/groups/";
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($_FILES["groupImage"]["name"]);
        $targetFile = $targetDir . uniqid() . "_" . $fileName;

        // Validate image
        $check = getimagesize($_FILES["groupImage"]["tmp_name"]);
        if ($check === false) {
            echo json_encode(["success" => false, "error" => "File is not an image"]);
            exit;
        }

        // Verify file size (e.g., 2MB max)
        if ($_FILES["groupImage"]["size"] > 2000000) {
            echo json_encode(["success" => false, "error" => "File too large (max 2MB)"]);
            exit;
        }

        if (move_uploaded_file($_FILES["groupImage"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("UPDATE g SET gImg = ? WHERE gID = ?");
            $stmt->bind_param("si", $targetFile, $groupID);
            $success = $stmt->execute();
            $stmt->close();

            if ($success) {
                echo json_encode([
                    "success" => true,
                    "newImagePath" => $targetFile
                ]);
            } else {
                echo json_encode(["success" => false, "error" => "Database update failed"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Error uploading file"]);
        }
        exit;

    case "add_member":
        $groupID = $_POST["groupID"] ?? null;
        $memberID = $_POST["memberID"] ?? null;

        if (!$groupID || !$memberID) {
            echo json_encode(["success" => false, "error" => "Missing parameters"]);
            exit;
        }

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
            $name_stmt = $conn->prepare("SELECT u.userName, g.gName FROM udata u, g WHERE u.UID = ? AND g.gID = ?");
            $name_stmt->bind_param("ii", $_SESSION["userID"], $groupID);
            $name_stmt->execute();
            $name_result = $name_stmt->get_result();
            $names = $name_result->fetch_assoc();
            $name_stmt->close();    

            // Add notification
            $notif_sql = "INSERT INTO notifications (userid, username, message, status, unread_count) VALUES (?, ?, ?, 'n', 1)";
            $notif_stmt = $conn->prepare($notif_sql);
            if (!$notif_stmt) {
                error_log("Debug - Prepare Error: " . $conn->error);
                die(json_encode(['success' => false, 'message' => 'Error preparing notification statement: ' . $conn->error]));
            }
            
            $message = $names['userName'] . " added you to " . $names['gName'] . ".";
            
            try {
                $notif_stmt->bind_param("iss", $memberID, $names['userName'], $message);
                if (!$notif_stmt->execute()) {
                    error_log("Debug - Execute Error: " . $notif_stmt->error);
                    die(json_encode(['success' => false, 'message' => 'Error adding notification: ' . $notif_stmt->error]));
                }
            } catch (Exception $e) {
                error_log("Debug - Exception: " . $e->getMessage());
                die(json_encode(['success' => false, 'message' => 'Error in notification process: ' . $e->getMessage()]));
            }
            
            $notif_stmt->close();
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

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
