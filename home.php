<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


include './config/db.php';
// Fetch user data from the database for the logged-in user
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, profile_pic FROM users WHERE id = ?";
$stmt = $con->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
} else {
    echo "Error: " . $con->error;
    exit();
}

// Default profile picture if none is set
if (empty($user_data['profile_pic'])) {
    $user_data['profile_pic'] = './Images/default_profile.JPG';
}

// Auto-generate JobCard_N0
function generateJobCardNumber($con)
{
    $lastJobCardQuery = "SELECT JobCard_N0 FROM jobcards ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con, $lastJobCardQuery);
    if ($row = mysqli_fetch_assoc($result)) {
        $lastNumber = (int) filter_var($row['JobCard_N0'], FILTER_SANITIZE_NUMBER_INT);
        return 'JCN' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }
    return 'JCN00001';
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
$currentTime = date('H:i:s');

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
    $status = 'project';

    $stmt = $con->prepare("INSERT INTO jobcards (Date, Time, JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size, Delivery_Date, Job_Description, Prepaired_By, Total_Charged, created_at, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $currentDate, $currentTime, $JobCard_N0, $Client_Name, $Project_Name, $Quantity, $Overall_Size, $Delivery_Date, $Job_Description, $Prepaired_By, $Total_Charged, $currentDate, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Project created successfully!'); window.location.href='" . basename($_SERVER['PHP_SELF']) . "';</script>";
    } else {
        $error_message = mysqli_error($con);
        echo "<script>alert('Error saving project: $error_message'); window.location.href='" . basename($_SERVER['PHP_SELF']) . "';</script>";
    }

    $stmt->close();
    mysqli_close($con);
    exit();
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
    <link rel="stylesheet" href="css/home.css">
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <style>
        .container {
            margin-top: -30px;
        }

        .add-project-btn {
            display: flex;
            align-items: center;
            background-color: #77c144;
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
            background-color: #77c144;
            color: white;
        }

        .badge-rejected {
            background-color: #dc3545;
            color: white;
        }

        .notification {
            position: relative;
            cursor: pointer;
        }

        .badge-counter {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 12px;
        }

        .dropdown-menu {
            width: 300px;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
    </style>
</head>

<body style="background-image: linear-gradient(rgba(255,255,255,0.8), rgba(255,255,255,0.8)), url('./Images/bg.JPG'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 100vh; overflow: hidden;">

    <?php include './sidebar.php'; ?>

    <!-- Notifications Icons -->
    <div class="left" style="margin-top: 10px;">
        <a href="./reports.php"><i class="fa fa-calendar text-secondary" aria-hidden="true"></i></a>
        <div class="notification dropdown">
            <i class="fa fa-bell fa-2x text-secondary" id="notificationIcon" data-bs-toggle="dropdown" aria-expanded="false"></i>
            <span class="badge-counter" id="notificationCount">0</span>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationIcon">
                <li class="dropdown-header">Notifications</li>
                <div id="notificationList" class="px-3">
                    <li class="dropdown-item text-muted">No new notifications</li>
                </div>
                <li>
                    <button class="dropdown-item text-center text-primary" id="clearNotifications" style="display: none;">
                        Clear Notifications
                    </button>
                </li>
            </ul>
        </div>

        <a href="user_profile.php"><i class="fa fa-cog text-secondary" aria-hidden="true"></i></a>
        <!-- User Profile Picture -->
        <div class="dropdown ms-3">
            <a href="./user_profile.php">
                <img src="<?php echo $user_data['profile_pic']; ?>" alt="Profile" class="profile-pic" id="profilePic" data-bs-toggle="dropdown" aria-expanded="false">
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- Bootstrap Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

    <script>
        const newProjects = [{
            id: 1,
            name: "New Project added"
        }, {
            id: 2,
            name: "Jobcard JCN09802 Finished!"
        }];
        const notificationCount = document.getElementById("notificationCount");
        const notificationList = document.getElementById("notificationList");
        const clearNotifications = document.getElementById("clearNotifications");

        function loadNotifications() {
            const count = newProjects.length;
            notificationCount.textContent = count;
            if (count > 0) {
                notificationCount.style.display = "inline";
                notificationList.innerHTML = "";
                newProjects.forEach(project => {
                    const listItem = document.createElement("li");
                    listItem.classList.add("dropdown-item");
                    listItem.textContent = `New: ${project.name}`;
                    notificationList.appendChild(listItem);
                });
                clearNotifications.style.display = "block";
            } else {
                notificationCount.style.display = "none";
                notificationList.innerHTML = `<li class="dropdown-item text-muted">No new notifications</li>`;
                clearNotifications.style.display = "none";
            }
        }

        document.getElementById("notificationIcon").addEventListener("click", () => {
            loadNotifications();
        });

        clearNotifications.addEventListener("click", () => {
            newProjects.length = 0;
            loadNotifications();
        });

        loadNotifications();
    </script>
    <div class="boxx1">
        <div class="miniboxx">
        </div>

        <?php
        $sql = "SELECT JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size FROM jobcards ORDER BY created_at ASC LIMIT 3";
        $result = mysqli_query($con, $sql);

        if (!$result) {
            die("Error executing query: " . mysqli_error($con));
        }
        $projects = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $projects[] = $row;
        }

        $user_id = $_SESSION['user_id'];
        $sql = "SELECT username, role FROM users WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($username, $role);
        $stmt->fetch();
        $stmt->close();
        $con->close();

        $hour = date('H');
        if ($hour >= 1 && $hour < 12) {
            $greeting = 'Good Morning ';
        } elseif ($hour >= 12 && $hour < 17) {
            $greeting = 'Good Afternoon ';
        } else {
            $greeting = 'Good Evening ';
        }

        ?>
        <style>
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .h {
                animation: fadeIn 0.5s ease-in-out;
            }

            .a {
                animation: fadeIn 0.6s ease-in;
            }
        </style>

        <div class="boxx2">
            <div class="hellotext">
                <div class="hello">
                    <div class="h"><strong><?php echo $greeting; ?></strong></div>
                    <div class="W"><strong><?php echo htmlspecialchars(', ' . ucwords($username)); ?></strong></div>
                </div>
                <div class="welcome">
                    <div class="s">STRAWBERRY </div>
                    <div class="a">ADVERTISING LTD</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bottombox">
        <div class="row">
            <?php

            include './config/db.php';

            if (!$con) {
                die("Database connection failed: " . mysqli_connect_error());
            }

            $search_term = '';
            $projects = []; // Initialize projects array

            if (isset($_POST['search'])) {
                $search_term = $_POST['search_term'];
                $search_query = "%" . $search_term . "%";

                // Check if prepare() is successful
                if ($stmt = $con->prepare("SELECT * FROM jobcards WHERE JobCard_N0 LIKE ? OR Client_Name LIKE ? OR Project_Name LIKE ?")) {
                    $stmt->bind_param("sss", $search_query, $search_query, $search_query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $projects = $result->fetch_all(MYSQLI_ASSOC); // fetch results based on search
                    $stmt->close();
                } else {
                    die("Error preparing query: " . $con->error);
                }
            } else {
                // Default: fetch all projects if no search is performed
                if ($stmt = $con->prepare("SELECT * FROM jobcards LIMIT 3")) {
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $projects = $result->fetch_all(MYSQLI_ASSOC);
                    $stmt->close();
                } else {
                    die("Error preparing query: " . $con->error);
                }
            }

            ?>
            <!-- Search Box -->
            <form method="POST" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" class="row g-3 col">
                <div class="col-auto" style="margin-top: 10px; width: 40%; margin-left:20px;">
                    <input type="search" class="form-control" name="search_term" placeholder="Search" value="<?php echo htmlspecialchars($search_term); ?>">
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
                        <th>#</th>
                        <th>Job Card No</th>
                        <th>Client Name</th>
                        <th>Project Name</th>
                        <th>Quantity</th>
                        <th>Overall Size</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                                    if ($status === 'project') {
                                        $statusClass = 'badge-pending';
                                    } elseif (in_array($status, ['manager_approved', 'sales_done', 'studio_done', 'workshop_done', 'accounts_done'])) {
                                        $statusClass = 'badge-approved';
                                    } else {
                                        $statusClass = 'badge-pending';
                                    }
                                    echo "<span class='badge-status $statusClass'>" . htmlspecialchars($status) . "</span>";
                                    ?>
                                </td>
                                <td>
                                    <a href="projectlist.php?id=<?php echo $project['JobCard_N0']; ?>" class="btn btn-dark btn-sm">Take Action</a>
                                    <a href="delete_project.php?id=<?php echo $project['JobCard_N0']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this project?')">Remove</a>
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
                    <form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return confirm('Are you sure you want to add this Job Card?');" style="margin-left: 40px;">
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
                                    <label for="Prepaired_By"><strong>Prepared By :</strong></label>
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
                                    <label for="Delivery_Date"><strong>Delivery Date :</strong></label>
                                    <input type="date" id="Delivery_Date" name="Delivery_Date" class="form-control" required>
                                </div>

                                <div class="job col">
                                    <label class="text-left "><strong>Job Description</strong></label>
                                    <input name="Job_Description" id="Job_Description" type="text" class="form-control" required></input>
                                </div>
                            </div>

                            <div class="qty row">
                                <div class="col">
                                    <label for="Total_Charged"><strong>Total Charged :</strong></label>
                                    <input type="text" id="Total_Charged" name="Total_Charged" class="form-control" required>
                                </div>
                                <div class="col">
                                    <div class="sub text-center">
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