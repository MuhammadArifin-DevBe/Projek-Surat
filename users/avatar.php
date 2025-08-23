<?php
require '../vendor/autoload.php';
require '../function.php'; // koneksi $conn

use LasseRafn\InitialAvatarGenerator\InitialAvatar;

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['id']; // ambil id user dari session

// query nama user dari database
$res = mysqli_query($conn, "SELECT username FROM users WHERE id='$userId' LIMIT 1");
$row = mysqli_fetch_assoc($res);
$name = $row ? $row['username'] : 'Default User';

// generate avatar
$avatar = new InitialAvatar();
$image  = $avatar->name($name)->generate();

header('Content-Type: image/png');
echo $image->stream('png', 100);
exit;
