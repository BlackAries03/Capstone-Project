<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'], $_SESSION['userID'])) {
    echo '<script>
            alert("Sila log masuk dahulu.");
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
    die("Sambungan gagal: " . mysqli_connect_error());
}

$user_id = $_SESSION['userID'];

$sql = "SELECT * FROM uData WHERE UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Persediaan gagal: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_name = $row['userName'];
    $userID = $row['UID'];
    $profilePic = !empty($row['profilePic']) ? $row['profilePic'] : 'picture/unknown.jpeg';
    $emailAddress = !empty($row['emailAddress']) ? $row['emailAddress'] : 'default gmail';
    $phoneNumber = !empty($row['phoneNumber']) ? $row['phoneNumber'] : 'tiada nombor telefon';
} else {
    $user_name = 'TIDAK DIKENALI';
}

$stmt->close();

$sql = "SELECT COUNT(FID) AS total_posts FROM feed WHERE UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Persediaan gagal: " . $conn->error);
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
    die("Ralat SQL: " . $conn->error);
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
            die("Persediaan gagal: " . $conn->error);
        }

        $stmt->bind_param("si", $newUsername, $userID);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['username'] = $newUsername;
                echo '<script>alert("Nama pengguna berjaya dikemas kini!"); window.location.href="_accountManagement.php";</script>';
            } else {
                echo '<script>alert("Tiada perubahan! Nama pengguna mungkin sama.");</script>';
            }
        } else {
            echo '<script>alert("Ralat mengemas kini nama pengguna: ' . $stmt->error . '");</script>';
        }

        $stmt->close();
    }

    if (!empty($_POST['new-email'])) {
        $newEmail = trim($_POST['new-email']);
        $stmt = $conn->prepare("UPDATE uData SET emailAddress = ? WHERE UID = ?");
        $stmt->bind_param("si", $newEmail, $userID);
        if ($stmt->execute()) {
            $_SESSION['emailAddress'] = $newEmail;
            echo '<script>alert("E-mel berjaya dikemas kini!"); window.location.href="_accountManagement.php";</script>';
        } else {
            echo '<script>alert("Ralat mengemas kini e-mel!");</script>';
        }
        $stmt->close();
    }

    if (!empty($_POST['new-phone'])) {
        $newPhone = trim($_POST['new-phone']);
        $stmt = $conn->prepare("UPDATE uData SET phoneNumber = ? WHERE UID = ?");
        $stmt->bind_param("si", $newPhone, $userID);
        if ($stmt->execute()) {
            $_SESSION['phoneNumber'] = $newPhone;
            echo '<script>alert("Nombor telefon berjaya dikemas kini!"); window.location.href="_accountManagement.php";</script>';
        } else {
            echo '<script>alert("Ralat mengemas kini nombor telefon!");</script>';
        }
        $stmt->close();
    }

    if (isset($_POST['delete-account'])) {
        $stmt = $conn->prepare("DELETE FROM uData WHERE UID = ?");
        $stmt->bind_param("i", $userID);
        if ($stmt->execute()) {
            session_destroy();
            echo '<script>alert("Akaun berjaya dipadam!"); window.location.href="login.php";</script>';
        } else {
            echo '<script>alert("Ralat memadam akaun!");</script>';
        }
        $stmt->close();
    }

    if (isset($_POST['psw'])) {
        $newPassword = trim($_POST["psw"]);
        $userID = $_SESSION["userID"];

        if (empty($newPassword)) {
            echo '<script>alert("Kata laluan tidak boleh kosong!");</script>';
            exit();
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE uData SET password = ? WHERE UID = ?");
        $stmt->bind_param("si", $hashedPassword, $userID);

        if ($stmt->execute()) {
            echo '<script>alert("Kata laluan berjaya dikemas kini!"); window.location.href="_accountManagement.php";</script>';
        } else {
            echo '<script>alert("Ralat mengemas kini kata laluan!");</script>';
        }

        $stmt->close();
    }
}

$userID = $_SESSION["userID"];
$sql = "SELECT COUNT(follow.UID) AS total_following FROM follow JOIN uData ON follow.UID = uData.UID WHERE follow.UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Persediaan gagal: " . $conn->error);
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($followingCount);
$stmt->fetch();
$stmt->close();

$_SESSION["following"] = intval($followingCount);

$sql = "SELECT COUNT(follow.UID) AS follower FROM follow JOIN uData on follow.UID = uData.UID WHERE follow.following = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Persediaan gagal: " . $conn->error);
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
            die("Persediaan gagal: " . $conn->error);
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
