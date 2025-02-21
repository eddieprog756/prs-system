<?php
session_start();
require 'vendor/autoload.php'; // PHPMailer autoload file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include './config/db.php';

// Handle user deactivation
if (isset($_GET['deactivate_user'])) {
  $user_id = (int)$_GET['deactivate_user'];
  $stmt = $con->prepare("UPDATE users SET status = 0 WHERE id = ?");
  if ($stmt) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
      $_SESSION['success'] = "User deactivated successfully.";
    } else {
      $_SESSION['error'] = "Failed to deactivate user.";
    }
    $stmt->close();
  } else {
    $_SESSION['error'] = "Database error: Unable to prepare deactivation statement.";
  }
  header('Location: ' . basename($_SERVER['PHP_SELF']));
  exit();
}
// Handle user reactivation
if (isset($_GET['activate_user'])) {
  $user_id = (int)$_GET['activate_user'];

  // Fetch user details for email notification
  $stmt_fetch = $con->prepare("SELECT email, full_name FROM users WHERE id = ?");
  $stmt_fetch->bind_param("i", $user_id);
  $stmt_fetch->execute();
  $user_details = $stmt_fetch->get_result()->fetch_assoc();
  $stmt_fetch->close();

  if ($user_details) {
    $email = $user_details['email'];
    $full_name = $user_details['full_name'];

    // Reactivate user
    $stmt = $con->prepare("UPDATE users SET status = 1 WHERE id = ?");
    if ($stmt) {
      $stmt->bind_param("i", $user_id);
      if ($stmt->execute()) {
        // Send reactivation email
        $mail = new PHPMailer(true);
        try {
          $mail->isSMTP();
          $mail->Host = 'smtp.gmail.com';
          $mail->SMTPAuth = true;
          $mail->Username = 'codeverse.mw@gmail.com'; // Your email
          $mail->Password = 'mdgfjvupuabqavpp'; // Your email password
          $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
          $mail->Port = 587;

          $mail->setFrom('prsystem@yourdomain.com', 'Admin');
          $mail->addAddress($email);

          $mail->isHTML(true);
          $mail->Subject = 'Account Reactivated';
          $mail->Body = "Dear $full_name,<br><br>Your account has been successfully reactivated. You can now log in.<br><br>Best Regards,<br>PRS";

          $mail->send();
          $_SESSION['success'] = "User reactivated successfully and email notification sent.";
        } catch (Exception $e) {
          $_SESSION['error'] = "User reactivated, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
      } else {
        $_SESSION['error'] = "Failed to reactivate user.";
      }
      $stmt->close();
    } else {
      $_SESSION['error'] = "Database error: Unable to prepare activation statement.";
    }
  } else {
    $_SESSION['error'] = "User not found.";
  }

  header('Location: ' . basename($_SERVER['PHP_SELF']));
  exit();
}

// Pagination setup
$limit = 5; // Number of users per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Handle search functionality
$search_term = '';
$search_condition = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
  $search_term = trim($_POST['search_term']);
  if (!empty($search_term)) {
    $search_condition = "AND (username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
  }
}

// Prepare SQL query for active users
$query_active = "SELECT * FROM users WHERE status = 1 $search_condition ORDER BY created_at DESC LIMIT ?, ?";
$stmt_active = $con->prepare($query_active);
if (!empty($search_condition)) {
  $search_like_term = '%' . $search_term . '%';
  $stmt_active->bind_param("sssii", $search_like_term, $search_like_term, $search_like_term, $start, $limit);
} else {
  $stmt_active->bind_param("ii", $start, $limit);
}
$stmt_active->execute();
$result_active = $stmt_active->get_result();
$active_users = $result_active->fetch_all(MYSQLI_ASSOC);

// Prepare SQL query for deactivated users
$query_inactive = "SELECT * FROM users WHERE status = 0 $search_condition ORDER BY created_at DESC LIMIT ?, ?";
$stmt_inactive = $con->prepare($query_inactive);
if (!empty($search_condition)) {
  $stmt_inactive->bind_param("sssii", $search_like_term, $search_like_term, $search_like_term, $start, $limit);
} else {
  $stmt_inactive->bind_param("ii", $start, $limit);
}
$stmt_inactive->execute();
$result_inactive = $stmt_inactive->get_result();
$inactive_users = $result_inactive->fetch_all(MYSQLI_ASSOC);

// Count total users for pagination
$query_total = "SELECT COUNT(*) AS total FROM users WHERE status = 1";
$total_active_users = $con->query($query_total)->fetch_assoc()['total'];
$total_pages = ceil($total_active_users / $limit);
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

    .pagination {
      justify-content: center;
    }
  </style>
</head>

<body style="background-image: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url('./Images/bg.JPG'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 100vh; overflow: hidden;">
  <?php include './sidebar.php'; ?>
  <div class="container" style="margin-left: 150px; width:1100px; margin-top:0;">


    <!-- Users List -->
    <div class="row">

      <div class="container mt-5 shadow-lg p-4" style="background-color: #f4f6f9; border-radius: 10px; margin-left: 200px; width:1000px;">
        <h3 class="text-center text-dark" style="font-weight: 700; font-size: 1.5rem;">Users Lists</h3>

        <?php if (isset($_SESSION['success'])) : ?>
          <div class="alert alert-success" role="alert"><?php echo $_SESSION['success'];
                                                        unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])) : ?>
          <div class="alert alert-danger" role="alert"><?php echo $_SESSION['error'];
                                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Search Box -->
        <form method="POST" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="row g-3 justify-content-center">
          <div class="col-md-6" style="margin-top: 10px;">
            <div class="input-group">
              <input type="search" class="form-control" name="search_term" placeholder="Search users" value="<?php echo htmlspecialchars($search_term); ?>">
              <div class="input-group-append">
                <button type="submit" name="search" class="btn btn-dark"><i class="fa fa-search"></i></button>
              </div>
            </div>
          </div>
        </form>

        <!-- Active Users -->
        <h5 class="text-success">Active Users</h5>
        <table class="table table-hover text-center">
          <thead style="background-color: #343a40; color: #ffffff;">
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Role</th>
              <th>Email</th>
              <th>Full Name</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($active_users)) : ?>
              <?php foreach ($active_users as $index => $user) : ?>
                <tr>
                  <td><?php echo $start + $index + 1; ?></td>
                  <td><?php echo htmlspecialchars($user['username']); ?></td>
                  <td><?php echo htmlspecialchars($user['role']); ?></td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                  <td>
                    <a href="?deactivate_user=<?php echo $user['id']; ?>" class="btn btn-outline-danger btn-sm">Deactivate</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="7" class="text-muted">No active users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- Deactivated Users -->
        <h5 class="text-danger mt-4">Deactivated Users</h5>
        <table class="table table-hover text-center">
          <thead style="background-color: #343a40; color: #ffffff;">
            <tr>
              <th>#</th>
              <th>Username</th>
              <th>Role</th>
              <th>Email</th>
              <th>Full Name</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($inactive_users)) : ?>
              <?php foreach ($inactive_users as $index => $user) : ?>
                <tr>
                  <td><?php echo $start + $index + 1; ?></td>
                  <td><?php echo htmlspecialchars($user['username']); ?></td>
                  <td><?php echo htmlspecialchars($user['role']); ?></td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                  <td>
                    <!-- Activate Button with Bootstrap Modal -->
                    <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#activateModal<?php echo $user['id']; ?>">Activate</button>

                    <!-- Bootstrap Modal -->
                    <div class="modal fade" id="activateModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="activateModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="activateModalLabel<?php echo $user['id']; ?>">Confirm Activation</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to activate <strong><?php echo htmlspecialchars($user['username']); ?></strong>'s account?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <a href="?activate_user=<?php echo $user['id']; ?>" class="btn btn-success">Yes, Activate</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else : ?>
              <tr>
                <td colspan="7" class="text-muted">No deactivated users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

      </div>

    </div>
  </div>



  <!-- Bootstrap Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
</body>

</html>