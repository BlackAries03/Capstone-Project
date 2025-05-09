<?php include 'getName.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="sidebarStyle.css">
  <title>Privacy and Policy</title>
  <style>
    .content {
        margin-left: 300px;
        height: 170vh;
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

    .back {
        margin-top: 10px;
    }

    /* Dark theme styles */
    .dark .content {
        background: #2e2e2e; /* Dark background color */
    }

    .dark #title {
        opacity: 1;
    }

    .dark #title h1 {
        margin-left: 10px;
        color: #e1e1e1; /* Dark theme text color */
    }

    .dark .container {
        background: #3c3c3c; /* Dark container background */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5); /* Darker box shadow */
        color: #e1e1e1; /* Dark theme text color */
    }

    .dark .back {
        margin-top: 10px;
        color: #e1e1e1; /* Dark theme text color */
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
      <a href="_Settings.php"><img src="picture\back-button.png" alt="back" style="width: 30px; height: 30px;"
          class="back">
      </a>
      <h1>Privacy & Policy</h1>
    </div>
    <div class="container">
      <h1>Privacy Policy</h1>
      <br>
      <p>
        <b>Data Collection</b>: "We collect information that you provide directly to us, such as when you
        create or modify your account, participate in interactive features, and request customer support. The
        types of information wemay collect include your name, email address, phone number, profile photo, posts,
        comments, and any other information you choose to provide.
      </p>
      <br>
      <p>
        <b>Data Usage</b>: "We use the information we collect to provide, maintain, and improve our services,
        including to
        personalize your experience, provide customer support, and communicate with you about products,
        services,
        and offers."
      </p>
      <br>
      <p>
        <b>Data Sharing</b>: "We may share your information with third-party vendors, service providers, and
        partners who
        perform services on our behalf, such as payment processing, data analysis, and customer service. We
        require
        these third parties to protect your information and only use it for the purposes we specify."
      </p>
      <br>
      <p>
        <b>User Rights</b>: "You have the right to access, correct, delete, or transfer your personal data. You
        can
        exercise these rights by contacting us through the provided channels in the 'Contact Us' section of this
        policy."
      </p>
      <br>
      <p>
        <b>Data Security</b>: "We implement a variety of security measures to safeguard your personal
        information
        from
        unauthorized access, use, or disclosure. These measures include encryption, access controls, and regular
        security assessments."
      </p>
      <br>
      <hr>

      <br>
      <h1>Terms of Service</h1>
      <br>
      <p>
        <b>Account Registration</b>: "To use our services, you must create an account by providing accurate and
        complete
        information. You are responsible for maintaining the confidentiality of your account credentials and for
        all
        activities that occur under your account."
      </p>
      <br>
      <p>
        <b>User Conduct</b>: "You agree not to engage in any activity that is harmful, offensive, or illegal.
        This
        includes
        harassment, hate speech, and the posting of content that infringes on others' intellectual property
        rights."
      </p>
      <br>
      <p>
        <b>Content Ownership</b>: "You retain ownership of the content you create and share on our platform.
        However, by
        posting content, you grant us a non-exclusive, royalty-free, worldwide license to use, display, and
        distribute your content for the purposes of operating and promoting our services."
      </p>
      <br>
      <p>
        <b>Termination of Service</b>: "We reserve the right to suspend or terminate your account if you violate
        these
        terms or engage in conduct that we deem harmful to the community or our services."
      </p>
      <br>
      <p>
        <b>Liability Limitations</b>: "We are not liable for any damages or losses that result from your use of
        our
        services. Our total liability to you for any claims arising from your use of our services is limited to
        the
        amount you have paid us in the past twelve months."
      </p>
      <br>
    </div>
  </div>
</body>

</html>