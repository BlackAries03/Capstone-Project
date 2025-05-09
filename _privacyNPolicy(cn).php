<?php include 'getName(cn).php'; ?>
<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="sidebarStyle.css">
  <title>隐私政策</title>
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
<?php include("sidebar(cn).php"); ?>

  <div class="content">
    <div id="title">
      <a href="_Settings(cn).php"><img src="picture\back-button.png" alt="返回" style="width: 30px; height: 30px;"
          class="back">
      </a>
      <h1>隐私政策</h1>
    </div>
    <div class="container">
      <h1>隐私政策</h1>
      <br>
      <p>
        <b>数据收集</b>: “我们收集您直接提供给我们的信息，例如，当您创建或修改您的帐户、参与互动功能和请求客户支持时。我们可能收集的信息类型包括您的姓名、电子邮件地址、电话号码、个人资料照片、帖子、评论和您选择提供的任何其他信息。”
      </p>
      <br>
      <p>
        <b>数据使用</b>: “我们使用我们收集的信息来提供、维护和改进我们的服务，包括个性化您的体验、提供客户支持以及与您沟通产品、服务和优惠。”
      </p>
      <br>
      <p>
        <b>数据共享</b>: “我们可能会与代表我们执行服务的第三方供应商、服务提供商和合作伙伴共享您的信息，例如支付处理、数据分析和客户服务。我们要求这些第三方保护您的信息，并仅将其用于我们指定的目的。”
      </p>
      <br>
      <p>
        <b>用户权利</b>: “您有权访问、纠正、删除或转移您的个人数据。您可以通过本政策“联系我们”部分提供的渠道行使这些权利。”
      </p>
      <br>
      <p>
        <b>数据安全</b>: “我们实施了多种安全措施，以保护您的个人信息免遭未经授权的访问、使用或披露。这些措施包括加密、访问控制和定期的安全评估。”
      </p>
      <br>
      <hr>

      <br>
      <h1>服务条款</h1>
      <br>
      <p>
        <b>账户注册</b>: “要使用我们的服务，您必须通过提供准确完整的信息来创建帐户。您有责任维护您的帐户凭据的机密性，并对您帐户下发生的所有活动负责。”
      </p>
      <br>
      <p>
        <b>用户行为</b>: “您同意不从事任何有害、冒犯或非法的活动。这包括骚扰、仇恨言论以及发布侵犯他人知识产权的内容。”
      </p>
      <br>
      <p>
        <b>内容所有权</b>: “您保留您在我们平台上创建和分享的内容的所有权。但是，通过发布内容，您授予我们非排他性、免版税、全球许可，以出于运营和推广我们服务的目的使用、展示和分发您的内容。”
      </p>
      <br>
      <p>
        <b>服务终止</b>: “如果您违反这些条款或从事我们认为对社区或我们的服务有害的行为，我们保留暂停或终止您的帐户的权利。”
      </p>
      <br>
      <p>
        <b>责任限制</b>: “我们对因您使用我们的服务而导致的任何损害或损失不承担责任。对于因您使用我们的服务而产生的任何索赔，我们对您的总责任限于您在过去十二个月内向我们支付的金额。”
      </p>
      <br>
    </div>
  </div>
</body>

</html>
