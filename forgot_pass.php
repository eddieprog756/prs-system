<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <link rel="stylesheet" href="./css/login.css">

  <!-- Style sheets -->
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

    /* Original CSS for the email field */
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
  </style>
</head>

<body>
  <div class="limiter">
    <div class="container-login100" style="background:url('./images/bg.jpg');">
      <div class="wrap-login100" style="height:80vh;">
        <div class="login100-pic js-tilt" data-tilt>
          <img src="./BlackLogoo.png" alt="IMG" style="margin-top:-80px;">
        </div>

        <form id="forgotPasswordForm" class="login100-form validate-form" style="margin-top:-80px;" method="POST">
          <span class="login100-form-title">
            Forgot Password
          </span>

          <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
            <input class="input100" type="email" name="email" id="email" placeholder="Enter your email" required>
            <span class="focus-input100"></span>
            <span class="symbol-input100">
              <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>
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

  <!-- Bootstrap Modal for Error Messages -->
  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body modal-error" id="modalErrorMessage">
          <!-- Error message will be injected here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="successModalLabel">Success</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="modalSuccessMessage">
          <!-- Success message will be injected here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript and jQuery Scripts -->
  <script>
    $('.js-tilt').tilt({
      scale: 1.1
    });

    (function($) {
      "use strict";

      $('#forgotPasswordForm').on('submit', function(event) {
        event.preventDefault(); // Prevent default form submission

        const email = $('#email').val();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(email) || !email.endsWith('.com')) {
          $('#modalErrorMessage').text('Please enter a valid email address ending with .com.');
          $('#errorModal').modal('show');
        } else {
          // If email is valid, proceed with AJAX form submission
          $.ajax({
            type: 'POST',
            url: 'forgot_password.php',
            data: {
              email: email
            },
            success: function(response) {
              if (response.error) {
                $('#modalErrorMessage').text(response.error);
                $('#errorModal').modal('show');
              } else {
                $('#modalSuccessMessage').text('A password reset link has been sent to your email.');
                $('#successModal').modal('show');
              }
            },
            error: function() {
              $('#modalErrorMessage').text('An error occurred. Please try again.');
              $('#errorModal').modal('show');
            }
          });
        }
      });

    })(jQuery);
  </script>
</body>

</html>