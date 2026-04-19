<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: index.php");
  exit;
}
include "../config/database.php";

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  
  // Ambil data penjualan untuk tahu motor mana yang transaksinya dibatalkan
  $penjualan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT motor_id FROM penjualan WHERE penjualan_id = $id"));
  
  if ($penjualan) {
    $motor_id = $penjualan['motor_id'];
    
    // 1. Ubah status motor kembali menjadi 'Siap Jual' agar masuk inventori/stok
    mysqli_query($conn, "UPDATE motor SET status_motor = 'Siap Jual' WHERE motor_id = $motor_id");
    
    // 2. Hapus data penjualan dari riwayat transaksi
    mysqli_query($conn, "DELETE FROM penjualan WHERE penjualan_id = $id");
    
    $_SESSION['success'] = "Transaksi berhasil dibatalkan. Motor otomatis dikembalikan ke Stok Siap Jual.";
  }
}

header("Location: index.php");
exit;
