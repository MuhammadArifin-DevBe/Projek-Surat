<?php
require '../function.php';

if (!isset($_GET['id'])) {
  echo "ID tidak ditemukan!";
  exit;
}

$id = $_GET['id'];

mysqli_query($conn, "DELETE FROM surat WHERE id = $id");
header("Location: suratmasuk.php"); // atau halaman utama kamu
exit;
