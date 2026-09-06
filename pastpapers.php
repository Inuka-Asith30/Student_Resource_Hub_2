<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
requireLogin();

$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$username = $user ? htmlspecialchars($user['username']) : 'Student';
$initial = strtoupper(substr($username, 0, 2));

$stmt = $pdo->query("SELECT * FROM notes ORDER BY created_at DESC");
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notes | Student Notes Management System</title>
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
        <li class="nav-item"><a class="nav-link" href="dashboard.php">&#127968; Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="pastpapers.php">&#128196; Notes</a></li>
        <li class="nav-item"><a class="nav-link" href="upload.php">&#11014;&#65039; Upload Notes</a></li>
      </ul>
      <div class="sidebar-logout">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">&#128682; Logout</a>
      </div>
    </nav>
    <main class="main-content">
      <div class="topbar">
        <h1>Notes</h1>
        <div class="user-chip">
          <span class="avatar"><?= $initial ?></span><span><?= $username ?></span>
        </div>
      </div>
      <div class="input-group search-bar">
        <input type="text" id="searchInput" class="form-control" placeholder="Search by subject name (e.g. Physics, Maths...)" />
        <button id="searchBtn" class="btn btn-primary" type="button">Search</button>
      </div>
      <section class="row g-3" id="papersGrid">
        <?php foreach ($notes as $note): ?>
        <div class="col-6 col-lg-4">
          <div class="card paper-card h-100 border-0" data-subject="<?= htmlspecialchars(strtolower($note['subject_name'])) ?>">
            <div class="subject-icon"><?= htmlspecialchars(strtoupper(substr($note['subject_name'], 0, 2))) ?></div>
            <h3><?= htmlspecialchars($note['subject_name']) ?></h3>
            <p class="paper-year">Year: <?= htmlspecialchars($note['note_year']) ?></p>
            <a href="<?= htmlspecialchars($note['file_path']) ?>" target="_blank" class="btn download-btn" style="text-decoration:none;">&#128229; View / Download</a>
          </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($notes)): ?>
          <p>No notes uploaded yet.</p>
        <?php endif; ?>
      </section>
      <p class="no-results" id="noResults" style="display:none;">No Notes matched your search.</p>
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
          <a href="auth/logout.php" class="btn btn-danger">&#128682; Logout</a>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
</body>
</html>