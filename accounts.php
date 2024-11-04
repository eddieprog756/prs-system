<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

require_once 'config/db.php';

$sql = "SELECT id, Project_Name, status FROM jobcards";
$result = mysqli_query($con, $sql);

if (!$result) {
  die("Error executing query: " . mysqli_error($con));
}

$projects = [];
while ($row = mysqli_fetch_assoc($result)) {
  $projects[] = $row;
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
  <title>Accounts Department</title>
  <style>
    body {
      background-color: #f4f4f9;
    }

    .progress {
      height: 30px;
      background-color: #e9ecef;
      border-radius: 20px;
      overflow: hidden;
      margin-bottom: 15px;
    }

    .progress-bar {
      height: 100%;
      background: linear-gradient(45deg, #77c144, #77c144);
      border-radius: 20px;
      transition: width 0.4s ease;
    }

    .btn-done {
      margin-top: 20px;
      background-color: #77c144;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .btn-done:hover {
      background-color: #218838;
    }

    .form-control {
      max-width: 300px;
    }
  </style>
</head>

<body>
  <?php include './sidebar2.php'; ?>

  <div class="container mt-5" style="width: 900px; background-color: white; border-radius: 20px; padding: 20px;">
    <div class="text-center mb-4">
      <h1 class="text-success font-weight-bold">Accounts Department</h1>
    </div>

    <div class="mb-3">
      <label class="pname font-weight-bold">PROJECT NAME:</label>
      <select id="projectDropdown" class="form-control" onchange="loadProjectStatus()">
        <option value="">Select Project</option>
        <?php foreach ($projects as $project): ?>
          <option value="<?php echo htmlspecialchars($project['id']); ?>">
            <?php echo htmlspecialchars($project['Project_Name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="status font-weight-bold text-success">PROJECT STATUS</div>
    <div class="progress">
      <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
    </div>
    <p id="percentage" class="text-center font-weight-bold" style="color:black;">0%</p>

    <div class="text-center mt-4">
      <button class="btn-done" onclick="markAsDone()">Mark as Done</button>
    </div>
  </div>

  <script>
    const statusMapping = {
      'project': 0,
      'sales_done': 20,
      'manager_approved': 40,
      'studio_done': 60,
      'workshop_done': 80,
      'accounts_done': 100
    };

    function loadProjectStatus() {
      const dropdown = document.getElementById('projectDropdown');
      const projectId = dropdown.value;
      if (!projectId) {
        document.getElementById('progressBar').style.width = '0%';
        document.getElementById('percentage').textContent = '0%';
        return;
      }

      fetch(`get_project_status.php?id=${projectId}`)
        .then(response => response.json())
        .then(data => {
          const status = data.status;
          const percentage = statusMapping[status] || 0;

          document.getElementById('progressBar').style.width = percentage + '%';
          document.getElementById('percentage').textContent = percentage + '%';
        });
    }

    function markAsDone() {
      const dropdown = document.getElementById('projectDropdown');
      const projectId = dropdown.value;
      if (!projectId) return;

      if (confirm('Are you sure you want to mark this project as done?')) {
        updateProjectStatus(projectId, 'accounts_done');
      }
    }

    function updateProjectStatus(projectId, status) {
      const percentage = statusMapping[status];
      fetch(`update_project_status.php`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            id: projectId,
            status: status
          })
        })
        .then(response => response.json())
        .then(data => {
          document.getElementById('progressBar').style.width = percentage + '%';
          document.getElementById('percentage').textContent = percentage + '%';

          if (data.status !== 'success') {
            alert('An error occurred while updating the project status.');
          }
        });
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>