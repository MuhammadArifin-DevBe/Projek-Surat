<?php
session_start();
require 'function.php';

if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit;
}

if (!isset($_GET['id'])) {
  echo "ID tidak ditemukan.";
  exit;
}

$id = $_GET['id'];
$surat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM surat WHERE id = $id"));

if (!$surat) {
  echo "Data surat tidak ditemukan.";
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jenis = $_POST['jenis'];
  $kategori = $_POST['kategori'];
  $nomor = $_POST['nomor'];
  $tanggal = $_POST['tanggal'];
  $penerima = $_POST['pengirim'];
  $perihal = $_POST['perihal'];
  $isi_pesan = $surat['isi_pesan'];

  // Upload file jika ada
  if (isset($_FILES['isi_pesan']) && $_FILES['isi_pesan']['error'] == 0) {
    $namaFile = $_FILES['isi_pesan']['name'];
    $tmpFile = $_FILES['isi_pesan']['tmp_name'];
    $targetDir = "uploads/";
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'txt'];

    if (in_array($ext, $allowed)) {
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
      $namaBaru = uniqid() . '.' . $ext;
      $targetFile = $targetDir . $namaBaru;

      if (move_uploaded_file($tmpFile, $targetFile)) {
        $isi_pesan = $targetFile;
      }
    }
  }

  $query = "UPDATE surat SET 
              jenis = '$jenis',
              kategori = '$kategori',
              nomor = '$nomor',
              tanggal = '$tanggal',
              penerima = '$penerima',
              perihal = '$perihal',
              isi_pesan = '$isi_pesan'
            WHERE id = $id";

  mysqli_query($conn, $query);

  if (mysqli_affected_rows($conn) > 0) {
    echo "<script>alert('Data berhasil diperbarui!'); window.location.href = 'suratkeluar.php';</script>";
  } else {
    echo "<script>alert('Tidak ada perubahan disimpan.');</script>";
  }
}
?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Edit Surat</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>
  <div class="container mt-4">
    <a href="tambah.php" class="btn btn-outline-primary mb-3">
      <i data-feather="arrow-left"></i> Kembali
    </a>

    <div class="card">
      <div class="card-header bg-primary text-white fw-bold">
        <i data-feather="edit"></i> Edit Data Surat
      </div>
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="jenis" class="form-label">Jenis Surat</label>
            <select class="form-select" id="jenis" name="jenis" required>
              <option value="masuk" <?= $surat['jenis'] === 'masuk' ? 'selected' : '' ?>>Surat Masuk</option>
              <option value="keluar" <?= $surat['jenis'] === 'keluar' ? 'selected' : '' ?>>Surat Keluar</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="kategori" class="form-label">Kategori Surat</label>
            <select class="form-select" id="kategori" name="kategori" required>
              <option value="UND" <?= $surat['kategori'] === 'UND' ? 'selected' : '' ?>>Undangan</option>
              <option value="EDR" <?= $surat['kategori'] === 'EDR' ? 'selected' : '' ?>>Edaran</option>
              <option value="PNJ" <?= $surat['kategori'] === 'PNJ' ? 'selected' : '' ?>>Penunjukan</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Surat</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $surat['tanggal'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="nomor" class="form-label">Nomor Surat</label>
            <input type="text" class="form-control" id="nomor" name="nomor" value="<?= $surat['nomor'] ?>" readonly required>
          </div>

          <div class="mb-3">
            <label for="pengirim" class="form-label">Penerima</label>
            <input type="text" class="form-control" id="pengirim" name="pengirim" value="<?= $surat['penerima'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="perihal" class="form-label">Perihal</label>
            <input type="text" class="form-control" id="perihal" name="perihal" value="<?= $surat['perihal'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="isi_pesan" class="form-label">Lampiran Baru (Opsional)</label>
            <input type="file" class="form-control" id="isi_pesan" name="isi_pesan">
            <?php if (!empty($surat['isi_pesan'])) : ?>
              <p class="mt-2">File sebelumnya: <a href="<?= $surat['isi_pesan'] ?>" target="_blank"><?= basename($surat['isi_pesan']) ?></a></p>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label for="email_pengirim" class="form-label">Dibuat oleh (Email)</label>
            <input type="text" class="form-control" value="<?= $surat['email_pengirim'] ?>" readonly>
          </div>

          <button type="submit" class="btn btn-primary">
            <i data-feather="save"></i> Simpan Perubahan
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    feather.replace();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
