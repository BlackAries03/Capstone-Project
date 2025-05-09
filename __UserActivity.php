<?php 
include 'getName.php';
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

$activeTimeQuery = "SELECT DATE(f.time) as date, COUNT(*) as count
                    FROM feed f
                    WHERE f.time >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                    GROUP BY DATE(f.time)
                    ORDER BY date";
$activeTime = $conn->query($activeTimeQuery)->fetch_all(MYSQLI_ASSOC);

$postCountQuery = "SELECT COUNT(*) as post_count FROM feed";
$postCount = $conn->query($postCountQuery)->fetch_assoc()['post_count'];

$commentCountQuery = "SELECT COUNT(*) as comment_count FROM comment";
$commentCount = $conn->query($commentCountQuery)->fetch_assoc()['comment_count'];

$likeCountQuery = "SELECT SUM(l) as total_likes FROM feed";
$likeCount = $conn->query($likeCountQuery)->fetch_assoc()['total_likes'] ?? 0;

$recentPostsQuery = "SELECT f.*, u.userName 
                     FROM feed f 
                     JOIN udata u ON f.UID = u.UID 
                     ORDER BY f.time DESC 
                     LIMIT 5";
$recentPosts = $conn->query($recentPostsQuery)->fetch_all(MYSQLI_ASSOC);

$newUsersQuery = "SELECT DATE_FORMAT(joinDate, '%Y-%m-%d') as date, COUNT(*) as count 
                  FROM udata 
                  WHERE joinDate >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                  GROUP BY DATE(joinDate)
                  ORDER BY date";
$newUsers = $conn->query($newUsersQuery)->fetch_all(MYSQLI_ASSOC);

$announcementQuery = "SELECT updateType, COUNT(*) as count 
                      FROM announcement 
                      WHERE timestamp >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                      GROUP BY updateType";
$announcementData = $conn->query($announcementQuery)->fetch_all(MYSQLI_ASSOC);

$recentAnnouncementQuery = "SELECT title, description, timestamp 
                            FROM announcement 
                            ORDER BY timestamp DESC 
                            LIMIT 1";
$recentAnnouncement = $conn->query($recentAnnouncementQuery)->fetch_assoc();

$announcementCountQuery = "SELECT COUNT(*) as announcement_count FROM announcement";
$announcementCount = $conn->query($announcementCountQuery)->fetch_assoc()['announcement_count'] ?? 0;

$followQuery = "SELECT 
    DATE(n.created_at) as date, 
    COUNT(CASE WHEN n.message LIKE '%is following you%' THEN 1 END) as follows,
    COUNT(CASE WHEN n.message LIKE '%unfollowed you%' THEN 1 END) as unfollows
FROM notifications n
WHERE n.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
GROUP BY DATE(n.created_at)
ORDER BY date";

$groupQuery = "SELECT 
    DATE(n.created_at) as date,
    COUNT(CASE WHEN n.message LIKE '%added you to%' THEN 1 END) as additions,
    COUNT(CASE WHEN n.message LIKE '%removed you from%' THEN 1 END) as removals
FROM notifications n
WHERE n.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
GROUP BY DATE(n.created_at)
ORDER BY date";

$reportQuery = "SELECT 
    DATE(n.created_at) as date,
    COUNT(*) as count
FROM notifications n
WHERE n.message LIKE '%Admin has deleted post%'
    AND n.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
GROUP BY DATE(n.created_at)
ORDER BY date";

$testQuery = "SELECT message, created_at, DATE(created_at) as formatted_date 
             FROM notifications 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
             ORDER BY created_at DESC 
             LIMIT 10";
$testResult = $conn->query($testQuery);
if ($testResult) {
    $messages = $testResult->fetch_all(MYSQLI_ASSOC);
    echo "<!-- Sample data: " . json_encode($messages) . " -->";
}

if ($result = $conn->query($followQuery)) {
    $followData = $result->fetch_all(MYSQLI_ASSOC);
    echo "<!-- Follow Query Success: " . count($followData) . " rows returned -->";
    echo "<!-- Follow Data: " . json_encode($followData) . " -->";
} else {
    echo "<!-- Follow Query Error: " . $conn->error . " -->";
}

if ($result = $conn->query($groupQuery)) {
    $groupData = $result->fetch_all(MYSQLI_ASSOC);
    echo "<!-- Group Query Success: " . count($groupData) . " rows returned -->";
    echo "<!-- Group Data: " . json_encode($groupData) . " -->";
} else {
    echo "<!-- Group Query Error: " . $conn->error . " -->";
}

if ($result = $conn->query($reportQuery)) {
    $reportData = $result->fetch_all(MYSQLI_ASSOC);
    echo "<!-- Report Query Success: " . count($reportData) . " rows returned -->";
    echo "<!-- Report Data: " . json_encode($reportData) . " -->";
} else {
    echo "<!-- Report Query Error: " . $conn->error . " -->";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebarStyle.css">
    <title>User Activity</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .content {
            margin-left: 300px;
            height: 100vh;
            padding: 20px;
        }

        .back {
            margin-top: 10px;
        }

        #title {
            display: flex;
            opacity: 1;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .summary-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
            margin: 0 10px;
            text-align: center;
        }

        .summary-item h3 {
            margin: 0;
            color: #666;
        }

        .summary-item .number {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }

        .expandable-list {
            margin: 20px 0;
        }

        .expandable-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            cursor: pointer;
        }

        .expandable-item h3 {
            margin: 0;
            color: #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .expandable-content {
            display: none;
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .chart-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .chart-container.small {
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }

        .recent-posts {
            margin-top: 20px;
        }

        .recent-posts h2 {
            margin-bottom: 15px;
        }

        .post-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .post-item:last-child {
            border-bottom: none;
        }

        .post-item h4 {
            margin: 0;
            color: #333;
        }

        .post-item p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 0.9em;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .pagination button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .pagination button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .datepicker-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .datepicker-container span {
            font-size: 1.5em;
            font-weight: bold;
            margin: 0 10px;
        }

        .datepicker-container button {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 2em;
            font-weight: bold;
        }

        .toggle-button {
            cursor: pointer;
            width: 24px;
            height: 24px;
            margin-left: 10px;
            transition: transform 0.3s ease;
            transform: rotate(180deg);
        }

        .toggle-button.rotated {
            transform: rotate(0deg);
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
            <h1>User Activity</h1>
        </div>

        <div class="summary-row">
            <div class="summary-item">
                <h3>Total Posts</h3>
                <div class="number"><?php echo $postCount; ?></div>
            </div>
            <div class="summary-item">
                <h3>Total Comments</h3>
                <div class="number"><?php echo $commentCount; ?></div>
            </div>
            <div class="summary-item">
                <h3>Total Likes</h3>
                <div class="number"><?php echo $likeCount; ?></div>
            </div>
        </div>

        <div class="expandable-list">
            <div class="expandable-item">
                <h3>
                    Daily Active Time & Recent Posts
                    <img src="picture/up-arrow-circle-hi-removebg-preview.png" class="toggle-button"
                        onclick="toggleContent(this)">
                </h3>
                <div class="expandable-content">
                    <div class="datepicker-container">
                        <button onclick="changeDateRange(-1, 'activeTime'); event.stopPropagation();">&lt;</button>
                        <span id="activeTimeDateRange"></span>
                        <button onclick="changeDateRange(1, 'activeTime'); event.stopPropagation();">&gt;</button>
                    </div>
                    <div class="chart-container">
            <canvas id="activeTimeChart"></canvas>
        </div>
        <div class="container recent-posts">
            <h2>Recent Posts</h2>
            <?php foreach ($recentPosts as $post): ?>
            <div class="post-item">
                <h4><?php echo htmlspecialchars($post['Fname']); ?></h4>
                            <p>Posted by <?php echo htmlspecialchars($post['userName']); ?> at
                                <?php echo date('Y-m-d H:i', strtotime($post['time'])); ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="expandable-item">
                <h3>
                    New Users
                    <img src="picture/up-arrow-circle-hi-removebg-preview.png" class="toggle-button"
                        onclick="toggleContent(this)">
                </h3>
                <div class="expandable-content">
                    <div class="datepicker-container">
                        <button onclick="changeDateRange(-1, 'newUsers'); event.stopPropagation();">&lt;</button>
                        <span id="newUsersDateRange"></span>
                        <button onclick="changeDateRange(1, 'newUsers'); event.stopPropagation();">&gt;</button>
                    </div>
                    <div class="chart-container">
                        <canvas id="newUsersChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="expandable-item">
                <h3>
                    Announcements
                    <img src="picture/up-arrow-circle-hi-removebg-preview.png" class="toggle-button"
                        onclick="toggleContent(this)">
                </h3>
                <div class="expandable-content">
                    <div class="datepicker-container">
                        <button onclick="changeDateRange(-1, 'announcement'); event.stopPropagation();">&lt;</button>
                        <span id="announcementDateRange"></span>
                        <button onclick="changeDateRange(1, 'announcement'); event.stopPropagation();">&gt;</button>
                    </div>
                    <div class="chart-container small">
                        <canvas id="announcementChart"></canvas>
                    </div>
                    <div class="recent-posts">
                        <h2>Recent Update</h2>
                        <h4><?php echo htmlspecialchars($recentAnnouncement['title']); ?></h4>
                        <p><?php echo htmlspecialchars($recentAnnouncement['description']); ?></p>
                        <p><small>Posted on
                                <?php echo date('Y-m-d H:i', strtotime($recentAnnouncement['timestamp'])); ?></small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="expandable-item">
                <h3>
                    Follow/Unfollow Activity
                    <img src="picture/up-arrow-circle-hi-removebg-preview.png" class="toggle-button"
                        onclick="toggleContent(this)">
                </h3>
                <div class="expandable-content">
                    <div class="datepicker-container">
                        <button onclick="changeDateRange(-1, 'follow'); event.stopPropagation();">&lt;</button>
                        <span id="followDateRange"></span>
                        <button onclick="changeDateRange(1, 'follow'); event.stopPropagation();">&gt;</button>
                    </div>
                    <div class="chart-container">
                        <canvas id="followChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="expandable-item">
                <h3>
                    Group Activity
                    <img src="picture/up-arrow-circle-hi-removebg-preview.png" class="toggle-button"
                        onclick="toggleContent(this)">
                </h3>
                <div class="expandable-content">
                    <div class="datepicker-container">
                        <button onclick="changeDateRange(-1, 'group'); event.stopPropagation();">&lt;</button>
                        <span id="groupDateRange"></span>
                        <button onclick="changeDateRange(1, 'group'); event.stopPropagation();">&gt;</button>
                    </div>
                    <div class="chart-container">
                        <canvas id="groupChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="expandable-item">
                <h3>
                    Reported Posts
                    <img src="picture/up-arrow-circle-hi-removebg-preview.png" class="toggle-button"
                        onclick="toggleContent(this)">
                </h3>
                <div class="expandable-content">
                    <div class="datepicker-container">
                        <button onclick="changeDateRange(-1, 'report'); event.stopPropagation();">&lt;</button>
                        <span id="reportDateRange"></span>
                        <button onclick="changeDateRange(1, 'report'); event.stopPropagation();">&gt;</button>
                    </div>
                    <div class="chart-container">
                        <canvas id="reportChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const activeTimeData = <?php echo json_encode($activeTime); ?>;
        const newUsersData = <?php echo json_encode($newUsers); ?>;
        const announcementData = <?php echo json_encode($announcementData); ?>;
        const followData = <?php echo json_encode($followData); ?>;
        const groupData = <?php echo json_encode($groupData); ?>;
        const reportData = <?php echo json_encode($reportData); ?>;

        console.log('Follow Data:', followData);
        console.log('Group Data:', groupData);
        console.log('Report Data:', reportData);

        const activeTimeCtx = document.getElementById('activeTimeChart').getContext('2d');
        const activeTimeChart = new Chart(activeTimeCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Number of Posts',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        const newUsersCtx = document.getElementById('newUsersChart').getContext('2d');
        const newUsersChart = new Chart(newUsersCtx, {
            type: 'line',
            data: {
                labels: newUsersData.map(item => `Week ${item.week}`),
                datasets: [{
                    label: 'New Users',
                    data: newUsersData.map(item => item.count),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function (value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        }
                    }
                }
            }
        });

        const announcementCtx = document.getElementById('announcementChart').getContext('2d');
        const announcementChart = new Chart(announcementCtx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Announcements',
                    data: [],
                    backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)'],
                    borderColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 206, 86)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });

        const followCtx = document.getElementById('followChart').getContext('2d');
        const followChart = new Chart(followCtx, {
            type: 'bar',
            data: {
                labels: followData.map(item => formatChartDate(item.date)),
                datasets: [{
                    label: 'Follows',
                    data: followData.map(item => parseInt(item.follows) || 0),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1
                }, {
                    label: 'Unfollows',
                    data: followData.map(item => parseInt(item.unfollows) || 0),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        stacked: false
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        const groupCtx = document.getElementById('groupChart').getContext('2d');
        const groupChart = new Chart(groupCtx, {
            type: 'bar',
            data: {
                labels: groupData.map(item => item.date),
                datasets: [{
                    label: 'Added to Groups',
                    data: groupData.map(item => parseInt(item.additions) || 0),
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 1
                }, {
                    label: 'Removed from Groups',
                    data: groupData.map(item => parseInt(item.removals) || 0),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        stacked: false
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        const reportCtx = document.getElementById('reportChart').getContext('2d');
        const reportChart = new Chart(reportCtx, {
            type: 'bar',
            data: {
                labels: reportData.map(item => item.date),
                datasets: [{
                    label: 'Reported Posts',
                    data: reportData.map(item => parseInt(item.count) || 0),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        let currentDate = new Date();

        function toggleContent(button) {
            const content = button.closest('.expandable-item').querySelector('.expandable-content');
            const isVisible = content.style.display === 'block';
            content.style.display = isVisible ? 'none' : 'block';
            button.classList.toggle('rotated', !isVisible);
        }

        function changeDateRange(direction, chartType) {
            currentDate.setDate(currentDate.getDate() + (direction * 7));
            
            const endDate = new Date(currentDate);
            const startDate = new Date(currentDate);
            startDate.setDate(endDate.getDate() - 6);

            const dateRange = `${formatChartDate(startDate)} ~ ${formatChartDate(endDate)}`;
            document.getElementById(`${chartType}DateRange`).textContent = dateRange;
            
            $.ajax({
                url: 'fetchData.php',
                method: 'POST',
                data: {
                    chartType: chartType,
                    startDate: startDate.toISOString().split('T')[0],
                    endDate: endDate.toISOString().split('T')[0]
                },
                success: function(response) {
                    try {
                        const data = JSON.parse(response);
                        console.log('Received data:', data);
                        
                        if (data.error) {
                            console.error('Server error:', data.error);
                            return;
                        }

                        switch(chartType) {
                            case 'activeTime':
                                updateActiveTimeChart(activeTimeChart, data, startDate, endDate);
                                break;
                            case 'newUsers':
                                updateNewUsersChart(newUsersChart, data, startDate, endDate);
                                break;
                            case 'follow':
                                updateFollowChart(followChart, data, startDate, endDate);
                                break;
                            case 'announcement':
                                updateAnnouncementChart(announcementChart, data, startDate, endDate);
                                break;
                            case 'report':
                                updateReportChart(reportChart, data, startDate, endDate);
                                break;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                }
            });
        }

        function updateActiveTimeChart(chart, data, startDate, endDate) {
            console.log('Updating active time chart:', { data, startDate, endDate });

            const filledData = fillMissingDates(data, startDate, endDate, ['count']);
            console.log('Filled data:', filledData);

            const labels = filledData.map(item => formatChartDate(new Date(item.date)));
            const counts = filledData.map(item => parseInt(item.count) || 0);
            
            chart.data.labels = labels;
            chart.data.datasets[0].data = counts;
            chart.update();
        }

        function fillMissingDates(data, startDate, endDate, valueKeys = ['count']) {
            const dateRange = generateDateRange(startDate, endDate);
            
            return dateRange.map(date => {
                const existingData = data.find(item => {
                    const itemDate = new Date(item.date);
                    const compareDate = new Date(date);
                    return itemDate.toISOString().split('T')[0] === compareDate.toISOString().split('T')[0];
                });
                
                if (existingData) {
                    return existingData;
                }
                
                const emptyData = { date: date };
                valueKeys.forEach(key => emptyData[key] = 0);
                return emptyData;
            });
        }

        function generateDateRange(startDate, endDate) {
            const dates = [];
            const currentDate = new Date(startDate);
            const end = new Date(endDate);
            
            while (currentDate <= end) {
                dates.push(currentDate.toISOString().split('T')[0]);
                currentDate.setDate(currentDate.getDate() + 1);
            }
            return dates;
        }

        function updateNewUsersChart(chart, data, startDate, endDate) {
            console.log('Updating new users chart:', { data, startDate, endDate });
            
            const filledData = fillMissingDates(data, startDate, endDate, ['count']);
            const labels = filledData.map(item => item.date);
            const counts = filledData.map(item => parseInt(item.count) || 0);
            
            chart.data.labels = labels;
            chart.data.datasets[0].data = counts;
            chart.update();
        }

        function updateFollowChart(chart, data, startDate, endDate) {
            console.log('Updating follow chart:', { data, startDate, endDate });
            
            const filledData = fillMissingDates(data, startDate, endDate, ['follows', 'unfollows']);
            const labels = filledData.map(item => formatChartDate(new Date(item.date)));
            const follows = filledData.map(item => parseInt(item.follows) || 0);
            const unfollows = filledData.map(item => parseInt(item.unfollows) || 0);
            
            chart.data.labels = labels;
            chart.data.datasets[0].data = follows;
            chart.data.datasets[1].data = unfollows;
            chart.update();
        }

        function updateAnnouncementChart(chart, data, startDate, endDate) {
            console.log('Updating announcement chart:', { data, startDate, endDate });
            
            if (!data || data.length === 0) {
                chart.data.labels = [];
                chart.data.datasets[0].data = [];
                chart.update();
                return;
            }
            
            const labels = data.map(item => item.updateType || 'Other');
            const counts = data.map(item => parseInt(item.count) || 0);
            
            chart.data.labels = labels;
            chart.data.datasets[0].data = counts;
            chart.update();
        }

        function updateReportChart(chart, data, startDate, endDate) {
            console.log('Updating report chart:', { data, startDate, endDate });
            
            const filledData = fillMissingDates(data, startDate, endDate, ['count']);
            console.log('Filled report data:', filledData);
            
            const labels = filledData.map(item => formatChartDate(new Date(item.date)));
            const counts = filledData.map(item => parseInt(item.count) || 0);
            
            chart.data.labels = labels;
            chart.data.datasets[0].data = counts;
            chart.update();
        }

        console.log('Chart Data:', {
            followData: followData.map(item => ({
                date: item.date,
                follows: parseInt(item.follows) || 0,
                unfollows: parseInt(item.unfollows) || 0
            })),
            groupData: groupData.map(item => ({
                date: item.date,
                additions: parseInt(item.additions) || 0,
                removals: parseInt(item.removals) || 0
            })),
            reportData: reportData.map(item => ({
                date: item.date,
                count: parseInt(item.count) || 0
            }))
        });

        window.onload = function() {
            const today = new Date();
            const startDate = new Date(today);
            startDate.setDate(today.getDate() - 6);
            
            ['activeTime', 'newUsers', 'announcement', 'follow', 'group', 'report'].forEach(chartType => {
                changeDateRange(0, chartType);
            });
        };

        function formatChartDate(date) {
            if (typeof date === 'string') {
                date = new Date(date);
            }
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
        }
    </script>
</body>

</html>