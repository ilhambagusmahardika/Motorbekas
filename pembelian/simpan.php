<?php
session_start();
if (!isset($_SESSION['login'])) {
  header("Location: ../auth/login.php"); exit;
}
include "../config/database.php";

$merek = $_POST['merek'] === 'lainnya' ? ($_POST['merek_lain'] ?? 'Lainnya') : $_POST['merek'];
$model = $_POST['merek'] === 'lainnya' ? ($_POST['model_lain'] ?? $_POST['model']) : $_POST['model'];

// Upload foto
$path = '';
if (isset($_FILES['foto_motor']) && $_FILES['foto_motor']['error'] === 0) {
  $foto = $_FILES['foto_motor']['name'];
  $tmp  = $_FILES['foto_motor']['tmp_name'];
  $ext  = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
  $allowed = ['jpg','jpeg','png','gif','webp'];
  
  if (in_array($ext, $allowed)) {
    // Create directory if not exists
    if (!is_dir("../assets/img")) mkdir("../assets/img", 0755, true);
    $path = "../assets/img/" . time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $foto);
    move_uploaded_file($tmp, $path);
  }
}

// Prepared statement to prevent SQL injection
$stmt = mysqli_prepare($conn, 
  "INSERT INTO motor (tgl_pembelian, nama_penjual, merek, model, cc, warna, tahun_pembuatan, 
   nama_bpkb, plat_nomor, asal_wilayah, asal_kota, samsat_terdaftar, no_rangka, no_mesin, pajak_berlaku, 
   foto_motor, harga_beli, status_motor) 
   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Dibeli')"
);

$cc = $_POST['cc'] ?? '';
$samsat_terdaftar = $_POST['samsat_terdaftar'] ?? '';

mysqli_stmt_bind_param($stmt, "ssssssssssssssssi",
  $_POST['tgl_pembelian'],
  $_POST['nama_penjual'],
  $merek,
  $model,
  $cc,
  $_POST['warna'],
  $_POST['tahun_pembuatan'],
  $_POST['nama_bpkb'],
  $_POST['plat_nomor'],
  $_POST['asal_wilayah'],
  $_POST['asal_kota'],
  $samsat_terdaftar,
  $_POST['no_rangka'],
  $_POST['no_mesin'],
  $_POST['pajak_berlaku'],
  $path,
  $_POST['harga_beli']
);

mysqli_stmt_execute($stmt);
$_SESSION['success'] = "Data motor berhasil disimpan!";
header("Location: index.php");
exit;