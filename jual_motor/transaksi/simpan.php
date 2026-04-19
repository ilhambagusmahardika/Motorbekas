<?php
session_start();
if (!isset($_SESSION['login'])) {
  header("Location: ../auth/login.php"); exit;
}
include "../config/database.php";

$motor_id     = intval($_POST['motor_id'] ?? 0);
$tgl_jual     = $_POST['tgl_jual'] ?? date('Y-m-d');
$nama_pembeli = $_POST['nama_pembeli'] ?? '';
$harga_jual   = intval(str_replace('.', '', $_POST['harga_jual'] ?? 0));
$dijual_melalui = $_POST['dijual_melalui'] ?? '';

// Insert penjualan
$stmt = mysqli_prepare($conn, "INSERT INTO penjualan (motor_id, tgl_jual, nama_pembeli, harga_jual, dijual_melalui) VALUES (?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "issis", $motor_id, $tgl_jual, $nama_pembeli, $harga_jual, $dijual_melalui);
mysqli_stmt_execute($stmt);

// Update motor status to Terjual
$stmt2 = mysqli_prepare($conn, "UPDATE motor SET status_motor = 'Terjual' WHERE motor_id = ?");
mysqli_stmt_bind_param($stmt2, "i", $motor_id);
mysqli_stmt_execute($stmt2);

$_SESSION['success'] = "Transaksi penjualan berhasil disimpan!";
header("Location: index.php");
exit;