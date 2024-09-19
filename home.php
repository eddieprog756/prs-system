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
    $Date_Delivered = $_POST['Date_Delivered']; // Added Date Delivered field
    $Job_Description = $_POST['Job_Description'];
    $Prepaired_By = $_POST['Prepaired_By'];
    $Total_Charged = $_POST['Total_Charged'];
    $status = 'pending'; // Default status

    // Insert into jobcards table
    $stmt = $con->prepare("INSERT INTO jobcards (Date, Time, JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size, Delivery_Date, Date_Delivered, Job_Description, Prepaired_By, Total_Charged, created_at, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssss", $currentDate, $currentTime, $JobCard_N0, $Client_Name, $Project_Name, $Quantity, $Overall_Size, $Delivery_Date, $Date_Delivered, $Job_Description, $Prepaired_By, $Total_Charged, $currentDate, $status);

    if ($stmt->execute()) {
        $_SESSION['success'] = "New project saved successfully!";
    } else {
        $_SESSION['error'] = "Error saving project. Please try again.";
    }

    $stmt->close();
    mysqli_close($con);

    // Refresh the page or redirect after submission
    header("Location: " . basename($_SERVER['PHP_SELF']));
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

</head>

<body>

    <?php
    include './sidebar2.php';
    ?>

    <div class="left">
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <i class="fa fa-bell" aria-hidden="true"></i>
        <i class="fa fa-cog" aria-hidden="true"></i>
    </div>

    <?php
    include './partials/greetings.php';
    ?>

    <div class="bottombox">

        <d class="add">

            <div class="row">

                <!-- Search Box -->
                <form method="POST" class="row g-3 col">
                    <div class="col-auto">
                        <label for="inputPassword2" class="visually-hidden">search</label>
                        <input type="search" class="form-control" name="search_term" id="inputPassword2" placeholder="search" value="<?php echo htmlspecialchars($search_term); ?>">
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="search" class="btn btn-dark mb-3"><i class="fa fa-search"></i></button>
                    </div>
                </form>

                <!-- Add Jobcard button -->
                <div class="Addp col" id="historyBtn">

                    <div class="plus">
                        <i class="fa fa-plus"></i>
                    </div>
                    <div class="row">
                        <div class="addnew ">
                            <strong>ADD</strong> NEW PROJECT
                        </div>
                    </div>
                </div>
            </div>


            <!-- Projects Table Starting -->
            <div class="container mt-5">
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
                            <th scope="col">Action</th>
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
                                        $status = $project['status'] ?? 'Pending'; // Handle missing 'status' key
                                        echo htmlspecialchars($status);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (isset($project['status']) && $project['status'] === 'sales_done') {
                                        ?>
                                            <form method="POST" action="">
                                                <input type="hidden" name="approve_jobcard" value="<?php echo $project['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                            </form>
                                        <?php
                                        } else {
                                            echo '<button class="btn btn-secondary btn-sm" disabled>Approved</button>';
                                        }
                                        ?>
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


            <div class="viewpjects w-10" style="margin-left:20px; font-weight: 700;">
                <a class="pjectlink" href="./projectlist.php">VIEW ALL PROJECTS <i class="fa fa-project-diagram"></i></a>
            </div>
    </div>
    <div class="logodown" style="margin-top:150px; margin-right: 100px; ">
        <img src="./Images/BlackLogoo.png" alt="">
    </div>
    </div>

    <div class="imgclick">
        <img src="<?php echo asset('Images/BlackLogoo.png'); ?>" class="menu-icon" onclick="toggleMobileMenu()">
    </div>

    <!-- THE POP UP...................................... -->
    <div class="popup-container" id="popupContainer">
        <div class="popup">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <div class="leftlogo">
                <div class="other">
                    <div class="llogo">
                        <img src="./Images/BlackLogoo.png" alt="">
                    </div>
                    <!-- The form for submitting new jobcard -->
                    <form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="POST" style="margin-left: 40px;">

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
        var hideHistoryButton = localStorage.getItem("hideHistoryButton");

        if (hideHistoryButton === "true") {
            document.getElementById("historyBtn").style.display = "none";
        }

        // Toggle Mobile Menu
        function toggleMobileMenu() {
            var mobileMenu = document.querySelector(".sidenav");
            mobileMenu.style.display = (mobileMenu.style.display === "block") ? "none" : "block";
        }

        // Open and close popup
        function openPopup() {
            var popup = document.getElementById("popupContainer");
            popup.style.display = "flex";
        }

        function closePopup() {
            var popup = document.getElementById("popupContainer");
            popup.style.display = "none";
        }

        document.querySelector('.Addp').addEventListener('click', openPopup);

        function saveDetails() {
            var surname = document.getElementById("surname").value;
            localStorage.setItem("surname", surname);
        }

        var detailsDiv = localStorage.getItem("surname");
        localStorage.setItem("detailsDiv", detailsDiv);
    </script>

    <!-- Bootstrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

</body>

</html>