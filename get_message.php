<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed.");
}

if (!isset($_SESSION["userID"])) {
    die("Unauthorized access.");
}

$userID = $_SESSION["userID"];
$receiverID = $_GET["receiverID"] ?? null;

if (!$receiverID) {
    die("No chat partner selected.");
}

$sql = "SELECT * FROM chathistory 
        WHERE (senderID = ? AND receiverID = ?) 
        OR (senderID = ? AND receiverID = ?) 
        ORDER BY time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $userID, $receiverID, $receiverID, $userID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $message = base64_decode($row["message"]);
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $position = ($row["senderID"] == $userID) ? "right" : "left";

echo "<div class='chat-message $position' data-id='{$row['cID']}'>
        <div class='message-content'>
            <p>$safeMessage</p>
        </div>

        <div class='message-options'>
            <ul class='dots'>
                <li>&#x22EE;</li>
                <ul class='dropdown-menu'>";

                echo "<li class='forward-option' data-message='" . htmlspecialchars(json_encode($message), ENT_QUOTES, 'UTF-8') . "'>Forward</li>";

            if ($row["senderID"] == $userID) {
                echo "<li class='retrieve-option' data-id='{$row['cID']}'>Retrieve</li>";
            }

echo "          </ul>
            </ul>
        </div>
    </div>";
}
$stmt->close();
$conn->close();
