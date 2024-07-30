<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" type="x-con" href="Images/PR Logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <!-- Boostrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Eczar:wght@400..800&display=swap" rel="stylesheet">

    <!-- Style sheets -->
    <link rel="stylesheet" type="text/css" href="style.css">

    <style>
        body {
            background-size: cover;
            font-family: "Eczar", serif;
            font-optical-sizing: auto;
            font-style: normal;
        }

        .navbar-text {
            align-items: center;
            justify-content: center;

            font-size: 20px;
            margin-left: 500px;
        }
    </style>
</head>


<body style="background-image: url('./Images/retrosupply-jLwVAUtLOAQ-unsplash.jpg')">
    <header>
        <nav class="navbar bg-dark">
            <div class="container-fluid">
                <span class="navbar-text text-white">
                    PROJECT REPORTING SYSTEM
                </span>
            </div>
        </nav>
    </header>
    <div class="login-container w-50">

        <div class="logo" style="margin-left:-80px; margin-top: -30px; ">
            <img src="Images/PR Grey n gree 2.png" alt="">
        </div>

        <form method="POST" action="login.php">
            <!-- <h2 class="heading">Please Login</h2> -->
            <div class="inputplace" style="margin-top: 120px; ">
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
            </div>

            <div class="login text-center">
                <button type="submit" style="border-radius:100px; margin-top:-90px; margin-left:250px;"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
            </div>
            <?php if (isset($_GET['error'])) : ?>
                <div id="error-message" style="color: red; margin-top: 10px;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>
        </form>


    </div>
</body>


<!-- Boostrap Scripts -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</html>