<?php
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_GET['jobCardNo'])) {
    echo json_encode(['success' => false, 'message' => 'Job card number missing']);
    exit;
}

$jobCardNo = $_GET['jobCardNo'];
$proofSql = "SELECT Payment_Proof FROM jobcards WHERE JobCard_N0 = ?";
$stmt = $con->prepare($proofSql);
$stmt->bind_param("s", $jobCardNo);
$stmt->execute();
$proofResult = $stmt->get_result();
$proofRow = $proofResult->fetch_assoc();

if (!$proofRow || empty($proofRow['Payment_Proof'])) {
    echo json_encode(['success' => false, 'message' => 'No payment proof available']);
    exit;
}

echo json_encode([
    'success' => true,
    'proofPath' => $proofRow['Payment_Proof']
]);