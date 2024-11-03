<?php
session_start();
require_once 'config/db.php';

if (!isset($_GET['token'])) {
  $_SESSION['error'] = 'Invalid or expired token.';
  header('Location: forgot_password.php');
  exit();
}

$token = $_GET['token'];
$stmt = $con->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();
  $userId = $user['user_id'];
} else {
  $_SESSION['error'] = 'Invalid or expired token.';
  header('Location: forgot_password.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirm_password'];

  if ($password !== $confirmPassword) {
    $_SESSION['error'] = 'Passwords do not match.';
    header("Location: reset_password.php?token=$token");
    exit();
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
  $stmt->bind_param("si", $hashedPassword, $userId);
  $stmt->execute();

  $stmt = $con->prepare("DELETE FROM password_resets WHERE user_id = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();

  $_SESSION['success'] = 'Password reset successfully. You can now log in.';
  header('Location: index.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <style>
    body {
      background: #f8f9fa;
      font-family: Arial, sans-serif;
    }

    .container {
      max-width: 500px;
      margin-top: 50px;
    }

    .card {
      padding: 20px;
    }

    .password-input {
      position: relative;
    }

    .password-input .fa-eye,
    .password-input .fa-eye-slash {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
    }

    .alert {
      display: none;
    }
  </style>
</head>

<body>
  <div class="container">
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger" role="alert">
        <?= $_SESSION['error'] ?>
      </div>
    <?php unset($_SESSION['error']);
    endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success" role="alert">
        <?= $_SESSION['success'] ?>
      </div>
    <?php unset($_SESSION['success']);
    endif; ?>

    <div class="card">
      <h3 class="text-center">Reset Password</h3>
      <form method="POST" action="reset_password.php?token=<?= htmlspecialchars($token) ?>">
        <div class="form-group password-input">
          <label for="password">New Password</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
          <i class="fa fa-eye" id="togglePassword" style="color: #6c757d;"></i>
          <small class="form-text text-muted">Password should be at least 8 characters long.</small>
        </div>
        <div class="form-group password-input">
          <label for="confirm_password">Confirm New Password</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
          <i class="fa fa-eye" id="toggleConfirmPassword" style="color: #6c757d;"></i>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
      </form>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.alert').fadeIn(500).delay(3000).fadeOut(500);

      $('#togglePassword').on('click', function() {
        const password = $('#password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
      });

      $('#toggleConfirmPassword').on('click', function() {
        const confirmPassword = $('#confirm_password');
        const type = confirmPassword.attr('type') === 'password' ? 'text' : 'password';
        confirmPassword.attr('type', type);
        $(this).toggleClass('fa-eye fa-eye-slash');
      });
    });
  </script>
</body>

</html>