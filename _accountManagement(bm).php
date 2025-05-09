<?php include("getName(bm).php"); ?>
<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengurusan Akaun</title>
    <style>
        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            list-style: none;
            text-decoration: none;
        }

        /* Logo */
        .logo img {
            width: 70px;
        }

        /* Profile */
        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-top: 1.4rem;
        }

        .profile-img {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .profile-img::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border-radius: 50%;
            padding: 4px;
            background: linear-gradient(to right, #8c52ff, #5de0e6);
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            mask-composite: exclude;
            z-index: -1;
        }

        .profile-img .main-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            display: block;
        }

        .profile-img .overlay-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            opacity: 0.7;
            pointer-events: none;
        }

        .name {
            display: flex;
            align-items: center;
        }

        .name h1 {
            font-size: 1.1rem;
        }

        .name img {
            margin-left: 4px;
            width: 20px;
            object-fit: center;
        }

        .profile-img span {
            font-size: 0.938rem;
            font-weight: 400;
        }

        /* About */
        .about {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
            height: auto;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .follow-box {
            text-align: center;
            padding-right: 50px;
            padding-left: 50px;
            color: #000;
        }

        .follow-box h3 {
            font-size: 1rem;
            font-weight: 500;
        }

        .follow-box span {
            font-size: 0.938rem;
            font-weight: 400;
        }

        /* Title */
        #title {
            display: flex;
            opacity: 1;
        }

        #title h1 {
            margin-top: 2px;
            margin-left: 10px;
        }

        /* Content */
        .content {
            justify-content: center;
        }

        #option {
            width: 100%;
            height: 80px;
            margin-bottom: 60px;
            background: #989797;
            opacity: 0.8;
            position: relative;
        }

        #option:hover {
            opacity: 1;
        }

        .options {
            height: 80%;
            width: 80%;
            margin-top: 30px;
            margin-left: auto;
            margin-right: auto;
        }

        #option h1 {
            color: black;
            padding-top: 25px;
            margin-left: 10px;
            font-size: 150%;
        }

        /* Buttons */
        .button {
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
        }

        /* Modal */
        .box {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .item {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }

        .close-btn,
        .email-close-btn,
        .phone-close-btn,
        .psw-close-btn,
        .delAcc-close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .email-close-btn:hover,
        .phone-close-btn:hover,
        .psw-close-btn:hover,
        .delAcc-close-btn:hover,
        .close-btn:focus,
        .email-close-btn:focus,
        .phone-close-btn:focus,
        .psw-close-btn:focus,
        .delAcc-close-btn:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        /* Form */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            font-weight: bold;
        }

        input {
            margin-bottom: 20px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            font-size: 1rem;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        button[type="button"] {
            margin-bottom: 15px;
        }

        .back {
            margin-top: 5px;
            margin-left: 10px;
        }

        /* Dark Theme */
        body.dark {
            background-color: #424242;
            color: #e0e0e0;
        }

        .dark a {
            color: #bb86fc;
        }

        .dark .follow-box {
            color: #e0e0e0;
        }

        .dark #option {
            background: #333;
        }

        .dark .options {
            background: #424242;
        }

        .dark #option h1 {
            color: #e0e0e0;
        }

        .dark .button {
            background-color: #444;
            color: #e0e0e0;
        }

        .dark .button:hover {
            background-color: #555;
        }

        .dark .box {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .dark .item {
            background-color: #1f1f1f;
        }

        .dark .close-btn,
        .dark .email-close-btn,
        .dark .phone-close-btn,
        .dark .psw-close-btn,
        .dark .delAcc-close-btn {
            color: #bbb;
        }

        .dark .close-btn:hover,
        .dark .email-close-btn:hover,
        .dark .phone-close-btn:hover,
        .dark .psw-close-btn:hover,
        .dark .delAcc-close-btn:hover {
            color: #fff;
        }

        .dark label {
            color: #e0e0e0;
        }

        .dark input {
            background-color: #2d2d2d;
            color: #e0e0e0;
        }

        .dark .back {
            color: #e0e0e0;
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
</head>

<body>
    <div id="title">
        <a href="_Settings(bm).php"><img src="picture/back-button.png" alt="kembali" style="width: 30px; height: 30px;"
                class="back"></a>
        <h1>Pengurusan Akaun</h1>
    </div>
    <div class="content">
        <a href="#" class="logo">
            <img src="picture/logo.png" alt="logo" />
        </a>
        <!-- Gambar Profil -->
        <div class="profile">
            <form action="uploads.php" method="post" enctype="multipart/form-data" id="uploadForm">
                <div class="profile-img">
                    <label for="fileInput">
                        <img src="picture/add.png" alt="tambah profil" class="main-img" id="addProfileImage">
                        <img src="<?php echo !empty($profilePic) ? $profilePic : 'picture/unknown.jpeg'; ?>"
                            alt="Gambar Profil" class="overlay-img" />
                    </label>
                    <input type="file" id="fileInput" name="profilePic" accept="image/*" required>
                </div>
            </form>

            <style>
                .profile-img {
                    position: relative;
                    display: inline-block;
                    cursor: pointer;
                    width: 120px;
                    height: 120px;
                }

                .overlay-img {
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    object-fit: cover;
                }

                .main-img {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 30px;
                    height: 30px;
                    opacity: 0.7;
                }

                input[type="file"] {
                    display: none;
                    /* Hide file input */
                }

                button {
                    display: block;
                    margin-top: 10px;
                    /* Adds spacing between upload button */
                }
            </style>

            <div class="name">
                <h1 id="current-username"><?php echo htmlspecialchars($user_name); ?></h1>
                <img src="picture/verified.png" alt="sahkan" />
            </div>
            <span id="current-email">--<?php echo htmlspecialchars($emailAddress) ?>--</span>
            <span id="current-phone">--<?php echo htmlspecialchars($phoneNumber) ?>--</span>
        </div>
    </div>
    <!-- Mengenai -->
    <div class="about">
        <!-- Kotak 1 -->
        <div class="follow-box">
            <h3>54</h3>
            <span>Pos</span>
        </div>
        <!-- Kotak 2 -->
        <div class="follow-box">
            <h3><?php echo $_SESSION["follower"] ?></h3>
            <span>Pengikut</span>
        </div>
        <!-- Kotak 3 -->
        <div class="follow-box">
            <h3><?php echo $_SESSION["following"] ?></h3>
            <span>Mengikuti</span>
        </div>
    </div>
    </div>
    <div class="options">
        <a href="#" id="UNameEdit">
            <div id="option">
                <div id="title" style="padding-left: 30px;">
                    <img src="picture\images-removebg-preview (4).png" alt="uName" style="height: auto;width: 80px;">
                    <h1>TUKAR NAMA PENGGUNA</h1>
                </div>
            </div>
        </a>

        <a href="#" id="EmailEdit">
            <div id="option">
                <div id="title" style="padding-left: 30px;">
                    <img src="picture\download-removebg-preview (4).png" alt="mel" style="height: auto;width: 80px;">
                    <h1>KEMASKINI AKAUN EMAIL</h1>
                </div>
            </div>
        </a>
        <a href="#" id="PhoneEdit">
            <div id="option">
                <div id="title" style="padding-left: 30px;">
                    <img src="picture\download-removebg-preview (5).png" alt="telefon" style="height: auto;width: 80px;">
                    <h1>KEMASKINI NOMBOR TELEFON</h1>
                </div>
            </div>
        </a>
        <a href="#" id="PswEdit">
            <div id="option">
                <div id="title" style="padding-left: 30px;">
                    <img src="picture\download-removebg-preview (6).png" alt="katalaluan" style="height: auto;width: 80px;">
                    <h1>TETAPKAN SEMULA KATA LALUAN</h1>
                </div>
            </div>
        </a>
        <a href="#" id="DelAcc">
            <div id="option" style="background-color: red;">
                <div id="title" style="padding-left: 30px;">
                    <img src="picture\download-removebg-preview (7).png" alt="padamakaun" style="height: auto;width: 80px;">
                    <h1>PADAM AKAUN</h1>
                </div>
            </div>
        </a>


    </div>
    <div id="changeUName" class="box">
        <div class="item">
            <span class="close-btn">&times;</span>
            <h2>Tukar Nama Pengguna</h2>
            <form id="username-form" method="post">
                <label for="new-username">Nama Pengguna Baru:</label>
                <input type="text" placeholder="Nama Pengguna Baru" id="new-username" name="new-username" required>
                <button type="submit">Hantar</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('UNameEdit').addEventListener('click', function (event) {
            document.getElementById('changeUName').style.display = 'block';
        });

        document.querySelector('.close-btn').addEventListener('click', function () {
            document.getElementById('changeUName').style.display = 'none';
        });

        window.addEventListener('click', function (event) {
            const item = document.getElementById('changeUName');
            if (event.target === item) {
                item.style.display = 'none';
            }
        });
    </script>

    <div id="changeEmail" class="box">
        <div class="item">
            <span class="email-close-btn">&times;</span>
            <h2>KEMASKINI AKAUN EMAIL</h2>
            <form id="email-form" method="post">
                <label for="new-email">Emel Baharu</label>
                <input type="email" placeholder="XXX@gmail.com" 
                    pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
                    name="new-email" id="new-email" required>
                <button type="submit">Hantar</button>
            </form>

            <!-- Add this script -->
            <script>
                document.getElementById('email-form').addEventListener('submit', function(event) {
                    event.preventDefault();
                    const email = document.getElementById('new-email').value.trim();
                    const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;

                    if (!emailPattern.test(email)) {
                        alert('Sila masukkan alamat Gmail yang sah.');
                        return;
                    }

                    // If validation passes, submit the form
                    this.submit();
                });
            </script>
        </div>
    </div>
    <script>
        document.getElementById('EmailEdit').addEventListener('click', function () {
            document.getElementById('changeEmail').style.display = 'block';
        });

        document.querySelector('.email-close-btn').addEventListener('click', function () {
            document.getElementById('changeEmail').style.display = 'none';
        });

        window.addEventListener('click', function (event) {
            const item = document.getElementById('changeEmail');
            if (event.target === item) {
                item.style.display = 'none';
            }
        });
    </script>


    <div id="changePhone" class="box">
        <div class="item">
            <span class="phone-close-btn">&times;</span>
            <h2>KEMASKINI Nombor Telefon</h2>
            <form id="phone-form" method="post">
                <label for="new-phone">Nombor Telefon Baru</label>
                <input type="tel" placeholder="+601234567890" pattern="(\+60)[0-9]{9,10}" name="new-phone"
                    id="new-phone" required>
                <button type="submit"> Hantar</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('PhoneEdit').addEventListener('click', function (event) {
            document.getElementById('changePhone').style.display = 'block';
        });

        document.querySelector('.phone-close-btn').addEventListener('click', function () {
            document.getElementById('changePhone').style.display = 'none';
        });

        window.addEventListener('click', function (event) {
            const item = document.getElementById('changePhone');
            if (event.target === item) {
                item.style.display = 'none';
            }
        });
    </script>

    <div id="changePsw" class="box" style="display: none;">
        <div class="item">
            <span class="psw-close-btn">&times;</span>
            <h2>Tukar Kata Laluan</h2>
            <form id="psw-form" method="post">
                <label for="psw-email">Alamat Emel</label>
                <input type="email" placeholder="XXX@gmail.com"
                    pattern="[a-zA-Z0-9._%+-]+@(gmail\.com|hotmail\.com|yahoo\.com|apu\.edu\.my)" name="psw-email"
                    id="psw-email" required>
                <button type="button" id="request-otp">Minta OTP</button>
                <label for="otp-code">Masukkan OTP</label>
                <input type="text" name="otp-code" id="otp-code" required>
                <button type="submit">Hantar</button>
            </form>
        </div>
    </div>

    <div id="changePsw2" class="box" style="display: none;">
        <div class="item">
            <span class="psw2-close-btn">&times;</span>
            <h2>Tukar Kata Laluan</h2>
            <form id="psw2-form" method="post">
                <label for="new-psw">Kata Laluan Baru</label>
                <input type="password" placeholder="PASSWORD" name="psw" id="psw" required>
                <button type="submit">Hantar</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('PswEdit').addEventListener('click', function (event) {
            event.preventDefault();
            document.getElementById('changePsw').style.display = 'block';
        });

        document.querySelector('.psw-close-btn').addEventListener('click', function () {
            document.getElementById('changePsw').style.display = 'none';
        });

        document.getElementById('request-otp').addEventListener('click', function () {
            const email = document.getElementById('psw-email').value.trim();
            const emailPattern = /^[a-zA-Z0-9._%+-]+@(gmail\.com|hotmail\.com|yahoo\.com|apu\.edu\.my)$/;

            if (!emailPattern.test(email)) {
                alert('Sila masukkan alamat emel yang sah.');
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
                        alert("OTP telah dihantar ke emel anda!");
                        document.getElementById('changePsw').style.display = 'block';
                    } else {
                        alert("Ralat: " + data.message);
                    }
                })
                .catch(error => {
                    alert("Gagal menghantar OTP.");
                });
        });

        document.getElementById('psw-form').addEventListener('submit', function (event) {
            event.preventDefault();
            const userOTP = document.getElementById('otp-code').value.trim();

            fetch('verify_otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `otp=${encodeURIComponent(userOTP)}`
            })
                .then(response => response.json())
                .then(data => {
                    console.log("User Entered OTP:", userOTP);
                    console.log("Server Response:", data);

                    if (data.status === "success") {
                        document.getElementById('changePsw').style.display = 'none';
                        document.getElementById('changePsw2').style.display = 'block';
                    } else if (data.message === "Sesi tamat. Sila minta OTP baru.") {
                        alert("Sesi tamat. Sila minta OTP baru.");
                    } else {
                        alert('OTP tidak sah. Sila cuba lagi.');
                    }
                })
                .catch(error => {
                    console.error("Ralat:", error);
                    alert(`Ralat mengesahkan OTP.\n\nInput Anda: ${userOTP}`);
                });
        });

        document.querySelector('.psw2-close-btn').addEventListener('click', function() {
            document.getElementById('changePsw2').style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            const item = document.getElementById('changePsw2');
            if (event.target === item) {
                item.style.display = 'none';
            }
        });
    </script>

    <!-- Akaun del perlu menggunakan php untuk mengeluarkan data pengguna daripada db -->
    <div id="delAcc" class="box">
        <div class="item">
            <span class="delAcc-close-btn">&times;</span>
            <h2>PADANKAN AKOUNT</h2>
            <form id="delAcc-form" method="post">
                <img src="picture\download-removebg-preview (8).png" alt="amaran">
                <br>
                <span style="color: red; font-size: 11px">ANDA PASTI INGIN MEMADAM AKAUN INI?</span>
                <br>
                <span style="color: red; font-size: 10px;">TINDAKAN INI TIDAK DAPAT DIKEMBALIKAN!</span>
                <br>
                <button type="submit" style="background-color: green;"> BATAL </button>
                <br>
                <button type="submit" style="background-color: red;"> SAHKAN </button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('DelAcc').addEventListener('click', function (event) {
            document.getElementById('delAcc').style.display = 'block';
        });
        document.querySelector('.delAcc-close-btn').addEventListener('click', function () {
            document.getElementById('delAcc').style.display = 'none';
        });
        window.addEventListener('click', function (event) {
            const item = document.getElementById('delAcc');
            if (event.target === item) {
                item.style.display = 'none';
            }
        });

        document.getElementById("fileInput").addEventListener("change", function (event) {
            const file = event.target.files[0]; // Dapatkan fail yang dipilih
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById("profileImage").src = e.target.result;
                };
                reader.readAsDataURL(file);

                document.getElementById("uploadForm").submit();
            }
        });
    </script>
</body>

