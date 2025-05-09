<!DOCTYPE html>
<html>

<head>
    <title>Instakilogram Password Recovery</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="text">Password Recovery</div>
        <div class="page">
            <div class="title">Instakilogram</div>
            <form method="post" action="passwordrecovery2.php">
                <input type="text" placeholder="USERNAME" id="username" name="username" required>
                <input type="password" placeholder="NEW PASSWORD" name="psw" required>
                <button type="submit">NEXT</button>
            </form>
            <div class="footer-container">
                <div class="footer">
                    <p>Don't have an account? <a href="register.php">Sign up</a></p>
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "socialmedia";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['username']) && isset($_POST['psw'])) {
            $username = $_POST['username'];
            $newPassword = $_POST['psw'];

            // Hash the new password before storing it
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Find the user ID associated with the username
            $findUID = $conn->prepare("SELECT UID FROM uData WHERE userName = ?");
            $findUID->bind_param("s", $username);
            $findUID->execute();
            $result = $findUID->get_result();

            if ($row = $result->fetch_assoc()) {
                $tempUID = $row['UID'];
                $findUID->close();

                // Update password in the database
                $stmt = $conn->prepare("UPDATE uData SET password = ? WHERE UID = ?");
                $stmt->bind_param("si", $hashedPassword, $tempUID);
                $stmt->execute();

                // Check if the update was successful
                if ($stmt->affected_rows > 0) {
                    echo '<script>alert("Password successfully updated!"); window.location.href="login.php";</script>';
                } else {
                    echo '<script>alert("Password update failed!"); window.location.href="passwordrecovery2.php";</script>';
                }
                $stmt->close();
            } else {
                echo '<script>alert("User not found!"); window.location.href="login.php";</script>';
            }
        } else {
            echo '<script>alert("Invalid request!"); window.location.href="login.php";</script>';
        }
    }
    ?>

</body>

</html>
<style>
    body {
        margin: 0;
        padding: 0;
        display: flex;
        min-height: 100vh;
        align-items: center;
        justify-content: center;
        background: linear-gradient(#8c52ff, #5de0e6);
        font-family: 'Poppins', sans-serif;
    }

    .container {
        text-align: center;
    }

    .text {
        color: #fff;
        letter-spacing: 1px;
        margin: 10px;
    }

    .page {
        width: 350px;
        background: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .title {
        padding-bottom: 20px;
        font-size: 25px;
        font-family: 'Pacifico', cursive;
        color: black;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    form input {
        margin-bottom: 15px;
        padding: 10px;
        border: 1px solid black;
        border-radius: 5px;
        outline: none;
        font-size: 16px;
    }

    form button {
        border: none;
        background: #043fff;
        color: #fff;
        padding: 10px;
        border-radius: 10px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 50px;
    }

    form button:active {
        background: #5de0e6;
        transform: scale(0.98);
    }

    .footer-container {
        border: 1px solid black;
        margin-top: 20px;
        padding: 10px;
    }

    .footer {
        margin-top: 10px;
        font-size: 15px;
        color: #333;
    }

    .footer a {
        color: #5de0e6;
        text-decoration: underline;
    }
</style>