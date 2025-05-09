<?php include 'getName(bm).php'; ?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rakan</title>
    <link rel="stylesheet" href="sidebarStyle.css">
</head>

<body>

    <?php include("sidebar(bm).php"); ?>

    <div class="main-content">
        <div class="container">
            <header>
                <div class="header-left">
                    <h1>Rakan yang disyorkan</h1>
                </div>
                <div class="search-container">
                    <div class="search-icon">
                        <img src="picture/search.png" width="30" height="30" alt="search" />
                    </div>
                    <input type="text" id="recomend-search" placeholder="Cari...">
                </div>
            </header>
            <div id="no-result" style="display:none;">Tidak dapat mencari orang</div>
            <div class="friends-list">
                <?php include 'randomSuggestion(bm).php'; ?>
            </div>
        </div>
        <br>
        <br>
        <div class="container">
            <header>
                <div class="header-left">
                    <h1>Mengikuti pengguna</h1>
                </div>
                <div class="search-container">
                    <div class="search-icon">
                        <img src="picture/search.png" width="30" height="30" alt="search" />
                    </div>
                    <input type="text" id="search" placeholder="Cari...">
                </div>
            </header>
            <div class="friends-list">
                <?php if (!empty($followedUsers)): ?>
                    <?php foreach ($followedUsers as $user): ?>
                        <form method="POST" action="follow_action(bm).php">
                            <input type="hidden" name="uid" value="<?php echo $user["UID"]; ?>">
                            <input type="hidden" name="action" value="unfollow">
                            <div class="friend unread" onclick="this.parentNode.submit();">
                                <img src="<?php echo !empty(htmlspecialchars($user["profilePic"])) ? htmlspecialchars($user["profilePic"]) : 'picture/unknown.jpeg'; ?>"
                                    alt="Profile Picture">  
                                <div class="description">
                                    <div class="text-info">
                                        <h2><?php echo htmlspecialchars($user["userName"]); ?></h2>
                                    </div>
                                    <div class="follow-container">
                                        <div class="follow-box">Berhenti ikut</div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Anda tidak mengikuti siapa pun.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>

        document.querySelectorAll('input[id="search"]').forEach(searchBar => {
            searchBar.addEventListener('input', function () {
                const searchValue = this.value.toLowerCase();
                const container = this.closest('.container');
                const friends = container.querySelectorAll('.friend'); // Find only relevant friends
                let hasResults = false;

                friends.forEach(friend => {
                    const friendName = friend.querySelector('.text-info h2').textContent.toLowerCase();
                    if (friendName.includes(searchValue)) {
                        friend.style.display = 'flex';
                        hasResults = true;
                    } else {
                        friend.style.display = 'none';
                    }
                });

                const noResult = container.querySelector('#no-result');
                if (noResult) {
                    noResult.style.display = hasResults ? 'none' : 'block';
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
            const searchBar = document.getElementById("recomend-search");
            if (!searchBar) return; // Exit if search bar not found

            // Load stored username and fill search bar
            const storedUser = localStorage.getItem("followedUser");
            if (storedUser) {
                searchBar.value = storedUser;
                localStorage.removeItem("followedUser");

                // Wait a short time before triggering search (ensures elements exist)
                setTimeout(() => {
                    searchBar.dispatchEvent(new Event("input"));
                }, 100);
            }

            // Search functionality when user types
            searchBar.addEventListener("input", function () {
                const searchValue = this.value.toLowerCase();
                const container = this.closest(".container");
                if (!container) return; // Ensure container exists

                const friends = container.querySelectorAll(".friend"); // Find all friends
                let hasResults = false;

                friends.forEach(friend => {
                    const friendName = friend.querySelector(".text-info h2").textContent.toLowerCase();
                    if (friendName.includes(searchValue)) {
                        friend.style.display = "flex";
                        hasResults = true;
                    } else {
                        friend.style.display = "none";
                    }
                });

                // Show/hide "No Results" message
                const noResult = container.querySelector("#no-result");
                if (noResult) {
                    noResult.style.display = hasResults ? "none" : "block";
                }
            });
        });

    </script>

</html>



<style>
    :root {
        --light-bg: antiquewhite;
        --light-container-bg: #fff;
        --light-text: #000;
        --light-border: #ddd;
        --search-bg-light: white;
        --search-border-light: #ddd;
        --search-text-light: #000;

        --dark-bg: #2e2e2e;
        --dark-container-bg: #3c3c3c;
        --dark-text: #e1e1e1;
        --dark-border: #555;
        --search-bg-dark: #444;
        --search-border-dark: #555;
        --search-text-dark: #e1e1e1;

        --friend-img-bg: #5de0e6;
        --follow-btn-bg: rgb(0, 106, 255);
        --follow-btn-bg-hover: blue;
        --follow-btn-bg-active: grey;
        --unfollow-btn-bg: grey;
        --unfollow-btn-bg-hover: darkgrey;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        display: flex;
        background: var(--light-bg);
        height: 100vh;
        margin: 0;
        padding: 0;
    }

    .sidebar {
        width: 300px;
        background: var(--light-container-bg);
        height: 100vh;
        position: fixed;
        overflow-y: auto;
    }

    .main-content {
        margin-left: 300px;
        padding: 1rem;
        flex-grow: 1;
        background: var(--light-bg);
        overflow-y: auto;
    }

    .container {
        background: var(--light-container-bg);
        border-radius: 1rem;
        padding: 1rem;
        color: var(--light-text);
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--light-border);
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
        color: var(--light-text);
    }

    .header-left h1 {
        margin: 0;
        font-size: 1.5rem;
        color: var(--light-text);
    }

    /* Style for the no-result message */
    #no-result {
        text-align: center;
        color: red;
        margin-top: 20px;
    }

    /* Friend list styling */
    .friend {
        display: flex;
        align-items: center;
        padding: 1rem;
        margin-bottom: 0;
        border-bottom: 1px solid var(--light-border);
    }

    .friend:last-child {
        border-bottom: none;
    }

    .friend img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
        padding: 2px;
        background: var(--friend-img-bg);
    }

    .friend .description {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    .friend .description .text-info {
        display: flex;
        flex-direction: column;
    }

    .friend .description h2,
    .friend .description p {
        margin: 0;
        font-size: 1rem;
    }

    .friend .description h2 {
        margin-bottom: 5px;
    }

    .follow-container {
        margin-left: auto;
    }

    /* Search bar styling */
    .search-container {
        display: flex;
        align-items: center;
        border: 1px solid var(--search-border-light);
        border-radius: 15px;
        padding: 0.5rem;
        width: 250px;
        background-color: var(--search-bg-light);
    }

    .search-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.5rem;
        color: var(--search-text-light);
    }

    .search-container input {
        border: none;
        outline: none;
        font-size: 1rem;
        width: 100%;
        color: var(--search-text-light);
    }

    /* Follow button styling */
    .follow-btn {
        background-color: var(--follow-btn-bg);
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s;
    }

    .follow-btn:hover {
        background-color: var(--follow-btn-bg-hover);
    }

    .follow-btn:active {
        background-color: var(--follow-btn-bg-active);
    }

    .unfollow-btn {
        background-color: var(--unfollow-btn-bg);
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s;
    }

    .unfollow-btn:hover {
        background-color: var(--unfollow-btn-bg-hover);
    }

    .follow-box {
        cursor: pointer;
    }

    /* Dark theme */
    .dark body {
        background: var(--dark-bg);
    }

    .dark .sidebar {
        background: rgb(33, 33, 33);
        color: var(--dark-text);
    }

    .dark .main-content {
        background: var(--dark-bg);
        color: var(--dark-text);
    }

    .dark .container {
        background: var(--dark-container-bg);
        color: var(--dark-text);
    }

    .dark header {
        border-bottom: 1px solid var(--dark-border);
    }

    .dark .header-left button {
        color: var(--dark-text);
    }

    .dark .header-left h1 {
        color: var(--dark-text);
    }

    .dark .friend {
        border-bottom: 1px solid var(--dark-border);
    }

    .dark .search-container {
        border: 1px solid var(--search-border-dark);
        background-color: var(--search-bg-dark);
    }

    .dark .search-icon {
        color: var(--search-text-dark);
    }

    .dark .search-container input {
        color: black;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
            const theme = localStorage.getItem('theme') || 'default';
            if (theme === 'dark') {
                document.body.classList.add('dark');
            }
        });
</script>