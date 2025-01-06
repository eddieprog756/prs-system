<?php
session_start();
include 'config/db.php';

header('Content-Type: application/json');

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

// Debugging logs
error_log("Debug: Project ID - $projectId");
error_log("Debug: Current Status - " . ($currentStatus ?: 'Not found'));
error_log("Debug: User Role - $userRole");
error_log("Debug: New Status - $newStatus");

if (!$currentStatus) {
  error_log("Error: No status found for project ID: $projectId");
  echo json_encode(['status' => 'error', 'message' => 'Project not found or status is missing.']);
  exit();
}

// Define allowed transitions
$allowedTransitions = [
  'sales' => [
    'project' => 'sales_done',
  ],
  'administration' => [
    'sales_done' => 'administration_done',
  ],
  'designer' => [
    'manager_approved' => 'studio_done',
  ],
  'workshop' => [
    'studio_done' => 'workshop_done',
  ],
  'accounts' => [
    'workshop_done' => 'accounts_done',
  ],
];

// Validate role-based status transitions
if (!isset($allowedTransitions[$userRole][$currentStatus]) || $allowedTransitions[$userRole][$currentStatus] !== $newStatus) {
  echo json_encode([
    'status' => 'error',
    'message' => 'Not authorized to update status or invalid status transition.',
    'details' => [
      'userRole' => $userRole,
      'currentStatus' => $currentStatus,
      'newStatus' => $newStatus,
      'expectedStatus' => $allowedTransitions[$userRole][$currentStatus] ?? 'None'
    ]
  ]);
  exit();
}

// Update the project status
$updateQuery = "UPDATE jobcards SET status = ? WHERE id = ?";
$stmt = $con->prepare($updateQuery);
if (!$stmt) {
  echo json_encode(['status' => 'error', 'message' => 'Failed to prepare update query.']);
  exit();
}
$stmt->bind_param("si", $newStatus, $projectId);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'message' => 'Project status updated successfully.']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to update project status.']);
}
exit();
