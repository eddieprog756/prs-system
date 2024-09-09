<div class="boxx1">
  <div class="miniboxx">

  </div>

  <?php
  // session_start();
  include './config/db.php';

  $sql = "SELECT JobCard_N0, Client_Name, Project_Name, Quantity, Overall_Size FROM jobcards ORDER BY created_at DESC LIMIT 5";
  $result = mysqli_query($con, $sql);

  if (!$result) {
    die("Error executing query: " . mysqli_error($con));
  }
  $projects = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $projects[] = $row;
  }
  // Assume the user ID is stored in the session
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
  </style>
  <div class="boxx2">
    <div class="hellotext">
      <div class="hello">
        <div class="h"><strong><?php echo $greeting; ?></strong></div>
        <div class="W"><?php echo htmlspecialchars(' , ' . $role); ?></div>
      </div>
      <div class="welcome">
        <div class="s">STRAWBERRY </div>
        <div class="a">ADVERTISING LTD</div>
      </div>
    </div>
  </div>

</div>