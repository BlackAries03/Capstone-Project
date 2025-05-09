<?php
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "socialmedia";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $UID = intval($_POST['UID']);
    $Fname = trim($_POST['Fname']);

    // Check if UID exists
    $checkUID = $conn->prepare("SELECT UID FROM udata WHERE UID = ?");
    $checkUID->bind_param("i", $UID);
    $checkUID->execute();
    $checkUID->store_result();

    if ($checkUID->num_rows > 0) {
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = "uploads/";
            $fileName = uniqid() . "_" . basename($_FILES['picture']['name']); // Create a unique file name
            $targetFilePath = $uploadDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetFilePath)) {
                    // Save data to database
                    $stmt = $conn->prepare("INSERT INTO feed (UID, Fimage, Fname, time) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
                    $stmt->bind_param("iss", $UID, $targetFilePath, $Fname);

                    if ($stmt->execute()) {
                        echo "<script>window.location.href='main.php';</script>";
                    } else {
                        echo "<p style='color:red;'>Error saving to database: " . htmlspecialchars($stmt->error) . "</p>";
                    }
                } else {
                    echo "<p style='color:red;'>Failed to save the uploaded file. Please try again.</p>";
                }
            } else {
                echo "<p style='color:red;'>Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.</p>";
            }
        } else {
            echo "<p style='color:red;'>Please upload a valid image file.</p>";
        }
    } else {
        echo "<p style='color:red;'>Invalid UID. Please ensure the user exists in the database.</p>";
    }

    $checkUID->close();
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>
