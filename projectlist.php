<?php

session_start();
/*require_once('config/db.php');
$query = "select * from jobcard";
$result = mysqli_query($con,$query);
*/

require_once 'config/db.php';
require_once 'config/functions.php';
$sql = "SELECT JobCard_N0, Client_Name, Project_Name, Date, Quantity, Delivery_Date, Prepaired_By, Total_Charged FROM jobcards ORDER BY created_at DESC LIMIT 5";
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
$result = display_data();

mysqli_close($con);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/projectlist.css">
    <link rel="stylesheet" href="./css/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/projectlist.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png">
    <link rel="stylesheet" href="./css/https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
</head>

<body class="bg-green" style="font-size: 15px;">
    <?php
    include './partials/sidebar.php';
    ?>

    <div class=" " style="color: #111;">
        <a href="home.php"><i class="fa fa-chevron-left .text-black " style="color: #111; font-size:24px; margin-left: 10px; margin-top:10px;" aria-hidden="true"></i></a>
    </div>

    <div class="container" style="max-width: 1000px; float: right; margin-left:300px;">
        <div class="row mt-5">
            <div class="col">
                <div class="card">
                    <div class="card-header" style="background-color: #77c144;">
                        <h2 class="display-6 text-center">Projects</h2>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered text-center">
                            <tr class="" style="background-color: #111; color:white">
                                <td>Date</td>
                                <td>Jobcard N0</td>
                                <td>Client Name</td>
                                <td>Project Name</td>
                                <td>Quantity</td>
                                <td>Delivery Date</td>
                                <td>Prepaired By</td>
                                <td>Total Charged</td>
                            </tr>
                            <?php if (!empty($projects)) : ?>
                                <?php foreach ($projects as $project) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($project['Date']); ?></td>
                                        <td><?php echo htmlspecialchars($project['JobCard_N0']); ?></td>
                                        <td><?php echo htmlspecialchars($project['Client_Name']); ?></td>
                                        <td><?php echo htmlspecialchars($project['Project_Name']); ?></td>
                                        <td><?php echo htmlspecialchars($project['Quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($project['Delivery_Date']); ?></td>
                                        <td><?php echo htmlspecialchars($project['Prepaired_By']); ?></td>
                                        <td><?php echo htmlspecialchars($project['Total_Charged']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8">No recent projects found.</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!--<div class="sidenav">
        <div class="back">
                    <a href="home.php"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                </div>
        <div class="logo">
            <img src="Images/PR Logo.png" alt="">
        </div>
        <nav>
            <ul id="links">
                <li><a href="home.php"><i class="fa fa-list" aria-hidden="true"></i> PROJECT LIST</a></li>
                <li><a href="status.php"><i class="fa fa-list" aria-hidden="true"></i> PROJECT HISTORY</a></li>
            </ul>
        </nav>   
    </div>

    <div class="left">
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <i class="fa fa-bell" aria-hidden="true"></i>
        <i class="fa fa-cog" aria-hidden="true"></i>
    </div>

    <div class="boxx1">
        <div class="miniboxx">
            
        </div>
        <div class="boxx2">
            <div class="hellotext">
                <div class="hello">
                    <div class="h"><strong>PROJECTS</strong></div>
                </div>
            </div>
        </div>
        
    </div> -->




    <script>
        // Your existing JavaScript code

        // Function to toggle the mobile side navigation
        function toggleMobileMenu() {
            var mobileMenu = document.querySelector(".sidenav");
            mobileMenu.style.display = (mobileMenu.style.display === "block") ? "none" : "block";
        }

        // Add this JavaScript code to toggle the popup
        function openPopup() {
            var popup = document.getElementById("popupContainer");
            popup.style.display = "flex";
        }

        function closePopup() {
            var popup = document.getElementById("popupContainer");
            popup.style.display = "none";
        }

        function saveDetails() {
            var name = document.getElementById("nameInput").value;

            localStorage.setItem("name", name);

            window.location.href = "status.php";
        }

        // Add this event listener to show the popup when clicking Addp
        document.querySelector('.Addp').addEventListener('click', openPopup);
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>