<?php
session_start();
include "../config/database.php";

// Cegah akses jika bukan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

$prefix = strtoupper(trim($_POST['prefix']));
$suffix = strtoupper(trim($_POST['suffix'] ?? ''));
$wilayah = $_POST['wilayah'];
$kota = $_POST['kota'];

// Cek apakah kombinasi prefix+suffix sudah ada
$cek = mysqli_prepare($conn, "SELECT id FROM master_plat WHERE prefix = ? AND suffix = ?");
mysqli_stmt_bind_param($cek, "ss", $prefix, $suffix);
mysqli_stmt_execute($cek);
if (mysqli_stmt_fetch($cek)) {
    $_SESSION['error'] = "Data plat nomor dengan kombinasi Prefix & Suffix tersebut sudah ada!";
    header("Location: index.php");
    exit;
}
mysqli_stmt_close($cek);

$stmt = mysqli_prepare($conn, "INSERT INTO master_plat (prefix, suffix, wilayah, kota) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssss", $prefix, $suffix, $wilayah, $kota);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Kode Plat berhasil ditambahkan!";
} else {
    $_SESSION['error'] = "Gagal menambahkan kode plat baru!";
}

header("Location: index.php");
exit;
?>
