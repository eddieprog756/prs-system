<?php
session_start();
require_once 'config/db.php'; // Ensure this file contains the database connection code

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.com$/', $email)) {
        $_SESSION['error'] = 'Invalid email format. Please use an email ending with .com.';
        header('Location: index.php');
        exit();
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $con->prepare("SELECT id, role, password FROM users WHERE LOWER(email) = ?");
    if (!$stmt) {
        $_SESSION['error'] = 'Failed to prepare SQL statement.';
        header('Location: index.php');
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            switch ($user['role']) {
                case 'admin':
                    header('Location: ./home.php');
                    break;
                case 'designer':
                    header('Location: ./designer_home.php');
                    break;
                case 'sales':
                    header('Location: ./sales_home.php');
                    break;
                case 'studio':
                    header('Location: ./studio.php');
                    break;
                case 'workshop':
                    header('Location: ./workshop_home.php');
                    break;
                case 'accounts':
                    header('Location: ./accounts_home.php');
                    break;
                default:
                    $_SESSION['error'] = 'Invalid role. Please contact the administrator.';
                    header('Location: index.php');
                    break;
            }
            exit();
        } else {
            $_SESSION['error'] = 'Email or Password is incorrect.';
            header('Location: index.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Email or Password is incorrect.';
        header('Location: index.php');
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

    <link rel="stylesheet" href="./css/login.css">

    <!-- Style sheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        /* Original CSS styles */
        body {
            background-size: cover;
            background-repeat: no-repeat;
            font-optical-sizing: auto;
            font-family: "Poppins", sans-serif;
            font-weight: 400;
        }

        .error-message {
            color: #fff;
            background-color: #dc3545;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .username input[type="email"] {
            border: none;
            border-bottom: 2px solid #ddd;
            background: transparent;
            font-size: 18px;
            width: 100%;
            padding: 10px 0;
        }

        .username input[type="email"]:focus {
            border-bottom: 2px solid #000;
            outline: none;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .fa-eye,
        .password-toggle .fa-eye-slash {
            position: absolute;
            top: 16px;
            right: 16px;
            cursor: pointer;
        }

        .container-login100 {
            background-repeat: no-repeat;
            background-size: cover;
            overflow: hidden;
        }

        .mailer {
            margin-top: -30px;
        }

        .bg-image {
            background-size: cover;
            background-repeat: no-repeat;
            background-position: fixed;

        }
    </style>

    <!-- Google reCAPTCHA -->
    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
</head>

<body>
    <div class="limiter">
        <div class="container-login100 bg-image" style="background:url('./images/sfffffff.jpg'); background-size:cover; background-repeat:no-repeat;">
            <div class="wrap-login100" style="height:80vh;">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="./BlackLogoo.png" alt="IMG" style="margin-top:-80px;">
                </div>

                <form id="loginForm" class="login100-form validate-form" style="margin-top:-80px;" method="POST" action="index.php">
                    <?php if ($error): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <span class="login100-form-title">
                        Please Login
                    </span>

                    <div class="wrap-input100 validate-input mailer" data-validate="Valid email is required: ex@abc.xyz">
                        <input class="input100" type="email" name="email" id="email" placeholder="Email" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input password-toggle" data-validate="Password is required">
                        <input class="input100" type="password" name="password" id="password" placeholder="Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                        <i class="fa fa-eye" id="togglePassword"></i>
                    </div>

                    <!-- Google reCAPTCHA -->
                    <!-- <div class="g-recaptcha" data-sitekey="6LdzkTQqAAAAALHRWd6QUWoOAYhTLvglKiGc7a4P"></div> -->

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">
                            Login
                        </button>
                    </div>

                    <div class="text-center p-t-12">
                        <span class="txt1">
                            Forgot
                        </span>
                        <a class="txt2" href="./forgot_pass.php">
                            Password?
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
            // Toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
        });
    </script>

    <script>
        // Initialize Tilt Effect
        $('.js-tilt').tilt({
            scale: 1.1
        });
    </script>
</body>

</html>