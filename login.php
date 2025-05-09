<?php
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Instagram Login Form Design</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="website icon" type="png" href="picture\logo.png">
</head>

<body>

    <div class="container">
        <div class="text">Log in to Continue</div>
        <div class="page">
            <div class="title">Instakilogram</div>
            <form method="POST">
                <input type="text" name="emailAddress" placeholder="Email">
                <input type="password" name="password" placeholder="Password">
                <button type="submit" name="submit">Log In</button>

            </form>
            <?php include("loginDB.php"); ?>

            <div class="forget-id">
                <a href="passwordrecovery1.php">Forget password?</a>
            </div>
            <div class="signup">
                <p>Don't have an account?<a href="register.php">Sign up</a></p>
            </div>
        </div>
    </div>

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
        font-family: sans-serif;
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
        border-radius: 5px;
        padding-bottom: 15px;
    }

    .title {
        padding: 30px 0;
        text-transform: capitalize;
        font-size: 25px;
        font-family: 'Pacifico', cursive;
    }

    form {
        width: 75%;
        display: flex;
        flex-direction: column;
        position: relative;
        left: 50%;
        transform: translateX(-50%);
    }

    form input {
        margin-bottom: 5px;
        border: 1px solid rgba(0, 0, 0, 0.4);
        border-radius: 3px;
        outline: none;
    }

    form input:focus {
        border: 1px solid rgba(0, 0, 0, 0.7);
        background: #efefef;
    }

    input[type="text"],
    input[type="password"] {
        padding-left: 10px;
        position: relative;
        height: 35px;
        font-size: 17px;
    }

    input::placeholder {
        font-weight: normal;
        font-size: 13px;
        transition: 0.3s ease;
    }

    input:focus::placeholder {
        position: absolute;
        font-size: 10px;
        transform: translateY(-13px);
    }

    form button {
        border: none;
        background: #043fff;
        padding: 5px 0;
        margin-top: 5px;
        color: #fff;
        border-radius: 3px;
        font-weight: bold;
        text-transform: capitalize;
        letter-spacing: 1px;
        outline: none;
        cursor: pointer;
    }

    form button:active {
        background: #2c58fe;
        transform: scale(0.995);
    }

    .forget-id {
        margin: 15px;
    }

    .forget-id a {
        text-decoration: none;
        color: #025fd2;
        font-size: 12px;
        font-weight: 500;
    }

    .page .signup {
        position: relative;
        border: 1px solid #b6b6b6;
        border-radius: 2px;
        width: 90%;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 30px;
    }

    .signup a {
        text-decoration: none;
        margin-left: 5px;
        color: #008aff;
        font-weight: bold;
    }
</style>