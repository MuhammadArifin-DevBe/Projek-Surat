<?php
$conn = mysqli_connect("localhost", "root", "", "project");

function registrasi($data) {
  global $conn;
  $username = strtolower(stripslashes($data["username"]));
  $password = mysqli_real_escape_string($conn, $data["password"]);

  // cek username sudah ada atau belum
  $result = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
  if (mysqli_fetch_assoc($result)) {
    echo "<script>alert('Username sudah terdaftar!');</script>";
    return false;
  }

  // enkripsi password
  $password = password_hash($password, PASSWORD_DEFAULT);

  // tambahkan ke database
  mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$username', '$password')");
  return mysqli_affected_rows($conn);
}
?>
