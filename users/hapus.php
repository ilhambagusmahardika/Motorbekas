<?php
session_start();
include "../config/database.php";

// Cegah akses jika bukan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses Ditolak!");
}

$id = $_GET['id'];

// Jangan biarkan admin menghapus dirinya sendiri via URL
if ($id == $_SESSION['user_id']) {
    $_SESSION['error'] = "Anda tidak bisa menghapus akun Anda sendiri!";
    header("Location: index.php");
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success'] = "Karyawan berhasil dihapus!";
} else {
    $_SESSION['error'] = "Gagal menghapus karyawan!";
}

header("Location: index.php");
exit;
?>
