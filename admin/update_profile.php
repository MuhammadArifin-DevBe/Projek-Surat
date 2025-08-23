<?php
session_start();
require '../function.php'; // koneksi $conn

// cek login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['id'];
$message = "";

// ambil data user saat ini
$res = mysqli_query($conn, "SELECT * FROM users WHERE id='$userId'");
$user = mysqli_fetch_assoc($res);

// update profil
if (isset($_POST['update'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : $user['password'];

    // proses upload avatar jika ada
    $avatarFile = $user['avatar']; // default tetap avatar lama
    if (!empty($_FILES['avatar']['name'])) {
        $fileName = time() . "_" . basename($_FILES['avatar']['name']);
        $targetDir = "../uploads/avatars/";
        $targetFile = $targetDir . $fileName;

        // buat folder kalau belum ada
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // validasi file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (in_array($_FILES['avatar']['type'], $allowedTypes)) {
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                $avatarFile = $fileName;
            }
        }
    }

    // update ke database
    $query = "UPDATE users SET username='$username', password='$hashedPassword', avatar='$avatarFile' WHERE id='$userId'";
    if (mysqli_query($conn, $query)) {
        $message = "Profil berhasil diperbarui.";
        // refresh data user
        $res = mysqli_query($conn, "SELECT * FROM users WHERE id='$userId'");
        $user = mysqli_fetch_assoc($res);
    } else {
        $message = "Gagal update profil: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>My Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2>My Profil</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message; ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']); ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password Baru (kosongkan jika tidak diganti)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Foto Avatar</label><br>
            <?php if ($user['avatar']): ?>
                <img src="../uploads/avatars/<?= $user['avatar']; ?>" alt="Avatar" width="80" height="80" class="rounded-circle mb-2"><br>
            <?php else: ?>
                <img src="avatar.php" alt="Avatar" width="80" height="80" class="rounded-circle mb-2"><br>
            <?php endif; ?>
            <input type="file" name="avatar" class="form-control">
        </div>

        <button type="submit" name="update" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>

</body>
</html>
