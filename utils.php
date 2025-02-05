<?php
function logAction($con, $userId, $action, $description = null) {
  $stmt = $con->prepare("INSERT INTO logs (user_id, action, description) VALUES (?, ?, ?)");
  if (!$stmt) {
      error_log("Failed to prepare log insertion: " . $con->error);
      return false;
  }
  $stmt->bind_param("iss", $userId, $action, $description);
  return $stmt->execute();
}
