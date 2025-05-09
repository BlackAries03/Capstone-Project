<?php
if (session_status() === PHP_SESSION_NONE) {
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

$loggedInUserID = $_SESSION['userID'] ?? null;
$targetUserID = $_POST['uid'] ?? null;
$action = $_POST['action'] ?? null;
$user_name = $_SESSION['username'] ?? 'UNKNOWN';


if (!$loggedInUserID || !$targetUserID || !in_array($action, ["follow", "unfollow"])) {
    die("Invalid request.");
}

if ($action === "follow") {
    $stmt1 = $conn->prepare("INSERT INTO follow (UID, following) VALUES (?, ?)");
    if ($stmt1) {
        $stmt1->bind_param("ii", $loggedInUserID, $targetUserID);
        $stmt1->execute();
        $stmt1->close();

        $notif_sql = "INSERT INTO notifications (userID, username, message, status) VALUES (?, ?, ?, 'n')";
        $notif_stmt = $conn->prepare($notif_sql);
        $message = "$user_name is following you.";
        $notif_stmt->bind_param("iss", $targetUserID, $user_name, $message);
        $notif_stmt->execute();
        $notif_stmt->close();

        header("Location: friend.php");
        exit();
    } else {
        die("SQL Error: " . $conn->error);
    }
} else if ($action === "unfollow") {
    $stmt1 = $conn->prepare("DELETE FROM follow WHERE UID = ? AND following = ?");
    if ($stmt1) {
        $stmt1->bind_param("ii", $loggedInUserID, $targetUserID);
        $stmt1->execute();
        $stmt1->close();

        $notif_sql = "INSERT INTO notifications (userID, username, message, status) VALUES (?, ?, ?, 'n')";
        $notif_stmt = $conn->prepare($notif_sql);
        $message = "$user_name unfollowed you.";
        $notif_stmt->bind_param("iss", $targetUserID, $user_name, $message);
        $notif_stmt->execute();
        $notif_stmt->close();

        header("Location: friend.php");
        exit();
    } else {
        die("SQL Error: " . $conn->error);
    }
}

$conn->close();
?>
