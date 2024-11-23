<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['reset_user_id'])) {
  header("Location: forgot_password.php");
  exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];
  $userId = $_SESSION['reset_user_id'];

  // Check if passwords match
  if ($new_password === $confirm_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $userId);

    if ($stmt->execute()) {
      $success = "Password has been reset successfully!";
      unset($_SESSION['reset_user_id']);
    } else {
      $error = "Error updating password. Please try again.";
    }
  } else {
    $error = "Passwords do not match. Please try again.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <!-- Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <style>
    body {
      background-size: cover;
      font-family: "Eczar", serif;
      font-optical-sizing: auto;
      font-style: normal;
    }
  </style>
</head>

<body>
  <div class="limiter">
    <div class="container-login100" style="background:url('./images/back.jpg'); background-size:cover; background-repeat:no-repeat;">
      <div class="wrap-login100" style="height:80vh;">
        <div class="login100-pic js-tilt" data-tilt>
          <img src="./BlackLogoo.png" alt="IMG" style="margin-top:-80px;">
        </div>

        <form id="resetPasswordForm" class="login100-form validate-form" style="margin-top:-80px;" method="POST">
          <span class="login100-form-title">
            Reset Password
          </span>

          <div class="wrap-input100 validate-input" data-validate="Password is required">
            <input class="input100" type="password" name="new_password" id="new_password" placeholder="Enter new password" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
          </div>

          <div class="wrap-input100 validate-input" data-validate="Confirm password is required">
            <input class="input100" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
          </div>

          <div class="text-center mb-3">
            <input type="checkbox" onclick="togglePasswordVisibility()" class="mr-1"> Show Passwords
          </div>

          <div class="container-login100-form-btn">
            <button type="submit" class="login100-form-btn">
              Reset Password
            </button>
          </div>

          <?php if ($error): ?>
            <p class="text-danger text-center mt-3"><?php echo htmlspecialchars($error); ?></p>
          <?php elseif ($success): ?>
            <p class="text-success text-center mt-3"><?php echo htmlspecialchars($success); ?></p>
          <?php endif; ?>

          <div class="text-center p-t-136">
            <a class="txt2" href="index.php">
              <i class="fa fa-arrow-left" aria-hidden="true"> Back to Login</i>
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JavaScript & jQuery -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tilt.js/1.2.1/tilt.jquery.min.js"></script>

  <script>
    // Initialize Tilt Effect
    $('.js-tilt').tilt({
      scale: 1.1
    });

    // Toggle Password Visibility
    function togglePasswordVisibility() {
      const newPassword = document.getElementById("new_password");
      const confirmPassword = document.getElementById("confirm_password");
      const type = newPassword.type === "password" ? "text" : "password";
      newPassword.type = type;
      confirmPassword.type = type;
    }
  </script>
</body>

</html>