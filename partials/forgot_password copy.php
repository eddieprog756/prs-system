<?php
session_start();
require_once 'config/db.php'; // Ensure this file contains the database connection code
require 'vendor/autoload.php'; // PHPMailer autoload file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $email)) {
    $_SESSION['error'] = 'Invalid email format. Please use an email ending with .com.';
    header('Location: forgot_password.php');
    exit();
  }

  // Check if email exists in the database
  $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $userId = $user['id'];

    // Generate a unique token
    $token = bin2hex(random_bytes(32));
    $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Store the token in the database with expiration time
    $stmt = $con->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $token, $expires_at);
    $stmt->execute();

    // Send the reset email
    $mail = new PHPMailer(true);

    try {
      //Server settings
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
      $mail->SMTPAuth = true;
      $mail->Username = 'ed.eddie756@gmail.com'; // Your Gmail address
      $mail->Password = 'dzubdkcvuemfjkvj'; // App password for your Gmail
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      // Recipients
      $mail->setFrom('support@prs.ac.mw', 'PRSYSTEM ADMIN');
      $mail->addAddress($email);

      // Content
      $mail->isHTML(true);
      $mail->Subject = 'Password Reset Request';
      $mail->Body    = "Click <a href='http://localhost/prsystem/reset_password.php?token=$token'>here</a> to reset your password. This link will expire in 1 hour.";

      $mail->send();
      $_SESSION['success'] = 'A password reset link has been sent to your email.';
    } catch (Exception $e) {
      $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  } else {
    $_SESSION['error'] = 'Error 404, Please Go Home👀';
  }

  header('Location: forgot_password.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/login.css">
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h2>Forgot Password</h2>
        <form method="POST" action="forgot_password.php">
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
          <?php endif; ?>
          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
              <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
          <?php endif; ?>
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
          </div>
          <button type="submit" class="btn btn-primary">Reset Password</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>