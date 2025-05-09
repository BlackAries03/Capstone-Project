<?php
include 'getName.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$chartType = $_POST['chartType'];
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];

$data = [];

switch ($chartType) {
    case 'activeTime':
        $query = "SELECT DATE(f.time) as date, COUNT(*) as count
                  FROM feed f
                  WHERE f.time BETWEEN ? AND ?
                  GROUP BY DATE(f.time)
                  ORDER BY date";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        break;
        
    case 'newUsers':
        $query = "SELECT DATE(joinDate) as date, COUNT(*) as count 
                  FROM udata 
                  WHERE joinDate BETWEEN ? AND ?
                  GROUP BY DATE(joinDate)
                  ORDER BY date";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        break;
        
    case 'announcement':
        $query = "SELECT updateType, COUNT(*) as count 
                  FROM announcement 
                  WHERE timestamp BETWEEN ? AND ?
                  GROUP BY updateType
                  ORDER BY updateType";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        break;
        
    case 'follow':
        $query = "SELECT DATE(created_at) as date,
                  COUNT(CASE WHEN message LIKE '%is following you%' THEN 1 END) as follows,
                  COUNT(CASE WHEN message LIKE '%unfollowed you%' THEN 1 END) as unfollows
                  FROM notifications
                  WHERE created_at BETWEEN ? AND ?
                  GROUP BY DATE(created_at)
                  ORDER BY date";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        break;
        
    case 'group':
        $query = "SELECT DATE(created_at) as date,
                  COUNT(CASE WHEN message LIKE '%added you to%' THEN 1 END) as additions,
                  COUNT(CASE WHEN message LIKE '%removed you from%' THEN 1 END) as removals
                  FROM notifications
                  WHERE created_at BETWEEN ? AND ?
                  GROUP BY DATE(created_at)
                  ORDER BY date";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        break;
        
    case 'report':
        $query = "SELECT DATE(created_at) as date,
                  COUNT(*) as count
                  FROM notifications
                  WHERE message LIKE '%Admin has deleted post%'
                  AND created_at BETWEEN ? AND ?
                  GROUP BY DATE(created_at)
                  ORDER BY date";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $startDate, $endDate);
        break;
        
    default:
        echo json_encode([]);
        exit;
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($data);
} else {
    echo json_encode(['error' => $stmt->error]);
}

$stmt->close();
$conn->close(); 