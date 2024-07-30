<?php
$localhost = "localhost";
$username = "root";
$password = "";
$dbname = "prs";

$con = mysqli_connect($localhost, $username, $password, $dbname);

if (!$con) {
  echo "Connection Failed";
} else {
  // echo "Connection Was successful!";


  // User data to be inserted
  // $users = [
  //   [
  //     'username' => 'Admin',
  //     'password' => password_hash('admin_password', PASSWORD_BCRYPT),
  //     'role' => 'admin',
  //     'email' => 'admin@example.com',
  //     'full_name' => 'Admin User'
  //   ],
  //   [
  //     'username' => 'Designer',
  //     'password' => password_hash('designer_password', PASSWORD_BCRYPT),
  //     'role' => 'designer',
  //     'email' => 'designer@example.com',
  //     'full_name' => 'Designer User'
  //   ],
  //   [
  //     'username' => 'Sales',
  //     'password' => password_hash('sales_password', PASSWORD_BCRYPT),
  //     'role' => 'sales',
  //     'email' => 'sales@example.com',
  //     'full_name' => 'Sales User'
  //   ]
  // ];

  // // Insert user data into the database
  // foreach ($users as $user) {
  //   $username = $user['username'];
  //   $password = $user['password'];
  //   $role = $user['role'];
  //   $email = $user['email'];
  //   $full_name = $user['full_name'];

  //   $sql = "INSERT INTO users (username, password, role, email, full_name) VALUES ('$username', '$password', '$role', '$email', '$full_name')";
  //   if ($con->query($sql) === TRUE) {
  //     echo "New record created successfully for user: $username\n";
  //   } else {
  //     echo "Error: " . $sql . "\n" . $conn->error . "\n";
  //   }
  // }

  // $conn->close();
}
