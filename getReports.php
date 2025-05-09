<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the reportpost table
$sql = "SELECT * FROM reportpost";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Escape output to prevent XSS attacks
        $RID = htmlspecialchars($row['RID']);
        $Title = htmlspecialchars($row['Title']);
        $Content = htmlspecialchars($row['Content']);
        $Reason = htmlspecialchars($row['Reason']);

        echo "<tr>";
        echo "<td>$RID</td>";
        echo "<td>$Title</td>";
        echo "<td><img src='$Content' width='500' height='300' alt='Content'></td>";
        echo "<td><a href='deleteContent.php?rid=$RID'><img src='picture/deletepost.png' width='30' height='30' alt='Delete' title='Delete Post'></a></td>";
        echo "<td><a href='ignoreReport.php?rid=$RID'><img src='picture/ignore.png' width='30' height='30' alt='Ignore' title='Ignore Report'></a></td>";
        echo "<td><textarea rows='13' cols='40' readonly style='resize: none';>$Reason</textarea></td>";
        echo "</tr>";

    }
} else {
    echo "<tr><td colspan='5'>No reports found</td></tr>";
}

$conn->close();
?>
