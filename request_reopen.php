// request_reopen.php
<?php
session_start();
include 'config/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'])) {
  $projectId = $data['id'];

  // Add logic to handle re-open request (e.g., log the request, notify admin)
  // Example: Log the request in a new table or send an email/notification to admin

  echo json_encode(['status' => 'success', 'message' => 'Re-open request recorded successfully']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>