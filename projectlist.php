<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

$sql = "SELECT JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size, status FROM jobcards ORDER BY created_at DESC LIMIT 5";
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
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./css/projectlist.css">
    <link rel="stylesheet" href="./css/status.css" />
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Eczar:wght@400..800&display=swap" rel="stylesheet">
    <style>
        .progress-bar {
            height: 30px;
            background-color: #77c144;
        }
    </style>
</head>

<body class="bg-green" style="font-size: 15px;">
    <?php include './partials/sidebar2.php'; ?>

    <div class="container" style="max-width: 1000px; float: right; margin-left:300px;">
        <div class="row mt-5">
            <div class="contents">
                <div class="pname"><strong>PROJECT</strong> NAME:</div>
                <select id="projectDropdown" class="form-control" onchange="updateStatus()">
                    <option value="">Select Project</option>
                    <?php foreach ($projects as $project) : ?>
                        <option value="<?php echo htmlspecialchars($project['status']); ?>">
                            <?php echo htmlspecialchars($project['Project_Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
                        <table class="table table-bordered text-center ">
                            <thead>
                                <tr class="bg-secondary" style="color:white">
                                    <!-- <th>Date</th> -->
                                    <th>Jobcard N0</th>
                                    <th>Client Name</th>
                                    <th>Project Name</th>
                                    <th>Quantity</th>
                                    <th>Overall Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($projects)) : ?>
                                    <?php foreach ($projects as $project) : ?>
                                        <tr>
                                            <!-- <td><?php echo htmlspecialchars($project['Date']); ?></td> -->
                                            <td><?php echo htmlspecialchars($project['JobCard_N0']); ?></td>
                                            <td><?php echo htmlspecialchars($project['Client_Name']); ?></td>
                                            <td><?php echo htmlspecialchars($project['Project_Name']); ?></td>
                                            <td><?php echo htmlspecialchars($project['Quantity']); ?></td>
                                            <td><?php echo htmlspecialchars($project['Overall_Size']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6">No recent projects found.</td>
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
    </script>
</body>

</html>