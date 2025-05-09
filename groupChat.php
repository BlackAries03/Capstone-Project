<?php
include 'getName.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$groupID = $_GET["groupID"] ?? null;

if (!isset($groupID) || !is_numeric($groupID)) {
    echo '<script>
    alert("Please select a group to start chatting.");
    setTimeout(function() {
        window.location.href = "message.php"; 
    }, 250); 
    </script>';
    exit();
}

$sql = "SELECT gName, gImg FROM g WHERE gID = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt->bind_param("i", $groupID);
$stmt->execute();
$groupResult = $stmt->get_result();
$stmt->close();

if ($groupResult->num_rows === 0) {
    die("Group not found.");
}

$group = $groupResult->fetch_assoc();
$groupName = htmlspecialchars($group["gName"]);
$groupPic = !empty($group["gImg"]) ? $group["gImg"] : "picture/group-chat.png";

$sql = "SELECT gm.UID, gm.message, gm.time, u.profilePic, gm.gID
        FROM groupmessage gm
        JOIN udata u ON gm.UID = u.UID
        WHERE gm.gID = ? 
        ORDER BY gm.time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $groupID);
$stmt->execute();
$messages = $stmt->get_result();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Chat Interface</title>
    <link rel="stylesheet" href="sidebarStyle.css">
    <link rel="stylesheet" href="chat.css">
    <style>
        
                /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            background: antiquewhite;
            height: 100vh;
        }

        /* Sidebar & Main Content */
        .main-content {
            margin-left: 300px;
            flex-grow: 1;
            overflow-y: auto;
            background: antiquewhite;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background: #f1f1f1;
            border-bottom: 1px solid #ddd;
            position: fixed;
            top: 0;
            left: 300px;
            width: calc(100% - 300px);
            z-index: 1000;
            max-height: 60px;
        }

        /* Back Button */
        .return-button {
            border: none;
            background: none;
            cursor: pointer;
            margin-right: 10px;
        }

        .return-button img {
            width: 30px;
            height: 30px;
        }

        .message.unread {
            display: flex;
            align-items: center;
            flex-grow: 1;
            margin-left: 10px;
        }

        .message.unread img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
            padding: 2px;
            background-color: #5de0e6;
        }

        .message.unread .description {
            display: flex;
            flex-direction: column;
        }

        .message.unread .description h2 {
            margin: 0;
            font-size: 1.25rem;
        }

        .message.unread .description p {
            margin: 0;
            color: gray;
        }

        .options {
            margin-left: auto;
            cursor: pointer;
        }

        .options img {
            width: 30px;
            height: 30px;
        }

        .header .message.unread .description {
            display: flex;
            flex-direction: column;
        }

        .header .message.unread .description h2 {
            margin: 0;
            font-size: 1.25rem;
        }

        .header .message.unread .description p {
            margin: 0;
            color: gray;
        }

        /* Chat Container */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
            overflow-y: auto;
            padding: 10px;
            padding-top: 70px;
        }

        /* Chat Messages */
        .chat-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
            overflow-y: auto;
            padding: 10px;
            padding-top: 70px; /* Adjust for fixed header */
        }

        .chat-message {
            display: flex;
            align-items: flex-end;
            margin-bottom: 10px;
            position: relative;
            max-width: 75%;
            word-wrap: break-word;
            border-radius: 18px;
            padding: 8px 12px;
            font-size: 14px;
            line-height: 1.4;
        }

        .chat-message.left {
            align-self: flex-start;
            background: #ffffff;
            border: 1px solid #ddd;
        }


        .chat-message.right {
            align-self: flex-end;
            background: #e3f2fd; /* Light blue like Messenger */
        }


        .chat-message .message-header {
            font-weight: bold;
            font-size: 12px;
            color: #555;
            position: absolute;
            top: -18px;
            left: 10px;
        }

        .chat-message .dots {
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
        }


        .chat-message.right .dots {
            left: -20px;
        }

        .chat-message.left .dots {
            right: -20px;
        }
        .chat-message .message-content {
            background: inherit;
            border-radius: 8px;
            padding: 6px 10px;
            max-width: 100%;
            font-size: 14px;
            word-break: break-word;
        }

        .chat-message .message-content p {
            max-width: 100ch;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: wrap;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            min-width: 100px;
            z-index: 1000;
            left: -40px;
            top: 50%;
            transform: translateY(-50%);
        }

        .dots {
            list-style: none;
            cursor: pointer;
            position: relative;
            display: inline-block;
            padding: 5px;
        }

        .dots li {
            font-size: 20px;
            padding: 5px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ccc;
            list-style: none;
            padding: 5px 0;
            margin: 0;
            width: 120px;
            z-index: 100;
        }

        .dropdown-menu li {
            padding: 8px;
            cursor: pointer;
        }

        .dropdown-menu li:hover {
            background: #f0f0f0;
        }

        .replied-message {
            background: #f0f8ff; /* Light blue background */
            border-left: 4px solid #5de0e6;
            padding-left: 10px;
            font-style: italic;
        }

        .chat-footer {
            position: fixed;
            bottom: 0;
            width: calc(100% - 300px);
            background: white;
            display: flex;
            align-items: center;
            border-top: 1px solid #ddd;
            padding: 10px;
        }

        /* Chat Footer */
        .chat-footer {
            position: fixed;
            bottom: 0;
            left: 300px;
            width: calc(100% - 300px);
            background: white;
            display: flex;
            align-items: center;
            border-top: 1px solid #ddd;
            padding: 10px;
            gap: 10px;
        }

        .chat-footer form {
            display: flex;
            width: 100%;
            align-items: center;
        }

        .chat-footer textarea {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            resize: none;
            height: 100px;
            min-width: 0;
            box-sizing: border-box;
        }

        .chat-footer button {
            background: #5de0e6;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            cursor: pointer;
            color: black;
            font-weight: bold;
            height: 50px;
            white-space: nowrap;
            margin-left: 10px;
        }

        .chat-container::-webkit-scrollbar {
            width: 6px;
        }

        .chat-container::-webkit-scrollbar-thumb {
            background: lightgray;
            border-radius: 10px;
        }

        body.dark {
            background: #121212;
            color: #e0e0e0;
        }

        .dark .main-content {
            background: #1e1e1e;
        }

        .dark .header {
            background: #252525;
            border-bottom: 1px solid #333;
        }

        .dark .chat-message.left {
            background: #2e2e2e;
            border: 1px solid #444;
            color: #e0e0e0;
        }

        .dark .chat-message.right {
            background: #3b3b3b;
            color: #ffffff;
        }

        .dark .message.unread img {
            background-color: #333;
        }

        .dark .options img {
            filter: brightness(0.8);
        }

        .dark .dropdown-menu {
            background: #333;
            border: 1px solid #444;
            color: #e0e0e0;
        }

        .dark .dropdown-menu li:hover {
            background: #444;
        }

        .dark .replied-message {
            background: #2a2a2a;
            border-left: 4px solid #5de0e6;
        }

        .dark .chat-footer {
            background: #222;
            border-top: 1px solid #333;
        }

        .dark .chat-footer textarea {
            background: #333;
            border: 1px solid #444;
            color: #e0e0e0;
        }

        .dark .chat-footer button {
            background: #5de0e6;
            color: black;
        }

        .dark .chat-container::-webkit-scrollbar-thumb {
            background: #444;
        }


        body {
            display: flex;
            background: antiquewhite;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 300px;
            background: #fff;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 300px;
            padding: 1rem;
            flex-grow: 1;
            background: antiquewhite;
            overflow-y: auto;
        }

        .container {
            border-radius: 1rem;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 1rem;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .header-left h1 {
            margin-left: 15px;
            font-size: 1.5rem;
        }

        .message-list {
            overflow-y: auto;
            padding: 1rem;
        }

        .message {
            display: flex;
            align-items: center;
        }

        .message img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            padding: 2px;
            background-color: aqua;
        }

        .delete-message-icon {
            display: none;
            /* Hide delete message icons by default */
            width: 50px;
            height: 50px;
            border-radius: 0;
            padding: 0;
            background: none;
        }

        .message .description {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .message .description h2 {
            margin: 0;
            font-size: 1rem;
        }

        .message .description p {
            margin: 0;
            color: gray;
        }

        .group-chat {
            position: fixed;
            bottom: 20px;
            right: 40px;
            opacity: 1;
            cursor: pointer;
        }

        .round-container {
            width: 60px;
            height: 60px;
            background-color: green;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .group-chat img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }

        .popup-banner {
            overflow-y: scroll;
            top: 100px;
            margin: auto;
            position: relative;
            max-width: 40%;
            height: 70%;
            background-color: white;
            padding: 1rem;
            border: 1px solid #ccc;
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .popup-banner::-webkit-scrollbar {
            display: none;
        }


        .popup-banner .popup-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .popup-banner button {
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            align-self: flex-start;
        }

        .popup-banner h1 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .input-group {
            width: 100%;
            margin-bottom: 1rem;
        }

        .input-group label {
            display: block;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .input-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
        }

        .input-group .search-button {
            width: 30px;
            background: none;
            border: none;
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 35px;
        }

        #addImage {
            border-radius: 50%;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .image-preview {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-top: -55px;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: fill;
        }

        .message-box {
            width: 100%;
            padding: 1rem;
            background-color: #f0f0f0;
            border-radius: 0.5rem;
            text-align: center;
            margin-bottom: 1rem;
        }

        .privacy-settings {
            width: 100%;
            display: flex;
            justify-content: space-around;
            margin-bottom: 1rem;
        }

        .privacy-settings label {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
        }

        .privacy-settings input {
            margin-right: 0.5rem;
        }

        .create-button {
            width: 100%;
            padding: 0.75rem;
            background-color: green;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 1rem;
        }

        .create-button:hover {
            background-color: darkgreen;
        }

        .friend {
            display: flex;
            align-items: center;
            padding: 1rem;
            margin-bottom: 0;
            border-bottom: 1px solid #ddd;
        }

        .friend:last-child {
            border-bottom: none;
        }

        .friend img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            padding: 2px;
            background: #5de0e6;
        }

        .friend .description {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .friend .description .text-info {
            display: flex;
            flex-direction: column;
        }

        .friend .description h2,
        .friend .description p {
            margin: 0;
            font-size: 1rem;
        }

        .friend .description h2 {
            margin-bottom: 5px;
        }

        .follow-container {
            margin-left: auto;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const theme = localStorage.getItem('theme') || 'default';
            if (theme === 'dark') {
                document.body.classList.add('dark');
            }
        });
    </script>
</head>

<body>
    <?php include("sidebar.php"); ?>
    <div class="main-content">
        <header class="header">
            <button class="return-button" onclick="window.location.href='message.php'">
                <img src="picture/back-button.png" class="back" />
            </button>
            <div class="message unread">
                <img src="<?php echo $groupPic; ?>" alt="Group" />
                <div class="description">
                    <h2><?php echo $groupName; ?></h2>
                </div>
            </div>
            <div class="options" onclick="togglePopup()">
                <img src="picture/optiondots.png" alt="options" />
            </div>
        </header>

        <div class="container">
            <main class="chat-container" id="chatGroupContainer">

            </main>

            <footer class="chat-footer">
                <form id="chatGroupForm">
                    <input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
                    <textarea id="messageGroupInput" name="message" placeholder="Type a message..." rows="4"></textarea>
                    <button type="submit">Send</button>
                </form>
            </footer>
        </div>
    </div>
    <div class="overlay" id="overlay">
        <div class="popup-banner" id="popupBanner">
            <div class="popup-content">
                <button type="button" onclick="togglePopup()">←</button>
                <h1>GROUP MANAGEMENT</h1>
                <?php
                $conn = new mysqli("localhost", "root", "", "socialmedia");
                if ($conn->connect_error) {
                    die("Database connection failed: " . $conn->connect_error);
                }
                $stmt = $conn->prepare("SELECT role FROM groupmember WHERE gID = ? AND UID = ?");
                $stmt->bind_param("ii", $groupID, $userID);
                $stmt->execute();
                $result = $stmt->get_result();
                $roleRow = $result->fetch_assoc();
                $stmt->close();
                $userRole = $roleRow ? $roleRow['role'] : 'member';
                ?>

                <?php if ($userRole == 'creator'): ?>
                    <div class="input-group">
                        <label for="groupImage">GROUP IMAGE</label>
                        <img id="addImage" src="<?php echo $groupPic; ?>" width="50" height="50"
                            onclick="document.getElementById('fileInput').click();" />
                        <input type="file" id="fileInput" accept="image/*" style="display: none;"
                            onchange="previewImage(event)" />
                        <div class="image-preview" id="imagePreview"></div>
                    </div>
                    <div class="input-group">
                        <label for="groupName">GROUP NAME</label>
                        <input type="text" id="groupName" placeholder="<?php echo $groupName; ?>" onblur="updateGroupName()"
                            onkeypress="handleGroupNameKeyPress(event)" />
                    </div>
                    <div class="input-group">
                        <label for="addMember">ADD MEMBER</label>
                        <input type="text" id="addMember" placeholder="Enter member name" />
                    </div>
                    <div class="friends-list" style="height: 100px; width:100%; overflow-y: scroll; overflow-x: hidden;">
                        <?php
                        $mutualFollows = [];
                        $stmt = $conn->prepare("
                            SELECT u.UID, u.userName, u.profilePic 
                            FROM follow f1 
                            JOIN follow f2 ON f1.following = f2.UID 
                            JOIN udata u ON f1.following = u.UID 
                            LEFT JOIN groupmember gm ON gm.UID = u.UID AND gm.gID = ? 
                            WHERE f1.UID = ? 
                            AND f2.following = ? 
                            AND gm.UID IS NULL
                        ");
                        $stmt->bind_param("iii", $groupID, $userID, $userID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $mutualFollows = $result->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();
                        if (!empty($mutualFollows)):
                            foreach ($mutualFollows as $user):
                                ?>
                                <div class="friend unread">
                                    <img src="<?php echo !empty(htmlspecialchars($user["profilePic"])) ? htmlspecialchars($user["profilePic"]) : 'picture/unknown.jpeg'; ?>"
                                        alt="Profile Picture">
                                    <div class="description">
                                        <div class="text-info">
                                            <h2><?php echo htmlspecialchars($user["userName"]); ?></h2>
                                        </div>
                                        <div class="follow-container">
                                            <div class="add-box"
                                                onclick="addMember(this, <?php echo $user['UID']; ?>, <?php echo $groupID; ?>)">
                                                Add
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endforeach;
                        else:
                            ?>
                            <p id="no-result" style="color:red;">No other mutual user found.</p>
                        <?php endif; ?>
                    </div>

                    <div class="input-group">
                        <label for="MemberList">Member List</label>
                        <input type="text" id="MemberList" placeholder="Enter member name" />
                    </div>
                    <div class="friends-list" style="height: 100px; width:100%; overflow-y: scroll; overflow-x: hidden;">
                        <?php
                        $groupMembers = [];
                        $stmt = $conn->prepare("
                            SELECT u.UID, u.userName, u.profilePic 
                            FROM groupmember gm
                            JOIN udata u ON gm.UID = u.UID
                            WHERE gm.gID = ?
                        ");
                        $stmt->bind_param("i", $groupID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $groupMembers = $result->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();
                        if (!empty($groupMembers)):
                            foreach ($groupMembers as $user):
                                ?>
                                <div class="friend unread">
                                    <img src="<?php echo !empty(htmlspecialchars($user["profilePic"])) ? htmlspecialchars($user["profilePic"]) : 'picture/unknown.jpeg'; ?>"
                                        alt="Profile Picture">
                                    <div class="description">
                                        <div class="text-info">
                                            <h2><?php echo htmlspecialchars($user["userName"]); ?></h2>
                                        </div>
                                        <div class="follow-container">
                                            <?php if ($user['UID'] != $userID): ?>
                                                <div style="display:flex; flex-direction: row; gap: 20px;">
                                                    <div class="remove-box" style="color: red;"
                                                        onclick="removeMember(<?php echo $user['UID']; ?>, '<?php echo htmlspecialchars($user["userName"]); ?>', this)">
                                                        REMOVE
                                                    </div>
                                                    <div class="transfer-box"
                                                        onclick="transferRole(<?php echo $user['UID']; ?>, '<?php echo htmlspecialchars($user["userName"]); ?>', this)">
                                                        TRANSFER ROLE
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span>You (creator)</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endforeach;
                        else:
                            ?>
                            <p id="no-result" style="color:red;">No members found in the group.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="input-group">
                        <label for="MemberList">Member List</label>
                        <input type="text" id="MemberList" placeholder="Enter member name" />
                    </div>
                    <div class="members-list" style="height: 300px; width:100%; overflow-y: scroll; overflow-x: hidden;">
                        <?php
                        $groupMembers = [];
                        $stmt = $conn->prepare("
                            SELECT u.UID, u.userName, u.profilePic 
                            FROM groupmember gm
                            JOIN udata u ON gm.UID = u.UID
                            WHERE gm.gID = ?
                        ");
                        $stmt->bind_param("i", $groupID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $groupMembers = $result->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();

                        if (!empty($groupMembers)):
                            foreach ($groupMembers as $user):
                                ?>
                                <div class="friend unread">
                                    <img src="<?php echo !empty(htmlspecialchars($user["profilePic"])) ? htmlspecialchars($user["profilePic"]) : 'picture/unknown.jpeg'; ?>"
                                        alt="Profile Picture">
                                    <div class="description">
                                        <div class="text-info">
                                            <h2><?php echo htmlspecialchars($user["userName"]); ?></h2>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endforeach;
                        else:
                            ?>
                            <p id="no-result" style="color:red;">No members found in the group.</p>
                        <?php endif; ?>
                    </div>
                    <button id="leaveGroup"
                        style="background-color: red; color: white; border: none; padding: 10px 15px; cursor: pointer; width: 100%; font-size: 16px;text-align: center;margin-top: 10px;border-radius: 5px;">
                        LEAVE GROUP
                    </button>

                    <script>
                        document.getElementById("MemberList").addEventListener("input", function () {
                            const searchValue = this.value.toLowerCase();
                            const friends = document.querySelectorAll('.members-list .friend');
                            let hasResults = false;

                            friends.forEach(friend => {
                                const friendName = friend.querySelector('.text-info h2').textContent.toLowerCase();
                                if (friendName.includes(searchValue)) {
                                    friend.style.display = 'flex';
                                    hasResults = true;
                                } else {
                                    friend.style.display = 'none';
                                }
                            });

                            const noResult = document.getElementById('no-result');
                            if (noResult) {
                                noResult.style.display = hasResults ? 'none' : 'block';
                            }
                        });
                        document.getElementById("leaveGroup").addEventListener("click", function () {
                            if (confirm("Are you sure you want to leave this group?")) {
                                const groupID = new URLSearchParams(window.location.search).get("groupID");

                                fetch("leaveGroup.php", {
                                    method: "POST",
                                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                                    body: `groupID=${groupID}`
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert("You have left the group.");
                                            window.location.href = "message.php";
                                        } else {
                                            alert("Error: " + data.error);
                                        }
                                    })
                                    .catch(error => {
                                        console.error("Error:", error);
                                        alert("An unexpected error occurred.");
                                    });
                            }
                        });

                    </script>

                <?php endif; ?>
                <?php $conn->close(); ?>
            </div>
        </div>
    </div>



</body>

</html>

<script>

    function togglePopup() {
        localStorage.removeItem('groupMembers');
        const overlay = document.getElementById('overlay');
        overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener("DOMContentLoaded", function () {
        const chatContainer = document.getElementById("chatGroupContainer");
        const chatForm = document.getElementById("chatGroupForm");
        const messageInput = document.getElementById("messageGroupInput");
        let autoFetch = true;
        let isDropdownOpen = false;

        function isAtBottom() {
            return chatContainer.scrollHeight - chatContainer.scrollTop <= chatContainer.clientHeight + 5;
        }

        function loadMessages() {
            if (!autoFetch || isDropdownOpen) return;
            const groupID = new URLSearchParams(window.location.search).get("groupID");
            if (!groupID) return;

            fetch("get_group_message.php?groupID=" + groupID)
                .then(response => response.text())
                .then(data => {
                    const wasAtBottom = isAtBottom();
                    chatContainer.innerHTML = data;
                    if (wasAtBottom) chatContainer.scrollTop = chatContainer.scrollHeight;
                    attachEventListeners();
                    autoFetch = true;
                })
                .catch(error => console.error("Error loading messages:", error));
        }

        function triggerMessageRefresh() {
            loadMessages();
        }

        setInterval(() => {
            if (isAtBottom() && autoFetch) {
                loadMessages();
            }
        }, 200);

        function attachEventListeners() {
            document.querySelectorAll(".dots li").forEach(dot => {
                dot.addEventListener("click", function (event) {
                    event.stopPropagation();
                    isDropdownOpen = true;
                    autoFetch = false;
                    const dropdownMenu = this.closest(".dots").querySelector(".dropdown-menu");
                    document.querySelectorAll(".dropdown-menu").forEach(menu => {
                        if (menu !== dropdownMenu) menu.style.display = "none";
                    });
                    dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
                });
            });

            document.querySelectorAll(".retrieve-option").forEach(option => {
                option.addEventListener("click", function () {
                    const messageID = this.dataset.id;
                    const groupID = new URLSearchParams(window.location.search).get("groupID");

                    if (confirm("Are you sure you want to retrieve this message? This action cannot be undone.")) {
                        fetch("retrieve_group_message.php", {
                            method: "POST",
                            body: new URLSearchParams({
                                groupID: groupID,
                                mID: messageID
                            }),
                            headers: { "Content-Type": "application/x-www-form-urlencoded" }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert("Message successfully retrieved!");
                                    autoFetch = true;
                                    isDropdownOpen = false;
                                    triggerMessageRefresh();
                                } else {
                                    const errorMessage = data.error ? `Error: ${data.error}` : "An unknown error occurred.";
                                    alert(errorMessage);
                                }
                            })
                            .catch(error => {
                                console.error("Error retrieving message:", error);
                                alert("An error occurred while retrieving the message.");
                            });
                    }
                });
            });

            document.querySelectorAll('.forward-option').forEach(option => {
                option.addEventListener("click", function () {
                    const message = JSON.parse(this.dataset.message);

                    localStorage.setItem("forwardedMessage", message);

                    window.location.href = "forwardto.php";
                });
            });

            document.addEventListener("click", function () {
                document.querySelectorAll(".dropdown-menu").forEach(menu => {
                    menu.style.display = "none";
                });
                isDropdownOpen = false;
            });
        }

        // Enter key behavior
        messageInput.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                if (event.shiftKey) {
                    return;
                } else {
                    event.preventDefault();
                    chatForm.dispatchEvent(new Event("submit"));
                }
            }
        });

        // Form submission handling
        chatForm.addEventListener("submit", function (event) {
            event.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            fetch("send_group_message.php", {
                method: "POST",
                body: new FormData(chatForm),
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = "";
                        loadMessages();
                    }
                });
            localStorage.removeItem("forwardedMessage");
        });

        // If there's a stored forwarded message, display it
        const storedMessage = localStorage.getItem("forwardedMessage");
        if (storedMessage) {
            messageInput.value = storedMessage;
        }

        const returnButton = document.getElementById("returnButton");
        if (returnButton) {
            returnButton.addEventListener("click", function () {
                localStorage.removeItem("forwardedMessage");
                window.history.back();
            });
        }
    });
    document.getElementById("addMember").addEventListener("input", function () {
        const searchValue = this.value.toLowerCase();
        const friends = document.querySelectorAll('.friends-list .friend');
        let hasResults = false;

        friends.forEach(friend => {
            const friendName = friend.querySelector('.text-info h2').textContent.toLowerCase();
            if (friendName.includes(searchValue)) {
                friend.style.display = 'flex';
                hasResults = true;
            } else {
                friend.style.display = 'none';
            }
        });

        const noResult = document.getElementById('no-result');
        if (noResult) {
            noResult.style.display = hasResults ? 'none' : 'block';
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const members = JSON.parse(localStorage.getItem('groupMembers')) || [];
        const buttons = document.querySelectorAll('.add-box');

        buttons.forEach(button => {
            const userId = button.getAttribute('data-user-id');
            if (members.some(member => member.id == userId)) {
                button.textContent = 'added';
                button.style.backgroundColor = 'grey';
                button.style.cursor = 'not-allowed';
            }
        });
    });


    function handleGroupNameKeyPress(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            updateGroupName();
            document.getElementById("groupName").blur(); // Remove focus after Enter
        }
    }

    function updateGroupName() {
        const groupNameInput = document.getElementById("groupName");
        const newName = groupNameInput.value.trim();
        const originalName = "<?php echo $groupName; ?>";
        const groupID = new URLSearchParams(window.location.search).get("groupID");

        if (newName === originalName) {
            return;
        }

        if (!newName) {
            alert("Group name cannot be empty!");
            groupNameInput.value = originalName;
            return;
        }

        fetch("group_details.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `action=update_name&groupID=${groupID}&newName=${encodeURIComponent(newName)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll(".description h2").forEach(element => {
                        element.textContent = newName;
                    });
                } else {
                    alert(`Error: ${data.error}`);
                    groupNameInput.value = originalName;
                }
            })
            .catch(error => {
                console.error("Error:", error);
                groupNameInput.value = originalName;
            });
    }
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("addImage").src = e.target.result;
                updateGroupImage();
            }
            reader.readAsDataURL(file);
        }
    }
    function updateGroupImage() {
        const fileInput = document.getElementById("fileInput");
        const groupID = new URLSearchParams(window.location.search).get("groupID");

        if (fileInput.files.length === 0) return;

        const formData = new FormData();
        formData.append("action", "update_image");
        formData.append("groupID", groupID);
        formData.append("groupImage", fileInput.files[0]);

        fetch("group_details.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Group image updated successfully!");
                    document.querySelectorAll('img[alt="Group"]').forEach(img => {
                        img.src = data.newImagePath + '?t=' + new Date().getTime();
                    });
                    document.getElementById("addImage").src = data.newImagePath + '?t=' + new Date().getTime();
                } else {
                    alert(`Error: ${data.error}`);
                    document.getElementById("addImage").src = "<?php echo $groupPic; ?>";
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred during upload");
                document.getElementById("addImage").src = "<?php echo $groupPic; ?>";
            });
    }
    function addMember(button, userID, groupID) {
        if (!userID || !groupID) {
            alert("Invalid user or group ID");
            return;
        }

        button.innerText = "Adding...";
        button.style.pointerEvents = "none";

        fetch("group_details.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `action=add_member&groupID=${groupID}&memberID=${userID}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.innerText = "Added";
                    button.style.backgroundColor = "#ccc";
                    button.style.cursor = "not-allowed";
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert(`Error: ${data.error}`);
                    button.innerText = "Add";
                    button.style.pointerEvents = "auto";
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An unexpected error occurred.");
                button.innerText = "Add";
                button.style.pointerEvents = "auto";
            });
    }


    function removeMember(userID, username, element) {
        if (confirm(`Are you sure you want to remove ${username} from the group?`)) {
            const groupID = new URLSearchParams(window.location.search).get("groupID");

            fetch("remove_member.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `groupID=${groupID}&userID=${userID}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${username} has been removed from the group.`);
                        element.closest('.friend').remove();
                    } else {
                        alert(`Error: ${data.error}`);
                    }
                })
                .catch(error => {
                    console.error("Error removing member:", error);
                    alert("An error occurred while removing the member.");
                });
        }
    }
    function transferRole(userID, username, element) {
        if (confirm(`Are you sure you want to transfer the creator role to ${username}?`)) {
            const groupID = new URLSearchParams(window.location.search).get("groupID");

            fetch("transfer_role.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `groupID=${groupID}&userID=${userID}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`The creator role has been transferred to ${username}.`);
                        window.location.reload();
                    } else {
                        alert(`Error: ${data.error}`);
                    }
                })
                .catch(error => {
                    console.error("Error transferring role:", error);
                    alert("An error occurred while transferring the role.");
                });
        }
    }
</script>