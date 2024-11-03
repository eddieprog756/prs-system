<?php
session_start();
require_once 'config/db.php'; // Ensure this file contains the database connection code
require 'vendor/autoload.php'; // PHPMailer autoload file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle AJAX POST request for password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Set header for JSON response
  header('Content-Type: application/json');
  $response = [];

  // Retrieve and sanitize the email input
  $email = trim($_POST['email'] ?? '');

  // Validate email format and ensure it ends with .com
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $email)) {
    $response['error'] = 'Please enter a valid email address ending with .com.';
    echo json_encode($response);
    exit();
  }

  // Prepare and execute SQL statement to check if email exists
  $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
  if (!$stmt) {
    $response['error'] = 'Database error: Unable to prepare statement.';
    echo json_encode($response);
    exit();
  }
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  // If email exists, proceed to generate token and send email
  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $userId = $user['id'];

    // Generate a unique token
    $token = bin2hex(random_bytes(32));
    $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Store the token in the database with expiration time
    $stmt = $con->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
    if (!$stmt) {
      $response['error'] = 'Database error: Unable to prepare statement.';
      echo json_encode($response);
      exit();
    }
    $stmt->bind_param("iss", $userId, $token, $expires_at);
    if (!$stmt->execute()) {
      $response['error'] = 'Database error: Unable to execute statement.';
      echo json_encode($response);
      exit();
    }

    // Initialize PHPMailer and configure SMTP settings
    $mail = new PHPMailer(true);

    try {
      // Server settings
      $mail->isSMTP();
      $mail->Host       = 'smtp.gmail.com'; // Replace with your SMTP server
      $mail->SMTPAuth   = true;
      $mail->Username   = 'ed.eddie756@gmail.com'; // Replace with your SMTP username
      $mail->Password   = 'dzubdkcvuemfjkvj'; // Replace with your SMTP password or app-specific password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port       = 587;

      // Recipients
      $mail->setFrom('temboedward756@gmail.com', 'Edward Tembo'); // Replace with your "from" address
      $mail->addAddress($email); // Add recipient

      // Content
      $mail->isHTML(true);
      $mail->Subject = 'Password Reset Request';
      $resetLink = "http://localhost/prsystem/reset_password.php?token=$token"; // Replace with your actual reset link
      $mail->Body    = "Hello,<br><br>You requested a password reset. Click the link below to reset your password:<br><a href='$resetLink'>Reset Password</a><br><br>This link will expire in 1 hour.<br><br>If you did not request this, please ignore this email.";

      $mail->send();
      $response['success'] = 'A password reset link has been sent to your email.';
    } catch (Exception $e) {
      $response['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  } else {
    $response['error'] = 'Error 404, Please Go Home👀';
  }

  // Return the JSON response
  echo json_encode($response);
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <!-- Bootstrap CSS -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="./css/login.css">
  <style>
    /* Custom Styles */
    body {
      background-size: cover;
      font-family: "Eczar", serif;
      font-optical-sizing: auto;
      font-style: normal;
    }

    .modal-error {
      color: red;
    }

    .username input[type="email"] {
      border: none;
      border-bottom: 2px solid #ddd;
      background: transparent;
      font-size: 18px;
      width: 100%;
      padding: 10px 0;
    }

    .username input[type="email"]:focus {
      border-bottom: 2px solid #000;
      outline: none;
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

        <form id="forgotPasswordForm" class="login100-form validate-form" style="margin-top:-80px;" method="POST">
          <span class="login100-form-title">
            Forgot Password
          </span>

          <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
            <input class="input100" type="email" name="email" id="email" placeholder="Enter your email" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>
          </div>

          <div class="container-login100-form-btn">
            <button type="submit" class="login100-form-btn">
              Reset Password
            </button>
          </div>

          <div class="text-center p-t-136">
            <a class="txt2" href="index.php">
              <i class="fa fa-arrow-left" aria-hidden="true"> Back to Login</i>
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap Modal for Error Messages -->
  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body modal-error" id="modalErrorMessage">
          <!-- Error message will be injected here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap Modal for Success Messages -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="successModalLabel">Success</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modalSuccessMessage">
          <!-- Success message will be injected here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery and Bootstrap JS (Ensure jQuery is loaded before Bootstrap JS) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <!-- Tilt.js (Ensure you have included the tilt.js library if you're using the tilt effect) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tilt.js/1.2.1/tilt.jquery.min.js"></script>

  <!-- JavaScript and jQuery Scripts -->
  <script>
    // Initialize Tilt Effect
    $('.js-tilt').tilt({
      scale: 1.1
    });

    (function($) {
      "use strict";

      $('#forgotPasswordForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const email = $('#email').val().trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Client-side validation
        if (!emailPattern.test(email) || !email.endsWith('.com')) {
          $('#modalErrorMessage').text('Please enter a valid email address ending with .com.');
          $('#errorModal').modal('show');
        } else {
          // If email is valid, proceed with AJAX form submission
          $.ajax({
            type: 'POST',
            url: '', // Submit to the same page
            data: {
              email: email
            },
            dataType: 'json',
            success: function(response) {
              if (response.error) {
                $('#modalErrorMessage').text(response.error);
                $('#errorModal').modal('show');
              } else if (response.success) {
                $('#modalSuccessMessage').text(response.success);
                $('#successModal').modal('show');
                // Optionally, reset the form
                $('#forgotPasswordForm')[0].reset();
              }
            },
            error: function() {
              $('#modalErrorMessage').text('An unexpected error occurred. Please try again.');
              $('#errorModal').modal('show');
            }
          });
        }
      });

    })(jQuery);
  </script>
</body>

</html>