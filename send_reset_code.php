<?php
session_start();
require_once 'config/db.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['error'] = 'Invalid email address.';
    echo json_encode($response);
    exit();
  }

  // Check if email exists
  $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
  if (!$stmt) {
    $response['error'] = 'Database error: unable to prepare statement.';
    echo json_encode($response);
    exit();
  }
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $userId = $user['id'];

    // Generate a 6-digit verification code and expiration time
    $code = mt_rand(100000, 999999);
    $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Insert or update code in password_resets table
    $stmt = $con->prepare("REPLACE INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $code, $expires_at);
    $stmt->execute();

    // Send email with PHPMailer
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host       = 'smtp.gmail.com';
      $mail->SMTPAuth   = true;
      $mail->Username   = 'your-email@gmail.com';
      $mail->Password   = 'your-email-password';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port       = 587;

      $mail->setFrom('your-email@gmail.com', 'Support');
      $mail->addAddress($email);

      $mail->isHTML(true);
      $mail->Subject = 'Your Password Reset Code';
      $mail->Body    = "Your password reset code is <strong>$code</strong>. This code will expire in 1 hour.";

      $mail->send();
      $response['success'] = 'Verification code sent to your email.';
    } catch (Exception $e) {
      $response['error'] = 'Could not send email. Please try again.';
    }
  } else {
    $response['error'] = 'Email not found.';
  }

  echo json_encode($response);
}
