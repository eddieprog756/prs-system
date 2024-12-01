<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

include './config/db.php'; // Database connection
// Fetch user data from the database for the logged-in user
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, profile_pic FROM users WHERE id = ?";
$stmt = $con->prepare($query);
if ($stmt) {
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user_data = $result->fetch_assoc();
} else {
  echo "Error: " . $con->error;
  exit();
}

// Default profile picture if none is set
if (empty($user_data['profile_pic'])) {
  $user_data['profile_pic'] = './Images/default_profile.JPG';
}
// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email, profile_pic FROM users WHERE id = ?";
$stmt = $con->prepare($query);
if ($stmt) {
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user_data = $result->fetch_assoc();
} else {
  echo "Error: " . $con->error;
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Profile picture update
  if (isset($_POST['update_profile']) && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $targetDir = "./Images/";
    $fileName = basename($_FILES["profile_pic"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
      $stmt = $con->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
      $stmt->bind_param("si", $targetFilePath, $user_id);
      $stmt->execute();
      $user_data['profile_pic'] = $targetFilePath;
    }
  }

  // Password change update
  if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Fetch the current hashed password
    $stmt = $con->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify current password and update if matches
    if (password_verify($current_password, $user['password'])) {
      if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        $stmt->execute();
        echo "<script>alert('Password updated successfully');</script>";
      } else {
        echo "<script>alert('New password and confirmation do not match');</script>";
      }
    } else {
      echo "<script>alert('Current password is incorrect');</script>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background-image: linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url('./Images/bg.JPG'); background-size: cover; background-position: center; background-repeat: no-repeat; height: 100vh; overflow: hidden;">
  <?php include './sidebar.php'; ?>
  <!-- Notifications Icons -->
  <div class="left" style="margin-top: 10px;">
    <!-- <i class="fa fa-calendar text-secondary" aria-hidden="true"></i> -->
    <div class="notification dropdown">
      <i class="fa fa-bell fa-2x text-secondary" id="notificationIcon" data-bs-toggle="dropdown" aria-expanded="false"></i>
      <span class="badge-counter" id="notificationCount">0</span>

      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationIcon">
        <li class="dropdown-header">Notifications</li>
        <div id="notificationList" class="px-3">
          <li class="dropdown-item text-muted">No new notifications</li>
        </div>
        <li>
          <button class="dropdown-item text-center text-primary" id="clearNotifications" style="display: none;">
            Clear Notifications
          </button>
        </li>
      </ul>
    </div>

    <a href="404.html"><i class="fa fa-cog text-secondary" aria-hidden="true"></i></a>
    <!-- User Profile Picture -->
    <div class="dropdown ms-3">
      <a href="./user_profile.php">
        <img src="<?php echo $user_data['profile_pic']; ?>" alt="Profile" class="profile-pic" id="profilePic" data-bs-toggle="dropdown" aria-expanded="false">
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
  <!-- Bootstrap Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

  <script>
    const newProjects = [{
      id: 1,
      name: "New Project Alpha"
    }, {
      id: 2,
      name: "New Project Beta"
    }];
    const notificationCount = document.getElementById("notificationCount");
    const notificationList = document.getElementById("notificationList");
    const clearNotifications = document.getElementById("clearNotifications");

    function loadNotifications() {
      const count = newProjects.length;
      notificationCount.textContent = count;
      if (count > 0) {
        notificationCount.style.display = "inline";
        notificationList.innerHTML = "";
        newProjects.forEach(project => {
          const listItem = document.createElement("li");
          listItem.classList.add("dropdown-item");
          listItem.textContent = `New project added: ${project.name}`;
          notificationList.appendChild(listItem);
        });
        clearNotifications.style.display = "block";
      } else {
        notificationCount.style.display = "none";
        notificationList.innerHTML = `<li class="dropdown-item text-muted">No new notifications</li>`;
        clearNotifications.style.display = "none";
      }
    }

    document.getElementById("notificationIcon").addEventListener("click", () => {
      loadNotifications();
    });

    clearNotifications.addEventListener("click", () => {
      newProjects.length = 0;
      loadNotifications();
    });

    loadNotifications();
  </script>
  <div class="boxx1">
    <div class="miniboxx">
    </div>

    <?php
    $sql = "SELECT JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size FROM jobcards ORDER BY created_at DESC LIMIT 5";
    $result = mysqli_query($con, $sql);

    if (!$result) {
      die("Error executing query: " . mysqli_error($con));
    }
    $projects = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $projects[] = $row;
    }

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username, role FROM users WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($username, $role);
    $stmt->fetch();
    $stmt->close();
    $con->close();

    $hour = date('H');
    if ($hour >= 1 && $hour < 12) {
      $greeting = 'Good Morning ';
    } elseif ($hour >= 12 && $hour < 17) {
      $greeting = 'Good Afternoon ';
    } else {
      $greeting = 'Good Evening ';
    }

    ?>
    <style>
      @keyframes fadeIn {
        from {
          opacity: 0;
        }

        to {
          opacity: 1;
        }
      }

      .h {
        animation: fadeIn 0.5s ease-in-out;
      }

      .a {
        animation: fadeIn 0.6s ease-in;
      }

      .notification {
        position: relative;
        cursor: pointer;
      }

      .badge-counter {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 3px 7px;
        font-size: 12px;
      }

      .dropdown-menu {
        width: 300px;
      }

      .profile-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
      }
    </style>
  </div>
  <div class="container mt-5" style="margin-left:270px;">
    <div class="row">
      <!-- Profile Picture and Email Update Card -->
      <div class="card mx-auto col" style="max-width: 500px; border-radius: 40px;">
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <input type="hidden" name="update_profile" value="1">
            <div class="text-center mb-3">
              <img src="<?php echo $user_data['profile_pic'] ?: './Images/default_profile.png'; ?>" alt="Profile Picture" class="img-thumbnail rounded-circle" width="150" style="border-radius: 50%; height: 50%; object-fit: cover;">
            </div>
            <div class="mb-3">
              <label for="profile_pic" class="form-label">Change Profile Picture</label>
              <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
              <div class="invalid-feedback">Please select a valid image file.</div>
            </div>
            <div class="row">
              <div class="mb-3 col">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" readonly>
              </div>
              <div class="mb-3 col">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>">
              </div>
            </div>
            <button type="submit" class="btn btn-dark mx-auto d-block" style=" background-color: #212529; border:none; border-radius:40px; justify-content:center; align-items:center; margin-top:10px;">Update</button>
          </form>
        </div>
      </div>

      <!-- Password Change Card -->
      <div class="card mx-auto col" style="max-width: 500px; margin-left: -20px; border-radius:40px;">
        <div class="card-body">
          <form method="POST" class="needs-validation" novalidate>
            <div class="text-center mb-3">
              <img src="./Images/pass.png" alt="Password Picture" width="200">
            </div>
            <input type="hidden" name="change_password" value="1">

            <div class="mb-3 position-relative">
              <label for="current_password" class="form-label">Current Password</label>
              <input type="password" class="form-control" id="current_password" name="current_password" required>
              <div class="invalid-feedback">Please enter your current password.</div>
            </div>
            <div class="row">
              <div class="mb-3 position-relative col">
                <label for="new_password" class="form-label">New Password</label>
                <div class="input-group ">
                  <input type="password" class="form-control" id="new_password" name="new_password" required>
                  <span class="input-group-text">
                    <i class="fa fa-eye" id="toggleNewPassword" style="cursor: pointer;"></i>
                  </span>
                </div>
                <div class="invalid-feedback">Please enter a new password.</div>
              </div>
              <div class="mb-3 position-relative col">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <div class="input-group ">
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                  <span class="input-group-text">
                    <i class="fa fa-eye" id="toggleConfirmPassword" style="cursor: pointer;"></i>
                  </span>
                </div>
                <div class="invalid-feedback">Please confirm your new password.</div>
              </div>
            </div>
            <button type="submit" class="btn btn-dark mx-auto d-block" style=" background-color: #212529; border:none; border-radius:40px; justify-content:center; align-items:center;">Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap & FontAwesome Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <!-- JavaScript for Form Validation and Password Toggle -->
  <script>
    // Bootstrap form validation
    (function() {
      'use strict'
      var forms = document.querySelectorAll('.needs-validation')
      Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          form.classList.add('was-validated')
        }, false)
      })
    })();

    // Password visibility toggle
    document.getElementById('toggleNewPassword').addEventListener('click', function() {
      const newPasswordField = document.getElementById('new_password');
      const type = newPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
      newPasswordField.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
      const confirmPasswordField = document.getElementById('confirm_password');
      const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordField.setAttribute('type', type);
      this.classList.toggle('fa-eye-slash');
    });
  </script>
</body>

</html>