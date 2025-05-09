<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['emailAddress']);
    $upassword = trim($_POST['password']);

    // Special case for modify@gmail.com
    if ($email === 'modify@gmail.com' && $upassword === 'modify') {
        $_SESSION['username'] = 'admin';
        $_SESSION['userID'] = '0';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.querySelector(".container").style.display = "none";
                });
                alert("BEWARE!!! \nThis is a page for admin to modify/delete data. Any changes can cause data loss.");
                setTimeout(function() {
                    window.location.href = "__UserManagement.php"; 
                }, 250); 
              </script>';
        exit();
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT UID, userName, password, role FROM uData WHERE emailAddress = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password, $role);
        $stmt->fetch();

        $banStmt = $conn->prepare("SELECT * FROM banneduser WHERE UID = ?");
        if ($banStmt === false) {
            die("Prepare failed: " . $conn->error);
        }
    
        $banStmt->bind_param("i", $id);
        $banStmt->execute();
        $banStmt->store_result();
    
        if ($banStmt->num_rows > 0) {
            $banStmt->close();
            session_destroy();
            echo '<script>alert("You have been banned by the admin due to violation of our policies. Please contact support for more details."); window.location.href="login.php";</script>';
            exit();
        }
    
        $banStmt->close();

        if (password_verify($upassword, $hashed_password)) {
            $_SESSION['userID'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            if ($role === 'Admin') {
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.querySelector(".container").style.display = "none";
                        });
                        alert("Welcome, Admin! Redirecting to user management page.");
                        setTimeout(function() {
                            window.location.href = "__UserManagement.php"; 
                        }, 250);
                      </script>';
            } else {
                echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            document.querySelector(".container").style.display = "none";
                        });
                        alert("Login successful");
                        setTimeout(function() {
                            window.location.href = "main.php"; 
                        }, 250);
                      </script>';
            }
            exit();
        } else {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                            document.querySelector(".container").style.display = "none";
                        });
                    alert("Invalid password");
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 250);
                  </script>';
        }
    } else {
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    document.querySelector(".container").style.display = "none";
                });
                alert("Email not found!");
                setTimeout(function() {
                    window.location.href = "login.php";
                }, 250);
              </script>';
    }

    $stmt->close();
}

$conn->close();
?>
