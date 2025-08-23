<?php
require '../vendor/autoload.php';
require '../function.php';

use LasseRafn\InitialAvatarGenerator\InitialAvatar;

session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['id'];
$res = mysqli_query($conn, "SELECT username, avatar FROM users WHERE id='$userId'");
$user = mysqli_fetch_assoc($res);

if ($user && $user['avatar']) {
    // kalau user punya avatar upload → tampilkan file
    $file = "../uploads/avatars/" . $user['avatar'];
    if (file_exists($file)) {
        header("Content-Type: image/png");
        readfile($file);
        exit;
    }
}

// fallback: avatar inisial
$name = $user ? $user['username'] : "User";
$avatar = new InitialAvatar();
$image  = $avatar->name($name)->generate();

header('Content-Type: image/png');
echo $image->stream('png', 100);
exit;