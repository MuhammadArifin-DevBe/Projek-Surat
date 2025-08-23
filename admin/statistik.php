<?php
session_start();
require '../function.php';

// Ambil data jumlah surat masuk per tanggal
$dataMasuk = mysqli_query($conn, "
    SELECT DATE(tanggal) as tgl, COUNT(*) as total
    FROM surat
    WHERE jenis = 'masuk'
    GROUP BY DATE(tanggal)
    ORDER BY DATE(tanggal)
");

// Ambil data jumlah surat keluar per tanggal
$dataKeluar = mysqli_query($conn, "
    SELECT DATE(tanggal) as tgl, COUNT(*) as total
    FROM surat
    WHERE jenis = 'keluar'
    GROUP BY DATE(tanggal)
    ORDER BY DATE(tanggal)
");

// Simpan hasil query ke array sementara
$dataM = [];
$dataK = [];
$allDates = [];

// Masukkan data masuk
while ($row = mysqli_fetch_assoc($dataMasuk)) {
  $dataM[$row['tgl']] = $row['total'];
  $allDates[] = $row['tgl'];
}

// Masukkan data keluar
while ($row = mysqli_fetch_assoc($dataKeluar)) {
  $dataK[$row['tgl']] = $row['total'];
  $allDates[] = $row['tgl'];
}

// Hilangkan duplikat tanggal & urutkan
$allDates = array_unique($allDates);
sort($allDates);

// Susun data final untuk chart
$labels = [];
$masukData = [];
$keluarData = [];

foreach ($allDates as $tgl) {
  $labels[] = $tgl;
  $masukData[] = isset($dataM[$tgl]) ? $dataM[$tgl] : 0;
  $keluarData[] = isset($dataK[$tgl]) ? $dataK[$tgl] : 0;
}
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
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Statistik Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
  body {
    background-color: white;
    background-position: center;
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

  <!-- NAVBAR -->
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

  <!-- NAVIGASI SAMPING -->
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


    <!-- KONTEN GRAFIK -->
    <div class="container mt-5">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <strong>Grafik Tren Surat Masuk & Keluar</strong>
        </div>
        <div class="card-body">
          <canvas id="suratChart" style="max-height: 400px;"></canvas>
        </div>
      </div>
    </div>

    <script>
      feather.replace();

      const ctx = document.getElementById('suratChart').getContext('2d');
      const suratChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: <?= json_encode($labels) ?>,
          datasets: [{
              label: 'Surat Masuk',
              data: <?= json_encode($masukData) ?>,
              borderColor: 'rgba(98, 209, 97, 1)',
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              fill: false, // tidak ada area warna di bawah garis
              tension: 0, // garis patah-patah, bukan melengkung
              pointRadius: 3, // titik data kecil
              pointHoverRadius: 6
            },
            {
              label: 'Surat Keluar',
              data: <?= json_encode($keluarData) ?>,
              borderColor: 'rgba(255, 44, 44, 1)',
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              fill: false,
              tension: 0,
              pointRadius: 3,
              pointHoverRadius: 6
            }
          ]
        },
        options: {
          responsive: true,
          interaction: {
            mode: 'index',
            intersect: false,
          },
          plugins: {
            tooltip: {
              enabled: true,
              mode: 'nearest',
              intersect: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>