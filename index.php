<?php
require_once 'includes/functions.php';
if (isLoggedIn()) {
    header("Location: dashboard.php");
} else {
    header("Location: auth/login.php");
}
exit();
?>
