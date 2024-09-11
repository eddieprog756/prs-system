<?php
require_once 'config/db.php';

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['id']) && isset($input['status'])) {
  $projectId = intval($input['id']);
  $status = $input['status'];
  $sql = "UPDATE jobcards SET status = '$status' WHERE id = $projectId";

  if (mysqli_query($con, $sql)) {
    echo json_encode(['status' => 'success']);
  } else {
    echo json_encode(['status' => 'error']);
  }
}
mysqli_close($con);
