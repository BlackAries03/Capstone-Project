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
$groupID = $_GET["groupID"] ?? null;

if (!$groupID) {
    die("No group selected.");
}

$sql = "SELECT gm.mID, gm.gID, gm.UID, gm.message, gm.time, u.userName, u.profilePic 
        FROM groupmessage gm
        JOIN udata u ON gm.UID = u.UID
        WHERE gm.gID = ? 
        ORDER BY gm.time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $message = base64_decode($row["message"]);
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $position = ($row["UID"] == $userID) ? "right" : "left";
    $formattedTime = date("Y-m-d H:i", strtotime($row["time"]));

    echo "<div class='chat-message-container' style='
    display: flex; 
    flex-direction: column; 
    align-items: " . ($position == 'left' ? 'flex-start' : 'flex-end') . "; 
    width: 100%; 
    margin-bottom: 10px;
'>
    <div style='display: flex; align-items: center; gap: 8px;'>
        " . ($position == 'left' ? "<img src='" . (!empty($row['profilePic']) ? $row['profilePic'] : 'picture/unknown.jpeg') . "' 
            alt='Profile Picture' 
            class='profile-pic' 
            style='width: 30px; height: 30px; border-radius: 50%;' />" : "") . "
        
        <span class='username' style='font-weight: bold;'>" . htmlspecialchars($row['userName']) . "</span>
        
        " . ($position == 'right' ? "<img src='" . (!empty($row['profilePic']) ? $row['profilePic'] : 'picture/unknown.jpeg') . "' 
            alt='Profile Picture' 
            class='profile-pic' 
            style='width: 30px; height: 30px; border-radius: 50%;' />" : "") . "
    </div>

    <div style='display: flex; align-items: center; width: 100%; max-width: 90%; " . ($position == 'right' ? 'flex-direction: row-reverse;' : '') . "'>
        <div class='chat-message " . ($position == 'left' ? 'left' : 'right') . "' style='
            max-width: 70%; 
            min-width: 50px;
            min-height: 20px;
            padding: 8px 12px;
            border-radius: 18px;
            margin-bottom: 10px;
            position: relative;
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.4;
        '>
            <div class='message-content'>
                <p style='margin: 0;'>" . $safeMessage . "</p>
            </div>
        </div>

        <div class='message-options'>
            <ul class='dots' style='
                list-style: none; 
                cursor: pointer; 
                position: relative;
                " . ($position == 'right' ? 'left: -20px;' : 'right: -20px;') . "
                top: 50%;
                transform: translateY(-50%);
            '>
                <li style='font-size: 16px; padding: 5px;'>&#x22EE;</li>
                <ul class='dropdown-menu'>
                    <li class='forward-option' data-message='" . htmlspecialchars(json_encode($message), ENT_QUOTES, 'UTF-8') . "'>Forward</li>
                    " . ($row["UID"] == $userID ? "<li class='retrieve-option' data-id='{$row['mID']}'>Retrieve</li>" : "") . "
                </ul>
            </ul>
        </div>
    </div>

    <span class='time' style='font-size: 10px; color: #555; margin-top: 4px; display: block; text-align: " . ($position == 'left' ? 'left' : 'right') . ";'>
        $formattedTime
    </span>
</div>";
}

$stmt->close();
$conn->close();
?>
