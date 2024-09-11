<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['jobCardNo']) && !empty($_POST['jobCardNo'])) {
    $jobCardNo = mysqli_real_escape_string($con, $_POST['jobCardNo']);

    // Prepare the SQL statement
    $sql = "UPDATE jobcards SET status = 'manager_approved' WHERE JobCard_N0 = '$jobCardNo'";

    // Execute the query
    if (mysqli_query($con, $sql)) {
      echo 'Success';
    } else {
      // Error handling
      error_log("Error updating job card status: " . mysqli_error($con)); // Log the error to the server's error log
      echo 'Error: ' . mysqli_error($con); // Provide error message for debugging
    }
  } else {
    echo 'Error: Job card number is missing.';
  }
  mysqli_close($con);
} else {
  echo 'Invalid request method.';
}
