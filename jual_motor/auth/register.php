<?php
session_start();
// Registrasi publik dinonaktifkan untuk keamaman aplikasi kasir.
// Karyawan baru HANYA BISA ditambahkan oleh Admin lewat halaman Manajemen Karyawan.
$_SESSION['error'] = "Pendaftaran via halaman depan ditutup. Silakan hubungi Admin toko.";
header("Location: login.php");
exit;
?>