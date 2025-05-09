<?php include 'getName(cn).php'; ?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>转发到界面</title>
    <link rel="stylesheet" href="sidebarStyle.css">
</head>

<body>
<?php include("sidebar(cn).php"); ?>
    <div class="main-content">
        <div class="container">
            <header>
                <div class="header-left">
                    <button id="returnButton" class="return-button" type="button" onclick="window.location.href='message.php'">
                        <img src="picture/back-button.png" width="30" height="30" alt="返回图标" class="back">
                    </button>
                    <h1>转发到</h1>
                </div>
            </header>
            <?php include "forward.php"; ?>
        </div>
    </div>
</body>

</html>


<style>
   * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        display: flex;
        background: antiquewhite;
        height: 100vh;
        margin: 0;
        padding: 0;
    }

    .sidebar {
        width: 300px;
        background: #fff;
        height: 100vh;
        position: fixed;
        overflow-y: auto;
    }

    .main-content {
        margin-left: 300px;
        padding: 1rem;
        flex-grow: 1;
        background: antiquewhite;
        overflow-y: auto;
    }

    .container {
        background: #fff;
        border-radius: 1rem;
        padding: 1rem;
    }

    .header {
        display: flex;
        align-items: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 1rem;
    }

    .header-left {
        display: flex;
        align-items: center;
    }

    .header-left button {
        border: none;
        background: none;
        cursor: pointer;
        margin-right: 10px;
    }

    .header-left h1 {
        margin: 0;
        font-size: 1.5rem;
    }

    .message-list {
        overflow-y: auto;
        padding: 1rem;
    }

    .message {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .message img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
        padding: 2px;
        background: #5de0e6;
    }

    .message .description {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .message .description h2 {
        margin: 0;
        font-size: 1rem;
    }

    .message .description p {
        margin: 0;
        color: gray;
    }

    .unread {
        background-color: #f9f9f9;
    }

    .back {
        margin-top: 5px;
    }

    /* Dark Theme */
    .dark {
        --dark-background: #2e2e2e;
        --dark-sidebar-background: #3c3c3c;
        --dark-text-color: #e1e1e1;
        --dark-border-color: #555;
    }

    .dark body {
        background: var(--dark-background);
    }

    .dark .sidebar {
        background: var(--dark-sidebar-background);
    }

    .dark .main-content {
        background: var(--dark-background);
    }

    .dark .container {
        background: var(--dark-sidebar-background);
        color: var(--dark-text-color);
    }

    .dark .header {
        background: var(--dark-sidebar-background);
        color: var(--dark-text-color);
        border-bottom: 1px solid var(--dark-border-color);
    }

    .dark .header-left h1 {
        color: var(--dark-text-color);
    }

    .dark .message {
        background: var(--dark-sidebar-background);
        color: var(--dark-text-color);
        border-bottom: 1px solid var(--dark-border-color);
    }

    .dark .message .description p {
        color: var(--dark-text-color);
    }

    .dark .unread {
        background-color: #555;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const messageInput = document.getElementById("messageInput");
        const storedMessage = localStorage.getItem("forwardedMessage");

        if (storedMessage && messageInput) {
            messageInput.value = storedMessage;
        }

        const returnButton = document.getElementById("returnButton");
        if (returnButton) {
            returnButton.addEventListener("click", function () {
                localStorage.removeItem("forwardedMessage");
                if (messageInput) messageInput.value = "";
                window.history.back();
            });
        }
    });

    window.addEventListener("popstate", function () {
        localStorage.removeItem("forwardedMessage");
        const messageInput = document.getElementById("messageInput");
        if (messageInput) messageInput.value = "";
    });

    if (!window.performance || window.performance.navigation.type === 2) { 
        localStorage.removeItem("forwardedMessage");
        const messageInput = document.getElementById("messageInput");
        if (messageInput) messageInput.value = "";
    }

    document.addEventListener('DOMContentLoaded', (event) => {
        const theme = localStorage.getItem('theme') || 'default';
        if (theme === 'dark') {
            document.body.classList.add('dark');
        }
    });
</script>
