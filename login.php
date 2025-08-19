<?php
require 'function.php';
session_start();

if (isset($_SESSION["login"])) {
  if ($_SESSION['role'] === 'admin') {
    header("Location: admin/index.php");
  } else {
    header("Location: users/index.php");
  }
  exit;
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim($_POST["username"]);
  $password = $_POST["password"];

  if ($username === "") {
    $errors['username'] = "Username tidak boleh kosong.";
  }

  if ($password === "") {
    $errors['password'] = "Password tidak boleh kosong.";
  }

  if (!$errors) {
    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    if (mysqli_num_rows($result) === 1) {
      $user = mysqli_fetch_assoc($result);
      if (password_verify($password, $user['password'])) {
        $_SESSION["login"] = true;
        $_SESSION["user"] = $user['username'];
        $_SESSION["email"] = $user['email'];
        $_SESSION["id"] = $user['id'];
        $_SESSION["role"] = $user['role']; // simpan role

        if ($user['role'] === 'admin') {
          header("Location: index.php");
        } else {
          header("Location: users/index.php");
        }
        exit;
      } else {
        $errors['password'] = "Password salah.";
      }
    } else {
      $errors['username'] = "Username tidak ditemukan.";
    }
  }
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    body {
      background-image: url('img/bg.jpg');
      background-size: cover;
      background-position: center;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background-color: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 15px;
      padding: 30px;
      width: 100%;
      max-width: 400px;
      color: #fff;
    }
  </style>
</head>

<body>
  <div class="login-card text-center shadow">
    <div class="mb-2">
      <i data-feather="user" width="48" height="48" color="#333"></i>
    </div>
    <h2 class="mb-3" style="color:#333;">User Login</h2>
    <form action="" method="POST">
      <div class="mb-3 text-start">
        <input type="text" class="form-control <?php if (isset($errors['username'])) echo 'is-invalid'; ?>" name="username" placeholder="Masukkan username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <?php if (isset($errors['username'])): ?>
          <div class="invalid-feedback"><?= $errors['username'] ?></div>
        <?php endif; ?>
      </div>
      <div class="mb-3 text-start">
        <input type="password" class="form-control <?php if (isset($errors['password'])) echo 'is-invalid'; ?>" name="password" placeholder="Masukkan password">
        <?php if (isset($errors['password'])): ?>
          <div class="invalid-feedback"><?= $errors['password'] ?></div>
        <?php endif; ?>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="register.php">Belum Punya Akun?</a>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    feather.replace();
  </script>
</body>

</html>