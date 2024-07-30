<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png">
  <link rel="stylesheet" href="./css/https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

  <div class="login-container">
    <div class="logo">
      <img src="Images/PR Grey n gree 2.png" alt="">
    </div>

    <form method="POST" action="register_submit.php">
      <div class="inputplace">
        <div class="username">
          <div class="useri">
            <i class="fa fa-user" aria-hidden="true"></i>
          </div>
          <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="password">
          <div class="locki">
            <i class="fa fa-lock" aria-hidden="true"></i>
          </div>
          <input type="password" name="password" placeholder="Password***" required>
        </div>

        <div class="role">
          <select name="role" required>
            <option value="admin">Admin</option>
            <option value="designer">Designer</option>
            <option value="sales">Sales</option>
          </select>
        </div>
      </div>

      <div class="login">
        <button type="submit"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
      </div>

      <?php if (isset($_GET['error'])) : ?>
        <div id="error-message" style="color: red; margin-top: 10px;">
          <?= htmlspecialchars($_GET['error']) ?>
        </div>
      <?php endif; ?>
    </form>
  </div>

</body>

</html>