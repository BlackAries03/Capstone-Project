<?php
include 'getName(cn).php';
if (session_status() == PHP_SESSION_NONE) {
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  header("Content-Type: application/json");

  if (isset($_POST['delete_post']) || (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false)) {
    $data = json_decode(file_get_contents("php://input"), true);
    $FID = intval($data['FID'] ?? 0);

    if ($FID > 0) {
      $getImage = $conn->prepare("SELECT Fimage FROM feed WHERE FID = ?");
      $getImage->bind_param("i", $FID);
      $getImage->execute();
      $getImage->bind_result($imagePath);
      $getImage->fetch();
      $getImage->close();

      if ($imagePath && file_exists($imagePath)) {
        unlink($imagePath);
      }

      $deleteStmt = $conn->prepare("DELETE FROM feed WHERE FID = ?");
      $deleteStmt->bind_param("i", $FID);

      if ($deleteStmt->execute()) {
        echo json_encode(["success" => true]);
      } else {
        echo json_encode(["success" => false, "error" => "Database deletion failed"]);
      }

      $deleteStmt->close();
    } else {
      echo json_encode(["success" => false, "error" => "Invalid post ID"]);
    }
    exit();
  }
}

$_SESSION['UID'] = $user_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  header('Content-Type: application/json');

  try {
    if ($_POST['action'] === 'getComments') {
      $fid = intval($_POST['fid']);
      $uid = $_SESSION['UID'];

      $sql = "SELECT c.*, u.userName, u.profilePic, 
                    EXISTS(SELECT 1 FROM likeCom WHERE comID = c.comID AND UID = ?) AS isLiked
                    FROM comment c 
                    JOIN udata u ON c.UID = u.UID 
                    WHERE c.FID = ? 
                    ORDER BY c.time DESC";

      $stmt = $conn->prepare($sql);
      $stmt->bind_param("ii", $uid, $fid);
      $stmt->execute();
      $result = $stmt->get_result();

      $comments = [];
      while ($row = $result->fetch_assoc()) {
        $comments[] = [
          'comID' => $row['comID'],
          'userName' => $row['userName'],
          'profilePic' => $row['profilePic'],
          'message' => $row['message'],
          'time' => $row['time'],
          'likes' => $row['l'],
          'isLiked' => (bool) $row['isLiked']
        ];
      }
      echo json_encode($comments);
      exit;
    }

    if ($_POST['action'] === 'addComment') {
      $fid = intval($_POST['fid']);
      $uid = intval($_SESSION['UID']);
      $message = $conn->real_escape_string($_POST['message']);

      $stmt = $conn->prepare("INSERT INTO comment (FID, UID, message) VALUES (?, ?, ?)");
      $stmt->bind_param("iis", $fid, $uid, $message);
      $stmt->execute();

      $newComID = $conn->insert_id;
      $stmt = $conn->prepare("SELECT c.*, u.userName, u.profilePic 
                               FROM comment c 
                               JOIN udata u ON c.UID = u.UID 
                               WHERE c.comID = ?");
      $stmt->bind_param("i", $newComID);
      $stmt->execute();
      $comment = $stmt->get_result()->fetch_assoc();

      $comment['isLiked'] = false;
      $comment['likes'] = 0;

      echo json_encode(['success' => true, 'comment' => $comment]);
      exit;
    }

    if ($_POST['action'] === 'likePost') {
      $fid = intval($_POST['fid']);
      $uid = intval($_SESSION['UID']);

      $check = $conn->prepare("SELECT 1 FROM likePost WHERE UID = ? AND FID = ?");
      $check->bind_param("ii", $uid, $fid);
      $check->execute();
      $exists = $check->get_result()->num_rows > 0;

      if ($exists) {
        $delete = $conn->prepare("DELETE FROM likePost WHERE UID = ? AND FID = ?");
        $delete->bind_param("ii", $uid, $fid);
        $delete->execute();

        $update = $conn->prepare("UPDATE feed SET l = l - 1 WHERE FID = ?");
      } else {
        $insert = $conn->prepare("INSERT INTO likePost (UID, FID) VALUES (?, ?)");
        $insert->bind_param("ii", $uid, $fid);
        $insert->execute();

        $update = $conn->prepare("UPDATE feed SET l = l + 1 WHERE FID = ?");
      }

      $update->bind_param("i", $fid);
      $update->execute();

      $result = $conn->query("SELECT l FROM feed WHERE FID = $fid");
      $count = $result->fetch_assoc()['l'];

      echo json_encode(['success' => true, 'liked' => !$exists, 'likes' => $count]);
      exit;
    }

    if ($_POST['action'] === 'getFullFname') {
      $fid = intval($_POST['fid']);

      $stmt = $conn->prepare("SELECT Fname FROM feed WHERE FID = ?");
      $stmt->bind_param("i", $fid);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'Fname' => $row['Fname']]);
      } else {
        echo json_encode(['success' => false, 'error' => 'Post not found']);
      }
      exit;
    }

    if ($_POST['action'] === 'getLikeCount') {
      $fid = intval($_POST['fid']);
      $uid = intval($_SESSION['UID']);

      $stmt = $conn->prepare("SELECT l FROM feed WHERE FID = ?");
      $stmt->bind_param("i", $fid);
      $stmt->execute();
      $result = $stmt->get_result();
      $likeCount = $result->fetch_assoc()['l'];

      $check = $conn->prepare("SELECT 1 FROM likePost WHERE UID = ? AND FID = ?");
      $check->bind_param("ii", $uid, $fid);
      $check->execute();
      $isLiked = $check->get_result()->num_rows > 0;

      echo json_encode(['success' => true, 'likes' => $likeCount, 'isLiked' => $isLiked]);
      exit;
    }

    if ($_POST['action'] === 'likeComment') {
      $comID = intval($_POST['comID']);
      $uid = intval($_SESSION['UID']);

      $check = $conn->prepare("SELECT 1 FROM likeCom WHERE UID = ? AND comID = ?");
      $check->bind_param("ii", $uid, $comID);
      $check->execute();
      $exists = $check->get_result()->num_rows > 0;

      if ($exists) {
        $delete = $conn->prepare("DELETE FROM likeCom WHERE UID = ? AND comID = ?");
        $delete->bind_param("ii", $uid, $comID);
        $delete->execute();

        $update = $conn->prepare("UPDATE comment SET l = l - 1 WHERE comID = ?");
      } else {
        $insert = $conn->prepare("INSERT INTO likeCom (UID, comID) VALUES (?, ?)");
        $insert->bind_param("ii", $uid, $comID);
        $insert->execute();

        $update = $conn->prepare("UPDATE comment SET l = l + 1 WHERE comID = ?");
      }

      $update->bind_param("i", $comID);
      $update->execute();

      $result = $conn->query("SELECT l FROM comment WHERE comID = $comID");
      $count = $result->fetch_assoc()['l'];

      echo json_encode(['success' => true, 'liked' => !$exists, 'likes' => $count]);
      exit;
    }

  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    exit;
  }
}

function getComments($conn, $fid)
{
  $sql = "SELECT c.*, u.userName, u.profilePic 
            FROM comment c 
            JOIN udata u ON c.UID = u.UID 
            WHERE c.FID = ? 
            ORDER BY c.time DESC";

  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    error_log("SQL Prepare Error: " . $conn->error);
    return false;
  }

  $stmt->bind_param("i", $fid);

  if (!$stmt->execute()) {
    error_log("SQL Execute Error: " . $stmt->error);
    return false;
  }

  return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="ms">
<link rel="website icon" type="image/png" href="picture\logo.png">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laman Utama</title>
  <link rel="stylesheet" href="sidebarStyle.css">
  <style>
    :root {
        --light-bg: antiquewhite;
        --light-container-bg: #fff;
        --light-text: #000;
        --light-border: #ddd;
        --gradient: linear-gradient(90deg, #007bff, #0056b3);

        --dark-bg: #2e2e2e;
        --dark-container-bg: #3c3c3c;
        --dark-text: #e1e1e1;
        --dark-border: #555;
        --dark-gradient: linear-gradient(90deg, #0056b3, #003e7e);
        --overlay-bg: rgba(0, 0, 0, 0.8);
    }

    body {
        display: flex;
        background: antiquewhite;
        color: var(--light-text);
        transition: background-color 0.3s, color 0.3s;
    }

    .header {
        position: fixed;
        width: calc(100% - 300px);
        top: 0;
        left: 300px;
        background: var(--light-container-bg);
        padding: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #searchBar {
        width: 80%;
        padding: 10px;
        border-radius: 50px;
        font-size: 1rem;
    }

    .feed-container {
        padding: 20px;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-gap: 20px;
    }

    .grid-item {
        background: var(--light-container-bg);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        height: auto;
    }

    .grid-item img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 1rem 1rem 0 0;
    }

    .post-info {
        padding: 10px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        width: 100%;
    }

    .post-profile {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .post-details {
        display: flex;
        flex-direction: column;
        gap: 5px;
        width: 100%;
    }

    .post-details h4 {
        margin: 0;
        font-size: 0.9rem;
        color: #555;
    }

    .likes {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        color: #555;
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 2000;
        justify-content: center;
        align-items: center;
    }

    .maximized-feed {
        background: var(--light-container-bg);
        border-radius: 1rem;
        padding: 20px;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        position: relative;
        height: 77vh;
        max-height: 80vh;
        width: 70%;
    }

    .maximized-feed img {
        max-width: 60%;
        max-height: 80%;
        height: 80%;
        border-radius: 1rem;
    }

    .interaction-section {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
        max-width: 40%;
        margin-left: 20px;
        height: auto;
    }

    .profile {
        display: flex;
        align-items: center;
    }

    .profile-picture-container {
        width: 70px;
        height: 55px;
        overflow: hidden;
        margin-right: 150px;
    }

    .profile-picture {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-info {
        margin-top: -55px;
        margin-left: -20px;
    }

    .name {
        font-weight: bold;
    }

    .date {
        display: block;
        font-size: 0.8em;
        color: grey;
        margin-bottom: 20px;
    }

    .return-button {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 20px;
        margin-right: 10px;
    }

    .return-button:hover {
        background-color: #0056b3;
    }

    .like-count {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .like-count img {
        width: 24px;
        height: 24px;
        margin-right: 10px;
    }

    .comments-list {
        max-height: 270px;
        overflow-y: auto;
        margin-bottom: 10px;
        width: 250px;
    }

    .comment {
        width: 100%;
        margin-bottom: 15px;
        padding: 10px;
        border-bottom: 1px solid #ddd;
        word-wrap: break-word;
        white-space: normal;
        overflow-wrap: break-word;
    }

    .comment-message {
        margin: 5px 0;
        font-size: 0.9em;
        word-wrap: break-word;
        white-space: normal;
        overflow-wrap: break-word;
    }

    .commenter-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }

    .comment-avatar-container {
        width: 60px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 50%;
    }

    .comment-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .commenter-name {
        font-weight: bold;
    }

    .comment-time {
        font-size: 0.8em;
        color: #777;
    }

    .comment-like {
        display: flex;
        align-items: center;
        gap: 5px;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        font-size: 0.9em;
        color: #555;
    }

    .comment-like img {
        width: 16px;
        height: 16px;
    }

    .comment-input-container {
        display: flex;
        width: 100%;
        gap: 10px;
        align-items: center;
        margin-top: 10px;
    }

    #commentInput {
        flex: 1;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        height: 40px;
        resize: none;
    }

    .main-home {
        position: absolute;
        width: calc(100% - 300px);
        top: 20px;
        left: 300px;
        background: var(--light-container-bg);
        border-radius: 1rem 0 0 1rem;
    }

    /* Continued Code from Second Part */

    .search {
        display: flex;
        align-items: center;
        background: #f8f8f8;
        height: 2.4rem;
        padding: 10px;
        border-radius: 10px;
        border: 2px solid black;
        flex: 1;
        margin-right: 10px;
    }

    .search img {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }

    .search input {
        border: none;
        outline: none;
        background: transparent;
        flex: 1;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-content img {
        width: 30px;
        height: 30px;
        cursor: pointer;
    }

    .btn {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 8px;
        background: var(--gradient);
        white-space: nowrap;
    }

    .btn img {
        width: 30px;
        height: 30px;
        margin-right: 10px;
    }

    .btn-text {
        color: #fff;
    }

    .btn img {
        margin-right: 10px;
    }

    .stories-title {
        display: flex;
        justify-content: space-between;
        margin-top: 4rem;
    }

    .stories-title h1 {
        font-size: 1.8rem;
    }

    .stories-title .btn {
        display: flex;
        align-items: center;
        color: #000;
    }

    .stories-title .btn i {
        font-size: 24px;
        margin-right: 10px;
    }

    .stories {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-top: 2rem;
        padding: 10px;
    }

    .stories-img {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 2px solid #5de0e6;
        cursor: pointer;
    }

    .stories-img img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 50%;
        object-position: center;
    }

    .stories-img .add {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        color: #fff;
        background: #5de0e6;
        bottom: 0;
        right: 0;
    }

    .following-stories {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        padding: 10px;
        min-height: 85px;
    }

    .story-avatar-container {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 2px solid #5de0e6;
        cursor: pointer;
        overflow: hidden;
        flex-shrink: 0;
    }

    .story-avatar-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .add-story {
        position: absolute;
        bottom: 20px;
        width: 30px;
        height: 30px;
        cursor: pointer;
        z-index: 3;
        display: none;
    }

    .following-stories::-webkit-scrollbar {
        display: none;
    }

    .following-stories {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .feed {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1rem;
    }

    .feed h1 {
        font-size: 1.7rem;
    }

    .main-post {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, auto));
        gap: 11px;
    }

    .post-box {
        width: 300px;
    }

    .post-box img {
        width: 100%;
        height: 344px;
        object-fit: cover;
    }

    .post-img {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: 2px solid #5de0e6;
    }

    .post-img img {
        width: 27px;
        height: 27px;
        object-fit: cover;
        border-radius: 50%;
        object-position: center;
        border: none;
    }

    .post-profile {
        display: flex;
        align-items: center;
    }

    .post-profile h3 {
        font-size: 12px;
        font-weight: 600px;
        margin-left: 5px;
    }

    .likes {
        display: flex;
        align-items: center;
    }

    .likes i {
        font-size: 20px;
        margin-left: 7px;
    }

    .likes span {
        font-size: 14px;
        margin-left: 7px;
    }

    .header-content img {
        cursor: pointer;
    }

    .popup {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 102%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .popup-content {
        background-color: var(--light-container-bg);
        margin: 15% auto;
        padding: 20px;
        border-radius: 15px;
        border: 1px solid #888;
        width: 80%;
        max-width: 400px;
        position: relative;
        text-align: left;
    }

    .popup-content img {
        display: block;
        margin: 0 auto 10px;
    }

    .popup-content h2 {
        margin-top: 30px;
        text-align: left;
    }

    .file-label {
        display: block;
        width: 50px;
        height: 50px;
        cursor: pointer;
        border: 2px dashed #ccc;
        border-radius: 10px;
        text-align: center;
        line-height: 50px;
        margin: 10px 0;
    }

    .file-label img {
        max-width: 100%;
        max-height: 100%;
        object-fit: cover;
    }

    .back-btn {
        position: absolute;
        top: 10px;
        left: 10px;
        cursor: pointer;
    }

    .popup-content .add-btn {
        position: absolute;
        bottom: 20px;
        right: 20px;
        background-color: #007BFF;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .popup-content .add-btn:hover {
        background-color: #0056b3;
    }

    .popup-content label {
        font-size: 20px;
        display: block;
        margin-bottom: 10px;
    }

    .large-input {
        width: 100%;
        padding: 10px;
        font-size: 18px;
        margin-bottom: 20px;
        border-radius: 5px;
        border: 1px solid #888;
        box-sizing: border-box;
    }

    .hidden-title {
        display: none;
    }

    .image-container {
        position: relative;
        width: 100%;
        height: auto;
        border: 2px solid black;
    }

    .deleteIcon {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 25px;
        height: 25px;
        cursor: pointer;
    }

    .stories-banner {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .stories-background {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: black;
        background-size: contain !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
        border-radius: 10px;
        z-index: 1;
    }

    .stories-content {
        background: black;
        padding: 20px;
        border-radius: 10px;
        position: relative;
        width: 500px;
        height: 700px;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 2;
    }

    .back-button {
        position: absolute;
        top: 10px;
        left: 10px;
        width: 30px;
        height: 30px;
        cursor: pointer;
        z-index: 3;
    }

    .profile-pic {
        position: absolute;
        top: 10px;
        left: 50px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        z-index: 3;
    }

    .user-info {
        position: absolute;
        top: 10px;
        left: 90px;
        display: flex;
        flex-direction: column;
        z-index: 3;
        color: white;
    }

    .username {
        font-weight: bold;
        color: white;
    }

    .timestamp {
        font-size: 0.8em;
        color: rgba(255, 255, 255, 0.8);
    }

    .user-info,
    .username,
    .timestamp {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .add-story {
        width: 30px;
        height: 30px;
        margin-top: 400px;
        z-index: 3;
    }

    .stories-content p {
        margin-top: 200px;
        text-align: center;
        z-index: -1;
    }

    .fname {
        max-height: 30px;
        overflow-y: scroll;
        font-size: 15px;
        font-weight: bold;
        margin: 10px 0;
        word-wrap: break-word;
        white-space: normal;
        max-width: 100%;
    }

    .story-nav-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.3);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        z-index: 4;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .story-nav-button:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .story-nav-prev {
        left: 10px;
    }

    .story-nav-next {
        right: 10px;
    }

    .story-nav-button img {
        width: 20px;
        height: 20px;
    }

    .stories-content .profile-pic,
    .stories-content .user-info,
    .stories-content .add-story {
        transition: all 0.3s ease;
    }

    /* Dark theme */
    .dark body {
        background: #2e2e2e;
        color: var(--dark-text);
    }

    .dark .header {
        background: var(--dark-container-bg);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    }

    .dark .feed-container,
    .dark .main-home,
    .dark .popup-content {
        background: var(--dark-container-bg);
    }

    .dark .grid-item,
    .dark .maximized-feed {
        background: var(--dark-container-bg);
    }

    .dark .popup-content .add-btn {
        background-color: #007bff;
    }

    .dark .popup-content .add-btn:hover {
        background-color: #0056b3;
    }

    .dark .comment,
    .dark .search {
        background-color: var(--dark-container-bg);
    }

    .dark .comment-input-container #commentInput,
    .dark .input-group input {
        background-color: var(--dark-bg);
        color: var(--dark-text);
        border: 1px solid var(--dark-border);
    }

    
    .dark .post-img img,
    .dark .profile-picture,
    .dark .comment-avatar,
    .dark .post-box img,
    .dark .stories-img img,
    .dark .story-avatar-container img,
    .dark .profile-pic {
        border: 2px solid var(--dark-border);
    }

    .dark .comments-list {
        border-bottom: 1px solid var(--dark-border);
    }

    .dark .stories-background {
        background-color: var(--overlay-bg);
    }

    .dark .post-details h4,
    .dark .likes i,
    .dark .likes span,
    .dark .comment-like,
    .dark .username,
    .dark .timestamp {
        color: var(--dark-text);
    }

    .dark .notificationHeader,
    .dark .stories-title .btn,
    .dark .stories-content .profile-pic,
    .dark .stories-content .user-info,
    .dark .fname,
    .dark .file-label,
    .dark .deleteIcon,
    .dark .hidden-title,
    .dark .image-container,
    .dark .story-nav-button,
    .dark .story-nav-button img {
        color: var(--dark-text);
    }

/* Additional Styles */

  </style>

</head>

<body>
  <?php include("sidebar(cn).php"); ?>
  <div class="main-home">
    <div class="header">
      <div class="search">
        <img src="picture/search.png" alt="search icon">
        <input type="text" id="searchBar" placeholder="搜索" oninput="filterPosts()" />
      </div>
      <div class="header-content">
        <img src="picture/notification.png" alt="notification icon" onclick="window.location.href='notification(cn).php'">

        <a href="#" class="btn">
          <img src="picture/add.png" alt="add icon">
          <div class="btn-text">添加帖子</div>
          <input type="file" accept="image/*" id="photo-input" style="display: none;" onchange="uploadImage(event)">
        </a>
      </div>
    </div>
    <div class="stories-title">
      <h1>故事</h1>
    </div>
    <div class="stories">
      <div class="stories-img" onclick="showStories('own')">
        <img src="<?php echo $_SESSION['profilePic'] ?? 'picture/unknown.jpeg'; ?>" alt="Your story">
        <div class="add">+</div>
      </div>
      <div class="following-stories" id="followingStories"></div>
    </div>

    <div id="stories-banner" class="stories-banner">
      <div class="stories-content">
        <div class="stories-background"></div>
        <button class="story-nav-button story-nav-prev" onclick="navigateStory(-1)">
          <img src="picture/previousImg.png" alt="Previous">
        </button>
        <button class="story-nav-button story-nav-next" onclick="navigateStory(1)">
          <img src="picture/previousImg.png" alt="Next" style="transform: rotate(180deg);">
        </button>
        <img src="picture/previousImg.png" alt="back-button" class="back-button" onclick="hideStories()">
        <img src="<?php echo $profilePic ?>" alt="profile" class="profile-pic">
        <div class="user-info">
          <span class="username"><?php echo htmlspecialchars($user_name); ?></span>
          <span class="timestamp"></span>
        </div>
        <img src="picture/white add.png" alt="add" class="add-story"
          onclick="document.getElementById('file-input').click()">
        <input type="file" id="file-input" accept="image/*" style="display: none;" onchange="uploadPhoto(event)">
      </div>
    </div>

    <div class="feed">
      <h1>我的帖子</h1>
    </div>
    <div class="feed-container">
      <div class="grid" id="gridContainer">
        <?php
        $sql = "SELECT f.FID, f.UID, f.Fimage, f.Fname, f.time, f.l AS likeCount, 
                u.userName, u.profilePic, 
                COUNT(c.comID) AS commentCount
         FROM feed f 
         JOIN udata u ON f.UID = u.UID 
         LEFT JOIN comment c ON f.FID = c.FID 
         WHERE f.UID = ? 
         GROUP BY f.FID 
         ORDER BY f.time DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $fname = htmlspecialchars($row["Fname"]);
            $truncatedFname = strlen($fname) > 10 ? substr($fname, 0, 10) . '...' : $fname;

            echo '<div class="grid-item" onclick="maximizeFeed(this, ' . $row['FID'] . ')" data-time="' . htmlspecialchars($row["time"]) . '" data-fname="' . htmlspecialchars($row["Fname"]) . '">';
            echo '  <div class="image-container">';
            echo '    <img src="' . htmlspecialchars($row["Fimage"]) . '" alt="' . htmlspecialchars($row["Fname"]) . '">';
            echo '    <img src="picture/deletepost.png" alt="delete post" class="deleteIcon" data-fid="' . $row["FID"] . '" onclick="deletePost(event)" style="width: 25px; height: auto;">';
            echo '  </div>';
            echo '  <div class="post-info" >';
            echo '    <div class="post-profile" style="display:none;">';
            echo '      <div class="post-img">';
            echo '        <img src="' . (!empty($row["profilePic"]) ? htmlspecialchars($row["profilePic"]) : "picture/unknown.jpeg") . '" alt="Profile Picture">';
            echo '      </div>';
            echo '      <h3>' . htmlspecialchars($row["userName"]) . '</h3>';
            echo '    </div>';
            echo '    <div class="post-details">';
            echo '      <h4>' . $truncatedFname . '</h4>';
            echo '      <div class="likes">';
            echo '        <img src="picture/like1.png" alt="like" style="width: 24px; height: auto;"><span>' . htmlspecialchars($row["likeCount"]) . '</span>';
            echo '        <img src="picture/chat.png" alt="comment" style="width: 24px; height: auto;"></img> <span>' . htmlspecialchars($row["commentCount"]) . '</span>';
            echo '      </div>';
            echo '    </div>';
            echo '  </div>';
            echo '</div>';
          }
        } else {
          echo '<p>未找到帖子.</p>';
        }
        $conn->close();
        ?>
      </div>
      <div id="no-result" style="display: none; text-align: center; margin-top: 20px;">
      未找到帖子. 请重新搜索!
      </div>
    </div>
    <div id="overlay" class="overlay">
      <div id="maximizedFeed" class="maximized-feed">
        <button class="return-button" onClick="closeOverlay()">返回</button>
        <img id="maximizedImage" src="" alt="">
        <div class="interaction-section">
          <div class="profile">
            <div class="profile-picture-container">
              <img src="<?php echo $profilePic ?>" class="profile-picture" alt="Profile Picture"
                style="border-radius:50%; width: 70px; height: 70px;">
            </div>
            <div class="profile-info">
              <span class="name"><?php echo $user_name ?></span>
              <span class="date">??? time ago</span>
            </div>
          </div>
          <h3 class="fname" id="maximizedFname"></h3>
          <div class="like-count">
            <span id="likeCount"></span>
          </div>
          <div id="commentsList" class="comments-list"></div>
          <div class="comment-input-container">
            <textarea id="commentInput" placeholder="添加评论..." onkeydown="handleCommentInput(event)"></textarea>
          </div>
        </div>
      </div>
    </div>
    <div id="popup-banner" class="popup">
      <div class="popup-content">
        <img src="picture/back-button.png" width="30" height="30" alt="Back" class="back-btn" onclick="closePopup()" />
        <h2>上传照片</h2>

        <form id="upload-form" action="mainDB.php" method="POST" enctype="multipart/form-data"
          onsubmit="return validateTitle(event)">
          <label for="file-input2" class="file-label">
            <img src="picture/add.png" width="50" height="50" alt="Add" />
          </label>
          <input type="file" name="picture" id="file-input2" accept="image/*" style="display: none;"
            onchange="previewImage(event)" required />

          <label for="photo-title">标题:</label>
          <input type="text" name="Fname" id="photo-title" placeholder="输入标题" class="large-input" required />
          <div id="title-error" style="color: red; display: none; margin-bottom: 10px;"></div>

          <input type="hidden" name="UID" value="<?php echo htmlspecialchars($user_id); ?>">

          <button type="submit" class="add-btn">添加</button>
        </form>
      </div>
    </div>

    <script>
      let currentFeedId = null;
      let currentStoryIndex = 0;
      let stories = [];
      let currentStoryType = 'own';
      let currentUserId = null;

      function navigateStory(direction) {
        const currentStories = currentStoryType === 'own' ?
          stories.own :
          stories.following.filter(s => s.UID === currentUserId);

        const newIndex = currentStoryIndex + direction;
        if (newIndex >= 0 && newIndex < currentStories.length) {
          currentStoryIndex = newIndex;
          displayStory(currentStoryIndex);
          updateStoryNavigation();
        }
      }

      function displayStory(index) {
        const currentStories = currentStoryType === 'own' ?
          stories.own :
          stories.following.filter(s => s.UID === currentUserId);

        if (!currentStories || currentStories.length === 0) {
          if (currentStoryType === 'own') {
            const addStoryButton = document.querySelector('.add-story');
            if (addStoryButton) addStoryButton.style.display = 'block';
            const storiesBackground = document.querySelector('.stories-background');
            if (storiesBackground) {
              storiesBackground.style.backgroundImage = 'none';
            }
          }
          return;
        }

        const story = currentStories[index];
        const storiesBackground = document.querySelector('.stories-background');
        const profilePic = document.querySelector('.stories-content .profile-pic');
        const username = document.querySelector('.stories-content .username');
        const timestamp = document.querySelector('.stories-content .timestamp');

        if (storiesBackground) {
          const imagePath = story.img || story.image_path;
          storiesBackground.style.backgroundImage = `url('${imagePath}')`;
          storiesBackground.style.backgroundSize = 'contain';
          storiesBackground.style.backgroundPosition = 'center';
          storiesBackground.style.backgroundRepeat = 'no-repeat';
        }
        if (profilePic) profilePic.src = story.profilePic || 'picture/unknown.jpeg';
        if (username) username.textContent = story.userName;
        if (timestamp) timestamp.textContent = formatRelativeTime(story.upload_time || story.postTime);

        const addStoryButton = document.querySelector('.add-story');
        if (addStoryButton) {
          addStoryButton.style.display = currentStoryType === 'own' ? 'block' : 'none';
        }
      }

      function updateStoryNavigation() {
        const currentStories = currentStoryType === 'own' ?
          stories.own :
          stories.following.filter(s => s.UID === currentUserId);

        const prevButton = document.querySelector('.story-nav-prev');
        const nextButton = document.querySelector('.story-nav-next');

        if (currentStories && currentStories.length > 1) {
          if (prevButton) prevButton.style.display = currentStoryIndex > 0 ? 'block' : 'none';
          if (nextButton) nextButton.style.display = currentStoryIndex < currentStories.length - 1 ? 'block' : 'none';
        } else {
          if (prevButton) prevButton.style.display = 'none';
          if (nextButton) nextButton.style.display = 'none';
        }
      }

      document.addEventListener("DOMContentLoaded", function () {
        function uploadPhoto(event) {
          const file = event.target.files[0];
          const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];

          if (file && allowedTypes.includes(file.type)) {
            const formData = new FormData();
            formData.append('story_image', file);

            fetch('upload_story.php', {
              method: 'POST',
              body: formData
            })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  alert('Story uploaded successfully!');
                  loadStories();
                } else {
                  alert('Failed to upload story: ' + data.error);
                }
              })
              .catch(error => {
                console.error('Error:', error);
                alert('Error uploading story');
              });
          } else {
            alert("请上传有效的图像文件（JPEG、PNG、GIF、WEBP 或 BMP）。");
          }
        }

        function showStories(type, userId = null) {
          currentStoryType = type;
          currentUserId = userId;
          currentStoryIndex = 0;

          document.getElementById("stories-banner").style.display = "flex";

          const addStoryButton = document.querySelector('.add-story');
          if (addStoryButton) {
            addStoryButton.style.display = type === 'own' ? 'block' : 'none';
          }

          if (type === 'own') {
            if (stories.own.length > 0) {
              displayStory(0);
            } else {
              const storiesBackground = document.querySelector('.stories-background');
              if (storiesBackground) {
                storiesBackground.style.backgroundImage = 'none';
              }
              if (addStoryButton) {
                addStoryButton.style.display = 'block';
              }
            }
          } else if (type === 'following' && userId) {
            const userStories = stories.following.filter(s => s.UID === userId);
            if (userStories.length > 0) {
              displayStory(0);
            }
          }

          updateStoryNavigation();
        }

        function hideStories() {
          document.getElementById("stories-banner").style.display = "none";
        }

        async function loadStories() {
          try {
            const response = await fetch('get_stories.php');
            const data = await response.json();

            if (data.success) {
              stories = {
                own: data.own_stories || [],
                following: data.following_stories || []
              };

              populateFollowingStories();

              if (document.getElementById('stories-banner').style.display === 'flex') {
                if (currentStoryType === 'own') {
                  if (stories.own.length > 0) {
                    displayStory(currentStoryIndex);
                  }
                } else if (currentStoryType === 'following' && currentUserId) {
                  displayStory(currentStoryIndex);
                }
                updateStoryNavigation();
              }
            }
          } catch (error) {
            console.error('Error loading stories:', error);
          }
        }

        function populateFollowingStories() {
          const followingStoriesContainer = document.getElementById('followingStories');
          followingStoriesContainer.innerHTML = '';

          const uniqueUsers = new Set();
          stories.following.forEach(story => {
            if (!uniqueUsers.has(story.UID)) {
              uniqueUsers.add(story.UID);
              const container = document.createElement('div');
              container.className = 'story-avatar-container';

              const avatar = document.createElement('img');
              avatar.src = story.profilePic || 'picture/unknown.jpeg';
              avatar.className = 'story-avatar';
              avatar.onclick = () => showStories('following', story.UID);

              container.appendChild(avatar);
              followingStoriesContainer.appendChild(container);
            }
          });
        }

        window.uploadPhoto = uploadPhoto;
        window.showStories = showStories;
        window.hideStories = hideStories;
        window.navigateStory = navigateStory;

        loadStories();
      });

      function openPopup() {
        const fileInput = document.getElementById('file-input2');
        fileInput.value = '';

        const imgElement = document.querySelector(".file-label img");
        imgElement.src = 'picture/add.png';
        imgElement.removeAttribute('data-imageSrc');

        document.getElementById("photo-title").value = '';

        document.getElementById("popup-banner").style.display = "flex";
      }

      function closePopup() {
        const fileInput = document.getElementById('file-input2');
        fileInput.value = '';

        const imgElement = document.querySelector(".file-label img");
        imgElement.src = 'picture/add.png';
        imgElement.removeAttribute('data-imageSrc');

        document.getElementById("photo-title").value = '';

        document.getElementById("popup-banner").style.display = "none";
      }

      document.querySelector(".btn").addEventListener("click", openPopup);

      function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function (e) {
            const imgElement = document.querySelector(".file-label img");
            imgElement.src = e.target.result;
            imgElement.dataset.imageSrc = e.target.result;

            localStorage.setItem("imageData", e.target.result);
          };
          reader.readAsDataURL(file);
        }
      }

      document.addEventListener("DOMContentLoaded", function () {
        const deleteIcon = document.getElementById("deleteIcon");
        if (deleteIcon) {
          deleteIcon.addEventListener("click", deletePost);
        }
      });

      function deletePost(event) {
        event.stopPropagation();
        const button = event.target;
        const postId = button.getAttribute("data-fid");

        if (!postId) {
          console.error("Post ID is undefined!");
          return;
        }

        if (confirm("Are you sure you want to delete this post?")) {
          fetch("delete_post.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "postID=" + encodeURIComponent(postId)
          })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                const postContainer = button.closest(".grid-item");
                if (postContainer) postContainer.remove();
              } else {
                alert("Failed to delete post: " + (data.error || "Unknown error"));
              }
            })
            .catch(error => console.error("Error:", error));
        }
      }

      async function maximizeFeed(element, feedId) {
        const imgSrc = element.querySelector('img').src;
        const overlay = document.getElementById('overlay');
        const maximizedImage = document.getElementById('maximizedImage');
        const postTime = element.getAttribute('data-time');

        maximizedImage.src = imgSrc;
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        const profilePic = element.querySelector('.post-img img').src;
        const username = element.querySelector('.post-profile h3').textContent;
        document.querySelector('.maximized-feed .profile-picture').src = profilePic;
        document.querySelector('.maximized-feed .name').textContent = username;

        try {
          const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=getFullFname&fid=${feedId}`
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const result = await response.json();
          if (result.success) {
            document.getElementById('maximizedFname').textContent = result.Fname;
          }
        } catch (error) {
          console.error('Error fetching full Fname:', error);
          document.getElementById('maximizedFname').textContent = 'Error loading title';
        }

        const relativeTime = formatRelativeTime(postTime);
        document.querySelector('.maximized-feed .date').textContent = relativeTime;

        currentFeedId = feedId;

        await fetchLikeCount(feedId);

        loadComments(feedId);
      }

      async function fetchLikeCount(feedId) {
        try {
          const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=getLikeCount&fid=${feedId}`
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const result = await response.json();
          if (result.success) {
            const likeCountElement = document.getElementById('likeCount');
            likeCountElement.textContent = result.likes + "    喜欢";
          }
        } catch (error) {
          console.error('Error fetching like count:', error);
        }
      }

      function closeOverlay() {
        const overlay = document.getElementById('overlay');
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
      }

      async function loadComments(feedId) {
        try {
          const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=getComments&fid=${feedId}`
          });

          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }

          const data = await response.json();
          const commentsList = document.getElementById('commentsList');
          commentsList.innerHTML = '';

          if (data.length === 0) {
            commentsList.innerHTML = '<p>还没有评论。成为第一个发表评论的人！</p>';
            return;
          }

          data.forEach(comment => {
            const commentDiv = document.createElement('div');
            commentDiv.className = 'comment';
            commentDiv.innerHTML = `
                <div class="commenter-info">
                  <div class="comment-avatar-container">
                      <img src="${comment.profilePic || 'picture/unknown.jpeg'}" 
                          class="comment-avatar" 
                          alt="Profile Picture">
                  </div>
                  <span class="commenter-name">${comment.userName}</span>
                  <span class="comment-time">${formatRelativeTime(comment.time)}</span>
              </div>
              <p class="comment-message">${comment.message}</p>
              <button class="comment-like" onclick="likeComment(${comment.comID}, this)">
                  <img src="${comment.isLiked ? 'picture/like2.png' : 'picture/like1.png'}" alt="like">
                  <span>${comment.likes}</span>
              </button>`;
            commentsList.appendChild(commentDiv);
          });
        } catch (error) {
          console.error('Error loading comments:', error);
          const commentsList = document.getElementById('commentsList');
          commentsList.innerHTML = '<p>加载评论时出错。请再试一次。</p>';
        }
      }

      async function likeComment(comID, button) {
        try {
          const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=likeComment&comID=${comID}`
          });

          const result = await response.json();
          if (result.success) {
            const img = button.querySelector('img');
            const countSpan = button.querySelector('span');
            img.src = result.liked ? 'picture/like2.png' : 'picture/like1.png';
            countSpan.textContent = result.likes;
          }
        } catch (error) {
          console.error('Error liking comment:', error);
        }
      }

      async function toggleLike() {
        try {
          const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=likePost&fid=${currentFeedId}`
          });

          const result = await response.json();
          if (result.success) {
            const like1 = document.getElementById('like1');
            const like2 = document.getElementById('like2');
            const likeCount = document.getElementById('likeCount');

            like1.style.display = result.liked ? 'none' : 'block';
            like2.style.display = result.liked ? 'block' : 'none';
            likeCount.textContent = result.likes;
          }
        } catch (error) {
          console.error('Error liking post:', error);
        }
      }

      function handleCommentInput(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
          event.preventDefault();
          addComment();
        }
      }

      async function addComment() {
        const commentText = document.getElementById('commentInput').value.trim();
        if (!commentText) return;

        try {
          const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=addComment&fid=${currentFeedId}&message=${encodeURIComponent(commentText)}`
          });

          const data = await response.json();
          if (data.success) {
            document.getElementById('commentInput').value = '';
            loadComments(currentFeedId);
          }
        } catch (error) {
          console.error('错误加载评论。', error);
        }
      }

      function formatRelativeTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;

        const minute = 60 * 1000;
        const hour = minute * 60;
        const day = hour * 24;
        const month = day * 30;
        const year = day * 365;

        if (diff < minute) {
          return '刚才';
        } else if (diff < hour) {
          const minutes = Math.floor(diff / minute);
          return `${minutes} 分钟${minutes > 1 ? '' : ''} 前`;
        } else if (diff < day) {
          const hours = Math.floor(diff / hour);
          return `${hours} 小时${hours > 1 ? '' : ''} 前`;
        } else if (diff < month) {
          const days = Math.floor(diff / day);
          return `${days} 天${days > 1 ? '' : ''} 前`;
        } else if (diff < year) {
          const months = Math.floor(diff / month);
          return `${months} 月${months > 1 ? '' : ''} 前`;
        } else {
          const years = Math.floor(diff / year);
          return `${years} 年${years > 1 ? '' : ''} 前`;
        }
      }

      document.getElementById("searchBar").addEventListener("input", function () {
        const searchValue = this.value.toLowerCase().trim(); 
        const posts = document.querySelectorAll('.grid-item');
        const noResult = document.getElementById('no-result');
        let hasResults = false;

        if (searchValue === "") {
            posts.forEach(post => {
                post.style.display = 'flex'; 
            });
            if (noResult) {
                noResult.style.display = 'none'; 
            }
            return; 
        }

        posts.forEach(post => {
            const postName = post.getAttribute('data-fname').toLowerCase();
            if (postName.includes(searchValue)) {
                post.style.display = 'flex'; 
                hasResults = true;
            } else {
                post.style.display = 'none'; 
            }
        });

        if (noResult) {
            noResult.style.display = hasResults ? 'none' : 'block';
        }
    });

      const badWords = [
        'arse', 'arsehead', 'arsehole', 'ass', 'asshole',
        'bastard', 'bitch', 'bloody', 'bollocks', 'brotherfucker', 'bugger', 'bullshit',
        'childfucker', 'christonabike', 'christonacracker', 'cock', 'cocksucker', 'crap', 'cunt',
        'dammit', 'damn', 'damned', 'damnit', 'dick', 'dickhead', 'dumbass', 'dyke',
        'faggot', 'fatherfucker', 'fuck', 'fucker', 'fucking',
        'goddammit', 'goddamn', 'goddamned', 'goddamnit', 'godsdamn',
        'hell', 'holyshit', 'horseshit',
        'inshit',
        'jackarse', 'jackass', 'jesuschrist', 'jesusfuck', 'jesusharoldchrist', 'jesushchrist', 'jesusmaryandjoseph', 'jesuswept',
        'kike',
        'motherfucker',
        'nigga', 'nigra',
        'pigfucker', 'piss', 'prick', 'pussy',
        'shit', 'shitass', 'shite', 'siblingfucker', 'sisterfuck', 'sisterfucker', 'slut', 'sonofabitch', 'sonofawhore', 'spastic', 'sweetjesus',
        'twat',
        'wanker'
      ];

      function cleanText(text) {
        // Remove all special characters except letters and spaces
        return text.replace(/[^a-zA-Z\s]/g, '').replace(/\s+/g, ' ').trim();
      }

      function validateTitle(event) {
        const titleInput = document.getElementById('photo-title');
        const titleError = document.getElementById('title-error');
        let title = titleInput.value.toLowerCase().trim();

        // Clean the input text to remove special characters
        title = cleanText(title);

        // Create a regex pattern to detect bad words even when spaced out
        const badWordsPattern = new RegExp(`\\b(${badWords.join('|').split('').join('\\s*')})\\b`, 'gi');

        if (badWordsPattern.test(title)) {
          event.preventDefault();
          titleError.textContent = '标题包含不当内容。请修改您的标题。';
          titleError.style.display = 'block';
          return false;
        }

        titleError.style.display = 'none';
        return true;
      }

      document.getElementById('photo-title').addEventListener('input', function () {
        const titleError = document.getElementById('title-error');
        let title = this.value.toLowerCase().trim();

        // Clean the input text to remove special characters
        title = cleanText(title);

        const badWordsPattern = new RegExp(`\\b(${badWords.join('|').split('').join('\\s*')})\\b`, 'gi');

        if (badWordsPattern.test(title)) {
          titleError.textContent = '标题包含不当内容。请修改您的标题。';
          titleError.style.display = 'block';
        } else {
          titleError.style.display = 'none';
        }
      });
    </script>
</body>

</html>