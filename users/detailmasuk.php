<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit;
}

require 'function.php';

if (!isset($_GET['id'])) {
  echo "ID surat tidak ditemukan.";
  exit;
}

$id = $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM surat WHERE id = $id");
$surat = mysqli_fetch_assoc($result);

if (!$surat) {
  echo "Surat tidak ditemukan.";
  exit;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Detail Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container mt-5">
  <h2>Detail Surat</h2>
  <table class="table table-bordered">
    <tr>
      <th>Nomor Surat</th>
      <td><?= htmlspecialchars($surat['nomor']) ?></td>
    </tr>
    <tr>
      <th>Jenis Surat</th>
      <td><?= htmlspecialchars($surat['jenis']) ?></td>
    </tr>
    <tr>
      <th>Perihal</th>
      <td><?= htmlspecialchars($surat['perihal']) ?></td>
    </tr>
    <tr>
      <th>Isi Surat</th>
      <td><?= nl2br(htmlspecialchars($surat['isi_pesan'])) ?></td>
    </tr>
    <tr>
      <th>Penerima</th>
      <td><?= htmlspecialchars($surat['penerima']) ?></td>
    </tr>
    <tr>
      <th>Tanggal</th>
      <td><?= date("d M Y", strtotime($surat['tanggal'])) ?></td>
    </tr>
  </table>

  <a href="suratmasuk.php" class="btn btn-primary"><i data-feather="arrow-left"></i> Kembali</a>
</div>

<script>
  feather.replace();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
