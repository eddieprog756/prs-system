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

// Fetch projects including JobCard_N0
$sql = "SELECT id, Project_Name, JobCard_N0, status FROM jobcards";
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
    <title>Project Approval for Designer</title>
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
    </style>
</head>

<body>
    <?php include './sidebar2.php'; ?>

    <div class="container mt-5" style="width: 1000px; background-color: white; border-radius: 20px; padding: 20px; margin-left: 400px;">
        <h3 class="text-center">Designer Project Approval Status</h3>
        <div class="mb-3">
            <label class="font-weight-bold">PROJECT NAME:</label>
            <select id="projectDropdown" class="form-control rounded-pill" onchange="loadProjectStatus()">
                <option value="">Select Project</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?php echo htmlspecialchars($project['id']); ?>" data-status="<?php echo htmlspecialchars($project['status']); ?>">
                        <?php echo htmlspecialchars($project['Project_Name'] . ' - ' . $project['JobCard_N0']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="progress">
            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
        </div>
        <p id="percentage" class="text-center font-weight-bold" style="color:black;">0%</p>

        <div class="text-center mt-4">
            <button class="btn-done" onclick="approveProject()">Submit Project as Studio Done</button>
        </div>
    </div>

    <script>
        const statusMapping = {
            'project': 10,
            'sales_done': 20,
            'manager_approved': 40,
            'studio_done': 60,
            'workshop_done': 80,
            'accounts_done': 100
        };

        const userRole = "<?php echo $user_role; ?>";

        function loadProjectStatus() {
            const dropdown = document.getElementById('projectDropdown');
            const selectedOption = dropdown.options[dropdown.selectedIndex];
            const currentStatus = selectedOption.getAttribute('data-status');

            if (!currentStatus) {
                document.getElementById('progressBar').style.width = '0%';
                document.getElementById('percentage').textContent = '0%';
                return;
            }

            const percentage = statusMapping[currentStatus] || 0;
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('percentage').textContent = percentage + '%';
        }

        function approveProject() {
            const dropdown = document.getElementById('projectDropdown');
            const selectedOption = dropdown.options[dropdown.selectedIndex];
            const projectId = dropdown.value;
            const currentStatus = selectedOption.getAttribute('data-status');

            if (!projectId) {
                alert("Please select a project.");
                return;
            }

            if (userRole === 'designer') {
                if (currentStatus === 'manager_approved') {
                    updateProjectStatus(projectId, 'studio_done');
                } else if (currentStatus === 'accounts_done') {
                    alert("Project is already finished.");
                } else {
                    alert("Project not ready for submission. Contact manager for approval first.");
                }
            } else {
                alert("You are not authorized to update this project.");
            }
        }

        function updateProjectStatus(projectId, status) {
            const percentage = statusMapping[status];

            fetch('update_project_status.php', {
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
                        alert("Project status updated to Studio Done.");
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        alert(data.message || "Failed to update the project status.");
                    }
                })
                .catch(error => console.error('Error updating project status:', error));
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>