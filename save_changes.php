<?php
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

// Use null coalescing operator to set default values if keys are not present
$title = $_POST['title'] ?? '';
$updateType = $_POST['updateType'] ?? '';
$description = $_POST['description'] ?? '';

// Validate input (simple validation example)
if (empty($title) && empty($updateType) && empty($description)) {
    echo "Please fill in at least one field.";
    exit();
}

// Prepare the SQL statement
$sql = "INSERT INTO announcement (title, updateType, description) VALUES (?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sss", $title, $updateType, $description);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record inserted successfully.";
        // Redirect to the announcement page
        header("Location: __Announcement.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
