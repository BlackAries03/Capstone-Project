<?php
include 'getName(bm).php';

$conn = new mysqli("localhost", "root", "", "socialmedia");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userID = $_SESSION["userID"] ?? null;

// Function to create a new notification and update unread count
function createNotification($conn, $userID, $username, $message, $profilePic)
{
    $status = 'n';

    $stmt = $conn->prepare("INSERT INTO notifications (userid, username, message, status) VALUES (?, ?, ?, ?, )");
    $stmt->bind_param("isss", $userID, $username, $message, $status);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE notifications SET unread_count = unread_count + 1 WHERE userid = ?");
    $stmt->bind_param("i", $userID);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->close();
}

// Example: Creating a new notification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_notification"])) {
    $username = $_POST["username"];
    $message = $_POST["message"];
    $profilePic = $_POST["profilePic"];

    createNotification($conn, $userID, $username, $message, $profilePic);
}

$followedUsers = [];
if ($userID) {
    $stmt = $conn->prepare("SELECT following FROM follow WHERE UID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $followedUsers[] = $row['following'];
    }
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["mark_all_read"])) {
    $stmt = $conn->prepare("UPDATE notifications SET status = 'y' WHERE userid = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $userID);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE notifications SET unread_count = 0 WHERE userid = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->close();

    header("Location: notification(bm).php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["notif_id"])) {
    $notifID = $_POST["notif_id"];

    // Mark the notification as read
    $stmt = $conn->prepare("UPDATE notifications SET status = 'y' WHERE id = ? AND userid = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("ii", $notifID, $userID);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->close();

    header("Location: notification(bm).php");
    exit;
}

$sql = "SELECT n.id, n.message, n.username, 
               CASE 
                   WHEN n.username = 'Admin' THEN 'picture/unknown.jpeg'
                   ELSE IFNULL(u.profilePic, 'picture/unknown.jpeg') 
               END AS profilePic,
               n.created_at, n.status, u.UID AS trigger_user_id 
        FROM notifications n 
        LEFT JOIN udata u ON n.username = u.userName 
        WHERE n.userid = ? 
        ORDER BY n.created_at DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing fetch statement: " . $conn->error);
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$notifications = [];
while ($notif = $result->fetch_assoc()) {
    $notif['profilePic'] = !empty($notif['profilePic']) ? htmlspecialchars($notif['profilePic']) : 'picture/unknown.jpeg';
    $notifications[] = $notif;
}
$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi</title>
    <link rel="stylesheet" href="sidebarStyle.css">
    <style>
        :root {
            --light-bg: antiquewhite;
            --light-container-bg: #fff;
            --light-text: black;
            --light-border: #ddd;
            --notification-bg: blue;
            --notification-text: #fff;
            --btn-bg: #007bff;
            --btn-hover-bg: #0056b3;
            --btn-following-bg: grey;
            --unread-bg: pink;
            --back-btn-bg: none;

            --dark-bg: #2e2e2e;
            --dark-container-bg: #3c3c3c;
            --dark-text: #e1e1e1;
            --dark-border: #555;
            --notification-bg-dark: darkblue;
            --notification-text-dark: #e1e1e1;
            --btn-bg-dark: #0056b3;
            --btn-hover-bg-dark: #003e7e;
            --btn-following-bg-dark: grey;
            --unread-bg-dark: #444;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--light-bg);
            color: var(--light-text);
            transition: background-color 0.3s, color 0.3s;
        }

        .container {
            display: flex;
            justify-content: center;
            background-color: antiquewhite;
            margin-left: 300px;
            width: calc(100%-300px)%;

            height: 100vh;
            overflow: hidden;
        }

        .notificationContainer {
            background-color: var(--light-container-bg);
            width: 70%;
            margin: 2rem;
            padding: 1rem 1rem;
            border-radius: 1rem;
            overflow-y: auto;
            max-height: calc(100vh - 8rem);
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .back button {
            border: none;
            background: var(--back-btn-bg);
            cursor: pointer;
            padding: 0;
        }

        .notificationHeader {
            display: flex;
            align-items: center;
        }

        #num-of-notif {
            background-color: var(--notification-bg);
            color: var(--notification-text);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            width: 30px;
            height: 30px;
            border-radius: 0.5rem;
            margin-left: -100px;
        }

        #mark-as-read {
            color: gray;
            cursor: pointer;
            transition: 0.6s ease;
        }

        #mark-as-read:hover {
            color: var(--light-text);
        }

        main.notifications {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .notification img,
        .notificationCard img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid pink;
            object-fit: cover;
        }

        .notificationCard .description {
            margin-left: 10px;
            display: flex;
            justify-content: space-between;
            flex-direction: column;
        }

        .notification-entry {
            position: relative;
            margin-bottom: 1rem;
        }

        .follow-btn {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            padding: 5px 10px;
            background-color: var(--btn-bg);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .notificationCard {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 1rem;
            border-radius: 1rem;
            cursor: pointer;
            border: 1px var(--light-text);
            text-align: left;
        }

        .notificationCard .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notificationCard .description {
            flex: 1;
        }

        .follow-btn {
            padding: 5px 10px;
            background-color: var(--btn-bg);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .follow-btn.following {
            background-color: var(--btn-following-bg);
            cursor: not-allowed;
        }

        .follow-btn:hover:not(.following) {
            background-color: var(--btn-hover-bg);
        }

        .unread {
            background-color: var(--unread-bg);
        }

        .back {
            margin-top: 5px;
            margin-right: 5px;
        }

        /* Dark theme */
        .dark body {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }

        .dark .container {
            background-color: #2e2e2e
        }

        .dark .notificationContainer {
            background-color: var(--dark-container-bg);
        }

        .dark .notificationCard {
            border: 1px solid var(--dark-border);
        }

        .dark #num-of-notif {
            background-color: var(--notification-bg-dark);
            color: var(--notification-text-dark);
        }

        .dark .follow-btn {
            background-color: var(--btn-bg-dark);
        }

        .dark .follow-btn:hover:not(.following) {
            background-color: var(--btn-hover-bg-dark);
        }

        .dark .unread {
            background-color: var(--unread-bg-dark);
        }

        .dark header,
        .dark .notificationHeader,
        .dark main.notifications,
        .dark .notification img,
        .dark .notificationCard img,
        .dark .notification-entry,
        .dark .back button {
            color: var(--dark-text);
        }

    </style>
</head>

<body>
    <?php include("sidebar(bm).php"); ?>
    <div class="container">
        <div class="notificationContainer">
            <header>
                <div class="header">
                    <div class="back">
                        <button type="button" onclick="window.location.href='main(bm).php'">
                            <img src="picture/back-button.png" width="30" height="30" alt="back icon" class="back">
                        </button>
                    </div>
                    <h1>Notifikasi</h1>
                </div>
                <div class="notificationHeader">
                    <span id="num-of-notif"></span>
                </div>
                <form method="POST" action="notification(bm).php" id="mark-all-form">
                    <input type="hidden" name="mark_all_read" value="1">
                    <p id="mark-as-read">Tandai semua sebagai dibaca</p>
                </form>
            </header>
            <main class="notifications">
                <?php foreach ($notifications as $notif):
                    $triggerUserId = $notif['trigger_user_id'];
                    $isFollowing = in_array($triggerUserId, $followedUsers);
                    $showButton = ($notif['username'] !== $_SESSION['username']);

                    $hideFollowButton = preg_match('/added you to|removed you from|Admin has deleted post/', $notif['message']);
                    ?>
                    <div class="notification-entry">
                        <form method="POST" action="notification(bm).php" class="notif-form">
                            <input type="hidden" name="notif_id" value="<?= $notif['id'] ?>">

                            <button type="submit"
                                class="notificationCard <?= ($notif['status'] === 'n') ? 'unread' : '' ?>">
                                <div class="user-info">
                                    <img src="<?= $notif['profilePic'] ?>" alt="Profile"
                                        style="width: 40px; height: 40px; border-radius: 50%;" />
                                    <p style="font-weight: bold; font-size: 16px; margin: 0;"><?= $notif['username'] ?></p>
                                </div>

                                <div class="description">
                                    <p><?= $notif['message'] ?></p>
                                    <p style="color: gray; font-size: 12px;"><?= $notif['created_at'] ?></p>
                                </div>

                                <?php if ($showButton && !$hideFollowButton): ?>
                                    <button type="button" class="follow-btn <?= $isFollowing ? 'following' : '' ?>"
                                        data-username="<?= $notif['username'] ?>"
                                        onclick="followUser(event, '<?= $notif['username'] ?>')">
                                        <?= $isFollowing ? 'sedang ikut' : 'Ikut' ?>
                                    </button>
                                <?php endif; ?>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </main>
        </div>
    </div>

    <script>
        function followUser(event, username) {
            event.preventDefault();
            localStorage.setItem("followedUser", username);
            window.location.href = "friend(bm).php";
        }

        document.addEventListener("DOMContentLoaded", function () {
            function updateUnreadCount() {
                const unreadElements = document.querySelectorAll(".notificationCard.unread");
                const notifCountElement = document.getElementById("num-of-notif");

                if (!notifCountElement) return;

                if (unreadElements.length > 0) {
                    notifCountElement.innerText = unreadElements.length;
                    notifCountElement.style.display = "flex";
                } else {
                    notifCountElement.style.display = "none";
                }
            }

            updateUnreadCount();

            document.getElementById("mark-as-read").addEventListener("click", function (e) {
                e.preventDefault();

                fetch("notification(bm).php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "mark_all_read=1"
                })
                    .then(response => {
                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            document.querySelectorAll(".notificationCard.unread").forEach((notif) => {
                                notif.classList.remove("unread");
                                notif.style.backgroundColor = "transparent";
                            });
                            updateUnreadCount();
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });

            document.querySelectorAll(".notif-form").forEach((form) => {
                form.addEventListener("submit", function (e) {
                    const button = form.querySelector(".notificationCard");
                    if (button.classList.contains("unread")) {
                        button.classList.remove("unread");
                        button.style.backgroundColor = "transparent";
                        updateUnreadCount();
                    }
                });
            });
        });

        window.addEventListener("load", updateUnreadCount);
    </script>

</body>

</html>