<?php
session_start();
require './config/db.php'; // Database connection
require 'vendor/autoload.php'; // Include libraries like PHPExcel or MPDF for report generation

// Initialize variables
$data = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $start_date = $_POST['start_date'] ?? null;
  $end_date = $_POST['end_date'] ?? null;
  $format = $_POST['format'] ?? null;

  if ($start_date && $end_date && $format) {
    try {
      // Fetch data from jobcards and users tables
      $query = "
        SELECT j.project_name, j.status, j.created_at, u.full_name, u.email 
        FROM jobcards j
        INNER JOIN users u ON j.user_id = u.id
        WHERE j.created_at BETWEEN ? AND ?
        ORDER BY j.created_at DESC
      ";
      $stmt = $con->prepare($query);
      $stmt->bind_param("ss", $start_date, $end_date);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_all(MYSQLI_ASSOC);

      if (empty($data)) {
        $error = "No data found for the selected date range.";
      } else {
        // Generate report based on format
        if ($format === 'excel') {
          header("Content-Type: application/vnd.ms-excel");
          header("Content-Disposition: attachment; filename=report.xls");
          echo "Project Name\tStatus\tCreated At\tUser Name\tUser Email\n";
          foreach ($data as $row) {
            echo "{$row['project_name']}\t{$row['status']}\t{$row['created_at']}\t{$row['full_name']}\t{$row['email']}\n";
          }
          exit();
        } elseif ($format === 'pdf') {
          // PDF generation with MPDF
          $mpdf = new \Mpdf\Mpdf();
          $html = "<h1>Project Report</h1><table border='1' style='width:100%;'><thead><tr>
                      <th>Project Name</th><th>Status</th><th>Created At</th><th>User Name</th><th>User Email</th>
                   </tr></thead><tbody>";
          foreach ($data as $row) {
            $html .= "<tr>
                        <td>" . htmlspecialchars($row['project_name']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                        <td>" . htmlspecialchars($row['full_name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                      </tr>";
          }
          $html .= "</tbody></table>";
          $mpdf->WriteHTML($html);
          $mpdf->Output("report.pdf", "D");
          exit();
        } elseif ($format === 'word') {
          // Word generation
          header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
          header("Content-Disposition: attachment; filename=report.doc");
          echo "<h1>Project Report</h1><table border='1' style='width:100%;'><thead><tr>
                  <th>Project Name</th><th>Status</th><th>Created At</th><th>User Name</th><th>User Email</th>
               </tr></thead><tbody>";
          foreach ($data as $row) {
            echo "<tr>
                    <td>{$row['project_name']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['created_at']}</td>
                    <td>{$row['full_name']}</td>
                    <td>{$row['email']}</td>
                  </tr>";
          }
          echo "</tbody></table>";
          exit();
        }
      }
    } catch (Exception $e) {
      $error = "An error occurred while generating the report: " . $e->getMessage();
    }
  } else {
    $error = "Please fill in all fields.";
  }
}
?>

<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth;">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" />
  <style>
    body {
      background-color: #f5f5f5;
      font-family: 'Roboto', sans-serif;
    }

    .container {
      margin: 0 auto;
      max-width: 800px;
      width: 100%;
      padding: 15px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
    }

    .container h1 {
      margin-bottom: 20px;
      text-align: center;
    }

    .icon {
      margin-left: 5px;
    }
  </style>
</head>

<body style="background-image: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url('./Images/bg.JPG'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 100vh; overflow: hidden;">
  <?php include './sidebar2.php'; ?>

  <div class="container mt-5" style="width: 900px;">
    <h3 class="text-center fw-bold">Generate Reports</h3>
    <form id="reportForm" class="mt-4" method="POST">
      <div class="row mb-3">
        <div class="col-md-4">
          <label for="startDate" class="form-label">Start Date</label>
          <input type="date" class="form-control" id="startDate" name="start_date" required>
        </div>
        <div class="col-md-4">
          <label for="endDate" class="form-label">End Date</label>
          <input type="date" class="form-control" id="endDate" name="end_date" required>
        </div>
        <div class="col-md-4">
          <label for="format" class="form-label">Format</label>
          <select id="format" class="form-select" name="format" required>
            <option value="excel">Excel <i class="fas fa-file-excel icon text-success"></i></option>
            <option value="pdf">PDF <i class="fas fa-file-pdf icon text-danger"></i></option>
            <option value="word">Word <i class="fas fa-file-word icon text-primary"></i></option>
          </select>
        </div>
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-dark mx-auto d-block" style=" background-color: #212529; border:none; border-radius:40px; justify-content:center; align-items:center;">Download Report</button>
      </div>
    </form>
    <?php if (!empty($error)): ?>
      <div class="text-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>