<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include './config/db.php';

// Function to manage asset paths (images, css, js, etc.)
function asset($path)
{
    return './' . $path;
}

// Approve Job Card Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_jobcard'])) {
    $jobcard_id = $_POST['approve_jobcard'];

    // Update the status to 'manager_approved' for the selected job card
    $stmt = $con->prepare("UPDATE jobcards SET status = 'manager_approved' WHERE id = ?");
    $stmt->bind_param("i", $jobcard_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Job card status updated successfully.';
    } else {
        $_SESSION['error'] = 'Failed to update job card status.';
    }

    $stmt->close();
    mysqli_close($con);

    // Refresh the page to reflect changes
    header('Location: ' . basename($_SERVER['PHP_SELF']));
    exit();
}

// Search Logic
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
    include './partials/sidebar2.php';
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

        <div class="add">

            <!-- Search Box -->
            <form method="POST" class="row g-3">
                <div class="col-auto">
                    <label for="inputPassword2" class="visually-hidden">search</label>
                    <input type="search" class="form-control" name="search_term" id="inputPassword2" placeholder="search" value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" name="search" class="btn btn-dark mb-3"><i class="fa fa-search"></i></button>
                </div>
            </form>

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

                                    <!-- Fix the undefined status warning -->
                                    <td><?php echo !empty($project['status']) ? htmlspecialchars($project['status']) : 'Pending'; ?></td>

                                    <!-- Action Button Logic -->
                                    <td>
                                        <?php
                                        if (isset($project['user_id'])) {
                                            $project_user_id = $project['user_id'];
                                            $current_status = isset($project['status']) ? $project['status'] : 'pending';

                                            if (in_array($current_status, ['manager_approved', 'studio_done', 'workshop_done', 'accounts_done'])) {
                                                echo '<span class="badge badge-success">Approved</span>';
                                            } else {
                                        ?>
                                                <form method="POST" action="">
                                                    <input type="hidden" name="approve_jobcard" value="<?php echo $project_user_id; ?>">
                                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                </form>
                                        <?php
                                            }
                                        } else {
                                            echo '<span class="badge badge-warning">User ID Missing</span>';
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
                <a class="pjectlink" href="projectlist.php/">VIEW ALL PROJECTS <i class="fa fa-project-diagram"></i></a>
            </div>
        </div>
        <div class="logodown" style="margin-top:-10px; margin-left:900px; position:fixed;">
            <img src="<?php echo asset('Images/BlackLogoo.png'); ?>" alt="">
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
                        <img src="<?php echo asset('Images/BlackLogoo.png'); ?>" alt="">
                    </div>
                    <form action="#" method="POST">

                        <div class="left">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            <i class="fa fa-bell" aria-hidden="true"></i>
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </div>

                        <div class="deets">
                            <div class="newp">
                                <strong>NEW</strong> PROJECT
                            </div>

                            <div class="top">
                                <label for="datePicker">Date : </label>
                                <input type="date" id="datePicker" name="Date" style=" padding-left: 15px; " required onchange="showSelectedDate()">
                                <p id="selectedDate"></p>
                                <label for="timePicker">Time : </label>
                                <input type="time" id="timePicker" name="Time" style=" padding-left: 15px; " required onclick="setCurrentTime()">
                                <p id="currentTime"></p>
                                <label for="fname">Job Card No : </label>
                                <input type="text" id="fname" name="JobCard_N0" style="width: 170px;  padding-left: 15px;  " required>
                            </div>

                            <label for="fname">Client Name & Contact:</label>
                            <input type="text" id="fname" name="Client_Name" style="width: 389px;  padding-left: 15px;" required> <br>

                            <label for="surname">Project Name :</label>
                            <input type="text" id="surname" name="Project_Name" style="width: 479px;  padding-left: 15px;" required> <br>

                            <div class="qty">
                                <label for="Quantity">Quantity : </label>
                                <input type="text" id="Quantity" name="Quantity" style="width: 186px;  padding-left: 15px;" required>
                                <label for="Overall Size">Overall Size : </label>
                                <input type="text" id="Overall Size" name="Overall_Size" style="width: 190px;  padding-left: 15px;" required>
                            </div>

                            <div class="dates">
                                <label for="datePicker">Delivery Date : </label>
                                <input type="date" id="datePicker" name="Delivery_Date" style="width: 157.2px;  padding-left: 15px;" required onchange="showSelectedDate()">
                                <p id="selectedDate"></p>
                                <label for="datePicker">Date Delivered : </label>
                                <input type="date" id="datePicker" name="Date_Delivered" style="width: 178px;  padding-left: 15px;" required onchange="showSelectedDate()">
                                <p id="selectedDate"></p>
                            </div>
                        </div>

                        <div class="job">
                            <div class="bold">
                                <strong>Jobs Costing and Drawing Information</strong>
                            </div>
                            <div class="light">
                                Description of Service/Item
                            </div>
                            <textarea name="Job_Description" id="jobd" cols="30" rows="10" style="border-radius: 10px; padding-left: 15px;" required></textarea>
                        </div>

                        <div class="qty">
                            <label for="Prepaired">Prepaired By : </label>
                            <input type="text" id="Prepaired" name="Prepaired_By" style="width: 186px;  padding-left: 15px;" required>
                            <label for="OverallSize2"><strong>Total Charged</strong> : </label>
                            <input type="text" id="Overall Size" name="Total_Charged" style="width: 136px;  padding-left: 15px;" required>
                        </div>
                        <div class="sub">
                            <input type="submit" id="submittbtn" name="submit" value="SAVE PROJECT" onclick="saveDetails()">
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