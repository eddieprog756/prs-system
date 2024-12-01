<?php
session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include './config/db.php';

// Add user logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $hashed_password = password_hash($password, PASSWORD_BCRYPT);
  $role = $_POST['role'];
  $email = $_POST['email'];
  $full_name = $_POST['full_name'];
  $created_at = date('Y-m-d H:i:s');

  $stmt = $con->prepare("INSERT INTO users (username, password, role, email, full_name, created_at) VALUES (?, ?, ?, ?, ?, ?)");
  if ($stmt === false) {
    $_SESSION['error'] = "SQL Error: " . $con->error;
    header('Location: ' . basename($_SERVER['PHP_SELF']));
    exit();
  }

  $stmt->bind_param("ssssss", $username, $hashed_password, $role, $email, $full_name, $created_at);

  if ($stmt->execute()) {
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = "smtp.gmail.com";
      $mail->SMTPAuth = true;
      $mail->Username = "ed.eddie756@gmail.com";
      $mail->Password = "dzubdkcvuemfjkvj";
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      $mail->setFrom('prsystem@strawberry.com', 'Admin');
      $mail->addAddress($email);

      $mail->isHTML(true);
      $mail->Subject = 'Welcome to the System';
      $mail->Body = "Dear $full_name,<br><br>Welcome to the system! Your login details are as follows:<br><br>Username: $username<br>Password: $password<br><br>Please change your password upon first login.<br><br>Best Regards,<br>PRS";

      $mail->send();
      $_SESSION['success'] = "User added successfully! An email with login details has been sent to $email.";
    } catch (Exception $e) {
      $_SESSION['error'] = "User added, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  } else {
    $_SESSION['error'] = "Failed to add user. SQL Error: " . $stmt->error;
  }

  $stmt->close();
  mysqli_close($con);
  header('Location: ' . basename($_SERVER['PHP_SELF']));
  exit();
}

// Fetch existing users
$stmt = $con->prepare("SELECT * FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <style>
    .container {
      margin-top: 50px;
    }

    .table-container {
      margin-top: 20px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      padding: 20px;
      background-color: #f8f9fa;
    }

    .table th {
      background-color: #77c144;
      color: #fff;
      font-weight: bold;
    }

    .table td,
    .table th {
      padding: 15px;
      text-align: center;
    }

    .form-header {
      color: #77c144;
      font-weight: bold;
      text-align: center;
      margin-bottom: 20px;
    }

    .alert {
      text-align: center;
    }
  </style>
</head>

<body style="background-image: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url('./Images/bg.JPG'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 100vh; overflow: hidden;">
  <?php include './sidebar.php'; ?>

  <div class="container">
    <div class="row justify-content-center" style="background-color: #EEEEEE; padding: 30px; width:80%; margin-left: 20%; border-radius:40px;">
      <div class="col-8">
        <h3 class="text-center text-dark">Add User</h3>

        <form method="POST" class="row g-3 needs-validation" action="" novalidate style="width: 100%;">
          <div class="col-md-6 position-relative" data-toggle="tooltip" data-placement="bottom" title="Enter a unique username">
            <label for="username" class="form-label">Username</label>
            <input type="text" id="username" name="username" class="form-control" required placeholder="Enter a unique username" style="border-radius: 20px;">
            <div class="invalid-tooltip">
              Please provide a valid username.
            </div>
          </div>

          <div class="col-md-6 position-relative" data-toggle="tooltip" data-placement="bottom" title="Password will be generated automatically">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" value="strawbelly@2024" class="form-control" required readonly style="border-radius: 20px; cursor: help;">
            <div class="invalid-tooltip">
              Please provide a password.
            </div>
          </div>

          <div class="col-md-6 position-relative" data-toggle="tooltip" data-placement="bottom" title="Select a role for the user">
            <label for="role" class="form-label">Role</label>
            <select id="role" name="role" class="form-select" required style="border-radius: 20px;">
              <option selected disabled value="">Choose role...</option>
              <option value="admin">Admin</option>
              <option value="designer">Designer</option>
              <option value="sales">sales</option>
              <option value="studio">Studio</option>
              <option value="workshop">Workshop</option>
            </select>
            <div class="invalid-tooltip">
              Please select a role.
            </div>
          </div>

          <div class="col-md-6 position-relative" data-toggle="tooltip" data-placement="bottom" title="Enter a valid email address">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required placeholder="Enter a valid email" style="border-radius: 20px;">
            <div class="invalid-tooltip">
              Please provide a valid email.
            </div>
          </div>

          <div class="col-md-12 position-relative" data-toggle="tooltip" data-placement="bottom" title="Enter the full name of the user">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" id="full_name" name="full_name" class="form-control" required placeholder="Enter the full name" style="border-radius: 20px;">
            <div class="invalid-tooltip">
              Please provide the full name.
            </div>
          </div>

          <div class="col-12 text-center">
            <button type="submit" name="submit" class="btn btn-success" style="border-radius: 30px; padding: 10px 20px;">Add User</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Bootstrap form validation
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
</body>

</html>