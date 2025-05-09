<?php include("getName.php");?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sidebar</title>
  <link rel="stylesheet" href="sidebarStyle.css">
</head>

<body>
  <div class="sidebar">
    <a href="main.php" class="logo">
      <img src="picture/logo.png" alt="logo" />
    </a>
    <div class="profile">
      <div class="profile-img">
        <img src="<?php echo !empty($profilePic) ? $profilePic : 'picture/unknown.jpeg'; ?>" alt="Profile Picture" />
      </div>
      <div class="name">
        <h1><?php echo htmlspecialchars($user_name); ?></h1>
        <img src="picture/verified.png" alt="verify" />
      </div>
      <span class="email">--<?php echo htmlspecialchars($emailAddress) ?>--</span>
    </div>
    <div class="about">
      <div class="box">
        <h3><?php echo $_SESSION["total_posts"]?></h3>
        <span>Posts</span>
      </div>
      <div class="box">
        <h3><?php echo $_SESSION["follower"] ?></h3>
        <span>Followers</span>
      </div>
      <div class="box">
        <h3><?php echo $_SESSION["following"] ?></h3>
        <span>Followings</span>
      </div>
    </div>
    <div class="menu">
      <a href="main.php">
        <span class="icon">
          <img src="picture/home.png" width="30" height="30">
        </span>
        Homepage
      </a>

      <a href="notification.php">
        <span class="icon">
          <img src="picture/notification.png" width="30" height="30">
        </span>
        Notification
        <span id="num-of-notif2"></span>
      </a>

      <a href="explore.php">
        <span class="icon">
          <img src="picture/search.png" width="30" height="30">
        </span>
        Explore
      </a>

      <a href="message.php">
        <span class="icon">
          <img src="picture/messenger.png" width="30" height="30">
        </span>
        Message
      </a>

      <a href="announcement.php">
        <span class="icon">
          <img src="picture/announcement.png" width="30" height="30">
        </span>
        Announcement
      </a>

      <a href="friend.php">
        <span class="icon">
          <img src="picture/friends.png" width="30" height="30">
        </span>
        Friends
      </a>

      <a href="_Settings.php">
        <span class="icon">
          <img src="picture/settings.png" width="30" height="30">
        </span>
        Settings
      </a>

      <a href="logout.php">
        <span class="icon">
          <img src="picture/exit.png" width="30" height="30">
        </span>
        Logout
      </a>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      fetch('fetch_count.php')
        .then(response => response.json())
        .then(data => {
          let notifBadge = document.getElementById("num-of-notif2");
          let count = data.unread_count;

          if (count > 0) {
            notifBadge.textContent = count;
            notifBadge.style.display = "flex";
          } else {
            notifBadge.style.display = "none";
          }
        })
        .catch(error => console.error('Error fetching unread count:', error));
    });

    document.addEventListener('DOMContentLoaded', (event) => {
            const theme = localStorage.getItem('theme') || 'default';
            if (theme === 'dark') {
                document.body.classList.add('dark');
            }
    });
  </script>

  <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,200;0,400;0,500;0,700;0,800;1,100;1,200;1,300;1,600;1,800&display=swap");

    :root {
      --gradient: linear-gradient(to right, #8c52ff, #5de0e6);
      --light-bg: #f8f8f8;
      --dark-bg:rgb(33, 33, 33);
      --text-light: #000;
      --text-dark: #e1e1e1;
      --highlight-color: #e2336b;
      --notif-bg: red;
    }

    * {
      font-family: "Poppins", sans-serif;
      margin: 0;
      padding: 0px;
      box-sizing: border-box;
      list-style: none;
      text-decoration: none;
    }

    .sidebar {
      display: flex;
      flex-direction: column;
      position: fixed;
      width: 300px;
      height: 100vh;
      background: var(--light-bg);
      z-index: 2;
    }

    .dark .sidebar {
      background: var(--dark-bg);
    }

    .logo img {
      width: 70px;
    }

    .profile {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      margin-top: 1.4rem;
    }

    .profile-img {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 80px;
      height: 80px;
      border-radius: 50%;
      border: 2px solid transparent;
      background-image: var(--gradient);
      background-origin: border-box;
      background-clip: content-box, border-box;
    }

    .profile-img img {
      width: 70px;
      height: 70px;
      object-fit: cover;
      border-radius: 50%;
      object-position: center;
    }

    .name {
      display: flex;
      align-items: center;
      color: var(--text-light);
    }

    .dark .name {
      color: var(--text-dark);
    }

    .name h1 {
      font-size: 1.1rem;
    }

    .name img {
      margin-left: 4px;
      width: 20px;
      object-fit: center;
    }

    .profile-img span {
      font-size: 0.938rem;
      font-weight: 400;
      color: var(--text-light);
    }

    .dark .profile-img span {
      color: var(--text-dark);
    }

    .about {
      display: flex;
      justify-content: space-between;
      margin-top: 1rem;
      color: var(--text-light);
    }

    .dark .about {
      color: var(--text-dark);
    }

    .box {
      text-align: center;
    }

    .box h3 {
      font-size: 1rem;
      font-weight: 500;
    }

    .box span {
      font-size: 0.938rem;
      font-weight: 400;
    }

    .menu a {
      width: 100%;
      font-size: 1rem;
      color: var(--text-light);
      display: flex;
      align-items: center;
      line-height: 40px;
    }

    .dark .menu a {
      color: var(--text-dark);
    }

    .menu a:hover,
    .menu .active {
      color: var(--highlight-color);
    }

    .menu .icon {
      margin-right: 1rem;
      font-size: 20px;
      margin-top: 7px;
    }

    .menu a:hover::before,
    .menu .active::before {
      content: "";
      position: absolute;
      right: 0;
      width: 2px;
      height: 17px;
      background: var(--highlight-color);
    }

    .main-home {
      padding: 20px;
      overflow: hidden;
      color: var(--text-light);
    }

    .dark .main-home {
      color: var(--text-dark);
    }

    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .header-content {
      display: flex;
      align-items: center;
    }

    .header-content i {
      color: var(--text-light);
      font-size: 20px;
      margin-left: 1rem;
    }

    .dark .header-content i {
      color: var(--text-dark);
    }

    .header-content .btn {
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 8px;
      background: var(--gradient);
      margin-left: 1rem;
    }

    .header-content .btn i {
      color: hsla(0, 0%, 100%, 0.6);
      margin-left: 10px;
    }

    .btn text {
      color: #fff;
    }

    #num-of-notif2 {
      background-color: var(--notif-bg);
      color: #fff;
      display: none;
      /* Initially hidden */
      align-items: center;
      justify-content: center;
      font-weight: 700;
      width: 30px;
      height: 30px;
      border-radius: 0.5rem;
      margin-left: 10px;
    }

    .dark #num-of-notif2 {
      color: var(--text-dark);
    }
    
    .dark .email {
      color: var(--text-dark);
    }

  </style>