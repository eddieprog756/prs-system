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

// Edit user logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
  $user_id = $_POST['user_id'];
  $username = $_POST['username'];
  $role = $_POST['role'];
  $email = $_POST['email'];
  $full_name = $_POST['full_name'];

  $stmt = $con->prepare("UPDATE users SET username = ?, role = ?, email = ?, full_name = ? WHERE id = ?");
  if ($stmt === false) {
    $_SESSION['error'] = "SQL Error: " . $con->error;
    header('Location: ' . basename($_SERVER['PHP_SELF']));
    exit();
  }

  $stmt->bind_param("ssssi", $username, $role, $email, $full_name, $user_id);

  if ($stmt->execute()) {
    $_SESSION['success'] = "User updated successfully!";
  } else {
    $_SESSION['error'] = "Failed to update user. SQL Error: " . $stmt->error;
  }

  $stmt->close();
  mysqli_close($con);

  header('Location: ' . basename($_SERVER['PHP_SELF']));
  exit();
}

// Delete user logic
if (isset($_GET['delete_user'])) {
  $user_id = $_GET['delete_user'];

  $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
  if ($stmt === false) {
    $_SESSION['error'] = "SQL Error: " . $con->error;
    header('Location: ' . basename($_SERVER['PHP_SELF']));
    exit();
  }

  $stmt->bind_param("i", $user_id);

  if ($stmt->execute()) {
    $_SESSION['success'] = "User deleted successfully!";
  } else {
    $_SESSION['error'] = "Failed to delete user. SQL Error: " . $stmt->error;
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
  <div class="container" style="margin-left: 150px; width:1100px;">
    <div class="row">
      <!-- Users Table -->
      <div class="container mt-5" style="margin-left: 200px; width:1000px;">
        <h3 class="text-center text-success fs-8" style="font-weight:600;">Users Lists</h3>
        <?php if (isset($_SESSION['success'])) : ?>
          <div class="alert alert-success"><?php echo $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])) : ?>
          <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                          unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <table class="table table-bordered text-center col">
          <thead class="bg-success text-white">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Username</th>
              <th scope="col">Role</th>
              <th scope="col">Email</th>
              <th scope="col">Full Name</th>
              <th scope="col">Created At</th>
              <th scope="col">Actions</th>
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
                  <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete_user=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="7">No users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- <div class="row">
      <div class="adduser-container col" style="width:50px; margin-left: 180px;">
        <h2 class="form-header">Add New User</h2>
      </div>
    </div> -->
  </div>

  <!-- Bootstrap Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
</body>

</html>