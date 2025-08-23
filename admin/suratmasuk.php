<?php
session_start();
require '../function.php';

if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit;
}

$email = $_SESSION['email'];

// Ambil parameter filter
$cari = isset($_GET['cari']) ? trim($_GET['cari']) : '';
$kategoriFilter = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

// Query awal (string, bukan langsung mysqli_query)
$queryMasuk = "SELECT * FROM surat WHERE jenis = 'masuk'";
$params = [];
$types = "";

// Filter pencarian
if ($cari !== '') {
  $queryMasuk .= " AND (nomor LIKE ? OR perihal LIKE ? OR isi_pesan LIKE ?)";
  $searchTerm = "%$cari%";
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $params[] = $searchTerm;
  $types .= "sss";
}

// Filter kategori
if ($kategoriFilter !== '') {
  $queryMasuk .= " AND kategori = ?";
  $params[] = $kategoriFilter;
  $types .= "s";
}

// Urutkan data
$queryMasuk .= " ORDER BY tanggal DESC";

// Eksekusi prepared statement
$stmt = mysqli_prepare($conn, $queryMasuk);
if (!empty($params)) {
  mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$suratMasuk = mysqli_stmt_get_result($stmt);
/* --- ambil data user untuk sidebar --- */
$userId   = $_SESSION['id'] ?? 0;
$username = 'User';
$email    = '-';

if ($userId) {
    // lebih aman pakai prepared statement
    if ($stmt = mysqli_prepare($conn, "SELECT username, email FROM users WHERE id = ? LIMIT 1")) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $username = $row['username'] ?? $username;
            $email    = $row['email'] ?? $email;
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Surat Masuk</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="../css/style.css">
</head>
<style>
  body {
    background-color: white;
    background-position: center;
  }

  .tree a.active {
    font-weight: bold;
    color: blue !important;
    border-bottom: 2px solid blue;
  }

  .avatar-thumb {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    /* bikin bulat */
    object-fit: cover;
    /* isi penuh */
    border: 2px solid #fff;
    /* biar rapi ada border putih */
    background-color: #f0f0f0;
  }
</style>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-primary" id="navbar">
    <div class="container-fluid mb-3 mt-3">
      <img src="../img/uniska.png" alt="Logo Uniska">
      <a class="navbar-brand fw-bold text-white" href="#">
        Sistem Surat Keluar & Masuk<br>
        <small class="fw-normal">Laboratorium Komputer</small>
      </a>
      <!-- Avatar (klik untuk buka sidebar) -->
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarText">
        <div class="d-flex align-items-center gap-2">
          <!-- Avatar (klik untuk buka sidebar) -->
          <img src="avatar.php" alt="Avatar" class="avatar-thumb"
            title="Profil" data-bs-toggle="offcanvas" data-bs-target="#profileSidebar">
        </div>
      </div>
    </div>
  </nav>

  <!-- Sidebar Profile -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="profileSidebar" aria-labelledby="profileSidebarLabel">
    <div class="offcanvas-header bg-primary text-white">
      <div class="d-flex align-items-center gap-2">
        <img src="avatar.php" alt="Avatar" class="avatar-thumb">
        <div>
          <h5 class="offcanvas-title mb-0" id="profileSidebarLabel"><?= htmlspecialchars($username) ?></h5>
          <small class="opacity-75"><?= htmlspecialchars($email) ?></small>
        </div>
      </div>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="list-group mb-3">
        <a href="update_profile.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2">
          <i data-feather="user"></i><span>My Profil</span>
        </a>
        <a href="../logout.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2 text-danger">
          <i data-feather="log-out"></i><span>Logout</span>
        </a>
      </div>
    </div>
  </div>
  </div>
  </nav>

  <!-- Sidebar -->
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
      <a href="user.php" class="<?= basename($_SERVER['PHP_SELF']) === 'user.php' ? 'active' : '' ?>">
        <i data-feather="users"></i> Data Pengguna
      </a>
    </div>

    <!-- Form search -->
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
        <a href="tambah_surat_masuk.php" class="btn btn-outline-primary"><i data-feather="plus"></i> Tambah Surat</a>
      </form>
    </div>

    <!-- Daftar Surat -->
    <div class="card mb-5 mt-4">
      <div class="card-header bg-secondary text-white fw-semibold">
        <i data-feather="list"></i> Daftar Surat Masuk
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered m-0 text-center">
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
              <?php if (mysqli_num_rows($suratMasuk) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($suratMasuk)) : ?>
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
                    <td>
                      <a href="editmasuk.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm"> <i data-feather="edit-2"></i>
                      </a>
                      <a href="hapusmasuk.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')"> <i data-feather="trash-2"></i>
                      </a>
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