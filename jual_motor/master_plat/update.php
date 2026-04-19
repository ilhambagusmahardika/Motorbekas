<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Akses Ditolak!");

$id = trim($_POST['id']);
$prefix = strtoupper(trim($_POST['prefix']));
$suffix = strtoupper(trim($_POST['suffix'] ?? ''));
$wilayah = $_POST['wilayah'];
$kota = $_POST['kota'];

$stmt = mysqli_prepare($conn, "UPDATE master_plat SET prefix=?, suffix=?, wilayah=?, kota=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "ssssi", $prefix, $suffix, $wilayah, $kota, $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Data Plat Nomor berhasil diperbarui!";
} else {
    $_SESSION['error'] = "Gagal memperbarui plat.";
}

header("Location: index.php");
exit;
?>
