<?php
session_start();

require_once 'config/db.php';
require 'vendor/autoload.php'; // Include PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Simulate login and set user_id in session
$_SESSION['user_id'] = 1; // Replace with actual user ID after a successful login

// Function to send email notifications to designers
function sendEmailToDesigners($jobCardNo)
{
    global $con;

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

// Retrieve projects data
$sql = "SELECT Date, JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size, status, Payment_Proof FROM jobcards ORDER BY created_at DESC LIMIT 5";
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
        <div class="row mt-5" style="width:1000px; margin-left: 80px;">
            <div class="contents">
                <h2 class="text-center fs-2" style="font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 700; color: #4a4a4a; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 20px;">
                    Check Project Status
                </h2>

                <div class="mb-3" style="display: flex; justify-content: center; align-items: center;">
                    <label class="font-weight-bold me-2">PROJECT NAME:</label>
                    <select id="projectDropdown" class="form-select rounded-pill" style="width: 300px;" onchange="updateStatus()">
                        <option value="">Select Project</option>
                        <?php foreach ($projects as $project): ?>
                            <option
                                value="<?php echo htmlspecialchars($project['status']); ?>"
                                data-jobcard="<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                style="background-color: <?php echo $project['status'] === 'accounts_done' ? '#74c444' : '#fff'; ?>;">
                                <?php echo htmlspecialchars($project['Project_Name']) . ' - ' . htmlspecialchars($project['JobCard_N0']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                    <div class="card-header" style="border-radius: 20px 20px 0px 0px;">
                        <h2 class="display-7 text-center text-secondary fw-bold">Projects</h2>



                        <div class="text-center mt-3" style="width: 300px; margin-top: -10px;">
                            <select id="statusFilter" class="form-select text-white fw-bold bg-dark" onchange="filterTable()" style="border: none; outline: none; border-radius: 20px; width: 50%;">
                                <option value="">Filter Status</option>
                                <option value="manager_approved">Manager Approved</option>
                                <option value="sales_done">sales Done</option>
                                <option value="studio_done">Studio Done</option>
                                <option value="workshop_done">Workshop Done</option>
                                <option value="accounts_done">Accounts Done</option>
                            </select>
                        </div>
                    </div>
                    <!-- Search Field -->
                    <div class="row mt-2 ">
                        <form method="GET" action="" class="d-flex" style="width: 30%; margin-left:600px; margin-top: -56px;">
                            <input type="text" name="search_term" class="form-control" placeholder="Search by Job Card No, Client Name, or Project Name" value="<?php echo htmlspecialchars($_GET['search_term'] ?? ''); ?>">
                            <button type="submit" class="btn btn-dark ml-2" style="height: 38px; width:40px; ">
                                <i class="fa fa-search justify-content-center"></i> Search
                            </button>
                        </form>
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
                                <?php
                                // Add search functionality
                                $search_term = $_GET['search_term'] ?? '';
                                $projects_filtered = $projects;

                                if (!empty($search_term)) {
                                    $projects_filtered = array_filter($projects, function ($project) use ($search_term) {
                                        return stripos($project['Client_Name'], $search_term) !== false ||
                                            stripos($project['Project_Name'], $search_term) !== false ||
                                            stripos($project['JobCard_N0'], $search_term) !== false;
                                    });
                                }

                                if (!empty($projects_filtered)) :
                                    foreach ($projects_filtered as $project) :
                                ?>
                                        <tr data-status="<?php echo htmlspecialchars($project['status']); ?>" style="animation: fadeIn 0.4s ease-in-out;">
                                            <td><?php echo htmlspecialchars($project['Date'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['JobCard_N0'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Client_Name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Project_Name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Quantity'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Overall_Size'] ?? ''); ?></td>
                                            <td style="display: flex; align-items: center; justify-content: space-between;">
                                                <?php if ($project['status'] === 'project') : ?>
                                                    <button id="btn-<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                                        class="btn btn-secondary btn-inactive btn-sm"
                                                        style="border: none; border-radius: 40px; width: 120px;"
                                                        disabled>
                                                        sales
                                                    </button>
                                                <?php elseif ($project['status'] === 'sales_done') : ?>
                                                    <button id="btn-<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                                        class="btn btn-dark btn-sm"
                                                        onclick="approveProject('<?php echo htmlspecialchars($project['JobCard_N0']); ?>')" style="border: none; border-radius: 40px; width: 120px;">
                                                        Approve
                                                    </button>
                                                <?php elseif ($project['status'] === 'manager_approved') : ?>
                                                    <button id="btn-<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                                        class="btn btn-secondary btn-inactive btn-sm"
                                                        disabled style="border: none; border-radius: 40px; width: 120px;">
                                                        Workshop
                                                    </button>
                                                <?php elseif ($project['status'] === 'studio_done') : ?>
                                                    <button id="btn-<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                                        class="btn btn-success btn-sm"
                                                        onclick="approveProject('<?php echo htmlspecialchars($project['JobCard_N0']); ?>')" style="border: none; border-radius: 40px; width: 120px;">
                                                        Accounts
                                                    </button>
                                                <?php elseif ($project['status'] === 'workshop_done') : ?>
                                                    <button id="btn-<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                                        class="btn btn-secondary btn-inactive btn-sm"
                                                        disabled style="border: none; border-radius: 40px; width: 120px;">
                                                        Accounts
                                                    </button>
                                                <?php elseif ($project['status'] === 'accounts_done') : ?>
                                                    <button id="btn-<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                                        class="btn btn-success btn-sm" style="background-color: #74c444; border: none; border-radius: 40px; width: 120px;" disabled>
                                                        Project Finished
                                                    </button>
                                                <?php endif; ?>

                                                <button class="btn btn-secondary btn-sm"
                                                    onclick="viewProof('<?php echo htmlspecialchars($project['JobCard_N0']); ?>')"
                                                    style="border-radius: 20px; margin-left: 10px;">
                                                    <i class="fa fa-eye"></i> Payment
                                                </button>

                                            </td>
                                        </tr>
                                    <?php endforeach;
                                else : ?>
                                    <tr>
                                        <td colspan="7">No projects found matching your search.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- Popup for Viewing Proof -->
        <div id="proofPopup" class="popup-container" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center;">
            <div class="popup-content" style="background: #fff; border-radius: 0.5rem; padding: 1rem; width: 90vw; max-width: 650px; text-align: center; position: relative;">
                <span class="close-btn" onclick="closeProofPopup()" style="position: absolute; top: 0.5rem; right: 0.5rem; cursor: pointer; font-size: 1.2rem;">&times;</span>
                <h5 style="margin-top: 0;">Proof of Payment</h5>
                <div id="proofDisplay" style="margin-top: 1rem;">
                    <!-- Proof content (image/pdf) will be dynamically inserted here -->
                </div>
            </div>
        </div>

        <script>
            async function viewProof(jobCardNo) {
                try {
                    const response = await fetch(`get_payment_proof.php?jobCardNo=${jobCardNo}`);
                    const data = await response.json();

                    const proofDisplay = document.getElementById('proofDisplay');
                    proofDisplay.innerHTML = ''; // Clear previous content

                    if (data.success) {
                        const proofPath = data.proofPath;

                        if (proofPath.match(/\.(jpeg|jpg|png|gif)$/i)) {
                            proofDisplay.innerHTML = `<img src="./uploads/payment_proofs/${proofPath}" alt="Proof of Payment" style="max-width: 100%; height: auto;" />`;
                        } else if (proofPath.match(/\.pdf$/i)) {
                            proofDisplay.innerHTML = `<embed src="./uploads/payment_proofs/${proofPath}" type="application/pdf" width="100%" height="500px" />`;
                        } else {
                            proofDisplay.innerHTML = `<p>Unsupported file format: ${proofPath}</p>`;
                        }

                        document.getElementById('proofPopup').style.display = 'flex';
                    } else {
                        proofDisplay.innerHTML = `<p style="color: red;">${data.message}</p>`;
                        document.getElementById('proofPopup').style.display = 'flex';
                    }
                } catch (error) {
                    alert('Error fetching payment proof.');
                }
            }

            function closeProofPopup() {
                document.getElementById('proofPopup').style.display = 'none';
            }
        </script>



        <script>
            const statusMapping = {
                'project': 10,
                'sales_done': 20,
                'manager_approved': 40,
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