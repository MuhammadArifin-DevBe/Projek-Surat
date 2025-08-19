<?php
require '../function.php'; // koneksi $conn

// Ambil semua user
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-primary">
        <div class="container-fluid mb-3 mt-3">
            <img src="../img/uniska.png" alt="Logo Uniska">
            <a class="navbar-brand fw-bold" href="#">
                Sistem Surat Keluar & Masuk<br>
                <small class="fw-normal">Laboratorium Komputer</small>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0"></ul>
                <span class="navbar-text">
                    <a href="../logout.php" class="btn btn-primary d-flex align-items-center gap-2">
                        <i data-feather="user"></i> Logout
                    </a>
                </span>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
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

        <!-- KONTEN -->
        <div class="container mt-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <strong>Daftar Pengguna</strong>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($u = mysqli_fetch_assoc($users)) { ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><?= $u['username'] ?></td>
                                    <td><?= $u['email'] ?></td>
                                    <td><?= ucfirst($u['role']) ?></td>
                                    <td>
                                        <a href="hapus_user.php?id=<?= $u['id'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus user ini?')">
                                            <i data-feather="trash-2"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
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