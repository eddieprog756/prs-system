<?php
require 'vendor/autoload.php'; // Adjust the path if necessary
require_once 'config/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

if (isset($_GET['id']) && isset($_GET['status'])) {
  $projectId = intval($_GET['id']);
  $status = $_GET['status'];

  // Fetch project details
  $sql = "SELECT Project_Name FROM jobcards WHERE id = $projectId";
  $result = mysqli_query($con, $sql);
  $project = mysqli_fetch_assoc($result);
  $projectName = $project['Project_Name'];

  try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->Username = "codeverse.mw@gmail.com";
    $mail->Password = "mdgfjvupuabqavpp";

    // Recipients
    $mail->setFrom('your_email@example.com', 'Your Name');
    $mail->addAddress('temboedward756@gmail.com', 'Edward Tembo');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Project Status Update';
    $mail->Body    = "The status of the project <strong>$projectName</strong> has been updated to <strong>$status</strong>.";

    $mail->send();
    echo 'Message has been sent';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

mysqli_close($con);
