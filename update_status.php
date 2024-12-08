<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['jobCardNo']) && !empty(trim($_POST['jobCardNo']))) {
    $jobCardNo = trim($_POST['jobCardNo']);

    // Prepare the SQL statement to avoid SQL injection
    $sql = "UPDATE jobcards SET status = ? WHERE JobCard_N0 = ?";
    $stmt = $con->prepare($sql);

    if ($stmt) {
      $status = 'manager_approved'; // Set the new status
      $stmt->bind_param("ss", $status, $jobCardNo);

      // Execute the statement
      if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Job card status updated successfully.']);
      } else {
        // Log error and respond with appropriate message
        error_log("Update failed for JobCard_N0: $jobCardNo. " . ($stmt->error ?: 'No rows affected.'));
        echo json_encode(['status' => 'error', 'message' => 'Failed to update job card status.']);
      }

      $stmt->close();
    } else {
      // Log error if statement preparation fails
      error_log("SQL preparation error: " . $con->error);
      echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
    }
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Job card number is missing or invalid.']);
  }

  $con->close();
} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
