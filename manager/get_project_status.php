<?php
require_once 'config/db.php';

if (isset($_GET['id'])) {
  $projectId = intval($_GET['id']);
  $sql = "SELECT status FROM jobcards WHERE id = $projectId";
  $result = mysqli_query($con, $sql);

  if ($result) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode($data);
  } else {
    echo json_encode(['status' => 'error']);
  }
}
mysqli_close($con);
