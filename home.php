<?php
if(isset($_POST['submit'])){

    $Date = date('y-m-d', strtotime($_POST['Date']));
    $Time = $_POST['Time'];
    $JobCard_N0 = $_POST['JobCard_N0'];
    $Client_Name = $_POST['Client_Name'];
    $Project_Name = $_POST['Project_Name'];
    $Quantity = $_POST['Quantity'];
    $Overall_Size = $_POST['Overall_Size'];
    $Delivery_Date = date('y-m-d', strtotime($_POST['Delivery_Date']));
    $Date_Delivered = date('y-m-d', strtotime($_POST['Date_Delivered']));
    $Job_Description = $_POST['Job_Description'];
    $Prepaired_By = $_POST['Prepaired_By'];
    $Total_Charged = $_POST['Total_Charged'];  



    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'prsystem';

    $conn = mysqli_connect($host,$user,$pass,$dbname); 

    $sql = "INSERT INTO jobcard (Date,Time,JobCard_N0,Client_Name,Project_Name,Quantity,Overall_Size,Delivery_Date,Date_Delivered,Job_Description,Prepaired_By,Total_Charged) values ('$Date', '$Time', '$JobCard_N0', '$Client_Name', '$Project_Name', '$Quantity', '$Overall_Size', '$Delivery_Date', '$Date_Delivered', '$Job_Description', '$Prepaired_By', '$Total_Charged')";
   
    mysqli_query($conn,$sql);

}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="home.css">
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    
    <div class="sidenav">
        <div class="logo">
            <img src="Images/PR Logo.png" alt="">
        </div>
        <nav>
            <ul id="links">
                <li><a href="home.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
                
                <!--<li><a href="tasks.css"><i class="fa fa-check" aria-hidden="true"></i> Completed Tasks</a></li>-->
                <div class="linkssss" id="status" style="display:{{hidestatus}}">
                    <li><a href="status.html"><i class="fa fa-spinner" aria-hidden="true"></i> Status</a></li>
                </div>
                <div class="linkssss" id="studio" style="display:{{hidestudio}}">
                    <li><a href="studio.html"><i class="fa fa-building" aria-hidden="true"></i> Studio</a></li>
                </div>
                <div class="linkssss" id="workshop" style="display:{{hideworkshop}}">
                    <li><a href="workshop.html"><i class="fa fa-building" aria-hidden="true"></i> Workshop </a></li>
                </div>
                <div class="linkssss" id="accounts" style="display:{{hideaccounts}}">
                    <li><a href="accounts.html"><i class="fa fa-building" aria-hidden="true"></i> Accounts</a></li>
                </div>
                <div class="linkssss" id="warehouse" style="display:{{hidewarehouse}}">
                    <li><a href="sales.html"><i class="fa fa-building" aria-hidden="true"></i> Sales</a></li>
                </div>
                <div class="linkssss" id="projectlist" style="display:{{hideprojectlist}}">
                    <li><a href="projectlist.php"><i class="fa fa-building" aria-hidden="true"></i> Projects</a></li>
                </div>
                
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
                    <div class="h"><strong>Hello,</strong></div>
                    <div class="W"> Welcome...</div>
                </div>
                <div class="welcome">
                    <div class="s">STRAWBERRY </div>
                    <div class="a">ADVERTISING LTD</div>
                </div>
            </div>
        </div>
        
    </div>

    <div class="bottombox">
        <div class="add">
            <div class="Addp" id="historyBtn" style="display:{{showHistoryButton}}">
                <div class="plus">
                    <strong>+</strong>
                </div>
                <div class="addnew">
                    <strong>ADD</strong> NEW PROJECT
                </div>
            </div>
            <div class="viewpjects">
                <a href="projectlist.php">VIEW PROJECTS</a>
            </div>
        </div>
        <div class="logodown">
            <img src="Images/PR Grey n gree 2.png" alt="">
        </div>
    </div>

    <div class="imgclick">
        <img src="Images/menu2.png" class="menu-icon" 
        onclick="toggleMobileMenu()">
    </div>

    <!-- THE POP UP...................................... -->
<div class="popup-container" id="popupContainer" >
    <div class="popup">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <div class="leftlogo">
            
            <div class="other">
                <div class="llogo">
                    <img src="Images/PR Grey n gree 2.png" alt="">
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
                            <input type="date" id="datePicker" name="Date" style=" padding-left: 15px; "   required onchange="showSelectedDate()">
                            <p id="selectedDate"></p>
                            <label for="timePicker">Time : </label>
                            <input type="time" id="timePicker" name="Time" style=" padding-left: 15px; " required onclick="setCurrentTime()">
                            <p id="currentTime"></p>
                            <label for="fname">Job Card No : </label>
                            <input type="text" id="fname" name="JobCard_N0" style="width: 170px;  padding-left: 15px;  "  required>
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
                        <textarea name="Job_Description" id="jobd" cols="30" rows="10" style="border-radius: 10px; padding-left: 15px;" required ></textarea>
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
            //window.location.href = "status.html";
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

    

</body>
</html>