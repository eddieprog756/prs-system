<?php
require_once 'config/db.php';

if (isset($_GET['id'])) {
  $projectId = intval($_GET['id']);

  $query = "SELECT status FROM jobcards WHERE id = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param("i", $projectId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $statusMapping = [
      'project' => 10,
      'sales_done' => 20,
      'manager_approved' => 40,
      'studio_done' => 60,
      'workshop_done' => 80,
      'accounts_done' => 100,
    ];

    $status = $row['status'];
    $progress = $statusMapping[$status] ?? 0;

    echo json_encode(['success' => true, 'progress' => $progress]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Project not found.']);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Project ID is missing.']);
}
