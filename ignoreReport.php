<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['rid'])) {
    $rid = $conn->real_escape_string($_GET['rid']);
    
    // Delete from reportpost table only
    $deleteReportStmt = $conn->prepare("DELETE FROM reportpost WHERE RID = ?");
    if (!$deleteReportStmt) {
        die("Prepare failed: " . $conn->error);
    }
    $deleteReportStmt->bind_param("i", $rid);
    
    if ($deleteReportStmt->execute()) {
        echo "<script>alert('Report ignored successfully!'); window.location.href='__ContentManagement.php';</script>";
    } else {
        echo "Error: " . $deleteReportStmt->error;
    }
    
    $deleteReportStmt->close();
}

$conn->close();
?>