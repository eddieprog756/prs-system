<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$user_id = $_SESSION['user_id'];

// Set user role in the session if not already set
if (!isset($_SESSION['user_role'])) {
    $role_query = "SELECT role FROM users WHERE id = ?";
    $stmt = $con->prepare($role_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user_data = $result->fetch_assoc()) {
        $_SESSION['user_role'] = $user_data['role'];
    } else {
        die("User role not found.");
    }
}

$user_role = $_SESSION['user_role'];

// Fetch all projects
$sql = "SELECT id, Project_Name, status FROM jobcards";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$projects = $result->fetch_all(MYSQLI_ASSOC);

// Define status mapping
$statusMapping = [
    'project' => 10,
    'sales_done' => 20,
    'manager_approved' => 40,
    'studio_done' => 60,
    'workshop_done' => 80,
    'accounts_done' => 100,
];

$con->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <title>Project Approval</title>
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

    <div class="container mt-5" style="width: 900px; background-color: white; border-radius: 20px; padding: 20px;">
        <h3 class="text-center">Project Approval Status</h3>

        <div class="mb-3">
            <label class="font-weight-bold">PROJECT NAME:</label>
            <select id="projectDropdown" class="form-control">
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
            <button class="btn-done" id="initiateButton">Initiate Project</button>
        </div>
    </div>

    <script>
        document.getElementById('projectDropdown').addEventListener('change', async function() {
            const projectId = this.value;

            if (!projectId) {
                document.getElementById('progressBar').style.width = '0%';
                document.getElementById('percentage').textContent = '0%';
                return;
            }

            try {
                const response = await fetch(`get_project_status.php?id=${projectId}`);
                const data = await response.json();

                if (data.success) {
                    const percentage = data.progress || 0;
                    document.getElementById('progressBar').style.width = `${percentage}%`;
                    document.getElementById('percentage').textContent = `${percentage}%`;
                } else {
                    alert(data.message || "Failed to fetch project progress.");
                }
            } catch (error) {
                console.error('Error fetching project progress:', error);
                alert('An error occurred while fetching project progress.');
            }
        });

        document.getElementById('initiateButton').addEventListener('click', async function() {
            const projectId = document.getElementById('projectDropdown').value;

            if (!projectId) {
                alert("Please select a project.");
                return;
            }

            try {
                const response = await fetch('update_project_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: projectId,
                        status: 'sales_done'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(data.message || 'Project initiated successfully.');
                    location.reload(); // Reload to reflect the updated status
                } else {
                    alert(data.message || "Failed to initiate project.");
                }
            } catch (error) {
                console.error('Error initiating project:', error);
                alert('An error occurred while initiating the project.');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>