<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $Date = date('Y-m-d', strtotime($_POST['Date']));
    $Time = $_POST['Time'];
    $JobCard_N0 = $_POST['JobCard_N0'];
    $Client_Name = $_POST['Client_Name'];
    $Project_Name = $_POST['Project_Name'];
    $Quantity = $_POST['Quantity'];
    $Overall_Size = $_POST['Overall_Size'];
    $Delivery_Date = date('Y-m-d', strtotime($_POST['Delivery_Date']));
    $Date_Delivered = date('Y-m-d', strtotime($_POST['Date_Delivered']));
    $Job_Description = $_POST['Job_Description'];
    $Prepaired_By = $_POST['Prepaired_By'];
    $Total_Charged = $_POST['Total_Charged'];

    include './config/db.php';

    $sql = "INSERT INTO jobcards (Date, Time, JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size, Delivery_Date, Date_Delivered, Job_Description, Prepaired_By, Total_Charged)
            VALUES ('$Date', '$Time', '$JobCard_N0', '$Client_Name', '$Project_Name', '$Quantity', '$Overall_Size', '$Delivery_Date', '$Date_Delivered', '$Job_Description', '$Prepaired_By', '$Total_Charged')";

    if (mysqli_query($con, $sql)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
    $current_page = basename($_SERVER['PHP_SELF']); // Get the current page name

    mysqli_close($con);
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Boostrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- Google Fonts -->
    <link rel="preconect" href="https://fonts.googleapis.com">
    <link rel="preconect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Eczar:wght@400..800&display=swap" rel="stylesheet">
</head>

<body>

    <?php
    include '../partials/sidebar.php';
    ?>

    <div class="left">
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <i class="fa fa-bell" aria-hidden="true"></i>
        <i class="fa fa-cog" aria-hidden="true"></i>
    </div>

    <?php
    include '../partials/greetings.php';
    ?>

    <div class="bottombox">
        <div class="add">
            <div class="Addp" id="historyBtn">
                <div class="plus">
                    <i class="fa fa-pl us"></i>
                </div>
                <div class="row">
                    <div class="addnew ">
                        <strong>ADD</strong> NEW PROJECT
                    </div>
                </div>


            </div>

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
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        <?php foreach ($projects as $index => $project) : ?>
                            <tr>
                                <th scope="row"><?php echo $index + 1; ?></th>
                                <td><?php echo htmlspecialchars($project['JobCard_N0']); ?></td>
                                <td><?php echo htmlspecialchars($project['Client_Name']); ?></td>
                                <td><?php echo htmlspecialchars($project['Project_Name']); ?></td>
                                <td><?php echo htmlspecialchars($project['Quantity']); ?></td>
                                <td><?php echo htmlspecialchars($project['Overall_Size']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="viewpjects w-10 " style="margin-left:20px; font-weight: 700;">
                <a href="./projectlist.php/">VIEW ALL PROJECTS <i class="fa fa-project-diagram"></i></a>
            </div>
        </div>
        <div class="logodown" style="margin-top:-10px; margin-left:900px; position:fixed;">
            <img src="../Images/PR Grey n gree 2.png" alt="">
        </div>
    </div>

    <div class="imgclick">
        <img src="../Images/menu2.png" class="menu-icon" onclick="toggleMobileMenu()">
    </div>

    <!-- THE POP UP...................................... -->
    <div class="popup-container" id="popupContainer">
        <div class="popup">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <div class="leftlogo">

                <div class="other">
                    <div class="llogo">
                        <img src="../Images/PR Grey n gree 2.png" alt="">
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

                            <!-- ... (previous HTML code) ... -->

                            <label for="fname">Clinets Name & Contact:</label>
                            <input type="text" id="fname" name="Client_Name" style="width: 389px;  padding-left: 15px;" required> <br>

                            <!-- ... (remaining HTML code) ... -->

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
                                <input type="date" id="datePicker" name="Date_Delivered" style="width: 178px;  padding-left: 15px; " required onchange="showSelectedDate()">
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


        // Add this event listener to show the popup when clicking Addp
        document.querySelector('.Addp').addEventListener('click', openPopup);

        function saveDetails() {
            var surname = document.getElementById("surname").value;
            localStorage.setItem("surname", surname);
            //window.location.href = "status.php";
        }

        var detailsDiv = localStorage.getItem("surname");
        localStorage.setItem("detailsDiv", detailsDiv);

        var hidestudio = localStorage.getItem("hidestudio");

        // Check if the flag is set to hide the history button
        if (hidestudio === "true") {
            document.getElementById("studio").style.display = "none";
        }

        //.............................................................................................
        var hideworkshop = localStorage.getItem("hideworkshop");

        if (hideworkshop === "true") {
            document.getElementById("workshop").style.display = "none";
        }
        //.............................................................................................
        var hideaccounts = localStorage.getItem("hideaccounts");

        if (hideaccounts === "true") {
            document.getElementById("accounts").style.display = "none";
        }
        //.............................................................................................
        var hidewarehouse = localStorage.getItem("hidewarehouse");

        if (hidewarehouse === "true") {
            document.getElementById("warehouse").style.display = "none";
        }
        //.............................................................................................
        var hidestatus = localStorage.getItem("hidestatus");

        if (hidestatus === "true") {
            document.getElementById("status").style.display = "none";
        }
        //.............................................................................................
        var hideprojectlist = localStorage.getItem("hideprojectlist");

        if (hideprojectlist === "true") {
            document.getElementById("projectlist").style.display = "none";
        }
    </script>

    <!-- Boostrap Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>


</body>

</html>