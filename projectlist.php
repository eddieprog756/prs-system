<?php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';
require 'vendor/autoload.php'; // Include PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send email notifications to designers
function sendEmailToDesigners($jobCardNo)
{
    global $con;

    // Fetch designer emails
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

// Handle status update request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['jobCardNo']) && !empty($_POST['jobCardNo'])) {
        $jobCardNo = mysqli_real_escape_string($con, $_POST['jobCardNo']);

        // Update job card status
        $sql = "UPDATE jobcards SET status = 'manager_approved' WHERE JobCard_N0 = '$jobCardNo'";
        if (mysqli_query($con, $sql)) {
            // Send email notification to designers
            $emailSent = sendEmailToDesigners($jobCardNo);
            echo json_encode(['status' => 'success', 'emailSent' => $emailSent]);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Job card number is missing.']);
    }
    mysqli_close($con);
    exit();
}

// Retrieve projects data
$sql = "SELECT Date, JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size, status FROM jobcards ORDER BY created_at DESC LIMIT 5";
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

mysqli_close($con);
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
    <link href="https://fonts.googleapis.com/css2?family=Eczar:wght@400..800&display=swap" rel="stylesheet">
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

                <div class="row mt-3">
                    <div class="input-group">
                        <select id="projectDropdown" class="form-select" style="border-radius: 20px; width: 100%;" onchange="updateStatus()">
                            <option value="">SELECT PROJECT</option>
                            <?php foreach ($projects as $project) : ?>
                                <option value="<?php echo htmlspecialchars($project['status']); ?>" data-jobcard="<?php echo htmlspecialchars($project['JobCard_N0']); ?>">
                                    <?php echo htmlspecialchars($project['Project_Name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <br />
                <div class="status text-center">OVERALL PROJECT CURRENT STATUS</div>
                <div class="progress">
                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <div class="per dark-btn">
                    <p id="percentage" class="text-center">0%</p>
                </div>
            </div>

            <div class="col">
                <div class="card shadow-lg" style="border-radius: 20px;">
                    <div class="card-header bg-gradient-dark" style="border-radius: 20px 20px 0px 0px;">
                        <h2 class="display-7 text-center text-secondary fw-bold">Projects</h2>
                        <div class="text-center " style="width: 300px; margin-top: -40px; ">
                            <select id="statusFilter" class="form-select  text-white fw-bold" onchange="filterTable()" style="background-color: transparent; border: none; outline: none; border-radius: 20px; width: 50%;">
                                <option value="">Filter Status</option>
                                <option value="manager_approved">Manager Approved</option>
                                <option value="sales_done">Sales Done</option>
                                <option value="studio_done">Studio Done</option>
                                <option value="workshop_done">Workshop Done</option>
                                <option value="accounts_done">Accounts Done</option>
                            </select>
                            <script>
                                const statusFilter = document.getElementById('statusFilter');
                                statusFilter.style.background = 'linear-gradient(to right, #77c144, #77c144)';
                                statusFilter.style.transition = 'background 0.4s ease';
                                statusFilter.addEventListener('focus', () => {
                                    statusFilter.style.background = 'linear-gradient(to right, #77c144, #f7f7f7)';
                                });
                                statusFilter.addEventListener('blur', () => {
                                    statusFilter.style.background = 'linear-gradient(to right, #77c144, #77c144)';
                                });
                            </script>
                        </div>
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
                                <?php if (!empty($projects)) : ?>
                                    <?php foreach ($projects as $project) : ?>
                                        <tr data-status="<?php echo htmlspecialchars($project['status']); ?>" style="animation: fadeIn 0.4s ease-in-out;">
                                            <td><?php echo htmlspecialchars($project['Date'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['JobCard_N0'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Client_Name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Project_Name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Quantity'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Overall_Size'] ?? ''); ?></td>
                                            <td>
                                                <button id="btn-<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                                    class="btn btn-success <?php echo $project['status'] === 'sales_done'  ? '' : 'btn-inactive'; ?>"
                                                    onclick="approveProject('<?php echo htmlspecialchars($project['JobCard_N0']); ?>')"
                                                    <?php echo $project['status'] === 'sales_done' ? '' : 'disabled'; ?>>
                                                    <?php echo $project['status'] === 'manager_aprroved' ? 'Approve' : 'Approved'; ?>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="7">No recent projects found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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

            function updateStatus() {
                const dropdown = document.getElementById('projectDropdown');
                const selectedOption = dropdown.options[dropdown.selectedIndex];
                const status = selectedOption.value;
                const percentage = statusMapping[status] || 0;

                document.getElementById('progressBar').style.width = percentage + '%';
                document.getElementById('percentage').textContent = percentage + '%';
            }

            function approveProject(jobCardNo) {
                if (confirm('Are you sure you want to approve this project?')) {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'update_status.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                alert('Project status updated successfully!');
                                const button = document.getElementById('btn-' + jobCardNo);
                                button.classList.add('btn-inactive');
                                button.disabled = true;
                                button.textContent = 'Approved';
                                updateStatus();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        } else {
                            alert('An error occurred while updating the status.');
                        }
                    };
                    xhr.send('jobCardNo=' + encodeURIComponent(jobCardNo));
                }
            }

            function filterTable() {
                const filterValue = document.getElementById('statusFilter').value;
                const rows = document.querySelectorAll('#projectsTable tbody tr');

                rows.forEach(row => {
                    const status = row.getAttribute('data-status');
                    row.style.display = filterValue === '' || status === filterValue ? '' : 'none';
                });
            }
        </script>
</body>

</html>