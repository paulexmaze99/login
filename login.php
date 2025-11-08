<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select) > 0){
      $row = mysqli_fetch_assoc($select);
      $_SESSION['user_id'] = $row['id'];
      // Set user as logged in
      mysqli_query($conn, "UPDATE `user_form` SET is_logged_in = 1 WHERE id = {$row['id']}");
      header('location:home.php');
      exit;
   }else{
      $message[] = 'Incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- Bootstrap CSS CDN -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Add this in your <head> section to load Bootstrap Icons -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <style>
      body.dark-mode {
         background-color: #181a1b !important;
         color: #f8f9fa !important;
      }
   </style>
   <script>
      // Toggle dark mode
      function toggleDarkMode() {
         document.body.classList.toggle('dark-mode');
         localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
         const icon = document.getElementById('darkModeIcon');
         if(document.body.classList.contains('dark-mode')) {
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
         } else {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon');
         }
      }
      // On load, set dark mode if previously enabled
      window.onload = function() {
         const icon = document.getElementById('darkModeIcon');
         if(localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            icon.classList.remove('bi-moon');
            icon.classList.add('bi-sun');
         } else {
            icon.classList.remove('bi-sun');
            icon.classList.add('bi-moon');
         }
      }
   </script>

</head>
<body class="bg-light">
<div class="container py-5">
   <div class="row justify-content-center">
      <div class="col-md-5">
         <div class="card shadow">
            <div class="card-body">
               <div class="text-end mb-3">
                  <button class="btn btn-secondary btn-sm" onclick="toggleDarkMode()" type="button">
                     <span id="darkModeIcon" class="bi bi-moon"></span> <!-- Bootstrap Icons moon icon -->
                  </button>
               </div>
               <form action="" method="post" enctype="multipart/form-data">
                  <h3 class="mb-4 text-center">Login</h3>
                  <?php
                  if(isset($message)){
                     foreach($message as $msg){
                        echo '<div class="alert alert-danger text-center">'.$msg.'</div>';
                     }
                  }
                  ?>
                  <div class="mb-3">
                     <label class="form-label">Email</label>
                     <input type="email" name="email" class="form-control" required>
                  </div>
                  <div class="mb-3">
                     <label class="form-label">Password</label>
                     <input type="password" name="password" class="form-control" required>
                  </div>
                  <input type="submit" name="submit" value="Login" class="btn btn-primary w-100">
                  <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register</a></p>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Bootstrap JS Bundle CDN (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>