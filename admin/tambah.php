<?php
session_start();
require '../function.php';

if (!isset($_SESSION['login']) || !isset($_SESSION['email'])) {
  header("Location: login.php");
  exit;
}

$email_pengirim = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jenis = "keluar"; // otomatis untuk surat keluar
  $kategori = $_POST['kategori'];
  $nomor = $_POST['nomor'];
  $tanggal = $_POST['tanggal'];
  $penerima = $_POST['penerima'];
  $perihal = $_POST['perihal'];
  $isi_pesan = '';

  // Proses upload file
  if (isset($_FILES['isi_pesan']) && $_FILES['isi_pesan']['error'] == 0) {
    $namaFile = $_FILES['isi_pesan']['name'];
    $tmpFile = $_FILES['isi_pesan']['tmp_name'];
    $targetDir = "uploads/";
    $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png'];

    if (in_array($ext, $allowed)) {
      if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
      $namaBaru = uniqid() . '.' . $ext;
      $targetFile = $targetDir . $namaBaru;
      if (move_uploaded_file($tmpFile, $targetFile)) {
        $isi_pesan = $targetFile;
      }
    }
  }

  if ($kategori && $nomor && $tanggal && $penerima && $perihal) {
    $query = "INSERT INTO surat (jenis, kategori, nomor, tanggal, penerima, perihal, isi_pesan, email_pengirim)
              VALUES ('$jenis', '$kategori', '$nomor', '$tanggal', '$penerima', '$perihal', '$isi_pesan', '$email_pengirim')";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
      header("Location: suratkeluar.php?status=sukses");
      exit;
    } else {
      $error = "Gagal menyimpan data.";
    }
  } else {
    $error = "Semua field wajib diisi!";
  }
}

$surat = mysqli_query($conn, "SELECT * FROM surat WHERE jenis='keluar' ORDER BY id DESC");
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Surat Keluar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
<div class="container mt-4">
  <a href="suratkeluar.php" class="btn btn-outline-primary mb-3"><i data-feather="arrow-left"></i> Kembali</a>

  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Tambah Surat Keluar</div>
    <div class="card-body">
      <?php if (!empty($error)) : ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['status']) && $_GET['status'] === 'sukses') : ?>
        <div class="alert alert-success">Data berhasil disimpan!</div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label>Kategori</label>
          <select class="form-select" name="kategori" id="kategori" required>
            <option value="" disabled selected>Pilih Kategori</option>
            <option value="PMN">Permohonan</option>
            <option value="LPJ">Laporan Pertanggungjawaban</option>
            <option value="PBT">Pemberitahuan</option>
            <option value="LL">Lain-lain</option>
          </select>
        </div>

        <div class="mb-3">
          <label>Tanggal</label>
          <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Nomor Surat</label>
          <input type="text" name="nomor" id="nomor" class="form-control" readonly>
        </div>

        <div class="mb-3">
          <label>Penerima</label>
          <input type="text" name="penerima" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Perihal</label>
          <input type="text" name="perihal" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Lampiran</label>
          <input type="file" name="isi_pesan" class="form-control">
        </div>

        <button class="btn btn-primary"><i data-feather="save"></i> Simpan</button>
      </form>
    </div>
  </div>
<script>
feather.replace();

function convertToRomawi(bulan) {
  const romawi = ["I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII"];
  return romawi[bulan - 1];
}
function generateNomorSurat() {
  const kategori = document.getElementById('kategori').value;
  const tanggal = document.getElementById('tanggal').value;
  if (kategori && tanggal) {
    const dateObj = new Date(tanggal);
    const bulan = dateObj.getMonth() + 1;
    const tahun = dateObj.getFullYear();
    const bulanRomawi = convertToRomawi(bulan);
    const unik = Math.floor(100 + Math.random() * 900);
    document.getElementById('nomor').value = `${unik}/${kategori}/LPMUTU-UNISKA/P.15/${bulanRomawi}/${tahun}`;
  }
}
document.getElementById('kategori').addEventListener('change', generateNomorSurat);
document.getElementById('tanggal').addEventListener('change', generateNomorSurat);
window.addEventListener('DOMContentLoaded', function() {
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = String(today.getMonth() + 1).padStart(2, '0');
  const dd = String(today.getDate()).padStart(2, '0');
  document.getElementById('tanggal').value = `${yyyy}-${mm}-${dd}`;
  generateNomorSurat();
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
