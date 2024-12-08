<?php
require_once 'config/db.php';

// Validate if jobCardNo is set
if (isset($_GET['jobCardNo'])) {
    $jobCardNo = mysqli_real_escape_string($con, $_GET['jobCardNo']);

    // Query to get Payment_Proof from the database
    $query = "SELECT Payment_Proof FROM jobcards WHERE JobCard_N0 = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $jobCardNo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if result exists
    if ($row = $result->fetch_assoc()) {
        // Ensure Payment_Proof exists
        $proofPath = $row['Payment_Proof'];
        if (!empty($proofPath)) {
            echo json_encode(['success' => true, 'proofPath' => $proofPath]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No payment proof available for this Job Card Number.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No Job Card found for the given number.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Job Card Number is missing.']);
}
