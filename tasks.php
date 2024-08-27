<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="./css/tasks.css" />
  <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png" />
  <link
    rel="stylesheet"
    href="./css/https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
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

  <?php
  include('./partials/sidebar.php');
  ?>

  <div class="imgclick">
    <img
      src="Images/menu2.png"
      class="menu-icon"
      onclick="toggleMobileMenu()" />
  </div>
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