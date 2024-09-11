<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $recaptchaResponse = $_POST['g-recaptcha-response'];

  // Verify reCAPTCHA
  $secretKey = '6LdzkTQqAAAAAH6kQec9W42PFOYH_mnNIdwDINMa';
  $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
  $responseKeys = json_decode($verifyResponse, true);

  if (!$responseKeys['success']) {
    $response['error'] = 'reCAPTCHA verification failed. Please try again.';
    echo json_encode($response);
    exit();
  }

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $email)) {
    $response['error'] = 'Invalid email format. Please use an email ending with .com.';
    echo json_encode($response);
    exit();
  }

  // Prepare SQL statement to prevent SQL injection
  $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify password
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['role'] = $user['role'];
      $response['role'] = $user['role']; // Return the user role for redirection
    } else {
      $response['error'] = 'Incorrect password.';
    }
  } else {
    $response['error'] = 'Email not found.';
  }

  echo json_encode($response);
}
