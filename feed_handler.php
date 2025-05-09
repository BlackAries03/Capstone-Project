<?php
session_start();
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'username', 'password', 'database_name');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_GET['action'] == 'getFeeds') {
    $query = "SELECT f.*, u.username, u.profile_pic 
              FROM feed f
              JOIN users u ON f.UID = u.UID
              ORDER BY f.time DESC";
    $result = $conn->query($query);
    $feeds = [];
    while($row = $result->fetch_assoc()) {
        $feeds[] = $row;
    }
    echo json_encode($feeds);
}

if ($_GET['action'] == 'getFeed') {
    $fid = intval($_GET['fid']);
    $uid = $_SESSION['uid'] ?? 0;
    
    $feedQuery = "SELECT f.*, u.username, 
                  EXISTS(SELECT 1 FROM likes WHERE FID = $fid AND UID = $uid) as is_liked
                  FROM feed f
                  JOIN users u ON f.UID = u.UID
                  WHERE f.FID = $fid";
    
    $result = $conn->query($feedQuery);
    echo json_encode($result->fetch_assoc());
}

if ($_POST['action'] == 'toggleLike') {
    $fid = intval($_POST['fid']);
    $uid = $_SESSION['uid'];

    $check = $conn->query("SELECT * FROM likes WHERE FID = $fid AND UID = $uid");
    
    if($check->num_rows > 0) {
        $conn->query("DELETE FROM likes WHERE FID = $fid AND UID = $uid");
        $conn->query("UPDATE feed SET likes = GREATEST(likes - 1, 0) WHERE FID = $fid");
    } else {
        $conn->query("INSERT INTO likes (FID, UID) VALUES ($fid, $uid)");
        $conn->query("UPDATE feed SET likes = likes + 1 WHERE FID = $fid");
    }
    $count = $conn->query("SELECT likes FROM feed WHERE FID = $fid")->fetch_assoc()['likes'];
    echo json_encode(['success' => true, 'count' => $count]);
}

if ($_POST['action'] == 'addComment') {
    $fid = intval($_POST['fid']);
    $uid = $_SESSION['uid'];
    $message = $conn->real_escape_string($_POST['message']);
    
    $stmt = $conn->prepare("INSERT INTO comments (FID, UID, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $fid, $uid, $message);
    $stmt->execute();
    
    echo json_encode(['success' => true]);
}

if ($_GET['action'] == 'getComments') {
    $fid = intval($_GET['fid']);
    
    $query = "SELECT c.*, u.username, u.profile_pic 
              FROM comments c
              JOIN users u ON c.UID = u.UID
              WHERE c.FID = $fid
              ORDER BY c.time DESC";
    
    $result = $conn->query($query);
    $comments = [];
    while($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    echo json_encode($comments);
}