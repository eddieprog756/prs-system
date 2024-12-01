<?php
require './config/db.php';

// Check if user_id is provided
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'User ID is required.']);
  exit();
}

$userId = intval($_GET['user_id']);

// Fetch payment proof from the database
$query = "SELECT Payment_Proof FROM jobcards WHERE user_id = ? LIMIT 1";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $proofPath = $row['Payment_Proof'];

  if (!empty($proofPath)) {
    echo json_encode(['success' => true, 'proofPath' => $proofPath]);
  } else {
    echo json_encode(['success' => false, 'message' => 'No payment proof found for this user.']);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'No records found for this user.']);
}

$stmt->close();
$con->close();
