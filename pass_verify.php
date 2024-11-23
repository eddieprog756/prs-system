<?php
session_start();
require_once 'config/db.php';
require 'vendor/autoload.php';

$error = '';
$secretKey = '6LdzkTQqAAAAAH6kQec9W42PFOYH_mnNIdwDINMa'; // Replace with your reCAPTCHA secret key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve form inputs
  $code = $_POST['code'] ?? '';
  $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

  // Verify reCAPTCHA
  if (!empty($recaptchaResponse)) {
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
    $responseKeys = json_decode($verifyResponse, true);

    if ($responseKeys['success']) {
      // Check if the code matches any encrypted code in the database
      $stmt = $con->prepare("SELECT user_id, code FROM password_resets WHERE expires_at > NOW()");
      if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();

        $isValid = false;
        $userId = null;

        while ($row = $result->fetch_assoc()) {
          // Compare the entered code with each stored encrypted code
          if (password_verify($code, $row['code'])) {
            $isValid = true;
            $userId = $row['user_id'];
            break;
          }
        }

        if ($isValid) {
          // Store user ID in session and redirect to new_password.php
          $_SESSION['reset_user_id'] = $userId;
          header("Location: new_password.php");
          exit();
        } else {
          $error = "Invalid or expired verification code.";
        }
      } else {
        $error = "Database error: Unable to prepare statement.";
      }
    } else {
      $error = 'reCAPTCHA verification failed. Please try again.';
    }
  } else {
    $error = 'Please complete the reCAPTCHA verification.';
  }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <link rel="stylesheet" href="./css/login.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <style>
    body {
      background-size: cover;
      font-family: "Eczar", serif;
    }

    .modal-error {
      color: red;
    }
  </style>
</head>

<body>
  <div class="limiter">
    <div class="container-login100" style="background:url('./images/bg.jpg'); background-size:cover; background-repeat:no-repeat;">
      <div class="wrap-login100" style="height:70vh; display: flex; align-items: center; justify-content: center;">
        <form id="verifyCodeForm" class="login100-form validate-form" method="POST" action="" style="margin-top:-90px;">

          <span class="login100-form-title">
            Enter Verification Code
          </span>

          <div class="wrap-input100 validate-input" data-validate="Enter a valid code">
            <input class="input100" type="text" name="code" id="code" placeholder="Enter verification code" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <i class="fa fa-key" aria-hidden="true"></i>
            </span>
          </div>

          <!-- reCAPTCHA widget -->
          <div class="g-recaptcha mb-3" data-sitekey="6LdzkTQqAAAAALHRWd6QUWoOAYhTLvglKiGc7a4P"></div>

          <div class="container-login100-form-btn">
            <button type="submit" class="login100-form-btn">
              Verify Code
            </button>
          </div>

          <div class="text-center p-t-16">
            <a class="txt2" href="forgot_password.php">
              <i class="fa fa-arrow-left" aria-hidden="true"> Back to Forgot Password</i>
            </a>
          </div>

          <?php if (!empty($error)): ?>
            <p class="text-danger text-center mt-3"><?php echo htmlspecialchars($error); ?></p>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tilt.js/1.2.1/tilt.jquery.min.js"></script>
  <script>
    $('.js-tilt').tilt({
      scale: 1.1
    });
  </script>
</body>

</html>