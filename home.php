<?php
include 'config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Handle user switch
if (isset($_POST['switch_user_id'])) {
    $_SESSION['user_id'] = intval($_POST['switch_user_id']);
    header('Location: home.php');
    exit;
}

// Logout logic
if (isset($_GET['logout'])) {
    // Update login status if column exists
    if ($conn->query("SHOW COLUMNS FROM `user_form` LIKE 'is_logged_in'")->num_rows) {
        $stmt = $conn->prepare("UPDATE `user_form` SET is_logged_in = 0 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    session_destroy();
    header('Location: login.php?logout=1');
    exit;
}

// Fetch current user info
$stmt = $conn->prepare("SELECT * FROM `user_form` WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current_user = $stmt->get_result()->fetch_assoc();

// Fetch other users for switcher
$users_query = $conn->prepare("SELECT id, name FROM `user_form` WHERE id != ?");
$users_query->bind_param("i", $user_id);
$users_query->execute();
$users_result = $users_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Welcome, <?= htmlspecialchars($current_user['name']); ?>!</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
    body.dark-mode {
        background-color: #181a1b !important;
        color: #f8f9fa !important;
        transition: background-color 0.3s, color 0.3s;
    }
    .navbar-dark-mode {
        background-color: #212529 !important;
        transition: background-color 0.3s;
    }
    .profile-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #dee2e6;
    }
    .toast-container {
        z-index: 2000;
    }
</style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">
      <i class="bi bi-house-door"></i> My Dashboard
    </a>

    <div class="d-flex align-items-center ms-auto">
      <!-- Dark Mode Toggle -->
      <button class="btn btn-outline-secondary me-2" onclick="toggleDarkMode()" title="Toggle Dark Mode">
        <i id="darkModeIcon" class="bi bi-moon"></i>
      </button>

      <!-- User Switcher -->
      <?php if ($users_result->num_rows > 0): ?>
      <form method="post" class="me-2">
        <select name="switch_user_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width:150px;">
          <option disabled selected>Switch User</option>
          <?php while ($user = $users_result->fetch_assoc()): ?>
            <option value="<?= $user['id']; ?>"><?= htmlspecialchars($user['name']); ?></option>
          <?php endwhile; ?>
        </select>
      </form>
      <?php endif; ?>

      <!-- Profile Dropdown -->
      <div class="dropdown">
        <button class="btn btn-light dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="<?= $current_user['image'] ? 'uploaded_img/'.htmlspecialchars($current_user['image']) : 'images/default-avatar.png'; ?>" 
               class="rounded-circle me-2" width="32" height="32">
          <?= htmlspecialchars($current_user['name']); ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="update_profile.php"><i class="bi bi-person"></i> Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="home.php?logout=<?= $user_id; ?>"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="container py-5 text-center">
  <div class="card shadow-sm mx-auto" style="max-width: 500px;">
    <div class="card-body">
      <img src="<?= $current_user['image'] ? 'uploaded_img/'.htmlspecialchars($current_user['image']) : 'images/default-avatar.png'; ?>" class="profile-img mb-3" alt="Profile Image">
      <h4 class="fw-bold"><?= htmlspecialchars($current_user['name']); ?></h4>
      <p class="text-muted mb-3"><?= htmlspecialchars($current_user['email']); ?></p>
      <a href="update_profile.php" class="btn btn-primary me-2">
        <i class="bi bi-pencil-square"></i> Update Profile
      </a>
      <a href="home.php?logout=<?= $user_id; ?>" class="btn btn-outline-danger">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    </div>
  </div>

  <div class="mt-4">
    <p class="text-muted">
      Not your account?
      <a href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a> or
      <a href="register.php"><i class="bi bi-person-plus"></i> Register</a>
    </p>
  </div>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="logoutToast" class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body"><i class="bi bi-check-circle me-2"></i>Logout successful!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const darkModeEnabled = localStorage.getItem('darkMode') === 'true';
  const body = document.body;
  const navbar = document.querySelector('.navbar');
  const icon = document.getElementById('darkModeIcon');

  if (darkModeEnabled) {
    body.classList.add('dark-mode');
    navbar.classList.add('navbar-dark-mode');
    icon.classList.replace('bi-moon', 'bi-sun');
  }

  window.toggleDarkMode = function() {
    body.classList.toggle('dark-mode');
    navbar.classList.toggle('navbar-dark-mode');
    const enabled = body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', enabled);
    icon.classList.toggle('bi-moon');
    icon.classList.toggle('bi-sun');
  };

  // Show toast on logout redirect
  if (window.location.search.includes('logout=1')) {
    const toast = new bootstrap.Toast(document.getElementById('logoutToast'));
    toast.show();
  }
});
</script>
</body>
</html>
