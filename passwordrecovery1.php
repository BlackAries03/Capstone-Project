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
        <form id="loginForm">
            <input type="email" placeholder="XXX@gmail.com" 
                pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                id="email" required>
            <input type="password" placeholder="One-Time Password (OTP)" id="otp">
            <div class="resend-otp"><a href="#" id="resendOtpLink">Resend OTP</a></div>
            <button type="button" id="nextButton">NEXT</button>
        </form>
        <div class="footer-container">
            <div class="footer">
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</div>

<script>
    let otp;

    document.getElementById('resendOtpLink').addEventListener('click', function(event) {
        event.preventDefault();
        const email = document.getElementById('email').value.trim();
        const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

        if (!email) {
            alert('Please enter email address.');
            return;
        }

        if (!emailPattern.test(email)) {
            alert('Please enter a valid Gmail address.');
            return;
        }
       
        fetch('sendOTP.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("OTP has been sent to your email!");
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            alert("Failed to send OTP.");
        });
    });

    document.getElementById('nextButton').addEventListener('click', function() {
        const enteredOtp = document.getElementById('otp').value;
        
        if (!enteredOtp) {
            alert('Please enter the OTP.');
            return;
        }

        fetch('verify_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `otp=${encodeURIComponent(enteredOtp)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                window.location.href = 'passwordrecovery2.php';
            } else if (data.message === "Session expired. Please request a new OTP.") {
                alert("Session expired. Please request a new OTP.");
            } else {
                alert('Invalid OTP. Please try again.');
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error verifying OTP.");
        });
    });
</script>
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

    .resend-otp {
        text-align: left;
        margin-bottom: 15px;
        margin-left: 280px;
    }

    .resend-otp a {
        color: #5de0e6; 
        font-size: 12px;
        text-decoration: underline; 
    }

    form button {
        border: none;
        background: #043fff;
        color: #fff;
        padding: 10px;
        border-radius: 10px; 
        font-weight: bold;
        cursor: pointer;
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
