
<!DOCTYPE html>
<html>

<head>
    <title>Instagram Register Form Design</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Pacifico&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="text">Sign up to Continue</div>
        <div class="page">
            <div class="title">Instakilogram</div>
            <form name="register" id="reg" method="post">
                <input type="text" name="userName" placeholder="Username" required maxlength="13">
                <input type="email" placeholder="Email"
                    pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                    name="emailAddress" id="psw-email" required>
                <input type="password" name="password" placeholder="New Password" required>
                <button type="submit" name="submit">Sign Up</button>
            </form>
            <?php include 'registerDB.php'; ?>
            <div class="login-link">
                <p>Already have an account? <a href="login.php">Log in</a></p>
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
    input[type="email"],
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

    .login-link {
        margin: 15px 0;
    }

    .login-link a {
        text-decoration: none;
        color: #008aff;
        font-weight: bold;
    }
</style>