<?php include 'getName.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebarStyle.css">
    <title>About Us</title>
    <style>
        body {
            background: antiquewhite;
        }

        .content {
            margin-left: 300px;
            height: 120vh;
        }

        #title {
            display: flex;
            opacity: 1;
        }

        .container {
            display: flex;
            height: auto;
            width: 80%;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
            background: rgb(198, 195, 195);
            padding: 60px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 1rem;
        }

        .container img {
            flex: 1;
            max-width: 50%;
            height: auto;
            border-radius: 1rem;
        }

        .about-content {
            flex: 1;
            padding-left: 20px;
        }

        .about-content h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .about-content p {
            font-size: 1.2rem;
            line-height: 1.5;
            text-align: justify;
        }

        .back {
            margin-top: 10px;
            margin-right: 5px;
        }

        /* Dark theme styles */
        .dark .content {
            margin-left: 300px;
            height: 100%;
            background: #2e2e2e;
            /* Dark background color */
        }

        .dark #title {
            display: flex;
            opacity: 1;
        }

        .dark h1 {
            color: #e1e1e1;
        }

        .dark .container {
            display: flex;
            height: auto;
            width: 80%;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
            background: #3c3c3c;
            /* Dark container background */
            padding: 60px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            /* Darker box shadow */
            border-radius: 1rem;
            color: #e1e1e1;
            /* Text color for dark theme */
        }

        .dark .container img {
            flex: 1;
            max-width: 50%;
            height: auto;
            border-radius: 1rem;
        }

        .dark .about-content {
            flex: 1;
            padding-left: 20px;
        }

        .dark .about-content h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #e1e1e1;
            /* Text color for dark theme */
        }

        .dark .about-content p {
            font-size: 1.2rem;
            line-height: 1.5;
            text-align: justify;
            color: #e1e1e1;
            /* Text color for dark theme */
        }

        .dark .back {
            margin-top: 10px;
            margin-right: 5px;
            color: #e1e1e1;
            /* Text color for dark theme */
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
    <?php include("sidebar.php"); ?>
    <div class="content">
        <div id="title">
            <a href="_Settings.php">
                <img src="picture/back-button.png" alt="back" class="back" style="width: 30px; height: 30px;">
            </a>
            <h1>About Us</h1>
        </div>
        <div class="container">
            <img src="picture/aboutus.jpg" alt="About Us Image" style="width: 50%; height: 50%;">
            <div class="about-content">
                <h2>Welcome to InstaKilogram</h2>
                <p>At InstaKilogram, we believe in the power of visual storytelling. As a social media platform, we
                    empower our users to share their life moments, adventures, and creativity with the world. Whether
                    you're a professional photographer, an aspiring influencer, or someone who loves to capture everyday
                    memories, InstaKilogram is the perfect place to showcase your work and connect with like-minded
                    individuals.</p>
                <p>Our mission is to create a vibrant community where people can express themselves, discover new
                    inspirations, and build meaningful connections. We are committed to providing a user-friendly
                    experience, robust security measures, and continuous improvements to make InstaKilogram the ultimate
                    platform for visual content.</p>
            </div>
        </div>
        <h1 style="margin-top: 20px; margin-left: 45px;">OUR TEAM</h1>
        <div class="container" style="display: flex; flex-direction: column;">
            <div style="display: flex; flex-direction: row;">
                <img src="picture\WONG YAN XUN.jpg" alt="About Us Image" style="flex: 0; width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>GROUP LEADER</h1>
                    <h2>WONG YAN XUN</h2>

                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\WONG KAM LOKE.jpg" alt="About Us Image" style="flex: 0; width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>GROUP MEMBER</h1>
                    <h2>WONG KAM LOKE</h2>
                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\PHUA BIAN QI.jpg" alt="About Us Image" style="flex: 0;  width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>GROUP MEMBER</h1>
                    <h2>PHUA BIAN QI</h2>
                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\Tan yanyu.jpg" alt="About Us Image"
                    style="flex: 0;  min-width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>GROUP MEMBER</h1>
                    <h2>TAN YANYU</h2>
                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\Siew Jun Long.jpg" alt="About Us Image" style="flex: 0;  width: auto; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>GROUP MEMBER</h1>
                    <h2>SIEW JUN LONG</h2>
                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\Wong kar yi.jpg" alt="About Us Image" style="flex: 0;  width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>GROUP MEMBER</h1>
                    <h2>WONG KAR YI</h2>
                </div>
            </div>
        </div>

    </div>

</body>

</html>