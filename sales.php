<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="./css/sales.css" />
  <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png" />
  <link
    rel="stylesheet"
    href="./css/https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./css/home.js" />
  <title>Sales Department</title>
</head>

<body>
  <div class="left">
    <i class="fa fa-calendar" aria-hidden="true"></i>
    <i class="fa fa-bell" aria-hidden="true"></i>
    <i class="fa fa-cog" aria-hidden="true"></i>
  </div>

  <?php
  include('./partials/sales_sidebar.php');
  ?>
  <div class="imgclick">
    <img
      src="Images/menu2.png"
      class="menu-icon"
      onclick="toggleMobileMenu()" />
  </div>

  <div class="contents">
    <div class="pname"><strong>PROJECT</strong> NAME:</div>

    <div id="details"></div>

    <div class="status">PROJECT STATUS</div>

    <div class="containerr">
      <div class="hide">
        <label> <input type="checkbox" id="checkbox2" /> Workshop </label>

        <label> <input type="checkbox" id="checkbox3" /> Studio </label>

        <label>
          <input type="checkbox" id="checkbox4" /> Sent to Studio
        </label>
      </div>

      <div class="percentage-line">
        <div class="green-fill"></div>
      </div>

      <div class="per">
        <p id="percentage">0%</p>
      </div>
    </div>

    <div class="bottombox">
      <div class="pr">
        Click Below <strong>If Submitted</strong>
        <div class="acc">
          <label>
            <!--<input type="checkbox" id="checkbox1"> Assigned to Studio-->
            <input type="checkbox" id="checkbox1" /> Sales Done
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
      mobileMenu.style.display =
        mobileMenu.style.display === "block" ? "none" : "block";
    }

    document
      .getElementById("checkbox1")
      .addEventListener("change", function() {
        localStorage.setItem("salesStatus", this.checked ? "Sales Done" : "");
      });

    window.onload = function() {
      var surname = localStorage.getItem("surname");
      var detailsDiv = document.getElementById("details");
      detailsDiv.textContent = "" + surname;
    };
  </script>

  <script src="script.js"></script>
</body>

</html>