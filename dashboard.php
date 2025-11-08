<?php
include 'config.php';
session_start();

if (isset($_POST['delete_user']) && isset($_POST['delete_user_id'])) {
    $delete_id = intval($_POST['delete_user_id']);
    mysqli_query($conn, "DELETE FROM `user_form` WHERE id = '$delete_id'") or die('Delete failed');
    header('Location: dashboard.php');
    exit;
}

// Optional: Only allow access if logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

// Get total registered users
$user_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM `user_form`") or die('query failed');
$user_count = mysqli_fetch_assoc($user_count_query)['total'];

// Get currently logged in users
$user_online_query = mysqli_query($conn, "SELECT * FROM `user_form` WHERE is_logged_in = 1") or die('query failed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light">
<div class="container py-5">
   <div class="row justify-content-center">
      <div class="col-md-8">
         <div class="card shadow">
            <div class="card-body">
               <h2 class="mb-4 text-center">Dashboard</h2>
               <div class="alert alert-info mb-3">
                  <strong>Total Registered Users:</strong> <?php echo $user_count; ?>
               </div>
               <div class="alert alert-success mb-3">
                  <strong>Users Currently Logged In:</strong> <?php echo mysqli_num_rows($user_online_query); ?>
               </div>
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Profile Image</th>
                        <th>Edit</th>
                        <th>Delete</th> <!-- Add Delete column -->
                     </tr>
                  </thead>
                  <tbody>
                     <?php while($row = mysqli_fetch_assoc($user_online_query)): ?>
                     <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                           <?php if($row['image']): ?>
                              <img src="uploaded_img/<?php echo htmlspecialchars($row['image']); ?>" width="40" height="40" class="rounded-circle">
                           <?php else: ?>
                              <img src="images/default-avatar.png" width="40" height="40" class="rounded-circle">
                           <?php endif; ?>
                        </td>
                        <td>
                           <a href="update_profile.php?user_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        </td>
                        <td>
                           <form action="dashboard.php" method="post" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display:inline;">
                              <input type="hidden" name="delete_user_id" value="<?php echo $row['id']; ?>">
                              <button type="submit" name="delete_user" class="btn btn-sm btn-danger">Delete</button>
                           </form>
                        </td>
                     </tr>
                     <?php endwhile; ?>
                  </tbody>
               </table>
               <a href="home.php" class="btn btn-secondary mt-3">Back to Home</a>
            </div>
         </div>
      </div>
   </div>
</div>
</body>
</html>