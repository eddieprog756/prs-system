<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

include 'config/db.php';

$user_id = $_SESSION['user_id'];
$user_role = '';

// Fetch user role
$role_query = "SELECT role FROM users WHERE id = ?";
$stmt = $con->prepare($role_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($user_data = $result->fetch_assoc()) {
  $user_role = $user_data['role'];
}

$sql = "SELECT id, Project_Name, status FROM jobcards";
$result = mysqli_query($con, $sql);
if (!$result) {
  die("Error executing query: " . mysqli_error($con));
}
$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
  <title>Finalize and Close Project</title>
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

    .btn-finalize,
    .btn-reopen {
      margin-top: 20px;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-weight: bold;
    }

    .btn-finalize {
      background-color: #77c144;
    }

    .btn-finalize:hover {
      background-color: #218838;
    }

    .btn-reopen {
      background-color: #ff4c4c;
    }

    .btn-reopen:hover {
      background-color: #d92626;
    }
  </style>
</head>

<body>
  <?php include './sidebar2.php'; ?>

  <div class="container mt-5" style="width: 900px; background-color: white; border-radius: 20px; padding: 20px;">
    <h3 class="text-center">Finalize and Close Project</h3>
    <div class="mb-3">
      <label class="font-weight-bold">PROJECT NAME:</label>
      <select id="projectDropdown" class="form-control" onchange="loadProjectStatus()">
        <option value="">Select Project</option>
        <?php foreach ($projects as $project): ?>
          <option value="<?php echo htmlspecialchars($project['id']); ?>">
            <?php echo htmlspecialchars($project['Project_Name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="progress">
      <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
    </div>
    <p id="percentage" class="text-center font-weight-bold" style="color:black;">0%</p>

    <div class="text-center mt-4">
      <button id="btnFinalize" class="btn-finalize" onclick="finalizeProject()">Finalize and Close Project</button>
      <button id="btnReopen" class="btn-reopen d-none" onclick="requestReopen()">Request Project Re-Open</button>
    </div>
  </div>

  <script>
    const statusMapping = {
      'project': 10,
      'sales_done': 20,
      'manager_approved': 30,
      'studio_done': 60,
      'workshop_done': 80,
      'accounts_done': 100
    };

    const userRole = "<?php echo $user_role; ?>";

    function loadProjectStatus() {
      const dropdown = document.getElementById('projectDropdown');
      const projectId = dropdown.value;

      if (!projectId) {
        document.getElementById('progressBar').style.width = '0%';
        document.getElementById('percentage').textContent = '0%';
        document.getElementById('btnFinalize').classList.remove('d-none');
        document.getElementById('btnReopen').classList.add('d-none');
        return;
      }

      fetch(`get_project_status.php?id=${projectId}`)
        .then(response => response.json())
        .then(data => {
          const status = data.status;
          const percentage = statusMapping[status] || 0;

          document.getElementById('progressBar').style.width = percentage + '%';
          document.getElementById('percentage').textContent = percentage + '%';

          if (status === 'accounts_done') {
            document.getElementById('btnFinalize').classList.add('d-none');
            document.getElementById('btnReopen').classList.remove('d-none');
          } else {
            document.getElementById('btnFinalize').classList.remove('d-none');
            document.getElementById('btnReopen').classList.add('d-none');
          }
        });
    }

    function finalizeProject() {
      const dropdown = document.getElementById('projectDropdown');
      const projectId = dropdown.value;

      if (!projectId) {
        alert("Please select a project.");
        return;
      }

      fetch(`get_project_status.php?id=${projectId}`)
        .then(response => response.json())
        .then(data => {
          const currentStatus = data.status;

          if (userRole === 'accounts' && currentStatus === 'workshop_done') {
            // Allow accounts to finalize and close the project
            updateProjectStatus(projectId, 'accounts_done');
            alert("Project finalized and closed successfully.");
          } else {
            alert("Project is not ready for finalization. Previous steps are incomplete.");
          }
        })
        .catch(error => console.error('Error fetching project status:', error));
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
          if (data.status === 'success') {
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('percentage').textContent = percentage + '%';
            alert("Project status updated to Accounts Done.");
            setTimeout(() => location.reload(), 1000); // Auto-refresh after 1 second
          } else {
            alert(data.message || "Failed to update the project status.");
          }
        })
        .catch(error => console.error('Error updating project status:', error));
    }

    function requestReopen() {
      const dropdown = document.getElementById('projectDropdown');
      const projectId = dropdown.value;

      if (!projectId) {
        alert("Please select a project.");
        return;
      }

      if (confirm("Are you sure you want to request to reopen this project?")) {
        // Send a request to update the project re-open status in the database or notify relevant users
        fetch(`request_reopen.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              id: projectId
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              alert("Re-open request has been sent successfully.");
            } else {
              alert(data.message || "Failed to send re-open request.");
            }
          })
          .catch(error => console.error('Error requesting project re-open:', error));
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>