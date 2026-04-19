<?php
session_start();
include "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motor_id = intval($_POST['motor_id']);
    $pajak_baru = $_POST['pajak_baru'];
    $biaya_pajak_raw = $_POST['biaya_pajak'];
    
    // Remove non-numeric characters for the cost
    $biaya_pajak = preg_replace('/\D/', '', $biaya_pajak_raw);

    if ($motor_id && $pajak_baru && $biaya_pajak !== '') {
        // 1. Update tgl pajak di tabel motor
        $query1 = "UPDATE motor SET pajak_berlaku = '$pajak_baru' WHERE motor_id = $motor_id";
        mysqli_query($conn, $query1);

        // 2. Tambah catatan ke tabel perbaikan agar masuk modal
        $tgl_perbaikan = date('Y-m-d');
        $query2 = "INSERT INTO perbaikan (motor_id, biaya, keterangan, tgl_perbaikan) VALUES ($motor_id, $biaya_pajak, 'Perpanjang Pajak Tahunan', '$tgl_perbaikan')";
        mysqli_query($conn, $query2);

        $_SESSION['success'] = "Pajak berhasil diperpanjang, dan biaya Rp " . number_format($biaya_pajak, 0, ',', '.') . " telah ditambahkan ke inventori motor.";
    } else {
        $_SESSION['error'] = "Gagal memperpanjang pajak. Data tidak lengkap.";
    }

    header("Location: index.php");
    exit;
}
?>
