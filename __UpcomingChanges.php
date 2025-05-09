<?php include 'getName.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebarStyle.css">
    <title>Upcoming Changes</title>
    <style>
        .content {
            margin-left: 300px;
            height: 100vh;
        }

        .back {
            margin-top: 10px;
        }

        #title {
            display: flex;
            align-items: center;
            width: 100%;
        }

        #title h1 {
            margin-left: 10px;
        }

        .container {
            margin-top: 10px;
            padding: 20px;
            border: 2px solid #ccc;
            border-radius: 10px;
            width: 100%;
            background-color: #f9f9f9;
            position: relative;
            height: 90%;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .input-group textarea {
            height: 290px;
        }

        .radio-container {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #e9e9e9;
        }

        .radio-container label {
            display: block;
            font-weight: normal;
            position: relative;
            padding-left: 30px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .radio-container input[type="radio"] {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        .next-button {
            position: absolute;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .next-button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <a href="#" class="logo">
            <img src="picture/logo.png" alt="logo" />
        </a>
        <!--profile Image -->
        <div class="profile">
            <div class="profile-img">
                <img src="picture/unknown.jpeg" alt="profile" />
            </div>
            <div class="name">
                <h1>Admin</h1>
            </div>
        </div>
        <!-- Menu -->
        <div class="menu">
            <a href="__UserManagement.php">
                <span class="icon">
                    <img src="picture/usermanagement.png" width="30" height="30">
                </span>
                User Management
            </a>

            <a href="__ContentManagement.php">
                <span class="icon">
                    <img src="picture/contentmanagement.png" width="30" height="30">
                </span>
                Content Management
            </a>

            <a href="__UpcomingChanges.php">
                <span class="icon">
                    <img src="picture/upcomingupdate.png" width="30" height="30">
                </span>
                Upcoming Update
            </a>

            <a href="__UserActivity.php">
                <span class="icon">
                    <img src="picture/useractivity.png" width="30" height="30">
                </span>
                User Activity
            </a>

            <a href="__SystemPerformance.php">
                <span class="icon">
                    <img src="picture/performance.png" width="30" height="30">
                </span>
                System Performance
            </a>

            <a href="logout.php">
                <span class="icon">
                    <img src="picture/exit.png" width="30" height="30">
                </span>
                Logout
            </a>
        </div>
    </div>

    <div class="content">
        <div id="title">
            <h1>Upcoming Changes</h1>
        </div>
        <div class="container">
            <form method="POST" action="save_changes.php" onsubmit="return validateForm()">
                <div class="input-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" placeholder="Title" required>
                </div>
                <div class="input-group">
                    <label>Type of Update</label>
                    <div class="radio-container">
                        <label><input type="radio" name="updateType" value="Platform Configure and Settings"> Platform
                            Configure and Settings</label>
                        <label><input type="radio" name="updateType" value="System Configure"> System Configure</label>
                        <label><input type="radio" name="updateType" value="Privacy, Security and Compliance"> Privacy,
                            Security and Compliance</label>
                    </div>
                </div>
                <div class="input-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Description" required
                        style="resize: none;"></textarea>
                </div>
                <button type="submit" class="next-button">Next</button>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            var radios = document.getElementsByName("updateType");
            var formValid = false;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    formValid = true;
                    break;
                }
            }

            if (!formValid) {
                alert("Please select a Type of Update");
                return false;
            }
            return true;
        }
    </script>
</body>

</html>