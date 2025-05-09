<?php
include 'getName(bm).php';

$followedUsers = [];
if ($userID) {
    $stmt = $conn->prepare("SELECT u.UID, u.userName, u.profilePic 
                            FROM follow f 
                            JOIN uData u ON f.following = u.UID 
                            WHERE f.UID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $followedUsers[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesej</title>
    <link rel="stylesheet" href="sidebarStyle.css">
</head>

<body>
    <?php include("sidebar(bm).php"); ?>
    <div class="main-content">
        <div class="container">
            <header>
                <div class="header-left">
                    <h1>Mesej</h1>
                </div>
            </header>
            <main class="message-list">
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

                    echo '<div class="message unread" onclick="location.href=\'chat(bm).php?receiverID=' . $receiverID . '\';" style="cursor: pointer;">
                        <img src="' . htmlspecialchars($profilePic) . '" alt="' . htmlspecialchars($row["userName"]) . '" />
                        <div class="description">
                            <h2>' . htmlspecialchars($row["userName"]) . '</h2>
                        </div>
                      </div>';
                }

                $stmt->close();

                $sql = "SELECT g.gID, g.gName, g.gImg, 
               GROUP_CONCAT(u.userName ORDER BY u.userName ASC SEPARATOR ', ') AS members
                FROM groupmember gm 
                JOIN g ON gm.gID = g.gID 
                JOIN udata u ON gm.UID = u.UID
                WHERE gm.gID IN (SELECT gID FROM groupmember WHERE UID = ?)
                GROUP BY g.gID";

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

                    $membersList = $row["members"] ?? "";
                    if (strlen($membersList) > 30) {
                        $membersList = substr($membersList, 0, 30) . "...";
                    }

                    echo '<div class="message unread" onclick="location.href=\'groupChat(bm).php?groupID=' . $groupID . '\';" style="cursor: pointer;">
                            <img src="' . htmlspecialchars($groupImg) . '" alt="' . htmlspecialchars($row["gName"]) . '" />
                            <div class="description">
                                <h2>' . htmlspecialchars($row["gName"]) . '</h2>
                                <p class="members-list">' . htmlspecialchars($membersList) . '</p>
                            </div>
                        </div>';
                }

                $stmt->close();
                $conn->close();
                ?>
            </main>
        </div>

    </div>
    <div class="group-chat" onclick="togglePopup()">
        <div class="round-container">
            <img src="picture/group-chat.png" width="50" height="50" alt="Group Chat Icon">
        </div>
    </div>
    <div class="overlay" id="overlay">
        <div class="popup-banner" id="popupBanner">
            <div class="popup-content">
                <img class="back-button" src="picture/back-button.png" width="30" height="30" alt="Back Button" onclick="togglePopup()" style="cursor: pointer;" />
                <h1>BUAT KUMPULAN</h1>
                <div class="input-group">
                    <label for="groupImage">IMEJ KUMPULAN</label>
                    <img id="addImage" src="picture/add.png" width="50" height="50"
                        onclick="document.getElementById('fileInput').click();" />
                    <input type="file" id="fileInput" accept="image/*" style="display: none;"
                        onchange="previewImage(event)" />
                    <div class="image-preview" id="imagePreview"></div>
                </div>
                <div class="input-group">
                    <label for="groupName">NAMA KUMPULAN</label>
                    <input type="text" id="groupName" placeholder="Masukkan nama kumpulan" />
                </div>
                <div class="input-group">
                    <label for="addMember">TAMBAH AHLI</label>
                    <input type="text" id="addMember" placeholder="Masukkan nama ahli kumpulan" />
                </div>
                <div class="friends-list" style="height: 180px;width:100%; overflow-y: scroll; overflow-x: hidden;">
                    <?php
                    // Reopen the database connection
                    $conn = new mysqli("localhost", "root", "", "socialmedia");
                    if ($conn->connect_error) {
                        die("Database connection failed: " . $conn->connect_error);
                    }

                    $mutualFollows = [];
                    if ($userID) {
                        $stmt = $conn->prepare("SELECT u.UID, u.userName, u.profilePic FROM follow f1 JOIN follow f2 ON f1.following = f2.UID JOIN uData u ON f1.following = u.UID WHERE f1.UID = ? AND f2.following = ?");
                        $stmt->bind_param("ii", $userID, $userID);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $mutualFollows = $result->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();
                    }

                    if (!empty($mutualFollows)): ?>
                        <?php foreach ($mutualFollows as $user): ?>
                            <div class="friend unread">
                                <img src="<?php echo !empty(htmlspecialchars($user["profilePic"])) ? htmlspecialchars($user["profilePic"]) : 'picture/unknown.jpeg'; ?>"
                                    alt="Profile Picture">
                                <div class="description">
                                    <div class="text-info">
                                        <h2><?php echo htmlspecialchars($user["userName"]); ?></h2>
                                    </div>
                                    <div class="follow-container">
                                        <div class="add-box"
                                            onclick="toggleMember(<?php echo $user['UID']; ?>, '<?php echo htmlspecialchars($user["userName"]); ?>', this)">
                                            tambah
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p id="no-result" style="color:red;">Tiada ikutan bersama ditemui. Anda perlu mengikuti satu sama lain untuk
                        buat kumpulan.</p>
                    <?php endif; ?>
                </div>
                <button type="button" class="create-button" onclick="createGroup()">Buat</button>
            </div>
        </div>
    </div>


</body>

</html>

<style>
    :root {
        --light-bg: antiquewhite;
        --light-container-bg: #fff;
        --light-text: #000;
        --light-border: #ddd;
        --friend-img-bg: #5de0e6;
        --message-bg: #f9f9f9;
        --popup-bg: white;
        --popup-border: #ccc;
        --message-box-bg: #f0f0f0;

        --dark-bg: #2e2e2e;
        --dark-container-bg: #3c3c3c;
        --dark-text: #e1e1e1;
        --dark-border: #555;
        --friend-img-bg-dark: #4caf50;
        --message-bg-dark: #444;
        --popup-bg-dark: #3c3c3c;
        --popup-border-dark: #777;
        --message-box-bg-dark: #555;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        display: flex;
        background: var(--light-bg);
        height: 100vh;
        margin: 0;
        padding: 0;
    }

    .sidebar {
        width: 300px;
        background: var(--light-container-bg);
        height: 100vh;
        position: fixed;
        overflow-y: auto;
    }

    .main-content {
        margin-left: 300px;
        padding: 1rem;
        flex-grow: 1;
        background: var(--light-bg);
        overflow-y: auto;
    }

    .container {
        background: var(--light-container-bg);
        border-radius: 1rem;
        padding: 1rem;
        color: var(--light-text);
    }

    .header {
        display: flex;
        align-items: center;
        border-bottom: 1px solid var(--light-border);
        padding-bottom: 1rem;
    }

    .header-left {
        display: flex;
        align-items: center;
    }

    .header-left h1 {
        margin-left: 15px;
        font-size: 1.5rem;
        color: var(--light-text);
    }

    .message-list {
        overflow-y: auto;
        padding: 1rem;
    }

    .message {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid var(--light-border);
        margin-bottom: 20px;
    }

    .message img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
        padding: 2px;
        background-color: var(--friend-img-bg);
    }

    .delete-message-icon {
        display: none;
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
        color: var(--light-text);
    }

    .message .description p {
        margin: 0;
        color: gray;
    }

    .unread {
        background-color: var(--message-bg);
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
        margin-left: 450px;
        margin-top: 45px;
        position: relative;
        bottom: auto;
        right: auto;
        width: 50%;
        background-color: var(--popup-bg);
        padding: 1rem;
        border: 1px solid var(--popup-border);
        border-radius: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        color: var(--light-text);
    }

    .popup-banner h1 {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--light-text);
    }

    .back-button{
        margin-left: -700px;
        margin-bottom: -30px;
    }

    .input-group {
        width: 100%;
        margin-bottom: 1rem;
    }

    .input-group label {
        display: block;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: var(--light-text);
    }

    .input-group input {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid var(--popup-border);
        border-radius: 0.5rem;
        color: var(--light-text);
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
        background-color: var(--message-box-bg);
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
        color: var(--light-text);
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
        border-bottom: 1px solid var(--light-border);
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
        background: var(--friend-img-bg);
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
        color: var(--light-text);
    }

    .friend .description h2 {
        margin-bottom: 5px;
        color: var(--light-text);
    }

    .friend .description p {
        margin: 0;
        font-size: 1rem;
        color: gray;
    }

    .follow-container {
        margin-left: auto;
    }

    /* Dark theme */
    .dark body {
        background: var(--dark-bg);
    }

    .dark .sidebar {
        background:rgb(33, 33, 33);
        color: var(--dark-text);
    }

    .dark .main-content {
        background: var(--dark-bg);
        color: var(--dark-text);
    }

    .dark .container {
        background: var(--dark-container-bg);
        color: var(--dark-text);
    }

    .dark .header {
        border-bottom: 1px solid var(--dark-border);
    }

    .dark .header-left h1 {
        color: var(--dark-text);
    }

    .dark .message {
        border-bottom: 1px solid var(--dark-border);
    }

    .dark .message img {
        background-color: var(--friend-img-bg-dark);
    }

    .dark .unread {
        background-color: var(--message-bg-dark);
    }

    .dark .round-container {
        background-color: var(--friend-img-bg-dark);
    }

    .dark .popup-banner {
        background-color: var(--popup-bg-dark);
        border: 1px solid var(--popup-border-dark);
    }

    .dark .popup-banner button {
        color: var(--dark-text);
    }

    .dark .popup-banner h1 {
        color: var(--dark-text);
    }

    .dark .input-group label {
        color: var(--dark-text);
    }

    .dark .input-group input {
        border: 1px solid var(--popup-border-dark);
        color: var(--dark-text);
    }

    .dark .message-box {
        background-color: var(--message-box-bg-dark);
        color: var(--dark-text);
    }

    .dark .privacy-settings label {
        color: var(--dark-text);
    }

    .dark .create-button {
        background-color: green;
    }

    .dark .create-button:hover {
        background-color: darkgreen;
    }

    .dark .friend {
        border-bottom: 1px solid var(--dark-border);
    }

    .dark .friend img {
        background: var(--friend-img-bg-dark);
    }

    .dark .friend .description h2,
    .dark .friend .description p {
        color: var(--dark-text);
    }

</style>
<script>
    function previewImage(event) {
        const imagePreview = document.getElementById('imagePreview');
        const file = event.target.files[0];

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                addImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            alert('Please upload a valid image file.');
            imagePreview.innerHTML = '';
        }
    }

    function toggleMember(userId, userName, button) {
        let members = JSON.parse(localStorage.getItem('groupMembers')) || [];

        // Check if the user is already in the list
        const index = members.findIndex(member => member.id === userId);
        if (index === -1) {
            members.push({ id: userId, name: userName });
            button.textContent = 'telah tambah';
            button.style.backgroundColor = 'grey';
            button.style.cursor = 'not-allowed';
        } else {
            members.splice(index, 1);
            button.textContent = 'tambah';
            button.style.backgroundColor = '';
            button.style.cursor = 'pointer';
        }

        localStorage.setItem('groupMembers', JSON.stringify(members));
    }

    function createGroup() {
        const groupName = document.getElementById('groupName').value;
        const groupImage = document.getElementById('fileInput').files[0];
        const members = JSON.parse(localStorage.getItem('groupMembers')) || [];

        if (!groupName || members.length === 0) {
            alert('Sila berikan nama kumpulan dan tambah sekurang-kurangnya satu ahli.');
            return;
        }

        const formData = new FormData();
        formData.append('groupName', groupName);
        if (groupImage) {
            formData.append('groupImage', groupImage);
        }
        formData.append('members', JSON.stringify(members));

        fetch('create_group(bm).php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Kumpulan berjaya dicipta!');
                    localStorage.removeItem('groupMembers');
                    window.location.reload();
                } else {
                    alert('Gagal mencipta kumpulan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Sila muat naik gambar kumpulan.');
            });
    }

    function togglePopup() {
        localStorage.removeItem('groupMembers');
        const overlay = document.getElementById('overlay');
        overlay.style.display = overlay.style.display === 'block' ? 'none' : 'block';
    }

    // Search functionality
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
                button.textContent = 'telah tambah';
                button.style.backgroundColor = 'grey';
                button.style.cursor = 'not-allowed';
            }
        });
    });
</script>