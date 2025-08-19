<?php
require '../function.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // biar aman
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
}

header("Location: user.php");
exit;
