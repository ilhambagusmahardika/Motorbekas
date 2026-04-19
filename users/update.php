<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Akses Ditolak!");

$id = $_POST['id'];
$nama = trim($_POST['nama']);
$username = trim($_POST['username']);
$role = $_POST['role'];
$password = $_POST['password'] ?? '';

// Proteksi superadmin
if ($id == 1 && $role !== 'admin') {
    $role = 'admin';
}

if (!empty($password)) {
    // Jika ganti password
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "UPDATE users SET nama=?, username=?, role=?, password=? WHERE user_id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $nama, $username, $role, $hashed, $id);
} else {
    // Jika password tidak diganti
    $stmt = mysqli_prepare($conn, "UPDATE users SET nama=?, username=?, role=? WHERE user_id=?");
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $username, $role, $id);
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Data Karyawan berhasil diperbarui!";
} else {
    $_SESSION['error'] = "Gagal memperbarui data!";
}

header("Location: index.php");
exit;
?>
