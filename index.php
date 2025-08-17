<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header("Location: admin/index.php");
} else {
    header("Location: users/index.php");
}
exit;
?>
