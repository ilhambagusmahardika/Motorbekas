<?php
session_start();
include "../config/database.php";

// Cegah akses jika bukan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

$id = $_GET['id'];
$stmt = mysqli_prepare($conn, "DELETE FROM master_plat WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Kode Plat terhapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus plat!";
}

header("Location: index.php");
exit;
?>
