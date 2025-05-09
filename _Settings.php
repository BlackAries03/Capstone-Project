<?php include 'getName.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="sidebarStyle.css">
  <style>
    .layout {
      display: flex;
      height: 100vh;
      background: antiquewhite;
      /* Background color */
    }

    .content {
      margin-left: 300px;
      height: 100vh;
      width: 100vw;
      background: antiquewhite;
    }

    #title {
      display: flex;
      opacity: 1;
    }

    .container {
      height: auto;
      width: 90%;
      margin-top: 20px;
      margin-left: auto;
      margin-right: auto;
      background: white;
      padding: 60px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 1rem;
    }

    .options {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    #option {
      width: 100%;
      height: 80px;
      background: #989797;
      opacity: 0.8;
      position: relative;
      border-radius: 1rem;
    }

    #option:hover {
      opacity: 1;
    }

    #option h1 {
      color: black;
      padding-top: 20px;
      display: inline-block;
      /* To align title inline */
      margin-left: 10px;
      /* Adjust margin for spacing */
    }

    #option img {
      height: auto;
      width: 80px;
      display: inline-block;
    }

    #dropDownMenu {
      position: absolute;
      top: 50%;
      right: 30px;
      transform: translateY(-50%);
      list-style: none;
      z-index: 1;
    }

    #dropDownMenu ul {
      display: none;
      background: #ffffff;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 10px;
      border-radius: 4px;
    }

    #dropDownMenu ul li {
      padding: 5px 10px;
      cursor: pointer;
    }

    #dropDownMenu ul li a {
      text-decoration: none;
      color: black;
    }

    #dropDownMenu ul li:hover {
      background-color: #e2e6ea;
    }

    #option:hover #dropDownMenu ul {
      display: block;
    }

    .back {
      margin-top: 10px;
      margin-right: 5px;
    }

    #dropDownMenu2 {
      position: absolute;
      top: 50%;
      right: 30px;
      transform: translateY(-50%);
      list-style: none;
      z-index: 1;
    }

    #dropDownMenu2 ul {
      display: none;
      background: #ffffff;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 10px;
      border-radius: 4px;
    }

    #dropDownMenu2 ul li {
      padding: 5px 10px;
      cursor: pointer;
      border-bottom: 1px solid #ccc;
    }

    #dropDownMenu2 ul li:last-child {
      border-bottom: none;
    }

    #dropDownMenu2 ul li a {
      text-decoration: none;
      color: black;
    }

    #dropDownMenu2 ul li:hover {
      background-color: #e2e6ea;
    }

    #option:hover #dropDownMenu2 ul {
      display: block;
    }
    
  </style>
  <title>Settings</title>
</head>

<body>
  <div class="layout">
  <?php include("sidebar.php"); ?>
    <!-- settings -->
    <div class="content">
      <div id="title">
        <a href="main.php"><img src="picture\back-button.png" alt="back" class="back"
            style="width: 30px; height: 30px;">
        </a>
        <h1>Settings</h1>
      </div>
      <div class="container">
        <div class="options">
          <a href="_accountManagement.php">
            <div id="option">
              <div id="title" style="padding-left: 30px;">
                <img src="picture\images-removebg-preview.png" alt="account" style="height: auto; width: 80px;">
                <h1>ACCOUNT MANAGEMENT</h1>
              </div>
            </div>
          </a>

          <div id="option">
            <div id="title" style="padding-left: 30px;">
                <img src="picture/2531277-removebg-preview (1).png" alt="lang">
                <h1>LANGUAGE</h1>
            </div>
            <div id="dropDownMenu">
                <ul>
                    <li><a href="_Settings.php" id="lang-eng"><img src="picture/images-removebg-preview (2).png" alt="eng" style="height: auto; width: 30px;">ENG</a></li>
                    <li><a href="_Settings(cn).php" id="lang-cn"><img src="picture/download-removebg-preview (2).png" alt="cn" style="height: auto; width: 30px;">CN</a></li>
                    <li><a href="_Settings(bm).php" id="lang-bm"><img src="picture/download-removebg-preview (3).png" alt="bm" style="height: auto; width: 40px;">BM</a></li>
                </ul>
            </div>
        </div>

          <a href="_privacyNPolicy.php">
            <div id="option">
              <div id="title" style="padding-left: 30px;">
                <img src="picture\pngtree-protection-line-icon-vector-png-image_6690997-removebg-preview.png" alt="P&P">
                <h1>PRIVACY AND POLICY</h1>
              </div>
            </div>
          </a>
          <a href="_aboutUs.php">
            <div id="option">
              <div id="title" style="padding-left: 30px;">
                <img src="picture\download-removebg-preview (1).png" alt="aboutUs">
                <h1>ABOUT US</h1>
              </div>
            </div>
          </a>
          <a href="_Help.php">
            <div id="option">
              <div id="title" style="padding-left: 30px;">
                <img src="picture\images-removebg-preview (1).png" alt="help">
                <h1>HELP</h1>
              </div>
            </div>
          </a>
          <div id="option">
            <div id="title" style="padding-left: 30px;">
                <img src="picture/theme.png" alt="lang">
                <h1>THEME</h1>
            </div>
            <div id="dropDownMenu2">
                <ul>
                    <li id="theme-default" onclick="setTheme('default')">Bright</li>
                    <li id="theme-dark" onclick="setTheme('dark')">Dark</li>
                </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

<script>
    document.getElementById('lang-cn').addEventListener('click', function(event) {
        event.preventDefault();
        let currentURL = window.location.href;
        if (!currentURL.includes('(cn).php')) {
            let newURL = currentURL.replace(/(\.php)$/, '(cn)$1').replace(/\(bm\)/, '').replace(/\/$/, '');
            window.location.href = newURL;
        }
    });

    document.getElementById('lang-eng').addEventListener('click', function(event) {
        event.preventDefault();
        let currentURL = window.location.href;
        let newURL = currentURL.replace(/\(cn\)/, '').replace(/\(bm\)/, '');
        window.location.href = newURL;
    });

    document.getElementById('lang-bm').addEventListener('click', function(event) {
        event.preventDefault();
        let currentURL = window.location.href;
        if (!currentURL.includes('(bm).php')) {
            let newURL = currentURL.replace(/(\.php)$/, '(bm)$1').replace(/\(cn\)/, '').replace(/\/$/, '');
            window.location.href = newURL;
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
            fetch('fetch_count.php')
                .then(response => response.json())
                .then(data => {
                    let notifBadge = document.getElementById("num-of-notif2");
                    let count = data.unread_count;

                    if (count > 0) {
                        notifBadge.textContent = count; // Set count number
                        notifBadge.style.display = "flex"; // Show badge
                    } else {
                        notifBadge.style.display = "none"; // Hide badge if zero
                    }
                })
                .catch(error => console.error('Error fetching unread count:', error));
        });
    document.addEventListener('DOMContentLoaded', (event) => {
        const theme = localStorage.getItem('theme');
        if (theme) {
            setTheme(theme);
        }
    });

    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark');
        } else {
            document.body.classList.remove('dark');
        }
        localStorage.setItem('theme', theme);
    }
</script>

<style>
.dark .layout {
  display: flex;
  height: 100vh;
  background: #2e2e2e;
}

.dark .content {
  margin-left: 300px;
  height: 100vh;
  width: 100vw;
  background: #2e2e2e;
}

.dark #title {
  display: flex;
  opacity: 1;
}

.dark #title h1 {
  color: #e1e1e1;
}

.dark .container {
  height: auto;
  width: 90%;
  margin-top: 20px;
  margin-left: auto;
  margin-right: auto;
  background: #3c3c3c;
  padding: 60px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
  border-radius: 1rem;
  color: #e1e1e1;
}

.dark .options {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.dark #option {
  width: 100%;
  height: 80px;
  background: #4c4c4c;
  opacity: 0.8;
  position: relative;
  border-radius: 1rem;
}

.dark #option:hover {
  opacity: 1;
}

.dark #option h1 {
  color: #e1e1e1;
  padding-top: 20px;
  display: inline-block;
  margin-left: 10px;
}

.dark #option img {
  height: auto;
  width: 80px;
  display: inline-block;
}

.dark #dropDownMenu,
.dark #dropDownMenu2 {
  position: absolute;
  top: 50%;
  right: 30px;
  transform: translateY(-50%);
  list-style: none;
  z-index: 1;
}

.dark #dropDownMenu ul,
.dark #dropDownMenu2 ul {
  display: none;
  background: #4c4c4c;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
  padding: 10px;
  border-radius: 4px;
}

.dark #dropDownMenu ul li,
.dark #dropDownMenu2 ul li {
  padding: 5px 10px;
  cursor: pointer;
}

.dark #dropDownMenu ul li a,
.dark #dropDownMenu2 ul li a {
  text-decoration: none;
  color: #e1e1e1;
}

.dark #dropDownMenu ul li:hover,
.dark #dropDownMenu2 ul li:hover {
  background-color: #5c5c5c;
}

.dark #option:hover #dropDownMenu ul,
.dark #option:hover #dropDownMenu2 ul {
  display: block;
}

</style>

