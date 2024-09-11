<?php
// session_start();

// Example roles
$_SESSION['role'] = 'sales'; // Change this to test different roles like 'admin', 'designer', etc.

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
  <link rel="stylesheet" href="../css/home.css">
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
        <li><a href='./home.php' class="<?php echo ($current_page == './home.php') ? 'active' : ''; ?>"><i class=" fa fa-home" aria-hidden="true"></i> Home</a></li>

        <li><a href="./tasks.php"><i class="fa fa-check" aria-hidden="true"></i> Finished Tasks</a></li>

        <?php if ($role !== 'admin') : ?>
          <li><a href="./projectlist.php"><i class="fa fa-building" aria-hidden="true"></i> Projects</a></li>
        <?php endif; ?>

        <?php if ($role === 'admin') : ?>
          <!-- For Keeps -->
          <li><a href="./projectlist.php"><i class="fa fa-building" aria-hidden="true"></i> Projects</a></li>
          <li><a href="sales.php"><i class="fa fa-money-bill" aria-hidden="true"></i> Sales</a></li>
          <li><a href="./accounts.php"><i class="fa fa-building" aria-hidden="true"></i> Accounts</a></li>
          <li><a href="./studio.php"><i class="fa fa-building" aria-hidden="true"></i> Studio</a></li>
          <li><a href="workshop.php"><i class="fa fa-building" aria-hidden="true"></i> Workshop</a></li>
          <li><a href="./status.php"><i class="fa fa-spinner" aria-hidden="true"></i> Status</a></li>
        <?php endif; ?>

        <!-- Logout link triggers the modal -->
        <a href="#" class="text-secondary logout" data-bs-toggle="modal" data-bs-target="#logoutModal" style="margin-top:200px;">
          <i class="fa fa-sign-out-alt"></i> Log Out
        </a>
      </ul>
    </nav>
  </div>

  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to log out?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <a href="./logout.php" class="btn btn-primary">Log Out</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>