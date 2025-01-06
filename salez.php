<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config/db.php';

$user_id = $_SESSION['user_id'];
$user_role = '';

// Fetch user role securely
$role_query = "SELECT role FROM users WHERE id = ?";
$stmt = $con->prepare($role_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($user_data = $result->fetch_assoc()) {
    $user_role = $user_data['role'];
} else {
    die("Error fetching user role.");
}

// Fetch projects securely
$sql = "SELECT id, Project_Name, JobCard_N0, status FROM jobcards";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$projects = $result->fetch_all(MYSQLI_ASSOC);

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
        <div class="mb-3" style="display: flex; justify-content: center; align-items: center;">
            <label class="font-weight-bold me-2">PROJECT NAME:</label>
            <select id="projectDropdown" class="form-select rounded-pill" style="width: 300px;" onchange="loadProjectStatus()">
                <option value="">Select Project</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?php echo htmlspecialchars($project['id']); ?>"
                        data-status="<?php echo htmlspecialchars($project['status']); ?>"
                        style="background-color: <?php echo $project['status'] === 'accounts_done' ? '#74c444' : '#fff'; ?>;">
                        <?php echo htmlspecialchars($project['Project_Name']) . ' - ' . htmlspecialchars($project['JobCard_N0']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="progress">
            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
        </div>
        <p id="percentage" class="text-center font-weight-bold" style="color:black;">0%</p>

        <div class="text-center mt-4">
            <button class="btn-done" data-bs-toggle="modal" data-bs-target="#approvalModal">Initiate Project</button>
        </div>
    </div>

    <!-- Bootstrap Modal for Confirmation -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalLabel">Sales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to initiate this project?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark" id="confirmApproval">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Modal for Alerts -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 20px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="alertModalLabel">Sales Alerts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="alertModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
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

        document.getElementById('projectDropdown').addEventListener('change', () => {
            const dropdown = document.getElementById('projectDropdown');
            const selectedOption = dropdown.options[dropdown.selectedIndex];
            const projectId = dropdown.value;
            const currentStatus = selectedOption.getAttribute('data-status');

            if (!projectId) {
                document.getElementById('progressBar').style.width = '0%';
                document.getElementById('percentage').textContent = '0%';
                return;
            }

            const percentage = statusMapping[currentStatus] || 0;
            document.getElementById('progressBar').style.width = percentage + '%';
            document.getElementById('percentage').textContent = percentage + '%';
        });

        document.getElementById('confirmApproval').addEventListener('click', async () => {
            const dropdown = document.getElementById('projectDropdown');
            const selectedOption = dropdown.options[dropdown.selectedIndex];
            const projectId = dropdown.value;
            const currentStatus = selectedOption.getAttribute('data-status');

            if (!projectId) {
                showAlert("Please select a project.");
                return;
            }

            if (userRole === 'sales' && currentStatus === 'project') {
                await updateProjectStatus(projectId, 'sales_done');
            } else {
                showAlert("You are not authorized to initiate this project.");
            }

            document.getElementById('approvalModal').querySelector('[data-bs-dismiss="modal"]').click();
        });

        async function updateProjectStatus(projectId, newStatus) {
            const percentage = statusMapping[newStatus] || 0;

            try {
                const response = await fetch('update_project_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: projectId,
                        status: newStatus
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    document.getElementById('progressBar').style.width = percentage + '%';
                    document.getElementById('percentage').textContent = percentage + '%';
                    showAlert('Project status updated successfully.');
                } else {
                    showAlert(data.message || "Failed to update project status.");
                }
            } catch (error) {
                console.error('Error updating project status:', error);
                showAlert('An error occurred while updating project status.');
            }
        }

        function showAlert(message) {
            document.getElementById('alertModalBody').textContent = message;
            const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
            alertModal.show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>