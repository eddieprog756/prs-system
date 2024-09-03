<?php
require 'vendor/autoload.php'; // Ensure you have installed PHPMailer via Composer
require_once 'config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];

  // Check if the email exists in the database
  $stmt = $con->prepare("SELECT id, full_name FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $token = bin2hex(random_bytes(50)); // Generate a unique token
    $user_id = $user['id'];

    // Save the token in the database
    $stmt = $con->prepare("INSERT INTO password_resets (user_id, token) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $token);
    $stmt->execute();

    // Send the reset link to the user's email
    $resetLink = "http://yourwebsite.com/reset_password.php?token=$token"; // Adjust the URL as necessary
    $mail = new PHPMailer(true);

    try {
      $mail->Host = "smtp.gmail.com";
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Username = "ed.eddie756@gmail.com";
      $mail->Password = "dzubdkcvuemfjkvj";
      $mail->Port = 587;


      $mail->setFrom('prsadmin@gmail.com', 'PRS admin');
      $mail->addAddress($email, $user['full_name']);

      $mail->isHTML(true);
      $mail->Subject = 'Password Reset Request';
      $mail->Body = "Hello {$user['full_name']},<br><br>Click <a href='$resetLink'>here</a> to reset your password.<br><br>Thank you!";
      $mail->send();

      $response['success'] = 'A password reset link has been sent to your email.';
    } catch (Exception $e) {
      $response['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  } else {
    $response['error'] = 'No account found with that email address.';
  }

  echo json_encode($response);
}
