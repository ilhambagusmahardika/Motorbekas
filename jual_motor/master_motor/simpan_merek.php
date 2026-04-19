<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Akses Ditolak!");

$merek = $_POST['nama_merek'];

$stmt = mysqli_prepare($conn, "INSERT INTO master_merek (nama_merek) VALUES (?)");
mysqli_stmt_bind_param($stmt, "s", $merek);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Merek $merek berhasil ditambahkan!";
} else {
    $_SESSION['error'] = "Gagal menambah merek.";
}
header("Location: index.php");
exit;
?>
