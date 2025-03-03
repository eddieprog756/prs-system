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
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="./css/sales.css" />
  <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png" />
  <link rel="stylesheet" href="./css/https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./css/home.js" />
  <title>Designer Department</title>
  <style>
    .progress-bar {
      height: 30px;
      background-color: #77c144;
    }

    .btn-done {
      margin-top: 20px;
      background-color: #77c144;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn-done:hover {
      background-color: #218838;
    }
  </style>
</head>

<body>
  <?php include './sidebar2.php'; ?>

  <div class="left">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <i class="fa fa-bell" aria-hidden="true"></i>
    <i class="fa fa-cog" aria-hidden="true"></i>
  </div>


  <div class="imgclick">
    <img src="Images/menu2.png" class="menu-icon" onclick="toggleMobileMenu()" />
  </div>

  <div class="contents">
    <div class="pname"><strong>PROJECT</strong> NAME:</div>
    <select id="projectDropdown" class="form-control" onchange="loadProjectStatus()">
      <option value="">Select Project</option>
      <?php foreach ($projects as $project): ?>
        <option value="<?php echo htmlspecialchars($project['id']); ?>">
          <?php echo htmlspecialchars($project['Project_Name']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <div id="details"></div>

    <div class="status">PROJECT STATUS</div>

    <div class="containerr">
      <div class="percentage-line">
        <div id="progressBar" class="green-fill"></div>
      </div>
      <div class="per">
        <p id="percentage">0%</p>
      </div>
    </div>

    <div class="bottombox">
      <div class="pr">
        Click Below <strong>If Submitted</strong>
        <div class="acc">
          <label>
            <input type="checkbox" id="checkboxStudio" /> Designer Done
          </label>
        </div>
      </div>
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
        document.getElementById('checkboxStudio').checked = false;
        return;
      }

      fetch(`get_project_status.php?id=${projectId}`)
        .then(response => response.json())
        .then(data => {
          const status = data.status;
          const percentage = statusMapping[status] || 0;

          document.getElementById('progressBar').style.width = percentage + '%';
          document.getElementById('percentage').textContent = percentage + '%';
          document.getElementById('checkboxStudio').checked = status === 'studio_done';
        });
    }

    document.getElementById('checkboxStudio').addEventListener('change', function() {
      const dropdown = document.getElementById('projectDropdown');
      const projectId = dropdown.value;
      if (!projectId) return;

      const newStatus = this.checked ? 'studio_done' : 'sales_done';
      const percentage = statusMapping[newStatus];

      fetch(`update_project_status.php`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            id: projectId,
            status: newStatus
          })
        })
        .then(response => response.json())
        .then(data => {
          document.getElementById('progressBar').style.width = percentage + '%';
          document.getElementById('percentage').textContent = percentage + '%';

          if (data.status === 'success') {
            fetch(`send_email.php?id=${projectId}&status=${newStatus}`);
          }
        });
    });

    function markAsDone() {
      const dropdown = document.getElementById('projectDropdown');
      const projectId = dropdown.value;
      if (!projectId) return;

      if (confirm('Are you sure you want to mark this project as done?')) {
        const newStatus = 'accounts_done';
        const percentage = statusMapping[newStatus];

        fetch(`update_project_status.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              id: projectId,
              status: newStatus
            })
          })
          .then(response => response.json())
          .then(data => {
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('percentage').textContent = percentage + '%';

            if (data.status === 'success') {
              fetch(`send_email.php?id=${projectId}&status=${newStatus}`);
            }
          });
      }
    }
  </script>

</body>

</html>