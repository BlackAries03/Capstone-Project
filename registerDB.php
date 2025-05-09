<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $userName = trim($_POST['userName']);
    $emailAddress = trim($_POST['emailAddress']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM udata WHERE userName = ? OR emailAddress = ?");
    $stmt->bind_param("ss", $userName, $emailAddress);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Invalid email format.</p>";
        return;
    }

    // Add domain validation
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $emailAddress)) {
        echo "<p style='color:red;'>Only Gmail accounts (@gmail.com) are allowed.</p>";
        return;
    }

    if ($result->num_rows > 0) {
        echo "<p style='color:red;'>Username or Email already exists!</p>";
    } else {
        // Insert user data
        $stmt = $conn->prepare("INSERT INTO udata (userName, emailAddress, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $userName, $emailAddress, $password);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>Registration successful! Redirecting...</p>";
            echo "<script>setTimeout(function(){ window.location.href='main.php'; }, 2000);</script>";
        } else {
            echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
        }
    }
    $stmt->close();
}

$conn->close();