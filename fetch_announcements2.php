<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch announcements
$sql = "SELECT aID, title, updateType, description FROM announcement";
$result = $conn->query($sql);

$announcements = array();
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

$conn->close();

echo json_encode($announcements);
?>
