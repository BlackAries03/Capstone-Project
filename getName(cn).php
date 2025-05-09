<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'], $_SESSION['userID'])) {
    echo '<script>
            alert("请先登录。");
            setTimeout(function() {
                window.location.href = "login.php";
            }, 250);
          </script>';
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("数据库连接失败：" . mysqli_connect_error());
}

$user_id = $_SESSION['userID'];


$sql = "SELECT * FROM uData WHERE UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("SQL 预处理失败：" . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


if ($row = $result->fetch_assoc()) {
    $user_name = $row['userName'];
    $userID = $row['UID'];
    $profilePic = !empty($row['profilePic']) ? $row['profilePic'] : 'picture/unknown.jpeg';
    $emailAddress = !empty($row['emailAddress']) ? $row['emailAddress'] : '默认邮箱';
    $phoneNumber = !empty($row['phoneNumber']) ? $row['phoneNumber'] : '无电话号码';
} else {
    $user_name = '未知用户';
}

$stmt->close();

$sql = "SELECT COUNT(FID) AS total_posts FROM feed WHERE UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("SQL 预处理失败：" . $conn->error);
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($totalPosts);
$stmt->fetch();
$stmt->close();

$_SESSION["total_posts"] = intval($totalPosts);

$followedUsers = [];
$sql = "SELECT u.UID, u.userName, IFNULL(u.profilePic, 'picture/unknown.jpeg') AS profilePic 
        FROM uData u 
        JOIN follow f ON u.UID = f.following 
        WHERE f.UID = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL 预处理失败：" . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($user = $result->fetch_assoc()) {
    $followedUsers[] = $user;
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST['new-username'])) {
        $newUsername = trim($_POST['new-username']);
        $stmt = $conn->prepare("UPDATE uData SET userName = ? WHERE UID = ?");

        if ($stmt === false) {
            die("SQL 预处理失败：" . $conn->error);
        }

        $stmt->bind_param("si", $newUsername, $userID);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['username'] = $newUsername;
                echo '<script>alert("用户名更新成功！"); window.location.href="_accountManagement.php";</script>';
            } else {
                echo '<script>alert("未做任何更改！可能是相同的用户名。");</script>';
            }
        } else {
            echo '<script>alert("更新用户名时出错：" . $stmt->error . ");</script>';
        }

        $stmt->close();
    }
    if (!empty($_POST['new-email'])) {
        $newEmail = trim($_POST['new-email']);
        $stmt = $conn->prepare("UPDATE uData SET emailAddress = ? WHERE UID = ?");
        $stmt->bind_param("si", $newEmail, $userID);
        if ($stmt->execute()) {
            $_SESSION['emailAddress'] = $newEmail;
            echo '<script>alert("邮箱更新成功！"); window.location.href="_accountManagement.php";</script>';
        } else {
            echo '<script>alert("更新邮箱时出错！");</script>';
        }
        $stmt->close();
    }
    if (!empty($_POST['new-phone'])) {
        $newPhone = trim($_POST['new-phone']);
        $stmt = $conn->prepare("UPDATE uData SET phoneNumber = ? WHERE UID = ?");
        $stmt->bind_param("si", $newPhone, $userID);
        if ($stmt->execute()) {
            $_SESSION['phoneNumber'] = $newPhone;
            echo '<script>alert("电话号码更新成功！"); window.location.href="_accountManagement.php";</script>';
        } else {
            echo '<script>alert("更新电话号码时出错！");</script>';
        }
        $stmt->close();
    }

    if (isset($_POST['delete-account'])) {
        $stmt = $conn->prepare("DELETE FROM uData WHERE UID = ?");
        $stmt->bind_param("i", $userID);
        if ($stmt->execute()) {
            session_destroy();
            echo '<script>alert("账户删除成功！"); window.location.href="login.php";</script>';
        } else {
            echo '<script>alert("删除账户时出错！");</script>';
        }
        $stmt->close();
    }
    if (isset($_POST['psw'])) {
        $newPassword = trim($_POST["psw"]);
        $userID = $_SESSION["userID"];

        if (empty($newPassword)) {
            echo '<script>alert("密码不能为空！");</script>';
            exit();
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE uData SET password = ? WHERE UID = ?");
        $stmt->bind_param("si", $hashedPassword, $userID);

        if ($stmt->execute()) {
            echo '<script>alert("密码更新成功！"); window.location.href="_accountManagement.php";</script>';
        } else {
            echo '<script>alert("更新密码时出错！");</script>';
        }

        $stmt->close();
    }
}

$userID = $_SESSION["userID"];
$sql = "SELECT COUNT(follow.UID) AS total_following FROM follow JOIN uData ON follow.UID = uData.UID WHERE follow.UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("SQL 预处理失败：" . $conn->error);
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($followingCount);
$stmt->fetch();
$stmt->close();

$_SESSION["following"] = intval($followingCount);

$sql = "SELECT COUNT(follow.UID) AS follower FROM follow JOIN uData ON follow.UID = uData.UID WHERE follow.following = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("SQL 预处理失败：" . $conn->error);
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($followerCount);
$stmt->fetch();
$stmt->close();

$_SESSION["follower"] = intval($followerCount);

if (!function_exists('isDuplicate')) {
    function isDuplicate($conn, $column, $value) {
        $sql = "SELECT COUNT(*) FROM uData WHERE $column = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("SQL 预处理失败：" . $conn->error);
        }
        $stmt->bind_param("s", $value);
        $stmt->execute();
        
        $count = 0;
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        
        return $count > 0;
    }
}
?>
