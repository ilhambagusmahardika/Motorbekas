<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Akses Ditolak!");

$merek_id = $_POST['merek_id'];
$model = $_POST['nama_model'];

$stmt = mysqli_prepare($conn, "INSERT INTO master_model (merek_id, nama_model) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "is", $merek_id, $model);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Model $model berhasil ditambahkan!";
} else {
    $_SESSION['error'] = "Gagal menambah model.";
}
header("Location: index.php");
exit;
?>
