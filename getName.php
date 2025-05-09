<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'], $_SESSION['userID'])) {
    echo '<script>
            alert("Please Login First.");
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
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['userID'];

$sql = "SELECT * FROM uData WHERE UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_name = $row['userName'];
    $userID = $row['UID'];
    $profilePic = !empty($row['profilePic']) ? $row['profilePic'] : 'picture/unknown.jpeg';
    $emailAddress = !empty($row['emailAddress']) ? $row['emailAddress'] : 'default gmail';
    $phoneNumber = !empty($row['phoneNumber']) ? $row['phoneNumber'] : 'no phone number';
} else {
    $user_name = 'UNKNOWN';
}

$stmt->close();

$sql = "SELECT COUNT(FID) AS total_posts FROM feed WHERE UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
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
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($user = $result->fetch_assoc()) {
    $followedUsers[] = $user;
}

$stmt->close();

if (!function_exists('isDuplicate')) {
    function isDuplicate($conn, $column, $value) {
        $sql = "SELECT COUNT(*) FROM uData WHERE $column = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST['new-username'])) {
        $newUsername = trim($_POST['new-username']);

        if (isDuplicate($conn, 'userName', $newUsername)) {
            echo '<script>alert("Username already exists. Please choose another one.");</script>';
        } else {
            $stmt = $conn->prepare("UPDATE uData SET userName = ? WHERE UID = ?");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("si", $newUsername, $userID);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $_SESSION['username'] = $newUsername;
                    echo '<script>alert("Username updated successfully!"); window.location.href="_accountManagement.php";</script>';
                } else {
                    echo '<script>alert("No changes made! Username might be the same.");</script>';
                }
            } else {
                echo '<script>alert("Error updating username: ' . $stmt->error . '");</script>';
            }
            $stmt->close();
        }
    }

    if (!empty($_POST['new-email'])) {
        $newEmail = trim($_POST['new-email']);
        
        // Check for duplicate email
        if (isDuplicate($conn, 'emailAddress', $newEmail)) {
            echo '<script>alert("Email already exists. Please choose another one.");</script>';
        } else {
            $stmt = $conn->prepare("UPDATE uData SET emailAddress = ? WHERE UID = ?");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("si", $newEmail, $userID);
            if ($stmt->execute()) {
                $_SESSION['emailAddress'] = $newEmail;
                echo '<script>alert("Email updated successfully!"); window.location.href="_accountManagement.php";</script>';
            } else {
                echo '<script>alert("Error updating email!");</script>';
            }
            $stmt->close();
        }
    }

    if (!empty($_POST['new-phone'])) {
        $newPhone = trim($_POST['new-phone']);
        $stmt = $conn->prepare("UPDATE uData SET phoneNumber = ? WHERE UID = ?");
        $stmt->bind_param("si", $newPhone, $userID);
        if ($stmt->execute()) {
            $_SESSION['phoneNumber'] = $newPhone;
            echo '<script>alert("Phone number updated successfully!"); window.location.href="_accountManagement.php";</script>';
        } else {
            echo '<script>alert("Error updating phone number!");</script>';
        }
        $stmt->close();
    }

    if (isset($_POST['delete-account'])) {
        $stmt = $conn->prepare("DELETE FROM uData WHERE UID = ?");
        $stmt->bind_param("i", $userID);
        if ($stmt->execute()) {
            session_destroy();
            echo '<script>alert("Account deleted successfully!"); window.location.href="login.php";</script>';
        } else {
            echo '<script>alert("Error deleting account!");</script>';
        }
        $stmt->close();
    }

    if (isset($_POST['psw'])) {
        $newPassword = trim($_POST["psw"]);
        $userID = $_SESSION["userID"];

        if (empty($newPassword)) {
            echo '<script>alert("Password cannot be empty!");</script>';
            exit();
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE uData SET password = ? WHERE UID = ?");
        $stmt->bind_param("si", $hashedPassword, $userID);

        if ($stmt->execute()) {
            echo '<script>alert("Password Updated!"); window.location.href="_accountManagement.php";</script>';
        } else {
            echo '<script>alert("Error updating password!");</script>';
        }

        $stmt->close();
    }
}

$userID = $_SESSION["userID"];
$sql = "SELECT COUNT(follow.UID) AS total_following FROM follow JOIN uData ON follow.UID = uData.UID WHERE follow.UID = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
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
    die("Prepare failed". $conn->error);
}

$stmt->bind_param("i", $userID);
$stmt->execute();
$stmt->bind_result($followerCount);
$stmt->fetch();
$stmt->close();
$_SESSION["follower"] = intval($followerCount);