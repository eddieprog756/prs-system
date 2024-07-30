<?php
session_start();

//Include database connection
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $con->real_escape_string($_POST['username']);
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE username = '$username'";
  $result = $con->query($sql);

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];

      switch ($user['role']) {
        case 'admin':
          header("Location: studio.php");
          break;
        case 'designer':
          header("Location: designer_home.php");
          break;
        case 'sales':
          header("Location: sales_home.php");
          break;
        default:
          header("Location: login.php?error=Invalid role");
      }
    } else {
      header("Location: login.php?error=Invalid password");
    }
  } else {
    header("Location: login.php?error=User not found");
  }
}
