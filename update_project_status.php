// update_project_status.php

<?php
session_start();
include 'config/db.php';

// Decode incoming JSON data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id']) && isset($data['status'])) {
  $projectId = $data['id'];
  $newStatus = $data['status'];
  $userId = $_SESSION['user_id'];

  // Fetch user role
  $roleQuery = "SELECT role FROM users WHERE id = ?";
  $stmt = $con->prepare($roleQuery);
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  $userRole = $result->fetch_assoc()['role'] ?? null;

  // Check current status of the project
  $statusQuery = "SELECT status FROM jobcards WHERE id = ?";
  $stmt = $con->prepare($statusQuery);
  $stmt->bind_param("i", $projectId);
  $stmt->execute();
  $result = $stmt->get_result();
  $currentStatus = $result->fetch_assoc()['status'] ?? null;

  // Handle role-based approval logic
  if ($userRole === 'sales' && $currentStatus === 'project' && $newStatus === 'sales_done') {
    $updateQuery = "UPDATE jobcards SET status = 'sales_done' WHERE id = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("i", $projectId);
    $stmt->execute() ? json(['status' => 'success', 'message' => 'Project status updated to Sales Done']) : json(['status' => 'error', 'message' => 'Database update failed']);
  } elseif ($userRole === 'workshop' && $currentStatus === 'studio_done' && $newStatus === 'workshop_done') {
    $updateQuery = "UPDATE jobcards SET status = 'workshop_done' WHERE id = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("i", $projectId);
    $stmt->execute() ? json(['status' => 'success', 'message' => 'Project status updated to Workshop Done']) : json(['status' => 'error', 'message' => 'Database update failed']);
  } elseif ($userRole === 'accounts' && $currentStatus === 'workshop_done' && $newStatus === 'accounts_done') {
    // Final approval by accounts to close the project
    $updateQuery = "UPDATE jobcards SET status = 'accounts_done' WHERE id = ?";
    $stmt = $con->prepare($updateQuery);
    $stmt->bind_param("i", $projectId);
    $stmt->execute() ? json(['status' => 'success', 'message' => 'Project status updated to Accounts Done']) : json(['status' => 'error', 'message' => 'Database update failed']);
  } else {
    json(['status' => 'error', 'message' => 'Not authorized to update status or incorrect current status']);
  }
} else {
  json(['status' => 'error', 'message' => 'Invalid request']);
}

function json($message)
{
  echo json_encode($message);
  exit();
}
?>