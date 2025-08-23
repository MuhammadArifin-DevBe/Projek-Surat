<?php
require '../vendor/autoload.php';
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
include '../function.php';
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

<head>
    <meta charset="utf-8">
    <title>Surat Keluar</title>
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