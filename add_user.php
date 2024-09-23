<?php
session_start();
require 'vendor/autoload.php'; // PHPMailer autoload file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include './config/db.php';

// Add user logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  $username = $_POST['username'];
  $password = $_POST['password']; // Store plain text password to send via email
  $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password for storing
  $role = $_POST['role'];
  $email = $_POST['email'];
  $full_name = $_POST['full_name'];
  $created_at = date('Y-m-d H:i:s'); // Set the current time

  // Debugging: Error handling for SQL query
  $stmt = $con->prepare("INSERT INTO users (username, password, role, email, full_name, created_at) VALUES (?, ?, ?, ?, ?, ?)");
  if ($stmt === false) {
    $_SESSION['error'] = "SQL Error: " . $con->error;
    header('Location: ' . basename($_SERVER['PHP_SELF']));
    exit();
  }

  $stmt->bind_param("ssssss", $username, $hashed_password, $role, $email, $full_name, $created_at);

  if ($stmt->execute()) {
    // Send email with plain text password to the user's email
    $mail = new PHPMailer(true);
    try {
      // Server settings
      $mail->isSMTP();
      $mail->Host = "smtp.gmail.com";
      $mail->SMTPAuth = true;
      $mail->Username = "ed.eddie756@gmail.com";  // Your Gmail username
      $mail->Password = "dzubdkcvuemfjkvj";       // Your Gmail password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      // Recipients
      $mail->setFrom('prsystem@yourdomain.com', 'Admin');
      $mail->addAddress($email); // Send to the user's email

      // Content
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

  // Refresh the page to reflect changes
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
  <title>Add Users</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>

  <!-- Bootstrap CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Eczar:wght@400..800&display=swap" rel="stylesheet">
  <style>
    .container {
      margin-top: 50px;
    }

    .adduser-container {
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 10px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    .table-group-divider {
      margin-top: 10px;
    }

    .form-header {
      text-align: center;
      font-size: 24px;
      font-weight: bold;
    }

    .error-message {
      color: red;
    }

    .success-message {
      color: green;
    }
  </style>
</head>

<body>
  <?php include './sidebar.php'; ?>
  <div class="container">
    <div class="row">
      <!-- Users Table -->
      <div class="container mt-5" style="margin-left: 200px; width:1100px;">
        <h3 class="text-center">Users Lists</h3>
        <table class="table table-bordered text-center col">
          <thead class="bg-success text-white">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Username</th>
              <th scope="col">Role</th>
              <th scope="col">Email</th>
              <th scope="col">Full Name</th>
              <th scope="col">Created At</th>
            </tr>
          </thead>
          <tbody class="table-group-divider">
            <?php if (!empty($users)) : ?>
              <?php foreach ($users as $index => $user) : ?>
                <tr>
                  <th scope="row"><?php echo $index + 1; ?></th>
                  <td><?php echo htmlspecialchars($user['username']); ?></td>
                  <td><?php echo htmlspecialchars($user['role']); ?></td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="6">No users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="adduser-container col" style="width:50px; margin-left: 180px;">
        <h2 class="form-header">Add New User</h2>

        <?php if (isset($_SESSION['success'])) : ?>
          <p class="success-message"><?php echo $_SESSION['success'];
                                      unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
          <p class="error-message"><?php echo $_SESSION['error'];
                                    unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <!-- Add User Form -->
        <form method="POST" class="col" action="">
          <div class="row">
            <div class="form-group col">
              <label for="username">Username</label>
              <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group col">
              <label for="password">Password</label>
              <input type="password" id="password" value="strawbelly@2024" name="password" class="form-control" required>
            </div>
          </div>
          <div class="row">
            <div class="form-group col">
              <label for="role">Role</label>
              <select id="role" name="role" class="form-control" required>
                <option value="admin">Admin</option>
                <option value="designer">Designer</option>
                <option value="sales">Sales</option>
                <option value="studio">Studio</option>
                <option value="workshop">Workshop</option>
              </select>
            </div>
            <div class="form-group col">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" class="form-control" required>
            </div>
          </div>
          <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" class="form-control" required>
          </div>
          <div class="text-center">
            <button type="submit" name="submit" class="btn btn-success">Add User</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
</body>

</html>