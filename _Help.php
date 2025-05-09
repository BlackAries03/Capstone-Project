<?php include 'getName.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="sidebarStyle.css">
  <title>HELP</title>
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
<?php include("sidebar.php"); ?>

  <div class="content">
    <div id="title">
      <a href="_Settings.php"><img src="picture/back-button.png" alt="back" style="width: 30px; height: 30px;" class="back">
      </a>
      <h1>HELP</h1>
    </div>
    <div class="container">
      <div class="dropdown">
        <h2>Managing Your Profile</h2>
        <div class="dropdown-content">
          <h3>1. Updating Profile Information:</h3>
          <ol>
            <li>Go to your <a href="_Settings.php">Settings</a> page.</li>
            <li>Click on "Account Management".</li>
            <li>Update your information, such as your username, profile picture, and contact details.</li>
            <li>Click "Submit" to apply the changes.</li>
          </ol>

          <h3>2. Delete Account:</h3>
          <ol>
            <li>Go to your <a href="_Settings.php">Settings</a> page.</li>
            <li>Select "Delete Account".</li>
            <li>Confirm your action.</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>Using the Platform</h2>
        <div class="dropdown-content">
          <h3>1. Creating Posts:</h3>
          <ol>
            <li>Click on the "Create Post" button.</li>
            <li>Write your post in the text box.</li>
            <li>Add any media (photos, videos) by clicking on the "Add Media" button.</li>
            <li>Click "Post" to publish your content.</li>
          </ol>

          <h3>2. Interacting with Posts:</h3>
          <ul>
            <li><strong>Liking a Post:</strong> Click the "Like" button below the post.</li>
            <li><strong>Commenting:</strong> Click on the "Comment" button, write your comment, and click "Submit".</li>
            <li><strong>Sharing:</strong> Click the "Share" button to share the post on your profile or with friends.</li>
          </ul>
        </div>
      </div>

      <div class="dropdown">
        <h2>Messaging and Notifications</h2>
        <div class="dropdown-content">
          <h3>1. Sending Messages:</h3>
          <ol>
            <li>Go to the "Messages" section.</li>
            <li>Click on "New Message".</li>
            <li>Select the recipient from your friends list.</li>
            <li>Write your message and click "Send".</li>
          </ol>

          <h3>2. Managing Notifications:</h3>
          <ol>
            <li>Go to the "Notifications" section.</li>
            <li>Click on the notification settings icon.</li>
            <li>Customize your notification preferences (e.g., email notifications, push notifications).</li>
            <li>Click "Save" to apply the changes.</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>Account Security</h2>
        <div class="dropdown-content">
          <h3>1. Changing Password:</h3>
          <ol>
            <li>Go to the "Settings" menu.</li>
            <li>Select "Account Settings".</li>
            <li>Click on "Change Password".</li>
            <li>Enter your current password and your new password.</li>
            <li>Click "Save" to update your password.</li>
          </ol>

          <h3>2. Two-Factor Authentication:</h3>
          <ol>
            <li>Go to the "Security Settings" menu.</li>
            <li>Select "Two-Factor Authentication".</li>
            <li>Follow the instructions to enable two-factor authentication for your account.</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>Troubleshooting</h2>
        <div class="dropdown-content">
          <h3>1. Common Issues:</h3>
          <ul>
            <li><strong>Can't log in:</strong> Ensure your email and password are correct. If you've forgotten your password, click on "Forgot Password" to reset it.</li>
            <li><strong>Account hacked:</strong> Contact our support team immediately to secure your account.</li>
            <li><strong>Features not working:</strong> Clear your browser cache and ensure you're using the latest version of the browser.</li>
          </ul>

          <h3>2. Contact Support:</h3>
          <ul>
            <li><a href="mailto:adam@gmail.com"><strong>Email:</strong> adam@gmail.com</a></li>
            <li><a href="https://wa.me/+601234567890"><strong>Phone:</strong> +60 1234567890</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
