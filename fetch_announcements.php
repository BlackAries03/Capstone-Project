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
$sql = "SELECT aID, title FROM announcement";
$result = $conn->query($sql);

$announcements = "";
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $announcements .= "<div class='announcement-title' onclick='showAnnouncementDetails(" . $row["aID"] . ")'>";
        $announcements .= "<h2>" . htmlspecialchars($row["title"]) . "</h2>";
        $announcements .= "</div>";
    }
} else {
    $announcements = "No announcements found.";
}

$conn->close();

echo $announcements;
?>
