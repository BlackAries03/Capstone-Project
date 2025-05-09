<?php
// Database connection
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

// Get the posted data
$updates = json_decode(file_get_contents("php://input"), true);

if (!$updates) {
    die("No data received.");
}

file_put_contents("log.txt", print_r($updates, true)); // Logs the received data


// Prepare and execute the update queries
foreach ($updates as $update) {
    $aID = $update['aID'];
    $title = $conn->real_escape_string($update['title']);
    $updateType = $conn->real_escape_string($update['updateType']);
    $description = $conn->real_escape_string($update['description']);

    $sql = "UPDATE announcement SET title='$title', updateType='$updateType', description='$description' WHERE aID='$aID'";
    
    if ($conn->query($sql) === TRUE) {
        file_put_contents("log.txt", "Update successful for aID: $aID\n", FILE_APPEND);
    } else {
        file_put_contents("log.txt", "Error updating aID $aID: " . $conn->error . "\n", FILE_APPEND);
    }
}

$conn->close();
echo json_encode(["success" => true, "message" => "Announcements updated successfully."]);
?>
