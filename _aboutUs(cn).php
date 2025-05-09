<?php include 'getName.php'; ?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebarStyle.css">
    <title>关于我们</title>
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
    <?php include("sidebar(cn).php"); ?>
    <div class="content">
        <div id="title">
            <a href="_Settings(cn).php">
                <img src="picture/back-button.png" alt="返回" class="back" style="width: 30px; height: 30px;">
            </a>
            <h1>关于我们</h1>
        </div>
        <div class="container">
            <img src="picture/aboutus.jpg" alt="关于我们图片" style="width: 50%; height: 50%;">
            <div class="about-content">
                <h2>欢迎来到 InstaKilogram</h2>
                <p>在 InstaKilogram，我们相信视觉故事的力量。作为一个社交媒体平台，我们赋予用户与世界分享他们生活瞬间、冒险和创造力的能力。不论您是专业摄影师、未来的影响者，还是喜欢捕捉日常记忆的人，InstaKilogram 是展示您的作品并与志趣相投的个人连接的完美场所。</p>
                <p>我们的使命是创建一个充满活力的社区，让人们能够表达自己，发现新的灵感，并建立有意义的联系。我们致力于提供友好的用户体验、强大的安全措施以及持续的改进，使 InstaKilogram 成为视觉内容的终极平台。</p>
            </div>
        </div>
        <h1 style="margin-top: 20px; margin-left: 45px;">我们的团队</h1>
        <div class="container" style="display: flex; flex-direction: column;">
            <div style="display: flex; flex-direction: row;">
                <img src="picture\WONG YAN XUN.jpg" alt="关于我们图片" style="flex: 0; width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>团队领导</h1>
                    <h2>WONG YAN XUN</h2>

                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\WONG KAM LOKE.jpg" alt="关于我们图片" style="flex: 0; width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>团队成员</h1>
                    <h2>WONG KAM LOKE</h2>
                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\PHUA BIAN QI.jpg" alt="关于我们图片" style="flex: 0;  width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>团队成员</h1>
                    <h2>PHUA BIAN QI</h2>
                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\Tan yanyu.jpg" alt="关于我们图片"
                    style="flex: 0;  min-width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>团队成员</h1>
                    <h2>TAN YANYU</h2>
                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\Siew Jun Long.jpg" alt="关于我们图片" style="flex: 0;  width: auto; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>团队成员</h1>
                    <h2>SIEW JUN LONG</h2>
                </div>
            </div>
            <br>
            <div style="display: flex; flex-direction: row;">
                <img src="picture\Wong kar yi.jpg" alt="关于我们图片" style="flex: 0;  width: 186px; height: 248px;">
                <div style="margin-left: 20px;">
                    <h1>团队成员</h1>
                    <h2>WONG KAR YI</h2>
                </div>
            </div>
        </div>

    </div>

</body>


</html>