<?php


if (!isset($_SESSION['role'])) {
  // Redirect to login if the role is not set
  header("Location: index.php");
  exit();
}

$role = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="shortcut icon" type="x-con" href="./Images/PR Logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/projectlist.css">
  <link rel="stylesheet" href="./css/home.css">
  <style>
    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .sidenav ul {
      list-style-type: none;
      padding: 0;
    }

    .sidenav ul li a {
      display: block;
      padding: 10px;
      color: black;
      width: 180px;
      text-decoration: none;
      animation: fadeIn 1s ease-in-out;
    }

    .sidenav ul li a:hover,
    .sidenav ul li a.active {
      color: black;
      background-color: white;
      width: 180px;
      border-radius: 10px;
    }

    .logout {
      font-weight: 600;
      color: #77c144;
      cursor: pointer;
    }

    .logout:hover {
      cursor: pointer;
    }
  </style>

</head>

<body class="bg-green" style="font-size: 15px;">
  <div class="sidenav">
    <div class="logo">
      <img src="./Images/PRLogo.png" alt="">
    </div>
    <nav>
      <ul id="links">
        <!-- Common Home Link for All Users -->
        <!-- <li><a href='./home.php' class="<?php echo ($current_page == 'home.php') ? 'active' : ''; ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li> -->

        <?php if ($role === 'admin') : ?>
          <!-- Admin-Specific Links -->
          <li><a href='./home.php' class="<?php echo ($current_page == 'home.php') ? 'active' : ''; ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
          <li><a href="./projectlist.php" class="<?php echo ($current_page == 'projectlist.php') ? 'active' : ''; ?>"><i class="fa fa-building" aria-hidden="true"></i> Projects</a></li>
          <li><a href="sales.php" class="<?php echo ($current_page == 'sales.php') ? 'active' : ''; ?>"><i class="fa fa-money-bill" aria-hidden="true"></i> sales</a></li>
          <li><a href="./accounts.php" class="<?php echo ($current_page == 'accounts.php') ? 'active' : ''; ?>"><i class="fa fa-building" aria-hidden="true"></i> Accounts</a></li>
          <li><a href="./studio.php" class="<?php echo ($current_page == 'studio.php') ? 'active' : ''; ?>"><i class="fa fa-building" aria-hidden="true"></i> Studio</a></li>
          <li><a href="workshop.php" class="<?php echo ($current_page == 'workshop.php') ? 'active' : ''; ?>"><i class="fa fa-building" aria-hidden="true"></i> Workshop</a></li>
          <!-- <li><a href="./status.php" class="<?php echo ($current_page == 'status.php') ? 'active' : ''; ?>"><i class="fa fa-spinner" aria-hidden="true"></i> Status</a></li> -->
          <li><a href="./add_user.php" class="<?php echo ($current_page == 'add_user.php') ? 'active' : ''; ?>"><i class="fa fa-user" aria-hidden="true"></i> Add Users</a></li>
          <li><a href="./404.html" class="<?php echo ($current_page == '404.html') ? 'active' : ''; ?>"><i class="fa fa-user-check" aria-hidden="true"></i> User Profile</a></li>

        <?php elseif ($role === 'sales') : ?>
          <!-- sales-Specific Links -->
          <li><a href='./sales_home.php' class="<?php echo ($current_page == 'sales_home.php') ? 'active' : ''; ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
          <li><a href="./salez.php" class="<?php echo ($current_page == 'salez.php') ? 'active' : ''; ?>"><i class="fa fa-check" aria-hidden="true"></i> Submit Project</a></li>
          <!-- <li><a href="./projectlist.php" class="<?php echo ($current_page == 'projectlist.php') ? 'active' : ''; ?>"><i class="fa fa-building" aria-hidden="true"></i> Projects</a></li> -->
          <li><a href="./user_profile_staff.php" class="<?php echo ($current_page == 'user_profile_staff.php') ? 'active' : ''; ?>"><i class="fa fa-user-check" aria-hidden="true"></i> User Profile</a></li>
          <li><a href="./reports_sales.php"><i class="fa fa-clipboard" aria-hidden="true"></i> Reports</a></li>


        <?php elseif ($role === 'designer') : ?>
          <!-- Designer-Specific Links -->
          <li><a href='./designer_home.php' class="<?php echo ($current_page == 'designer_home.php') ? 'active' : ''; ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
          <li><a href="./studio.php" class="<?php echo ($current_page == 'studio.php') ? 'active' : ''; ?>"><i class="fa fa-palette" aria-hidden="true"></i> Studio</a></li>
          <li><a href="./user_profile_staff.php" class="<?php echo ($current_page == 'user_profile_staff.php') ? 'active' : ''; ?>"><i class="fa fa-user-check" aria-hidden="true"></i> User Profile</a></li>

        <?php elseif ($role === 'workshop') : ?>
          <!-- Workshop-Specific Links -->
          <li><a href='./workshop_home.php' class="<?php echo ($current_page == 'workshop_home.php') ? 'active' : ''; ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
          <li><a href="./workshop.php" class="<?php echo ($current_page == 'workshop.php') ? 'active' : ''; ?>"><i class="fa fa-building" aria-hidden="true"></i> Workshop</a></li>
          <li><a href="./user_profile_staff.php" class="<?php echo ($current_page == 'user_profile_staff.php') ? 'active' : ''; ?>"><i class="fa fa-user-check" aria-hidden="true"></i> User Profile</a></li>

        <?php elseif ($role === 'accounts') : ?>
          <!-- Accounts-Specific Links -->
          <li><a href='./accounts_home.php' class="<?php echo ($current_page == 'accounts_home.php') ? 'active' : ''; ?>"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
          <li><a href="./accounts.php" class="<?php echo ($current_page == 'accounts.php') ? 'active' : ''; ?>"><i class="fa fa-building" aria-hidden="true"></i> Accounts</a></li>
          <li><a href="./user_profile_staff.php" class="<?php echo ($current_page == 'user_profile_staff.php') ? 'active' : ''; ?>"><i class="fa fa-user-check" aria-hidden="true"></i> User Profile</a></li>
        <?php endif; ?>

        <!-- Logout link triggers the modal -->
        <a href="#" class="text-secondary logout" data-bs-toggle="modal" data-bs-target="#logoutModal" style="margin-top:200px;">
          <i class="fa fa-sign-out-alt"></i> Log Out
        </a>
      </ul>
    </nav>
  </div>

  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true" style="margin-top:10px; border-radius: 40px;">
    <div class="modal-dialog">
      <div class="modal-content" style=" border-radius: 20px;">
        <!-- <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div> -->
        <div class="modal-body text-center">
          Are you sure you want to log out?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal" style="border-radius:20px;">Cancel</button>
          <a href="./logout.php" class="btn btn-danger" style="border-radius:20px;">Log Out <i class=" fa fa-door-open"></i></a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>