<?php
session_start();
require_once 'config/db.php'; // Ensure this file contains the database connection code

// Check if token is present in the URL
if (!isset($_GET['token'])) {
    $_SESSION['error'] = 'Invalid or expired token.';
    header('Location: forgot_password.php');
    exit();
}

$token = $_GET['token'];

// Validate token and get the user ID
$stmt = $con->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $userId = $user['user_id'];
} else {
    $_SESSION['error'] = 'Invalid or expired token.';
    header('Location: forgot_password.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $_SESSION['error'] = 'Passwords do not match.';
        header("Location: reset_password.php?token=$token");
        exit();
    }

    // Hash the new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update the user's password in the database
    $stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    $stmt->execute();

    // Delete the used reset token
    $stmt = $con->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $_SESSION['success'] = 'Password has been reset successfully. You can now log in.';
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">

    <style>
        /* Original CSS styles */
        body {
            background-size: cover;
            font-family: "Eczar", serif;
            font-optical-sizing: auto;
            font-style: normal;
        }

        .modal-error {
            color: red;
        }

        .password-input {
            position: relative;
        }

        .password-input input[type="password"] {
            border: none;
            border-bottom: 2px solid #ddd;
            background: transparent;
            font-size: 18px;
            width: 100%;
            padding: 10px 0;
        }

        .password-input .fa-eye,
        .password-input .fa-eye-slash {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-input input[type="password"]:focus {
            border-bottom: 2px solid #000;
            outline: none;
        }
    </style>
</head>

<body>
    <div class="limiter">
        <div class="container-login100" style="background:url('./images/bg.jpg');">
            <div class="wrap-login100" style="height:80vh;">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="./BlackLogoo.png" alt="IMG" style="margin-top:-80px;">
                </div>

                <form class="login100-form validate-form" style="margin-top:-80px;" method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($_GET['token']); ?>">
                    <span class="login100-form-title">
                        Reset Password
                    </span>

                    <div class="wrap-input100 validate-input password-input">
                        <input class="input100" type="password" name="password" id="password" placeholder="Enter new password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                        <i class="fa fa-eye" id="togglePassword" style="color: #000;"></i>
                    </div>

                    <div class="wrap-input100 validate-input password-input">
                        <input class="input100" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                        <i class="fa fa-eye" id="toggleConfirmPassword" style="color: #000;"></i>
                    </div>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">
                            Reset Password
                        </button>
                    </div>

                    <div class="text-center p-t-136">
                        <a class="txt2" href="index.php">
                            <i class="fa fa-arrow-left" aria-hidden="true"> Back to Login</i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript to toggle password visibility -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#confirm_password');

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>