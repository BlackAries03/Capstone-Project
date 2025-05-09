<?php include 'getName(cn).php'; ?>
<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="sidebarStyle.css">
  <title>帮助</title>
  <style>
    .content {
      margin-left: 300px;
      height: 100vh;
      background: antiquewhite;
    }

    #title {
      display: flex;
      opacity: 1;
    }

    #title h1 {
      margin-left: 10px;
    }

    .container {
      width: 80%;
      margin: 20px auto;
      background: rgb(198, 195, 195);
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 1rem;
    }

    .dropdown {
      margin-bottom: 10px;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .dropdown h2 {
      margin: 0;
      padding: 10px;
      cursor: pointer;
      background: #f0f0f0;
      border-bottom: 1px solid #ccc;
    }

    .dropdown-content {
      display: none;
      padding: 10px;
    }

    .dropdown-content ol,
    .dropdown-content ul {
      padding-left: 20px;
    }

    .back {
      margin-top: 10px;
    }

    /* Dark theme */
    .dark .content {
      background: #2e2e2e;
    }

    .dark #title h1 {
      color: #e1e1e1;
    }

    .dark .container {
      background: #3c3c3c;
      color: #e1e1e1;
    }

    .dark .dropdown h2 {
      background: #444;
      color: #e1e1e1;
    }

    .dark .dropdown-content {
      background:rgb(79, 77, 77);
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const theme = localStorage.getItem('theme') || 'default';
      if (theme === 'dark') {
        document.body.classList.add('dark');
      }

      document.querySelectorAll('.dropdown h2').forEach(header => {
        header.addEventListener('click', () => {
          // Close all dropdown contents
          document.querySelectorAll('.dropdown-content').forEach(content => {
            content.style.display = 'none';
          });

          // Toggle the clicked dropdown content
          const content = header.nextElementSibling;
          content.style.display = content.style.display === 'block' ? 'none' : 'block';
        });
      });
    });
  </script>
</head>

<body>
<?php include("sidebar(cn).php"); ?>

  <div class="content">
    <div id="title">
      <a href="_Settings(cn).php"><img src="picture/back-button.png" alt="返回" style="width: 30px; height: 30px;" class="back">
      </a>
      <h1>帮助</h1>
    </div>
    <div class="container">
      <div class="dropdown">
        <h2>管理您的个人资料</h2>
        <div class="dropdown-content">
          <h3>1. 更新个人资料信息：</h3>
          <ol>
            <li>转到<a href="_Settings(cn).php">设置</a>页面。</li>
            <li>点击“帐户管理”。</li>
            <li>更新您的信息，例如您的用户名、个人资料照片和联系方式。</li>
            <li>点击“提交”以应用更改。</li>
          </ol>

          <h3>2. 删除帐户：</h3>
          <ol>
            <li>转到<a href="_Settings(cn).php">设置</a>页面。</li>
            <li>选择“删除帐户”。</li>
            <li>确认您的操作。</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>使用平台</h2>
        <div class="dropdown-content">
          <h3>1. 创建帖子：</h3>
          <ol>
            <li>点击“创建帖子”按钮。</li>
            <li>在文本框中撰写您的帖子。</li>
            <li>通过点击“添加媒体”按钮添加任何媒体（照片、视频）。</li>
            <li>点击“发布”以发布您的内容。</li>
          </ol>

          <h3>2. 与帖子互动：</h3>
          <ul>
            <li><strong>点赞帖子：</strong>点击帖子下方的“赞”按钮。</li>
            <li><strong>评论：</strong>点击“评论”按钮，撰写您的评论并点击“提交”。</li>
            <li><strong>分享：</strong>点击“分享”按钮以在您的个人资料或与朋友分享帖子。</li>
          </ul>
        </div>
      </div>

      <div class="dropdown">
        <h2>消息和通知</h2>
        <div class="dropdown-content">
          <h3>1. 发送消息：</h3>
          <ol>
            <li>转到“消息”部分。</li>
            <li>点击“新消息”。</li>
            <li>从您的朋友列表中选择收件人。</li>
            <li>撰写您的消息并点击“发送”。</li>
          </ol>

          <h3>2. 管理通知：</h3>
          <ol>
            <li>转到“通知”部分。</li>
            <li>点击通知设置图标。</li>
            <li>自定义您的通知偏好（例如电子邮件通知、推送通知）。</li>
            <li>点击“保存”以应用更改。</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>帐户安全</h2>
        <div class="dropdown-content">
          <h3>1. 更改密码：</h3>
          <ol>
            <li>转到“设置”菜单。</li>
            <li>选择“帐户设置”。</li>
            <li>点击“更改密码”。</li>
            <li>输入您的当前密码和新密码。</li>
            <li>点击“保存”以更新您的密码。</li>
          </ol>

          <h3>2. 双重身份验证：</h3>
          <ol>
            <li>转到“安全设置”菜单。</li>
            <li>选择“双重身份验证”。</li>
            <li>按照说明启用帐户的双重身份验证。</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>故障排除</h2>
        <div class="dropdown-content">
          <h3>1. 常见问题：</h3>
          <ul>
            <li><strong>无法登录：</strong>确保您的电子邮件和密码正确。如果您忘记了密码，请点击“忘记密码”以重置。</li>
            <li><strong>帐户被黑客入侵：</strong>立即联系我们的支持团队以保护您的帐户。</li>
            <li><strong>功能无法使用：</strong>清除您的浏览器缓存，并确保您使用的是最新版本的浏览器。</li>
          </ul>

          <h3>2. 联系支持：</h3>
          <ul>
            <li><a href="mailto:adam@gmail.com"><strong>电子邮件：</strong> adam@gmail.com</a></li>
            <li><a href="https://wa.me/+601234567890"><strong>电话：</strong> +60 1234567890</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
