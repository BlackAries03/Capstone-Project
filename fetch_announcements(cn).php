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
$sql = "SELECT title, updateType, description, timestamp FROM announcement";
$result = $conn->query($sql);

$announcements = "";
if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $descriptionWithBreaks = nl2br(htmlspecialchars($row["description"])); // Convert newlines to <br> tags
        $announcements .= "<div class='announcement'>";
        $announcements .= "<h2>" . htmlspecialchars($row["title"]) . "</h2>";
        $announcements .= "<p><span class='update-type'>" . htmlspecialchars($row["updateType"]) . "</span> (" . htmlspecialchars($row["timestamp"]) . ")</p>";
        $announcements .= "<p>" . $descriptionWithBreaks . "</p>";
        $announcements .= "</div>";
    }
} else {
    $announcements = "未找到公告.";
}

$conn->close();

echo $announcements;
?>
