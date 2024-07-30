<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="studio.css">
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.js">
    <title>Studio Department</title>
</head>

<body>

    <div class="left">
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <i class="fa fa-bell" aria-hidden="true"></i>
        <i class="fa fa-cog" aria-hidden="true"></i>
    </div>

    <div class="sidenav">
        <div class="logo">
            <img src="Images/PR Logo.png" alt="">
        </div>
        <nav>
            <ul id="links">
                <li><a href="home.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
                <!--<li><a href="tasks.css"><i class="fa fa-check" aria-hidden="true"></i> Completed Tasks</a></li>-->
                <div class="linkssss" id="studio" style="display:hidestudio">
                    <li><a href="studio.html"><i class="fa fa-building" aria-hidden="true"></i> Studio</a></li>
                </div>

            </ul>
        </nav>
    </div>

    <div class="imgclick">
        <img src="Images/menu2.png" class="menu-icon" onclick="toggleMobileMenu()">
    </div>

    <div class="contents">

        <div class="pname">
            <Strong>PROJECT</Strong> NAME:
        </div>

        <div id="details">

        </div>

        <div class="status">
            PROJECT STATUS
        </div>

        <div class="containerr">

            <div class="hide">
                <label>
                    <input type="checkbox" id="checkbox1"> Sales
                </label>

                <label>
                    <input type="checkbox" id="checkbox2"> Workshop
                </label>



                <label>
                    <input type="checkbox" id="checkbox4"> Sent to Workshop
                </label>


            </div>

            <div class="percentage-line">
                <div class="green-fill"></div>
            </div>

            <div class="per">
                <p id="percentage"> 0%</p>
            </div>

        </div>


        <div class="bottombox">

            <div class="pr">
                Click Below <strong>If Submitted</strong>
                <div class="acc">
                    <label>
                        <!--<input type="checkbox" id="checkbox3"> Sent to Workshop-->
                        <input type="checkbox" id="checkbox3"> Studio Done
                    </label>
                </div>
            </div>

        </div>

    </div>

    <script>
        // Your existing JavaScript code

        // Function to toggle the mobile side navigation
        function toggleMobileMenu() {
            var mobileMenu = document.querySelector(".sidenav");
            mobileMenu.style.display = (mobileMenu.style.display === "block") ? "none" : "block";
        }

        window.onload = function() {
            var name = localStorage.getItem("name");
            var surname = localStorage.getItem("surname");

            var detailsDiv = document.getElementById("details");
            detailsDiv.textContent = " " + name;
        };

        document.getElementById('checkbox3').addEventListener('change', function() {
            localStorage.setItem('studioStatus', this.checked ? 'Studio Done' : '');
        });



        window.onload = function() {
            var surname = localStorage.getItem("surname");
            var detailsDiv = document.getElementById("details");
            detailsDiv.textContent = "" + surname;
        };

        //.....................................................................................
    </script>

    <script src="script.js"></script>

</body>

</html>