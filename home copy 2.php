<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/db.php'; // Database connection

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];

    $sql = "INSERT INTO users (username, password, role, email, full_name) VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssss", $username, $password, $role, $email, $full_name);

    if ($stmt->execute()) {
        $_SESSION['success'] = "User added successfully!";
    } else {
        $_SESSION['error'] = "Error adding user: " . $stmt->error;
    }

    $stmt->close();
    $con->close();
    header("Location: add_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: "Eczar", serif;
        }

        .container {
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <?php
<<<<<<< HEAD
    include './sidebar2.php';
=======
    include './partials/sidebar2.php';
>>>>>>> refs/remotes/prs-system/main
    ?>

    <div class="left">
        <i class="fa fa-calendar" aria-hidden="true"></i>
        <i class="fa fa-bell" aria-hidden="true"></i>
        <i class="fa fa-cog" aria-hidden="true"></i>
    </div>

    <?php
    include './partials/greetings.php';
    ?>
    <div class="container">
        <h2>Add New User</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="add_user.php">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="designer">Designer</option>
                    <option value="sales">Sales</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
</body>

</html>