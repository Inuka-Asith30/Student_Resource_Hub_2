<?php
require_once '../includes/functions.php';
require_once '../includes/db.php';

if (isLoggedIn()) {
    header("Location: ../dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed])) {
                // Auto login user
                $user_id = $pdo->lastInsertId();
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user_id;

                // Redirect to dashboard.html
                echo "<script>
                        localStorage.setItem('isLoggedIn', 'true');
                        window.location.href = '../dashboard.php';
                      </script>";
                exit();
            } else {
                $error = "An error occurred during registration.";
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
  <title>Register | Student Past Paper Management System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
  <div class="login-page">
    <div class="card login-card border-0">
      <div class="logo-placeholder">LOGO</div>
      <h1>Register</h1>
      <p class="subtitle">Create an account to access resources</p>
      
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <form action="register.php" method="POST">
        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="e.g. jdoe" required />
        </div>
        <div class="form-group">
          <label for="email" class="form-label">Student Email</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="e.g. student@campus.edu" required />
        </div>
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Enter a secure password" required minlength="6" />
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
      </form>
      
      <p class="mt-3 mb-0">
        <a href="login.php" class="small">Already have an account? Login here.</a>
      </p>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/script.js"></script>
</body>
</html>
