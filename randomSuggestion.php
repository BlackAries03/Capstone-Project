<?php
if (session_status() === PHP_SESSION_NONE) {
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

$loggedInUserID = $_SESSION['userID'] ?? null;

$sql = "SELECT u.UID, u.userName, IFNULL(u.profilePic, 'picture/unknown.jpeg') AS profilePic 
        FROM udata u
        LEFT JOIN follow f ON u.UID = f.following AND f.UID = ?
        WHERE u.UID != ? AND f.following IS NULL
        ORDER BY RAND() LIMIT 18";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("ii", $loggedInUserID, $loggedInUserID);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();
$conn->close();
?>



<div class="friends-list">
    <?php if (!empty($users)): ?>
        <?php foreach ($users as $user): ?>
            <form method="POST" action="follow_action.php">
                <input type="hidden" name="uid" value="<?php echo $user["UID"]; ?>">
                <input type="hidden" name="action" value="follow">
                <div class="friend unread" onclick="this.parentNode.submit();">
                    <img src="<?php echo !empty(htmlspecialchars($user["profilePic"])) ? htmlspecialchars($user["profilePic"]) : 'picture/unknown.jpeg'; ?>"
                        alt="Profile Picture">
                    <div class="description">
                        <div class="text-info">
                            <h2><?php echo htmlspecialchars($user["userName"]); ?></h2>
                        </div>
                        <div class="follow-container">
                            <div class="follow-box">Follow</div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No users available.</p>
    <?php endif; ?>
</div>