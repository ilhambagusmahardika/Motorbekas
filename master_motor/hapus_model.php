<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') die("Akses Ditolak!");

$id = $_GET['id'];
$stmt = mysqli_prepare($conn, "DELETE FROM master_model WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Model berhasil dihapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus model.";
}
header("Location: index.php");
exit;
?>
