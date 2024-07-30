<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="./css/accounts.css" />
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png" />
    <link
      rel="stylesheet"
      href="./css/https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"
    />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./css/home.js" />
    <title>Accounts Department</title>
  </head>
  <body>
    <div class="left">
      <i class="fa fa-calendar" aria-hidden="true"></i>
      <i class="fa fa-bell" aria-hidden="true"></i>
      <i class="fa fa-cog" aria-hidden="true"></i>
    </div>

    <div class="sidenav">
      <div class="logo">
        <img src="Images/PR Logo.png" alt="" />
      </div>
      <nav>
        <ul id="links">
          <li>
            <a href="home.php"
              ><i class="fa fa-home" aria-hidden="true"></i> Home</a
            >
          </li>
          <!--<li><a href="tasks.css"><i class="fa fa-check" aria-hidden="true"></i> Completed Tasks</a></li>-->
          <div class="linkssss" id="studio" style="display:{{hidestudio}}">
            <li>
              <a href="accounts.php"
                ><i class="fa fa-building" aria-hidden="true"></i> Accounts</a
              >
            </li>
          </div>
          <div class="logout">
            <img src="Images/Logout2.png" onclick="logout()" alt="" />
          </div>
        </ul>
      </nav>
    </div>

    <div class="imgclick">
      <img
        src="Images/menu2.png"
        class="menu-icon"
        onclick="toggleMobileMenu()"
      />
    </div>

    <div class="contents">
      <div class="pname"><strong>PROJECT</strong> NAME:</div>

      <div id="details"></div>

      <div class="status">PROJECT STATUS</div>

      <div class="containerr">
        <div class="hide">
          <label> <input type="checkbox" id="checkbox1" /> Sales </label>

          <label> <input type="checkbox" id="checkbox2" /> Workshop </label>

          <label> <input type="checkbox" id="checkbox3" /> Studio </label>
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
              <input type="checkbox" id="checkbox4" /> Client Paid
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

      window.onload = function () {
        var surname = localStorage.getItem("surname");
        var detailsDiv = document.getElementById("details");
        detailsDiv.textContent = "" + surname;
      };

      document
        .getElementById("checkbox4")
        .addEventListener("change", function () {
          localStorage.setItem(
            "accountsStatus",
            this.checked ? "Accounts Done" : ""
          );
        });
    </script>

    <script src="script.js"></script>
  </body>
</html>
