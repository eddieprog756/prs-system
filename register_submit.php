<?php
// Database connection
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $conn->real_escape_string($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role = $conn->real_escape_string($_POST['role']);

  // Check if username already exists
  $sql = "SELECT * FROM users WHERE username = '$username'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    header("Location: register.php?error=Username already exists");
  } else {
    // Insert new user into the database
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    if ($conn->query($sql) === TRUE) {
      header("Location: login.php?success=Registration successful, please login");
    } else {
      header("Location: register.php?error=Error: " . $conn->error);
    }
  }
}
