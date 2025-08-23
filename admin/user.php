<?php
session_start(); 
require '../function.php';

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

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
    <title>Daftar Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="../css/style.css">
    <style>
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
</head>

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