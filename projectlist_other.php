<?php
session_start();

require_once 'config/db.php';
require 'vendor/autoload.php'; // Include PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Simulate login and set user_id in session
$_SESSION['user_id'] = 1; // Replace with actual user ID after a successful login

// Function to send email notifications to designers
function sendEmailToDesigners($jobCardNo)
{
  global $con;

  $emailQuery = "SELECT email FROM users WHERE role = 'designer'";
  $emailResult = mysqli_query($con, $emailQuery);

  if ($emailResult && mysqli_num_rows($emailResult) > 0) {
    $emails = [];
    while ($row = mysqli_fetch_assoc($emailResult)) {
      $emails[] = $row['email'];
    }

    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = "smtp.gmail.com";
      $mail->SMTPAuth = true;
      $mail->Username = "ed.eddie756@gmail.com";
      $mail->Password = "dzubdkcvuemfjkvj";
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->setFrom('PRS ADMIN', 'Project Updates');
      foreach ($emails as $email) {
        $mail->addAddress($email);
      }

      $mail->isHTML(true);
      $mail->Subject = "Job Card Approval Notification";
      $mail->Body    = "Job Card #$jobCardNo has been approved.";

      $mail->send();
      return true;
    } catch (Exception $e) {
      return false;
    }
  }
  return false;
}

// Retrieve projects data
$sql = "SELECT Date, JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size, status, Payment_Proof FROM jobcards ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($con, $sql);

if (!$result) {
  die("Error executing query: " . mysqli_error($con));
}

$projects = [];

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $projects[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Projects</title>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
  <style>
    .progress {
      height: 30px;
      background-color: #e9ecef;
      border-radius: 20px;
      margin-bottom: 15px;
    }

    .progress-bar {
      height: 100%;
      background: linear-gradient(45deg, #77c144, #77c144);
      border-radius: 20px;
      transition: width 0.4s ease;
    }

    .btn-inactive {
      cursor: not-allowed;
      opacity: 0.5;
    }
  </style>
</head>

<body class="bg-green" style="font-size: 15px;">
  <?php include './sidebar.php'; ?>

  <div class="container" style="max-width: 1000px; float: right; margin-left:300px;">
    <div class="row mt-5">
      <div class="contents">
        <h1 class="text-center fs-3" style="font-family:roboto; font-weight: bold;">CHECK PROJECT STATUS</h1>
        <div class="card shadow-lg" style="border-radius: 20px;">
          <div class="card-header bg-dark text-white" style="border-radius: 20px 20px 0px 0px;">
            <h2 class="text-center">Projects</h2>
          </div>
          <div class="card-body" style="overflow: auto;">
            <table class="table table-striped table-hover table-bordered text-center" id="projectsTable">
              <thead>
                <tr class="bg-dark" style="color:white">
                  <th>Date</th>
                  <th>Jobcard N0</th>
                  <th>Client Name</th>
                  <th>Project Name</th>
                  <th>Quantity</th>
                  <th>Overall Size</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($projects as $project) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($project['Date']); ?></td>
                    <td><?php echo htmlspecialchars($project['JobCard_N0']); ?></td>
                    <td><?php echo htmlspecialchars($project['Client_Name']); ?></td>
                    <td><?php echo htmlspecialchars($project['Project_Name']); ?></td>
                    <td><?php echo htmlspecialchars($project['Quantity']); ?></td>
                    <td><?php echo htmlspecialchars($project['Overall_Size']); ?></td>
                    <td>
                      <button class="btn btn-secondary btn-sm"
                        onclick="viewProof('<?php echo htmlspecialchars($project['JobCard_N0']); ?>')"
                        style="border-radius: 20px;">
                        <i class="fa fa-eye"></i> Payment
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup for Viewing Proof -->
    <div id="proofPopup" class="popup-container" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center;">
      <div class="popup-content" style="background: #fff; border-radius: 0.5rem; padding: 1rem; width: 90vw; max-width: 650px; text-align: center; position: relative;">
        <span class="close-btn" onclick="closeProofPopup()" style="position: absolute; top: 0.5rem; right: 0.5rem; cursor: pointer; font-size: 1.2rem;">&times;</span>
        <h5 style="margin-top: 0;">Proof of Payment</h5>
        <div id="proofDisplay" style="margin-top: 1rem;">
          <!-- Proof content (image/pdf) will be dynamically inserted here -->
        </div>
      </div>
    </div>

    <script>
      async function viewProof(jobCardNo) {
        try {
          const response = await fetch(`get_payment_proof.php?jobCardNo=${jobCardNo}`);
          const data = await response.json();

          const proofDisplay = document.getElementById('proofDisplay');
          proofDisplay.innerHTML = ''; // Clear previous content

          if (data.success) {
            const proofPath = data.proofPath;

            if (proofPath.match(/\.(jpeg|jpg|png|gif)$/i)) {
              proofDisplay.innerHTML = `<img src="./uploads/payment_proofs/${proofPath}" alt="Proof of Payment" style="max-width: 100%; height: auto;" />`;
            } else if (proofPath.match(/\.pdf$/i)) {
              proofDisplay.innerHTML = `<embed src="./uploads/payment_proofs/${proofPath}" type="application/pdf" width="100%" height="500px" />`;
            } else {
              proofDisplay.innerHTML = `<p>Unsupported file format: ${proofPath}</p>`;
            }

            document.getElementById('proofPopup').style.display = 'flex';
          } else {
            proofDisplay.innerHTML = `<p style="color: red;">${data.message}</p>`;
            document.getElementById('proofPopup').style.display = 'flex';
          }
        } catch (error) {
          alert('Error fetching payment proof.');
        }
      }

      function closeProofPopup() {
        document.getElementById('proofPopup').style.display = 'none';
      }
    </script>
  </div>
</body>

</html>