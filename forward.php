<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$userID = $_SESSION["userID"] ?? null;

if (!$userID) {
    die("User not logged in.");
}

$sql = "SELECT u.UID, u.userName, u.profilePic FROM follow f1 JOIN follow f2 ON f1.following = f2.UID JOIN uData u ON f1.following = u.UID WHERE f1.UID = ? AND f2.following = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $userID, $userID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $profilePic = !empty($row["profilePic"]) ? $row["profilePic"] : "picture/unknown.jpeg";
    $receiverID = $row["UID"];

    echo '<div class="message unread" onclick="location.href=\'chat.php?receiverID=' . $receiverID . '\';" style="cursor: pointer;">
        <img src="' . htmlspecialchars($profilePic) . '" alt="' . htmlspecialchars($row["userName"]) . '" />
        <div class="description">
            <h2>' . htmlspecialchars($row["userName"]) . '</h2>
        </div>
      </div>';
}

$stmt->close();

$sql = "SELECT g.gID, g.gName, g.gImg FROM groupmember gm JOIN g ON gm.gID = g.gID WHERE gm.UID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $groupImg = !empty($row["gImg"]) ? $row["gImg"] : "picture/group_default.jpeg";
    $groupID = $row["gID"];

    echo '<div class="message unread" onclick="location.href=\'groupChat.php?groupID=' . $groupID . '\';" style="cursor: pointer;">
            <img src="' . htmlspecialchars($groupImg) . '" alt="' . htmlspecialchars($row["gName"]) . '" />
            <div class="description">
                <h2>' . htmlspecialchars($row["gName"]) . '</h2>
            </div>
        </div>';
}

$stmt->close();
$conn->close();