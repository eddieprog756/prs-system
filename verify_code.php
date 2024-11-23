<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $code = $_POST['code'] ?? '';
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if ($new_password !== $confirm_password) {
    $error = 'Passwords do not match.';
  } else {
    // Check if the code is valid and not expired
    $stmt = $con->prepare("SELECT user_id FROM password_resets WHERE code = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $userId = $result->fetch_assoc()['user_id'];

      // Hash and update the password
      $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
      $updateStmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
      $updateStmt->bind_param("si", $hashed_password, $userId);
      $updateStmt->execute();

      // Delete the reset code after successful password reset
      $deleteStmt = $con->prepare("DELETE FROM password_resets WHERE user_id = ?");
      $deleteStmt->bind_param("i", $userId);
      $deleteStmt->execute();

      $_SESSION['success'] = 'Password reset successfully. You can now log in.';
      header("Location: login.php");
      exit();
    } else {
      $error = 'Invalid or expired code.';
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <link rel="stylesheet" href="./css/login.css">
  <style>
    body {
      background-size: cover;
      font-family: "Eczar", serif;
    }
  </style>
</head>

<body>
  <div class="limiter">
    <div class="container-login100" style="background:url('./images/bg.jpg'); background-size:cover;">
      <div class="wrap-login100" style="display: flex; align-items: center;">
        <form id="verifyCodeForm" class="login100-form validate-form" method="POST">
          <span class="login100-form-title">Reset Password</span>

          <div class="wrap-input100 validate-input" data-validate="Enter a valid code">
            <input class="input100" type="text" name="code" placeholder="Enter verification code" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100"><i class="fa fa-key" aria-hidden="true"></i></span>
          </div>

          <div class="wrap-input100 validate-input" data-validate="Enter new password">
            <input class="input100" type="password" name="new_password" placeholder="Enter new password" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100"><i class="fa fa-lock" aria-hidden="true"></i></span>
          </div>

          <div class="wrap-input100 validate-input" data-validate="Confirm new password">
            <input class="input100" type="password" name="confirm_password" placeholder="Confirm new password" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100"><i class="fa fa-lock" aria-hidden="true"></i></span>
          </div>

          <div class="container-login100-form-btn">
            <button type="submit" class="login100-form-btn">Reset Password</button>
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
</body>

</html>