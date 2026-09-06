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

  <!-- Bootstrap 5 CSS (loaded first so our own style.css can override it) -->
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
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">&#127968; Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="pastpapers.php">&#128196; Notes</a></li>
        <li class="nav-item"><a class="nav-link" href="upload.php">&#11014;&#65039; Upload Notes</a></li>
      </ul>
      <div class="sidebar-logout">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">&#128682; Logout</a>
      </div>
    </nav>

    <main class="main-content">

            <div class="topbar">
        <h1>Dashboard</h1>
        <div class="user-chip" id="userChip">
          <span class="avatar"><?= $initial ?></span><span><?= $username ?></span>
        </div>
      </div>

      <section class="welcome-banner">
        <h2>Welcome back, <?= $username ?>! &#128075;</h2>
        <p>Here's what's happening with your Notes today.</p>
      </section>

      
      
      <section class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="card stat-card h-100 border-0">
            <div class="stat-number"><?= htmlspecialchars($notesCount) ?></div>
            <div class="stat-label">Total Notes</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card stat-card h-100 border-0">
            <div class="stat-number">12</div>
            <div class="stat-label">Subjects Available</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card stat-card h-100 border-0">
            <div class="stat-number">34</div>
            <div class="stat-label">Notes You Downloaded</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card stat-card h-100 border-0">
            <div class="stat-number">5</div>
            <div class="stat-label">Notes You Uploaded</div>
          </div>
        </div>
      </section>

      
      <section class="row g-3 mb-4">
        <div class="col-6">
          <div class="card action-card h-100 border-0">
            <h3>Browse Notes</h3>
            <p>Search and download Notes by subject and year.</p>
            <a href="pastpapers.php">
              <button class="btn btn-primary">View Notes</button>
            </a>
          </div>
        </div>

        <div class="col-6">
          <div class="card action-card h-100 border-0">
            <h3>Share a Past Notes</h3>
            <p>Help other students by uploading a Notes you have.</p>
            <a href="upload.php">
              <button class="btn btn-primary">Upload Past Notes</button>
            </a>
          </div>
        </div>
      </section>

    </main>
  </div>


  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to log out of Campus Portal?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <a href="auth/logout.php" class="btn btn-danger">&#128682; Logout</a>
        </div>
      </div>
    </div>
  </div>

  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>