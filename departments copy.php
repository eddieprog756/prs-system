!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="./css/departments.css" />
  <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="./css/home.js" />
  <title>Document</title>
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
          <a href="home.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
        </li>
        <li>
          <a href="status.php"><i class="fa fa-spinner" aria-hidden="true"></i> Status</a>
        </li>
        <li>
          <a href="tasks.php"><i class="fa fa-check" aria-hidden="true"></i> Completed Tasks</a>
        </li>
      </ul>
    </nav>
  </div>

  <div class="imgclick">
    <img src="Images/menu2.png" class="menu-icon" onclick="toggleMobileMenu()" />
  </div>
  Departments...........
  <script>
    // Your existing JavaScript code

    // Function to toggle the mobile side navigation
    function toggleMobileMenu() {
      var mobileMenu = document.querySelector(".sidenav");
      mobileMenu.style.display =
        mobileMenu.style.display === "block" ? "none" : "block";
    }
  </script>
</body>

</html>