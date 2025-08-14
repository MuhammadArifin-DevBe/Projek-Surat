<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'function.php';
?>

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

    <!-- Navbar -->
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

        <!-- Konten -->
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-12">
                    <h3>Dashboard</h3>
                    <p>Selamat datang di Sistem Surat.</p>
                </div>
            </div>

            <div class="row mt-4 g-3"> <!-- g-3 untuk jarak antar card -->
                <!-- Surat Masuk -->
                <div class="col-12 col-md-3">
                    <div class="card text-white bg-success h-100">
                        <div class="card-body">
                            <h5 class="card-title">Surat Masuk</h5>
                            <p class="card-text display-6 mb-0">
                                <?php
                                $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM surat WHERE jenis='masuk'");
                                $row = mysqli_fetch_assoc($res);
                                echo $row['total'];
                                ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Surat Keluar -->
                <div class="col-12 col-md-3">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body">
                            <h5 class="card-title">Surat Keluar</h5>
                            <p class="card-text display-6 mb-0">
                                <?php
                                $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM surat WHERE jenis='keluar'");
                                $row = mysqli_fetch_assoc($res);
                                echo $row['total'];
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Tambahkan kartu lain sesuai kebutuhan -->
            </div>
        </div>

        <script>
            feather.replace();
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>