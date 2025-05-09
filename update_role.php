<?php
session_start();
error_log(print_r($_SESSION, true)); // Log session data

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database Connection Failed: " . mysqli_connect_error()]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['uid']) && is_numeric($data['uid'])) {
        $userID = intval($data['uid']);

        // Check if the UID exists in the udata table and get the current role
        $check_stmt = $conn->prepare("SELECT UID, role FROM uData WHERE UID = ?");
        $check_stmt->bind_param("i", $userID);
        $check_stmt->execute();
        $check_stmt->store_result();
        $check_stmt->bind_result($uid, $role);
        $check_stmt->fetch();

        if ($check_stmt->num_rows > 0) {
            // Toggle role between 'Admin' and ''
            $newRole = ($role === 'Admin') ? '' : 'Admin';

            // Update the role column
            $stmt = $conn->prepare("UPDATE uData SET role = ? WHERE UID = ?");
            if (!$stmt) {
                echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
                exit();
            }

            $stmt->bind_param("si", $newRole, $userID);
            if ($stmt->execute()) {
                error_log("Role updated successfully"); // Debugging log
                echo json_encode(["status" => "success", "message" => "Role updated successfully!", "role" => $newRole]);
            } else {
                error_log("Error updating role: " . $stmt->error); // Debugging log
                echo json_encode(["status" => "error", "message" => "Error updating role!"]);
            }

            $stmt->close();
        } else {
            echo json_encode(["status" => "error", "message" => "User ID does not exist!"]);
        }

        $check_stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid user ID!"]);
    }
}

$conn->close();
?>
