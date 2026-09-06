<?php
require_once 'includes/functions.php';
require_once 'includes/db.php';
requireLogin();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subjectName'] ?? '');
    $year = intval($_POST['NoteYear'] ?? 0);
    
    if (empty($subject) || $year < 2000 || !isset($_FILES['NoteFile']) || $_FILES['NoteFile']['error'] !== UPLOAD_ERR_OK) {
        $error = "Please provide valid subject, year, and a file.";
    } else {
        $fileTmpPath = $_FILES['NoteFile']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['NoteFile']['name']);
        $dest_path = "uploads/" . $fileName;
        
        $fileType = strtolower(pathinfo($dest_path, PATHINFO_EXTENSION));
        if ($fileType !== 'pdf') {
            $error = "Only PDF files are allowed.";
        } else {
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $pdo->prepare("INSERT INTO notes (user_id, subject_name, note_year, file_path) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$_SESSION['user_id'], $subject, $year, $dest_path])) {
                    $success = true;
                } else {
                    $error = "Database insertion failed.";
                }
            } else {
                $error = "Error uploading file.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload Note | Student Notes Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="app-layout">
    <nav class="navbar navbar-dark sidebar flex-column">
      <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
        <span class="logo-placeholder logo-placeholder-nav">LOGO</span>
        <span>Campus<br />Portal</span>
      </a>
      <ul class="navbar-nav flex-column w-100">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="notes.php">📄 Notes</a></li>
        <li class="nav-item"><a class="nav-link active" aria-current="page" href="upload.php">⬆️ Upload Notes</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">✉️ Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="auth/register.php">📝 Register</a></li>
      </ul>
      <div class="sidebar-logout">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">🚪 Logout</a>
      </div>
    </nav>
    <main class="main-content">
      <div class="topbar">
        <h1>Upload Note</h1>
        <div class="user-chip">
          <span class="avatar">US</span>
          <span>User</span>
        </div>
      </div>
      <div class="card upload-card border-0">
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger" role="alert">
              <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>
        <form id="uploadForm" action="upload.php" method="POST" enctype="multipart/form-data" novalidate>
          <div class="form-group">
            <label for="subjectName" class="form-label">Subject Name</label>
            <input type="text" id="subjectName" name="subjectName" class="form-control" placeholder="e.g. Physics" required />
          </div>
          <div class="form-group">
            <label for="NoteYear" class="form-label">Year</label>
            <input type="number" id="NoteYear" name="NoteYear" class="form-control" placeholder="e.g. 2024" min="2000" max="2100" required />
          </div>
          <div class="form-group">
            <label for="NoteFile" class="form-label">Upload File (PDF)</label>
            <input type="file" id="NoteFile" name="NoteFile" class="form-control" accept=".pdf" required />
          </div>
          <button type="submit" class="btn btn-primary">Upload Note</button>
        </form>
      </div>
    </main>
  </div>
  
  <div class="modal fade" id="uploadSuccessModal" tabindex="-1" aria-labelledby="uploadSuccessLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body text-center py-4">
          <div class="display-6 mb-2">✅</div>
          <h5 class="mb-1" id="uploadSuccessLabel">Upload Successful</h5>
          <p class="text-muted mb-0">Your Note was uploaded successfully!</p>
        </div>
        <div class="modal-footer justify-content-center border-0 pt-0">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
        </div>
      </div>
    </div>
  </div>

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
          <a href="auth/logout.php" class="btn btn-danger">🚪 Logout</a>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/script.js"></script>
  <?php if ($success): ?>
  <script>
      document.addEventListener("DOMContentLoaded", function() {
          var myModal = new bootstrap.Modal(document.getElementById('uploadSuccessModal'));
          myModal.show();
      });
  </script>
  <?php endif; ?>
</body>
</html>
