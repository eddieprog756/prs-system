<?php
session_start();
require './config/db.php'; // Database connection
require 'vendor/autoload.php'; // Include libraries

// Initialize variables
$data = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $start_date = $_POST['start_date'] ?? null;
  $end_date = $_POST['end_date'] ?? null;

  if ($start_date && $end_date) {
    try {
      // Fetch data from jobcards and users tables
      $query = "
                SELECT j.project_name, j.status, j.created_at, u.full_name, u.email 
                FROM jobcards j
                INNER JOIN users u ON j.id = u.id
                WHERE j.created_at BETWEEN ? AND ?
                ORDER BY j.created_at DESC
            ";
      $stmt = $con->prepare($query);

      if (!$stmt) {
        throw new Exception("Failed to prepare the query. Error: " . $con->error);
      }

      $stmt->bind_param("ss", $start_date, $end_date);
      $stmt->execute();
      $result = $stmt->get_result();

      if (!$result) {
        throw new Exception("Failed to execute query. Error: " . $stmt->error);
      }

      $data = $result->fetch_all(MYSQLI_ASSOC);

      if (empty($data)) {
        $error = "No data found for the selected date range.";
      }
    } catch (Exception $e) {
      $error = "An error occurred: " . $e->getMessage();
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
  <title>Generate Reports</title>
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
      padding: 15px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table,
    th,
    td {
      border: 1px solid #ddd;
    }

    th,
    td {
      padding: 10px;
      text-align: left;
    }
  </style>
</head>

<body style="background-image: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url('./Images/bg.JPG'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 100vh;">
  <?php include './sidebar.php'; ?>

  <div class="container mt-4" style="width:1000px; margin-left:380px;">
    <h3 class="fw-bold text-center">Generate Reports</h3>
    <form id="reportForm" method="POST">
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="startDate" class="form-label">Start Date</label>
          <input type="date" class="form-control" id="startDate" name="start_date" required>
        </div>
        <div class="col-md-6">
          <label for="endDate" class="form-label">End Date</label>
          <input type="date" class="form-control" id="endDate" name="end_date" required>
        </div>
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-dark">Generate Report</button>
      </div>
    </form>

    <?php if ($error): ?>
      <div class="text-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="mt-4">
      <table id="reportTable" class="table table-striped table-bordered table-hover">
        <thead style="background-color: #343a40; color: white;">
          <tr>
            <th>Project Name</th>
            <th>Status</th>
            <th>Created At</th>
            <th>User Name</th>
            <th>User Email</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($data)): ?>
            <?php foreach ($data as $row): ?>
              <tr style="background-color: <?php echo $row['status'] === 'pending' ? '#ff9800' : '#28a745'; ?>">
                <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center">No data available</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    function printTable() {
      const table = document.getElementById('reportTable').outerHTML;
      const newWindow = window.open('', '', 'width=800, height=600');
      newWindow.document.write('<html><head><title>Print Report</title></head><body>');
      newWindow.document.write('<h1>Generated Report</h1>');
      newWindow.document.write(table);
      newWindow.document.write('</body></html>');
      newWindow.document.close();
      newWindow.print();
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>