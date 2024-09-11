<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="./css/status.css" />
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png" />
    <link
      rel="stylesheet"
      href="./css/https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"
    />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./css/home.js" />
    <title>Document</title>
  </head>
  <body>


    <div class="contents">
      <div class="pname"><strong>PROJECT</strong> NAME:</div>

      <div id="details"></div>

      <div class="status">OVERALL PROJECT CURRENT STATUS</div>

      <div class="containerr">
        <div class="hide">
          <label> <input type="checkbox" id="checkbox1" /> Sales </label>

          <label> <input type="checkbox" id="checkbox2" /> Workshop </label>

          <label> <input type="checkbox" id="checkbox3" /> Studio </label>

          <label> <input type="checkbox" id="checkbox4" /> Accounts </label>
        </div>

        <div class="percentage-line">
          <div class="green-fill"></div>
        </div>

        <div class="per">
          <p id="percentage">0%</p>
        </div>
      </div>

      <div class="pr"><strong>DEPARTMENTAL</strong> PROGRESS</div>
      <div class="rep">REPORT</div>

      <div class="btdeets">
        <div id="salesProgress"></div>
        <div id="studioProgress"></div>
        <div id="workshopProgress"></div>
        <div id="accountsProgress"></div>
      </div>

      <div class="bottombox"></div>
    </div>

    <script>
      // Function to toggle the mobile side navigation
      function toggleMobileMenu() {
        var mobileMenu = document.querySelector(".sidenav");
        mobileMenu.style.display =
          mobileMenu.style.display === "block" ? "none" : "block";
      }

      window.onload = function () {
        var surname = localStorage.getItem("surname");
        var detailsDiv = document.getElementById("details");
        detailsDiv.textContent = "" + surname;
      };

      window.onload = function () {
        var detailsDiv = localStorage.getItem("detailsDiv");
        if (detailsDiv) {
          document.getElementById("details").textContent = detailsDiv;
        }
      };

      window.onload = function () {
        var salesStatus = localStorage.getItem("salesStatus");
        var studioStatus = localStorage.getItem("studioStatus");
        var workshopStatus = localStorage.getItem("workshopStatus");
        var accountsStatus = localStorage.getItem("accountsStatus");

        document.getElementById("salesProgress").textContent =
          salesStatus || "Sales X";
        document.getElementById("studioProgress").textContent =
          studioStatus || "Studio X";
        document.getElementById("workshopProgress").textContent =
          workshopStatus || "Workshop X";
        document.getElementById("accountsProgress").textContent =
          accountsStatus || "Accounts X";

        var surname = localStorage.getItem("surname");
        var detailsDiv = document.getElementById("details");
        detailsDiv.textContent = "" + surname;
      };
    </script>

    <script src="script.js"></script>
  </body>
</html>
