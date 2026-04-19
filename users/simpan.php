<?php
session_start();
include "../config/database.php";

// Cegah akses jika bukan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

$nama = $_POST['nama'];
$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

// Cek apakah username sudah ada
$cek = mysqli_query($conn, "SELECT username FROM users WHERE username = '$username'");
if (mysqli_num_rows($cek) > 0) {
    $_SESSION['error'] = "Username sudah terdaftar, gunakan yang lain!";
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $password, $role);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Karyawan baru berhasil ditambahkan!";
} else {
    $_SESSION['error'] = "Gagal menambahkan karyawan!";
}

header("Location: index.php");
exit;
?>
