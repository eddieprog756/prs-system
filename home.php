<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include './config/db.php';

// Auto-generate JobCard_N0
function generateJobCardNumber($con)
{
    $lastJobCardQuery = "SELECT JobCard_N0 FROM jobcards ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con, $lastJobCardQuery);
    if ($row = mysqli_fetch_assoc($result)) {
        $lastNumber = (int) filter_var($row['JobCard_N0'], FILTER_SANITIZE_NUMBER_INT);
        return 'JCN' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }
    return 'JCN00001'; // Start from JCN00001 if no previous job cards exist
}

// Fetch the current logged-in user’s name
function getPreparedBy($con, $userId)
{
    $stmt = $con->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['full_name'];
    }
    return 'Unknown User';
}

$jobCardNumber = generateJobCardNumber($con);
$preparedBy = getPreparedBy($con, $_SESSION['user_id']);
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s'); // Store the current time for the `Time` field

// Handle form submission to save the project in the jobcards table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $JobCard_N0 = $_POST['JobCard_N0'];
    $Client_Name = $_POST['Client_Name'];
    $Project_Name = $_POST['Project_Name'];
    $Quantity = $_POST['Quantity'];
    $Overall_Size = $_POST['Overall_Size'];
    $Delivery_Date = $_POST['Delivery_Date'];
    $Job_Description = $_POST['Job_Description'];
    $Prepaired_By = $_POST['Prepaired_By'];
    $Total_Charged = $_POST['Total_Charged'];
    $status = 'project'; // Set status to 'project' when creating a new job card

    // Insert into jobcards table
    $stmt = $con->prepare("INSERT INTO jobcards (Date, Time, JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size, Delivery_Date, Job_Description, Prepaired_By, Total_Charged, created_at, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $currentDate, $currentTime, $JobCard_N0, $Client_Name, $Project_Name, $Quantity, $Overall_Size, $Delivery_Date, $Job_Description, $Prepaired_By, $Total_Charged, $currentDate, $status);

    if ($stmt->execute()) {
        echo "<script>
                alert('Project created successfully!');
                window.location.href='" . basename($_SERVER['PHP_SELF']) . "';
              </script>";
    } else {
        // Show detailed error if project creation fails
        $error_message = mysqli_error($con);
        echo "<script>
                alert('Error saving project: $error_message');
                window.location.href='" . basename($_SERVER['PHP_SELF']) . "';
              </script>";
    }

    $stmt->close();
    mysqli_close($con);
    exit();
}

// Asset path helper
function asset($path)
{
    return './' . $path;
}

// Fetch projects logic
$search_term = '';
if (isset($_POST['search'])) {
    $search_term = $_POST['search_term'];
    $stmt = $con->prepare("SELECT * FROM jobcards WHERE JobCard_N0 LIKE ? OR Client_Name LIKE ? OR Project_Name LIKE ?");
    $search_term = '%' . $search_term . '%';
    $stmt->bind_param("sss", $search_term, $search_term, $search_term);
} else {
    $stmt = $con->prepare("SELECT * FROM jobcards");
}

$stmt->execute();
$result = $stmt->get_result();
$projects = $result->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?php echo asset('css/home.css'); ?>">
    <link rel="shortcut icon" type="x-con" href="<?php echo asset('Images/PR Logo.png'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Eczar:wght@400..800&display=swap" rel="stylesheet">
    <style>
        .pjectlink:hover {
            text-decoration: none;
            transition: 0.5s ease-out;
        }

        .container {
            margin-top: -30px;
        }

        .add-project-btn {
            display: flex;
            align-items: center;
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-project-btn i {
            margin-right: 10px;
        }

        .add-project-btn:hover {
            background-color: #218838;
        }

        .badge-status {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 5px;
        }

        .badge-pending {
            background-color: #ffc107;
            color: white;
        }

        .badge-approved {
            background-color: #28a745;
            color: white;
        }

        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }
    </style>

    <script>
        function confirmSaveProject() {
            return confirm('Are you sure you want to add this Job Card?');
        }
    </script>
</head>

<body>

    <?php include './sidebar2.php'; ?>

    <div class="left">
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <i class="fa fa-bell" aria-hidden="true"></i>
        <i class="fa fa-cog" aria-hidden="true"></i>
    </div>

    <?php include './partials/greetings.php'; ?>

    <div class="bottombox">

        <div class="row">

            <!-- Search Box -->
            <form method="POST" class="row g-3 col">
                <div class="col-auto" style="margin-top: 10px; width: 40%; margin-left:20px;">
                    <label for="inputPassword2" class="visually-hidden">Search</label>
                    <input type="search" class="form-control" name="search_term" id="inputPassword2" placeholder="Search" value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                <div class="col-auto" style="margin-top: 10px; width: 40%; margin-left:-10px;">
                    <button type="submit" name="search" class="btn btn-dark mb-3"><i class="fa fa-search"></i></button>
                </div>
            </form>

            <!-- Add Project Button -->
            <div class="col">
                <div class="add-project-btn" style="margin-top: 20px; width: 40%; margin-left:300px;" onclick="openPopup()">
                    <i class="fas fa-plus-circle"></i>
                    ADD NEW PROJECT
                </div>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="container mt-4">
            <h3>Recent Projects</h3>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Job Card No</th>
                        <th scope="col">Client Name</th>
                        <th scope="col">Project Name</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Overall Size</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <?php if (!empty($projects)) : ?>
                        <?php foreach ($projects as $index => $project) : ?>
                            <tr>
                                <th scope="row"><?php echo $index + 1; ?></th>
                                <td><?php echo htmlspecialchars($project['JobCard_N0']); ?></td>
                                <td><?php echo htmlspecialchars($project['Client_Name']); ?></td>
                                <td><?php echo htmlspecialchars($project['Project_Name']); ?></td>
                                <td><?php echo htmlspecialchars($project['Quantity']); ?></td>
                                <td><?php echo htmlspecialchars($project['Overall_Size']); ?></td>
                                <td>
                                    <?php
                                    $status = $project['status'] ?? 'pending';
                                    $statusClass = $status === 'approved' ? 'badge-approved' : ($status === 'rejected' ? 'badge-rejected' : 'badge-pending');
                                    echo "<span class='badge-status $statusClass'>" . htmlspecialchars($status) . "</span>";
                                    ?>
                                </td>
                                <td>
                                    <a href="edit_project.php?id=<?php echo $project['JobCard_N0']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete_project.php?id=<?php echo $project['JobCard_N0']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this project?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8">No projects found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="viewprojects w-10">
            <a class="pjectlink text-success" href="./projectlist.php" style="margin-left:20px; font-weight: 700; text-decoration:none; ">VIEW ALL PROJECTS <i class="fa fa-project-diagram"></i></a>
        </div>
    </div>

    <!-- Popup Form for Adding Project -->
    <div class="popup-container" id="popupContainer">
        <div class="popup">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <div class="leftlogo">
                <div class="other">
                    <div class="llogo">
                        <img src="./Images/BlackLogoo.png" alt="">
                    </div>
                    <!-- The form for submitting new jobcard -->
                    <form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return confirmSaveProject();" style="margin-left: 40px;">
                        <div class="deets">
                            <div class="row">
                                <div class="col">
                                    <label for="JobCard_N0"><strong>Job Card No :</strong></label>
                                    <input type="text" id="JobCard_N0" name="JobCard_N0" class="form-control" value="<?php echo $jobCardNumber; ?>" readonly>
                                </div>
                                <div class="col">
                                    <label for="Client_Name"><strong>Client Name & Contact :</strong></label>
                                    <input type="text" id="Client_Name" name="Client_Name" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <label for="Project_Name"><strong>Project Name :</strong></label>
                                    <input type="text" id="Project_Name" name="Project_Name" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label for="Prepaired_By"><strong>Prepaired By :</strong></label>
                                    <input type="text" id="Prepaired_By" name="Prepaired_By" class="form-control" value="<?php echo $preparedBy; ?>" readonly>
                                </div>
                            </div>

                            <div class="qty row">
                                <div class="col">
                                    <label for="Quantity"><strong>Quantity :</strong></label>
                                    <input type="text" id="Quantity" name="Quantity" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label for="Overall_Size"><strong>Overall Size :</strong></label>
                                    <input type="text" id="Overall_Size" name="Overall_Size" class="form-control" required>
                                </div>
                            </div>

                            <div class="dates row">
                                <div class="col">
                                    <label for="Delivery_Date"><strong> Date :</strong> </label>
                                    <input type="date" id="Delivery_Date" name="Delivery_Date" class="form-control" value="<?php echo $currentDate; ?>" readonly>
                                </div>

                                <div class="job col">
                                    <label class="text-left "> <strong>Other Information</strong></label>
                                    <input name="Job_Description" id="Job_Description" type="text" class="form-control" required></input>
                                </div>
                            </div>

                            <div class="qty row">
                                <div class="col">
                                    <label for="Total_Charged"><strong>Total Charged :</strong></label>
                                    <input type="text" id="Total_Charged" name="Total_Charged" class="form-control" required>
                                </div>
                                <div class="col">
                                    <div class="sub text-center ">
                                        <input type="submit" name="submit" value="SAVE PROJECT" class="btn btn-success">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPopup() {
            var popup = document.getElementById("popupContainer");
            popup.style.display = "flex";
        }

        function closePopup() {
            var popup = document.getElementById("popupContainer");
            popup.style.display = "none";
        }

        document.querySelector('.add-project-btn').addEventListener('click', openPopup);
    </script>

    <!-- Bootstrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>

</html>