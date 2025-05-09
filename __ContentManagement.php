<?php include 'getName.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebarStyle.css">
    <title>Content Management</title>
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
            <h1>Content Management</h1>
        </div>

        <div class="search-bar">
            <input type="text" id="search" placeholder="Search by RID or Title..." onkeyup="filterTable()">
        </div>
        <div class="refresh">
            <img src="picture/refresh.png" width="30" height="30" id="refresh-icon">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>RID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Delete Post</th>
                        <th>Ignore Report</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php include 'getReports.php'; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
<script>
    document.getElementById('refresh-icon').addEventListener('click', function () {
        // Reload the page
        location.reload();
    });

    function filterTable() {
        const searchInput = document.getElementById('search').value.toLowerCase();
        const table = document.querySelector('.table-container table tbody');
        const rows = table.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const ridCell = rows[i].getElementsByTagName('td')[0]; // RID column
            const titleCell = rows[i].getElementsByTagName('td')[1]; // Title column

            if (ridCell && titleCell) {
                const rid = ridCell.textContent.toLowerCase();
                const title = titleCell.textContent.toLowerCase();

                // Show rows where search input matches RID or Title
                rows[i].style.display = rid.includes(searchInput) || title.includes(searchInput) ? '' : 'none';
            }
        }
    }
</script>