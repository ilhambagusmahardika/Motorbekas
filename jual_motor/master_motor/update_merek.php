<?php
session_start();
include "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nama_merek = trim($_POST['nama_merek']);

    if ($id && $nama_merek) {
        $stmt = mysqli_prepare($conn, "UPDATE master_merek SET nama_merek = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $nama_merek, $id);
        mysqli_stmt_execute($stmt);
        $_SESSION['success'] = "Merek berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Data tidak valid.";
    }
}
header("Location: index.php");
exit;
