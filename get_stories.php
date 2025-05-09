<?php
session_start();
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'error' => 'Connection failed'
    ]);
    exit;
}

$logged_in_uid = $_SESSION['UID'] ?? null;
$current_time = date('Y-m-d H:i:s');

if (!$logged_in_uid) {
    echo json_encode([
        'success' => false,
        'error' => 'User not logged in'
    ]);
    exit;
}

try {
    $delete_sql = "DELETE FROM stories WHERE expiryTime < ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("s", $current_time);
    $stmt->execute();

    $own_stories_sql = "SELECT s.storiesID, s.UID, s.img as image_path, 
                               s.postTime as upload_time, s.expiryTime,
                               u.userName, u.profilePic 
                        FROM stories s 
                        JOIN udata u ON s.UID = u.UID 
                        WHERE s.UID = ? AND s.expiryTime > ?
                        ORDER BY s.postTime DESC";

    $following_stories_sql = "SELECT s.storiesID, s.UID, s.img as image_path, 
                                    s.postTime as upload_time, s.expiryTime,
                                    u.userName, u.profilePic 
                             FROM stories s 
                             JOIN udata u ON s.UID = u.UID 
                             JOIN follow f ON s.UID = f.following 
                             WHERE f.UID = ? AND s.expiryTime > ? 
                             ORDER BY s.postTime DESC";

    $stmt = $conn->prepare($own_stories_sql);
    $stmt->bind_param("is", $logged_in_uid, $current_time);
    $stmt->execute();
    $own_result = $stmt->get_result();
    $own_stories = [];
    while ($row = $own_result->fetch_assoc()) {
        $own_stories[] = $row;
    }

    $stmt = $conn->prepare($following_stories_sql);
    $stmt->bind_param("is", $logged_in_uid, $current_time);
    $stmt->execute();
    $following_result = $stmt->get_result();
    $following_stories = [];
    while ($row = $following_result->fetch_assoc()) {
        $following_stories[] = $row;
    }

    echo json_encode([
        'success' => true,
        'own_stories' => $own_stories,
        'following_stories' => $following_stories
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>