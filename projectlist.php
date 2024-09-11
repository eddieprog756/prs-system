<?php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

// Handle status update request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['jobCardNo']) && !empty($_POST['jobCardNo'])) {
        $jobCardNo = mysqli_real_escape_string($con, $_POST['jobCardNo']);

        // Prepare the SQL statement
        $sql = "UPDATE jobcards SET status = 'manager_approved' WHERE JobCard_N0 = '$jobCardNo'";
        // Execute the query
        if (mysqli_query($con, $sql)) {
            echo 'Success';
        } else {
            // Error handling
            error_log("Error updating job card status: " . mysqli_error($con)); // Log the error to the server's error log
            echo 'Error: ' . mysqli_error($con); // Provide error message for debugging
        }
    } else {
        echo 'Error: Job card number is missing.';
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
} else {
    echo "No projects found";
}

mysqli_close($con);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Projects</title>
    <meta charset="UTF-8">
    <link rel="stylesheet">
    <link rel="shortcut icon" type="x-con" href="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Eczar:wght@400..800&display=swap" rel="stylesheet">
    <style>
        .pjectlink:hover {
            text-decoration: none;
            transition: 0.5s ease-out;
        }

        .container {
            margin-top: -50px;
        }
    </style>
    <style>
        .progress-bar {
            height: 30px;
            background-color: #77c144;
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
                <h1 class="text-center">CHECK PROJECT STATUS</h1>
                <div class="pname"><strong>CHOOSE PROJECT</strong> NAME:</div>
                <select id="projectDropdown" class="form-control" onchange="updateStatus()">
                    <option value=""><b>Choose</b></option>
                    <?php foreach ($projects as $project) : ?>
                        <option value="<?php echo htmlspecialchars($project['status']); ?>">
                            <?php echo htmlspecialchars($project['Project_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                </br>
                <div class="status">OVERALL PROJECT CURRENT STATUS</div>
                <div class="progress">
                    <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <div class="per">
                    <p id="percentage">0%</p>
                </div>
            </div>

            <div class="col">
                <div class="card" style="border-radius: 20px;">
                    <div class="card-header" style="background-color: #77c144;">
                        <h2 class="display-6 text-center text-white">Projects</h2>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered text-center">
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
                                        <tr>
                                            <td><?php echo htmlspecialchars($project['Date'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['JobCard_N0'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Client_Name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Project_Name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Quantity'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($project['Overall_Size'] ?? ''); ?></td>
                                            <td>
                                                <button
                                                    id="btn-<?php echo htmlspecialchars($project['JobCard_N0']); ?>"
                                                    class="btn btn-success <?php echo $project['status'] === 'sales_done' ? '' : 'btn-inactive'; ?>"
                                                    onclick="approveProject('<?php echo htmlspecialchars($project['JobCard_N0']); ?>')"
                                                    <?php echo $project['status'] === 'sales_done' ? '' : 'disabled'; ?>>
                                                    Approve
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

        function updateStatus() {
            const dropdown = document.getElementById('projectDropdown');
            const status = dropdown.value;
            const percentage = statusMapping[status] || 0;

            const progressBar = document.getElementById('progressBar');
            const percentageText = document.getElementById('percentage');

            progressBar.style.width = percentage + '%';
            percentageText.textContent = percentage + '%';
        }

        function approveProject(jobCardNo) {
            if (confirm('Are you sure you want to approve this project?')) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_status.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('Project status updated successfully!');

                        // Update the button to be inactive after successful update
                        const button = document.getElementById('btn-' + jobCardNo);
                        button.classList.add('btn-inactive');
                        button.disabled = true;
                        button.textContent = 'Approved';

                        // Optionally, refresh the status of the progress bar
                        updateStatus();
                    } else {
                        alert('An error occurred while updating the status.');
                    }
                };
                xhr.send('jobCardNo=' + encodeURIComponent(jobCardNo));
            }
        }
    </script>
</body>

</html>