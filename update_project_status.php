<?php
session_start();
include 'config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
  exit();
}

// Validate the request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
  exit();
}

// Fetch and validate input data
$data = json_decode(file_get_contents("php://input"), true);
$projectId = $data['id'] ?? null;
$newStatus = $data['status'] ?? null;

if (!$projectId || !$newStatus) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid request data.']);
  exit();
}

$userId = $_SESSION['user_id'];

// Fetch user role securely
$roleQuery = "SELECT role FROM users WHERE id = ?";
$stmt = $con->prepare($roleQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userRole = $result->fetch_assoc()['role'] ?? null;

if (!$userRole) {
  echo json_encode(['status' => 'error', 'message' => 'Failed to fetch user role.']);
  exit();
}

// Fetch the current status of the project
$statusQuery = "SELECT status FROM jobcards WHERE id = ?";
$stmt = $con->prepare($statusQuery);
$stmt->bind_param("i", $projectId);
$stmt->execute();
$result = $stmt->get_result();
$currentStatus = $result->fetch_assoc()['status'] ?? null;

if (!$currentStatus) {
  echo json_encode(['status' => 'error', 'message' => 'Project not found.']);
  exit();
}

// Handle role-based status update logic
$updateQuery = null;

if ($userRole === 'sales' && $currentStatus === 'project' && $newStatus === 'sales_done') {
  $updateQuery = "UPDATE jobcards SET status = ? WHERE id = ?";
} elseif ($userRole === 'administration' && $currentStatus === 'sales_done' && $newStatus === 'administration_done') {
  $updateQuery = "UPDATE jobcards SET status = ? WHERE id = ?";
} elseif ($userRole === 'workshop' && $currentStatus === 'studio_done' && $newStatus === 'workshop_done') {
  $updateQuery = "UPDATE jobcards SET status = ? WHERE id = ?";
} elseif ($userRole === 'accounts' && $currentStatus === 'workshop_done' && $newStatus === 'accounts_done') {
  $updateQuery = "UPDATE jobcards SET status = ? WHERE id = ?";
} else {
  echo json_encode(['status' => 'error', 'message' => 'Not authorized to update status or invalid status transition.']);
  exit();
}

// Execute the update query
if ($updateQuery) {
  $stmt = $con->prepare($updateQuery);
  $stmt->bind_param("si", $newStatus, $projectId);

  if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Project status updated successfully.']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update project status.']);
  }
} else {
  echo json_encode(['status' => 'error', 'message' => 'Unable to process the request.']);
}

exit();
