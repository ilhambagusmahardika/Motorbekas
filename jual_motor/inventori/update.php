<?php
session_start();
if (!isset($_SESSION['login'])) {
  header("Location: ../auth/login.php"); exit;
}
include "../config/database.php";

// Tambah perbaikan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'tambah_perbaikan') {
  $motor_id  = intval($_POST['motor_id']);
  $biaya     = intval(str_replace('.', '', $_POST['biaya'] ?? 0));
  $keterangan = $_POST['keterangan'] ?? '';
  $tgl = $_POST['tgl_perbaikan'] ?? date('Y-m-d');
  
  $stmt = mysqli_prepare($conn, "INSERT INTO perbaikan (motor_id, biaya, keterangan, tgl_perbaikan) VALUES (?, ?, ?, ?)");
  mysqli_stmt_bind_param($stmt, "iiss", $motor_id, $biaya, $keterangan, $tgl);
  mysqli_stmt_execute($stmt);
  
  $_SESSION['success'] = "Biaya perbaikan berhasil ditambahkan!";
  header("Location: index.php?detail=$motor_id");
  exit;
}

// Edit perbaikan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_perbaikan') {
  if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit;
  }
  $perbaikan_id = intval($_POST['perbaikan_id']);
  $motor_id     = intval($_POST['motor_id']);
  $biaya        = intval(str_replace('.', '', $_POST['biaya'] ?? 0));
  $keterangan   = $_POST['keterangan'] ?? '';
  $tgl          = $_POST['tgl_perbaikan'] ?? date('Y-m-d');

  $stmt = mysqli_prepare($conn, "UPDATE perbaikan SET biaya=?, keterangan=?, tgl_perbaikan=? WHERE perbaikan_id=?");
  mysqli_stmt_bind_param($stmt, "issi", $biaya, $keterangan, $tgl, $perbaikan_id);
  mysqli_stmt_execute($stmt);

  $_SESSION['success'] = "Data perbaikan berhasil diperbarui!";
  header("Location: index.php?detail=$motor_id");
  exit;
}

// Siap Jual - ubah status motor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'siap_jual') {
  $motor_id = intval($_POST['motor_id']);
  $stmt = mysqli_prepare($conn, "UPDATE motor SET status_motor = 'Siap Jual' WHERE motor_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $motor_id);
  mysqli_stmt_execute($stmt);
  
  $_SESSION['success'] = "Motor berhasil ditandai Siap Jual!";
  header("Location: index.php");
  exit;
}

// Hapus perbaikan
if (isset($_GET['hapus_perbaikan'])) {
  $pid = intval($_GET['hapus_perbaikan']);
  $mid = intval($_GET['motor_id'] ?? 0);
  $stmt = mysqli_prepare($conn, "DELETE FROM perbaikan WHERE perbaikan_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $pid);
  mysqli_stmt_execute($stmt);
  
  $_SESSION['success'] = "Data perbaikan berhasil dihapus!";
  header("Location: index.php?detail=$mid");
  exit;
}

header("Location: index.php");
exit;