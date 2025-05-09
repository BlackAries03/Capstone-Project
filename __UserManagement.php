<?php include 'getName.php'; ?>
<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT UID, userName FROM udata";
$result = $conn->query($sql);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebarStyle.css">
    <title>User Management</title>
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

        .search-bar {
            margin-top: 30px;
            display: flex;
            justify-content: left;
        }

        .search-bar input {
            border-radius: 20px;
            border: 1px solid #ccc;
            padding: 5px 10px;
            width: 400px;
        }

        .refresh {
            display: flex;
            justify-content: right;
            margin-top: -30px;
        }

        .table-container {
            margin-top: 30px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table th,
        table td {
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .table-container img {
            margin-left: 40px;
            cursor: pointer;
        }

        .table-container img[src="picture/assignrole.png"] {
            margin-left: 5px;
        }

        .banned {
            color: red;
            font-weight: bold;
            margin-left: 10px;
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
            <h1>User Management</h1>
        </div>

        <div class="search-bar">
            <input type="text" id="search" placeholder="Search..." onkeyup="filterTable()">
        </div>

        <div class="refresh">
            <img src="picture/refresh.png" width="30" height="30" id="refresh-icon">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>UID</th>
                        <th>Username</th>
                        <th>Banned User</th>
                        <th>Unbanned User</th>
                        <th>Role</th>
                        <th>Delete User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT udata.UID, udata.userName, udata.role, banneduser.UID AS BID FROM udata LEFT JOIN banneduser ON udata.UID = banneduser.UID";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["UID"] . "</td>";
                            echo "<td>" . $row["userName"] . " <span class='banned' id='banned-" . $row["UID"] . "' ";
                            if (!empty($row["BID"])) {
                                echo "style='display:inline; color:red;' ";
                            } else {
                                echo "style='display:none;' ";
                            }
                            echo ">banned</span></td>";
                            echo '<td><img src="picture/block.png" width="30" height="30" class="block-user" data-uid="' . $row["UID"] . '" data-username="' . $row["userName"] . '"></td>';
                            echo '<td><img src="picture/unblock.png" width="30" height="30" class="unblock-user" data-uid="' . $row["UID"] . '" data-username="' . $row["userName"] . '"></td>';
                            echo '<td id="role-' . $row["UID"] . '"><img src="picture/assignrole.png" width="30" height="30" class="assign-role" data-uid="' . $row["UID"] . '">';
                            if ($row["role"] === "Admin") {
                                echo " Admin";
                            }
                            echo '</td>';
                            echo '<td><img src="picture/deleteuser.png" width="30" height="30" class="delete-user" data-uid="' . $row["UID"] . '" data-username="' . $row["userName"] . '"></td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No users found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</body>

</html>

<script>
    function filterTable() {
        const searchInput = document.getElementById('search').value.toLowerCase();
        const table = document.querySelector('.table-container table tbody');
        const rows = table.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const uidCell = rows[i].getElementsByTagName('td')[0]; // UID column
            const usernameCell = rows[i].getElementsByTagName('td')[1]; // Username column

            if (uidCell && usernameCell) {
                const uid = uidCell.textContent.toLowerCase();
                const username = usernameCell.textContent.toLowerCase();

                // Show rows where search input matches UID or Username
                rows[i].style.display = uid.includes(searchInput) || username.includes(searchInput) ? '' : 'none';
            }
        }
    }

    document.getElementById('refresh-icon').addEventListener('click', function () {
        // Reload the page
        location.reload();
    });


    document.addEventListener('DOMContentLoaded', function () {
        // Block user
        document.querySelectorAll('.block-user').forEach(function (button) {
            button.addEventListener('click', function () {
                const uid = this.getAttribute('data-uid');
                const username = this.getAttribute('data-username');

                // Send AJAX request to server
                fetch('ban_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ uid: uid, username: username })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(username + ' has been banned.');
                            // Show the banned label
                            document.getElementById('banned-' + uid).style.display = 'inline';
                            // Disable block button
                            this.style.pointerEvents = 'none';
                        } else {
                            alert('Failed to ban user. ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Unblock user
        document.querySelectorAll('.unblock-user').forEach(function (button) {
            button.addEventListener('click', function () {
                const uid = this.getAttribute('data-uid');
                const username = this.getAttribute('data-username');
                const bannedLabel = document.getElementById('banned-' + uid);

                // Send AJAX request to server
                fetch('unban_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ uid: uid, username: username })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(username + ' has been unbanned.');
                            // Hide the banned label
                            bannedLabel.style.display = 'none';
                            // Enable block button
                            document.querySelector(`[data-uid='${uid}'].block-user`).style.pointerEvents = 'auto';
                        } else {
                            alert(data.message || 'Failed to unban user.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // Delete user
        document.querySelectorAll('.delete-user').forEach(function (button) {
            button.addEventListener('click', function () {
                const uid = this.getAttribute('data-uid');
                const username = this.getAttribute('data-username');

                console.log("Attempting to delete user:", uid, username); // Debugging log

                if (confirm('Are you sure you want to delete ' + username + '?')) {
                    fetch('delete_user.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ uid: uid })
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Server Response:", data); // Debugging log

                            if (data.status === "success") {
                                alert(username + ' has been deleted.');
                                this.closest('tr').remove();
                                window.location.reload();
                            } else {
                                alert('Failed to delete user. ' + data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        });

        document.addEventListener("click", function (event) {
            if (event.target.classList.contains("assign-role")) {
                let userId = event.target.getAttribute("data-uid");

                console.log("Assign Role clicked for UID:", userId); // Debugging

                if (confirm("Are you sure you want to assign/unassign Admin role?")) {
                    fetch("update_role.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ uid: userId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Server Response:", data); // Debugging

                            if (data.status === "success") {
                                alert("User role updated successfully!");

                                // Get the target role cell
                                let roleCell = document.getElementById("role-" + userId);

                                if (data.role === "Admin") {
                                    roleCell.innerHTML = `<img src="picture/assignrole.png" width="30" height="30" class="assign-role" data-uid="${userId}"> Admin`;
                                } else {
                                    roleCell.innerHTML = `<img src="picture/assignrole.png" width="30" height="30" class="assign-role" data-uid="${userId}">`;
                                }
                            } else {
                                alert("Error: " + data.message);
                            }
                        })
                        .catch(error => console.error("Fetch Error:", error));
                }
            }
        });
    });


</script>