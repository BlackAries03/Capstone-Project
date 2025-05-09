<?php 
include 'getName.php';

function getSystemMetrics() {
    $metrics = [];

    $metrics['memory'] = [
        'current' => round(memory_get_usage() / 1024 / 1024, 2),
        'peak' => round(memory_get_peak_usage() / 1024 / 1024, 2),
        'limit' => ini_get('memory_limit')
    ];

    $metrics['execution'] = [
        'max_execution_time' => ini_get('max_execution_time'),
        'current_time' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 4)
    ];

    $app_path = dirname(__FILE__);
    $metrics['app_size'] = [
        'total_size' => round(dirSize($app_path) / 1024 / 1024, 2),
        'file_count' => countFiles($app_path)
    ];

    $metrics['php_info'] = [
        'version' => PHP_VERSION,
        'loaded_extensions' => get_loaded_extensions(),
        'session_status' => session_status(),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size')
    ];

    $metrics['os_info'] = [
        'OS' => php_uname('s') . ' ' . php_uname('r'),
        'Architecture' => php_uname('m'),
        'Processor' => 'N/A',
        'CPU Cores' => 'N/A',
        'Total Memory' => 'N/A', 
        'Free Memory' => 'N/A',
        'GPU' => 'N/A', 
        'PHP Server Uptime' => 'N/A' 
    ];

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $cpu_info = shell_exec('wmic cpu get Name 2>&1');
        if ($cpu_info !== null) {
            $cpu_info = trim(str_replace('Name', '', $cpu_info));
            $metrics['os_info']['Processor'] = trim($cpu_info);
        }
        
        $core_info = shell_exec('wmic cpu get NumberOfCores 2>&1');
        if ($core_info !== null) {
            $core_info = trim(str_replace('NumberOfCores', '', $core_info));
            $metrics['os_info']['CPU Cores'] = trim($core_info);
        }
        
        $memory_info = shell_exec('wmic memorychip get capacity 2>&1');
        if ($memory_info !== null) {
            $memory_values = preg_split('/\s+/', trim($memory_info));
            $total_memory = 0;
            foreach ($memory_values as $value) {
                if (is_numeric($value)) {
                    $total_memory += (int)$value;
                }
            }
            $metrics['os_info']['Total Memory'] = round($total_memory / (1024 * 1024 * 1024), 2) . ' GB';
            
            $free_memory = shell_exec('wmic OS get FreePhysicalMemory 2>&1');
            if ($free_memory !== null) {
                $free_memory = trim(str_replace('FreePhysicalMemory', '', $free_memory));
                $metrics['os_info']['Free Memory'] = round($free_memory / (1024 * 1024), 2) . ' GB';
            }
        }

        $gpu_info = shell_exec('wmic path win32_VideoController get name 2>&1');
        if ($gpu_info !== null) {
            $gpu_info = trim(str_replace('Name', '', $gpu_info));
            $metrics['os_info']['GPU'] = $gpu_info;
        }
    }

    if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
        $cpu_info = @file_get_contents('/proc/cpuinfo');
        if ($cpu_info !== false) {
            preg_match('/model name\s+:\s+(.+)$/m', $cpu_info, $cpu_model);
            preg_match('/cpu cores\s+:\s+(.+)$/m', $cpu_info, $cpu_cores);
            $metrics['os_info']['Processor'] = $cpu_model[1] ?? 'N/A';
            $metrics['os_info']['CPU Cores'] = $cpu_cores[1] ?? 'N/A';
        }

        $memory_info = @file_get_contents('/proc/meminfo');
        if ($memory_info !== false) {
            preg_match('/MemTotal:\s+(\d+)/m', $memory_info, $total_memory);
            preg_match('/MemFree:\s+(\d+)/m', $memory_info, $free_memory);
            $metrics['os_info']['Total Memory'] = isset($total_memory[1]) ? 
                round($total_memory[1] / 1024 / 1024, 2) . ' GB' : 'N/A';
            $metrics['os_info']['Free Memory'] = isset($free_memory[1]) ? 
                round($free_memory[1] / 1024 / 1024, 2) . ' GB' : 'N/A';
        }

        $gpu_info = shell_exec('lspci | grep -i vga 2>&1');
        if ($gpu_info !== null) {
            $metrics['os_info']['GPU'] = trim($gpu_info);
        }
    }
    
    return $metrics;
}

function dirSize($dir) {
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }
    return $size;
}

function countFiles($dir) {
    $count = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
        if ($file->isFile()) {
            $count++;
        }
    }
    return $count;
}

$metrics = getSystemMetrics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebarStyle.css">
    <title>System Performance</title>
    <style>
        .content {
            margin-left: 300px;
            padding: 20px;
        }

        .metrics-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .metric-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .metric-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #eee;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: #4CAF50;
            transition: width 0.3s ease;
        }

        .warning {
            background: #FFA500;
        }

        .critical {
            background: #FF4444;
        }

        .metric-value {
            font-size: 1.1em;
            color: #666;
        }

        .system-status {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .system-status h2 {
            margin-bottom: 10px;
        }

        .system-status .metric-value {
            font-size: 1.1em;
            color: #666;
            margin-top: 10px;
        }

        .status-normal {
            color: #4CAF50;
            font-weight: bold;
        }

        .status-warning {
            color: #FFA500;
            font-weight: bold;
        }

        .status-critical {
            color: #FF4444;
            font-weight: bold;
        }

        .section-title {
            margin: 30px 0 20px;
            color: #333;
            font-size: 1.5em;
        }

        .info-row {
            margin: 8px 0;
            display: flex;
            justify-content: flex-start;
            gap: 20px;
            border-bottom: 1px dashed #eee;
            padding-bottom: 5px;
        }

        .info-row span {
            font-weight: bold;
            color: #555;
            min-width: 150px;
        }

        .tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            width: 250px;
            z-index: 1000;
            line-height: 1.4;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .system-status {
            position: relative;
        }

        .system-status .tooltip {
            top: 100%;
            margin-top: 10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .metric-card {
            position: relative;
        }

        .metric-card .tooltip {
            bottom: 100%;
            margin-bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .status-message {
            font-size: 0.9em;
            color: #666;
            font-style: italic;
        }

        .metric-value {
            line-height: 1.6;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <a href="#" class="logo">
            <img src="picture/logo.png" alt="logo" />
        </a>
        <div class="profile">
            <div class="profile-img">
                <img src="picture/unknown.jpeg" alt="profile" />
            </div>
            <div class="name">
                <h1>Admin</h1>
            </div>
        </div>
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
        <h1>System Performance Monitor</h1>
        
        <div class="system-status" data-tooltip="Overall system health status based on memory usage and execution time performance">
            <h2>System Status: 
                <?php
                $status = 'Normal';
                $statusClass = 'status-normal';
                $statusMessage = '';
                
                $memoryLimit = intval($metrics['memory']['limit']);
                $currentMemory = $metrics['memory']['current'];
                $memoryPercentage = ($currentMemory / $memoryLimit) * 100;
                
                $maxTime = $metrics['execution']['max_execution_time'];
                $currentTime = $metrics['execution']['current_time'];
                $timePercentage = ($currentTime / $maxTime) * 100;

                if ($memoryPercentage > 70 || $timePercentage > 50) {
                    $status = 'Warning';
                    $statusClass = 'status-warning';
                    $statusMessage = 'System is experiencing higher than normal load';
                }
                if ($memoryPercentage > 85 || $timePercentage > 75) {
                    $status = 'Critical';
                    $statusClass = 'status-critical';
                    $statusMessage = 'System is under heavy load and requires attention';
                }
                
                echo "<span class='$statusClass'>$status</span>";
                if ($statusMessage) {
                    echo " <span class='status-message'>($statusMessage)</span>";
                }
                ?>
            </h2>
            <div class="metric-value">
                Memory Usage: <?php echo round($memoryPercentage, 1); ?>% (<?php echo $currentMemory; ?> MB of <?php echo $memoryLimit; ?> MB)<br>
                Execution Time: <?php echo round($timePercentage, 1); ?>% (<?php echo $currentTime; ?>s of <?php echo $maxTime; ?>s)
            </div>
        </div>

        <div class="metrics-container">
            <div class="metric-card" data-tooltip="Monitors PHP memory consumption. Current usage should stay below 70% for optimal performance">
                <div class="metric-title">PHP Memory Usage</div>
                <div class="progress-bar">
                    <?php
                    $memoryLimit = intval($metrics['memory']['limit']);
                    $currentMemory = $metrics['memory']['current'];
                    $memoryPercentage = ($currentMemory / $memoryLimit) * 100;
                    $memoryClass = $memoryPercentage > 75 ? 'critical' : ($memoryPercentage > 50 ? 'warning' : '');
                    ?>
                    <div class="progress-fill <?php echo $memoryClass; ?>" 
                         style="width: <?php echo $memoryPercentage; ?>%"></div>
                </div>
                <div class="metric-value">
                    Current: <?php echo $metrics['memory']['current']; ?> MB of <?php echo $memoryLimit; ?> MB<br>
                    Peak: <?php echo $metrics['memory']['peak']; ?> MB (<?php echo round(($metrics['memory']['peak'] / $memoryLimit) * 100, 1); ?>%)<br>
                    Available: <?php echo $memoryLimit - $currentMemory; ?> MB
                </div>
            </div>

            <div class="metric-card" data-tooltip="Tracks script execution duration. Should remain under 50% of max execution time for optimal response">
                <div class="metric-title">Script Execution</div>
                <div class="progress-bar">
                    <?php
                    $maxTime = $metrics['execution']['max_execution_time'];
                    $currentTime = $metrics['execution']['current_time'];
                    $timePercentage = ($currentTime / $maxTime) * 100;
                    $timeClass = $timePercentage > 75 ? 'critical' : ($timePercentage > 50 ? 'warning' : '');
                    ?>
                    <div class="progress-fill <?php echo $timeClass; ?>" 
                         style="width: <?php echo $timePercentage; ?>%"></div>
                </div>
                <div class="metric-value">
                    Current Time: <?php echo $currentTime; ?> seconds of <?php echo $maxTime; ?> seconds<br>
                    Remaining: <?php echo $maxTime - $currentTime; ?> seconds<br>
                    Usage: <?php echo round($timePercentage, 1); ?>% of limit
                </div>
            </div>

            <div class="metric-card" data-tooltip="Shows application storage metrics including total size and file count">
                <div class="metric-title">Application Size</div>
                <div class="metric-value">
                    Total Size: <?php echo $metrics['app_size']['total_size']; ?> MB<br>
                    File Count: <?php echo $metrics['app_size']['file_count']; ?> files<br>
                    Average File Size: <?php echo round($metrics['app_size']['total_size'] / $metrics['app_size']['file_count'], 2); ?> MB/file
                </div>
            </div>

            <div class="metric-card" data-tooltip="Displays current PHP configuration and runtime settings">
                <div class="metric-title">PHP Configuration</div>
                <div class="metric-value">
                    PHP Version: <?php echo $metrics['php_info']['version']; ?><br>
                    Upload Max Size: <?php echo $metrics['php_info']['upload_max_filesize']; ?><br>
                    Post Max Size: <?php echo $metrics['php_info']['post_max_size']; ?><br>
                    Session Status: <?php 
                        $session_status = $metrics['php_info']['session_status'];
                        echo $session_status == PHP_SESSION_ACTIVE ? 'Active' : 
                            ($session_status == PHP_SESSION_NONE ? 'None' : 'Disabled');
                    ?><br>
                    Extensions: <?php echo count($metrics['php_info']['loaded_extensions']); ?> loaded
                </div>
            </div>
        </div>

        <h2 class="section-title">Detailed System Information</h2>
        <div class="metrics-container">
            <div class="metric-card" data-tooltip="Key system specifications and performance metrics">
                <div class="metric-title">System Specifications</div>
                <div class="metric-value">
                    <?php
                    $display_order = [
                        'OS',
                        'Architecture',
                        'Processor',
                        'CPU Cores',
                        'GPU',
                        'Total Memory',
                        'Free Memory',
                        'PHP Server Uptime'
                    ];
                    
                    foreach ($display_order as $key) {
                        if (isset($metrics['os_info'][$key]) && $metrics['os_info'][$key] !== 'N/A') {
                            echo "<div class='info-row'><span>$key:</span> " . htmlspecialchars($metrics['os_info'][$key]) . "</div>";
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="metric-card" data-tooltip="Shows detailed PHP configuration and environment settings">
                <div class="metric-title">PHP Environment</div>
                <div class="metric-value">
                    <?php
                    $php_env = [
                        'PHP Version' => PHP_VERSION,
                        'Zend Version' => zend_version(),
                        'SAPI Interface' => php_sapi_name(),
                        'Default Timezone' => date_default_timezone_get(),
                        'Default Charset' => ini_get('default_charset')
                    ];
                    foreach ($php_env as $key => $value) {
                        echo "<div class='info-row'><span>$key:</span> $value</div>";
                    }
                    ?>
                </div>
            </div>

            <div class="metric-card" data-tooltip="Displays server configuration and capabilities">
                <div class="metric-title">Server Configuration</div>
                <div class="metric-value">
                    <?php
                    $server_info = [
                        'Server Software' => $_SERVER['SERVER_SOFTWARE'],
                        'Server API' => php_sapi_name(),
                        'Document Root' => $_SERVER['DOCUMENT_ROOT'],
                        'Server Protocol' => $_SERVER['SERVER_PROTOCOL'],
                        'HTTP Accept Encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? 'N/A'
                    ];
                    foreach ($server_info as $key => $value) {
                        echo "<div class='info-row'><span>$key:</span> $value</div>";
                    }
                    ?>
                </div>
            </div>

            <div class="metric-card" data-tooltip="Shows PHP limitations and boundaries for various operations">
                <div class="metric-title">PHP Limits</div>
                <div class="metric-value">
                    <?php
                    $php_limits = [
                        'Max Execution Time' => ini_get('max_execution_time') . ' seconds',
                        'Max Input Time' => ini_get('max_input_time') . ' seconds',
                        'Memory Limit' => ini_get('memory_limit'),
                        'Post Max Size' => ini_get('post_max_size'),
                        'Upload Max Filesize' => ini_get('upload_max_filesize')
                    ];
                    foreach ($php_limits as $key => $value) {
                        echo "<div class='info-row'><span>$key:</span> $value</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            location.reload();
        }, 60000);

        function createTooltip(element, isSystemStatus = false) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = element.getAttribute('data-tooltip');
            tooltip.style.opacity = '0';
            
            element.appendChild(tooltip);

            if (!isSystemStatus) {
                tooltip.style.bottom = '100%';
                tooltip.style.marginBottom = '10px';
            } else {
                tooltip.style.top = '100%';
                tooltip.style.marginTop = '10px';
            }
            
            setTimeout(() => tooltip.style.opacity = '1', 10);
            return tooltip;
        }

        document.querySelectorAll('[data-tooltip]').forEach(element => {
            const isSystemStatus = element.classList.contains('system-status');
            
            element.addEventListener('mouseenter', function() {
                if (this.tooltip) return;
                this.tooltip = createTooltip(this, isSystemStatus);
            });

            element.addEventListener('mouseleave', function() {
                if (this.tooltip) {
                    this.tooltip.style.opacity = '0';
                    setTimeout(() => {
                        this.tooltip.remove();
                        this.tooltip = null;
                    }, 200);
                }
            });
        });
    </script>
</body>
</html> 