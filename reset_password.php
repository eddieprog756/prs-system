<?php
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Verify the token
    $stmt = $con->prepare("SELECT user_id FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user_id = $result->fetch_assoc()['user_id'];

        // Update the user's password
        $stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $newPassword, $user_id);
        $stmt->execute();

        // Delete the token
        $stmt = $con->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();

        echo "Password has been updated successfully!";
    } else {
        echo "Invalid token.";
    }
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
</head>

<body>
    <div class="limiter">
        <div class="container-login100" style="background:url('./images/bg.jpg');">
            <div class="wrap-login100" style="height:80vh;">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="./BlackLogoo.png" alt="IMG" style="margin-top:-80px;">
                </div>

                <form id="resetPasswordForm" class="login100-form validate-form" style="margin-top:-80px;" method="POST">
                    <span class="login100-form-title">
                        Reset Password
                    </span>

                    <div class="wrap-input100 validate-input">
                        <input class="input100" type="password" name="password" id="password" placeholder="New Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">
                            Reset Password
                        </button>
                    </div>

                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

                </form>
            </div>
        </div>
    </div>
</body>

</html>
