<?php
session_start();
require '../function.php';

if (!isset($_SESSION['login']) || !isset($_SESSION['email'])) {
  header("Location: login.php");
  exit;
}

$email_pengirim = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jenis = 'masuk'; // otomatis masuk
  $kategori = $_POST['kategori'];
  $nomor = $_POST['nomor'];
  $tanggal = $_POST['tanggal'];
  $penerima = $_POST['pengirim'];
  $perihal = $_POST['perihal'];
  $isi_pesan = '';

  // Proses upload file
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

  // Validasi
  if ($jenis && $kategori && $nomor && $tanggal && $penerima && $perihal) {
    $query = "INSERT INTO surat (jenis, kategori, nomor, tanggal, penerima, perihal, isi_pesan, email_pengirim)
              VALUES ('$jenis', '$kategori', '$nomor', '$tanggal', '$penerima', '$perihal', '$isi_pesan', '$email_pengirim')";
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
      header("Location: tambah_surat_masuk.php?status=sukses");
      exit;
    } else {
      $error = "Gagal menyimpan data.";
    }
  } else {
    $error = "Semua field wajib diisi!";
  }
}

// Query surat selalu dijalankan agar tidak error
$surat = mysqli_query($conn, "SELECT * FROM surat ORDER BY id DESC");
?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Form Surat Otomatis</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

  <div class="container mt-4">
    <a href="suratmasuk.php" class="btn btn-outline-primary mb-3">
      <i data-feather="arrow-left"></i> Kembali
    </a>

    <div class="card mb-4">
      <div class="card-header bg-primary text-white fw-bold">
        <i data-feather="edit-3"></i> Form Pembuatan Surat Masuk
      </div>
      <div class="card-body">
        <?php if (isset($error)) : ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['status']) && $_GET['status'] === 'sukses') : ?>
          <div class="alert alert-success">Data berhasil disimpan!</div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
          <!-- <div class="mb-3">
            <label for="jenis" class="form-label">Jenis Surat</label>
            <select class="form-select" id="jenis" name="jenis" required>
              <option selected disabled>Pilih Jenis</option>
              <option value="masuk">Surat Masuk</option>
              <option value="keluar">Surat Keluar</option>
            </select>
          </div> -->

          <div class="mb-3">
            <label for="kategori" class="form-label">Kategori Surat</label>
            <select class="form-select" id="kategori" name="kategori" required>
              <option value="SPm">Permohonan</option>
              <option value="SU">Undangan</option>
              <option value="SP">Pemberitahuan</option>
              <option value="ST">Tugas</option>
              <option value="SPn">Peminjaman</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Surat</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
          </div>

          <div class="mb-3">
            <label for="nomor" class="form-label">Nomor Surat (Otomatis)</label>
            <input type="text" class="form-control" id="nomor" name="nomor" readonly required>
          </div>

          <div class="mb-3">
            <label for="pengirim" class="form-label">Penerima</label>
            <input type="text" class="form-control" id="pengirim" name="pengirim" placeholder="Instansi atau Individu" required>
          </div>

          <div class="mb-3">
            <label for="perihal" class="form-label">Perihal</label>
            <input class="form-control" id="perihal" name="perihal" placeholder="Isi perihal surat" required>
          </div>

          <div class="mb-3">
            <label for="isi_pesan" class="form-label">Lampiran File</label>
            <input type="file" class="form-control" id="isi_pesan" name="isi_pesan">
          </div>

          <button type="submit" class="btn btn-primary">
            <i data-feather="save"></i> Simpan Surat
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    feather.replace();

    function convertToRomawi(bulan) {
      const romawi = ["I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII"];
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
        const unik = Math.floor(100 + Math.random() * 900); // random 3 digit

        const hasil = `${unik}/${kategori}/LPMUTU-UNISKA/P.15/${bulanRomawi}/${tahun}`;
        document.getElementById('nomor').value = hasil;
      }
    }

    document.getElementById('kategori').addEventListener('change', generateNomorSurat);
    document.getElementById('tanggal').addEventListener('change', generateNomorSurat);
  </script>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      const today = new Date();
      const yyyy = today.getFullYear();
      const mm = String(today.getMonth() + 1).padStart(2, '0'); // bulan = 0-11
      const dd = String(today.getDate()).padStart(2, '0');

      document.getElementById('tanggal').value = `${yyyy}-${mm}-${dd}`;
      generateNomorSurat(); // auto-generate nomor juga langsung
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>