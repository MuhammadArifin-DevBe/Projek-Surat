<?php
session_start();
if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit;
}

require 'function.php';

$email = $_SESSION['email'];
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$kategoriFilter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

$queryKeluar = "SELECT * FROM surat WHERE jenis = 'keluar'";
if ($cari != '') {
  $queryKeluar .= " AND (nomor LIKE '%$cari%' OR perihal LIKE '%$cari%' OR isi_pesan LIKE '%$cari%')";
}
if ($kategoriFilter != '') {
  $queryKeluar .= " AND kategori = '$kategoriFilter'";
}
$queryKeluar .= " ORDER BY tanggal DESC";

$suratKeluar = mysqli_query($conn, $queryKeluar);
?>

<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Surat Keluar</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="style.css">
</head>
<style>
  body {
    background-color: white;
    background-position: center;
  }
</style>


<body>
  <nav class="navbar navbar-expand-lg bg-primary" id="navbar">
    <div class="container-fluid mb-3 mt-3">
      <img src="img/uniska.png" alt="Logo Uniska">
      <a class="navbar-brand fw-bold text-white" href="#">
        Sistem Surat Keluar & Masuk<br>
        <small class="fw-normal">Laboratorium Komputer</small>
      </a>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
        <span class="navbar-text">
          <a href="logout.php" class="btn btn-primary d-flex align-items-center gap-2">
            <i data-feather="user"></i> Logout
          </a>
        </span>
      </div>
    </div>
  </nav>

  <div class="card mt-5 mx-3">
    <div class="card-body tree border-bottom" id="treeNav">
      <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
        <i data-feather="home"></i> Dashboard
      </a>
      <a href="suratmasuk.php" class="<?= basename($_SERVER['PHP_SELF']) === 'suratmasuk.php' ? 'active' : '' ?>">
        <i data-feather="inbox"></i> Surat Masuk
      </a>
      <a href="suratkeluar.php" class="<?= basename($_SERVER['PHP_SELF']) === 'suratkeluar.php' ? 'active' : '' ?>">
        <i data-feather="send"></i> Surat Keluar
      </a>
      <a href="statistik.php" class="<?= basename($_SERVER['PHP_SELF']) === 'statistik.php' ? 'active' : '' ?>">
        <i data-feather="bar-chart"></i> Statistik
      </a>
    </div>


    <!-- FORM SEARCH + FILTER -->
    <div class="search-form mt-4 mb-3 px-3">
      <form method="GET" class="d-flex flex-wrap gap-2">
        <div class="input-group" style="max-width: 300px;">
          <input type="text" name="cari" class="form-control" placeholder="Cari Surat..." value="<?= htmlspecialchars($cari) ?>">
          <button class="btn btn-primary" type="submit"><i data-feather="search"></i></button>
        </div>

        <select name="kategori" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
          <option value="">Semua Kategori</option>
          <option value="UND" <?= ($kategoriFilter == 'UND') ? 'selected' : '' ?>>Undangan</option>
          <option value="EDR" <?= ($kategoriFilter == 'EDR') ? 'selected' : '' ?>>Edaran</option>
          <option value="PNJ" <?= ($kategoriFilter == 'PNJ') ? 'selected' : '' ?>>Penunjukan</option>
        </select>

        <a class="text-decoration-none" href="tambah.php">
          <button class="btn btn-outline-primary" type="button"><i data-feather="plus"></i> Tambah Surat</button>
        </a>
      </form>
    </div>

    <!-- Daftar Surat Keluar -->
    <div class="card mb-5 mt-4">
      <div class="card-header bg-secondary text-white fw-semibold">
        <i data-feather="list"></i> Daftar Surat Keluar
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered m-0">
            <thead class="table-light">
              <tr>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Nomor</th>
                <th>Tanggal</th>
                <th>Penerima</th>
                <th>Perihal</th>
                <th>Isi Pesan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($suratKeluar) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($suratKeluar)) : ?>
                  <tr>
                    <td><?= htmlspecialchars($row['jenis']) ?></td>
                    <td><?= htmlspecialchars($row['kategori']) ?></td>
                    <td><?= htmlspecialchars($row['nomor']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['penerima']) ?></td>
                    <td><?= htmlspecialchars($row['perihal']) ?></td>
                    <td>
                      <?php if (!empty($row['isi_pesan'])) : ?>
                        <a href="<?= htmlspecialchars($row['isi_pesan']) ?>" target="_blank">Lihat</a>
                      <?php else : ?>
                        Tidak Ada Pesan
                      <?php endif; ?>
                    </td>
                    <td style="display: flex; gap: 5px;">
                      <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                      <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="8" class="text-center">Tidak ada surat ditemukan.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <script>
    feather.replace();
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>