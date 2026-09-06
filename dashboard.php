<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
requireLogin();

$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$username = $user ? htmlspecialchars($user['username']) : 'Student';
$initial = strtoupper(substr($username, 0, 2));

// Fetch counts for stat cards
$stmt = $pdo->query("SELECT COUNT(*) FROM notes");
$notesCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | Student Notes Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="app-layout">
    <nav class="navbar navbar-dark sidebar flex-column">
      <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
        <img src="images/logo.png" alt="Logo" class="nav-logo" />
        <span>Campus<br />Portal</span>
      </a>
      <ul class="navbar-nav flex-column w-100">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">ðŸ  Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="pastpapers.php">ðŸ“„ Notes</a></li>
        <li class="nav-item"><a class="nav-link" href="upload.php">â¬†ï¸ Upload Notes</a></li>
        </ul>
      <div class="sidebar-logout">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">ðŸšª Logout</a>
      </div>
    </nav>
    <main class="main-content">
      <div class="topbar">
        <h1>Dashboard</h1>
        <div class="user-chip">
          <span class="avatar"><?= $initial ?></span><span><?= $username ?></span>
        </div>
      </div>
      <div class="welcome-banner">
        <h2>Welcome back, <?= $username ?>! ðŸ‘‹</h2>
        <p>Manage and explore your student notes here.</p>
      </div>
      <div class="row g-3">
          <div class="col-md-4">
              <div class="stat-card">
                  <div class="stat-number"><?= htmlspecialchars($notesCount) ?></div>
                  <div class="stat-label">Total Notes</div>
              </div>
          </div>
      </div>
    </main>
  </div>
  <!-- Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">Are you sure you want to log out?</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <a href="auth/logout.php" class="btn btn-danger">ðŸšª Logout</a>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
SESSION['user_id']]);
$user = $stmt->fetch();
$username = $user ? htmlspecialchars($user['username']) : 'Student';
$initial = strtoupper(substr($username, 0, 2));

// Fetch counts for stat cards
$stmt = $pdo->query("SELECT COUNT(*) FROM notes");
$notesCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | Student Notes Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="app-layout">
    <nav class="navbar navbar-dark sidebar flex-column">
      <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
        <img src="images/logo.png" alt="Logo" class="nav-logo" />
        <span>Campus<br />Portal</span>
      </a>
      <ul class="navbar-nav flex-column w-100">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">ðŸ  Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="pastpapers.php">ðŸ“„ Notes</a></li>
        <li class="nav-item"><a class="nav-link" href="upload.php">â¬†ï¸ Upload Notes</a></li>
        </ul>
      <div class="sidebar-logout">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">ðŸšª Logout</a>
      </div>
    </nav>
    <main class="main-content">
      <div class="topbar">
        <h1>Dashboard</h1>
        <div class="user-chip">
          <span class="avatar"><?= $initial ?></span><span><?= $username ?></span>
        </div>
      </div>
      <div class="welcome-banner">
        <h2>Welcome back, <?= $username ?>! ðŸ‘‹</h2>
        <p>Manage and explore your student notes here.</p>
      </div>
      <div class="row g-3">
          <div class="col-md-4">
              <div class="stat-card">
                  <div class="stat-number"><?= htmlspecialchars($notesCount) ?></div>
                  <div class="stat-label">Total Notes</div>
              </div>
          </div>
      </div>
    </main>
  </div>
  <!-- Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">Are you sure you want to log out?</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <a href="auth/logout.php" class="btn btn-danger">ðŸšª Logout</a>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>
