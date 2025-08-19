<?php
require 'function.php';
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

// Susun array untuk label tanggal dan nilai
$labels = [];
$masukData = [];
$keluarData = [];

while ($row = mysqli_fetch_assoc($dataMasuk)) {
  $labels[] = $row['tgl'];
  $masukData[] = $row['total'];
}

// Sinkronkan data keluar sesuai tanggal di label
foreach ($labels as $tgl) {
  $found = false;
  mysqli_data_seek($dataKeluar, 0);
  while ($row = mysqli_fetch_assoc($dataKeluar)) {
    if ($row['tgl'] === $tgl) {
      $keluarData[] = $row['total'];
      $found = true;
      break;
    }
  }
  if (!$found) {
    $keluarData[] = 0;
  }
} ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Statistik Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
  body {
    background-color: white;
    background-position: center;
  }
</style>

<body>

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg bg-primary">
    <div class="container-fluid mb-3 mt-3">
      <img src="img/uniska.png" alt="Logo Uniska">
      <a class="navbar-brand fw-bold" href="#">
        Sistem Surat Keluar & Masuk<br>
        <small class="fw-normal">Labolatorium Komputer</small>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
        <span class="navbar-toggler-icon"></span>
      </button>
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
              borderColor: 'rgba(54, 162, 235, 1)',
              backgroundColor: 'rgba(54, 162, 235, 0.2)',
              fill: true,
              tension: 0.3
            },
            {
              label: 'Surat Keluar',
              data: <?= json_encode($keluarData) ?>,
              borderColor: 'rgba(255, 99, 132, 1)',
              backgroundColor: 'rgba(255, 99, 132, 0.2)',
              fill: true,
              tension: 0.3
            }
          ]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              stepSize: 1
            }
          }
        }
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>