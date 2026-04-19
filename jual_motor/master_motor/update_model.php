<?php
session_start();
include "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $merek_id = intval($_POST['merek_id']);
    $nama_model = trim($_POST['nama_model']);

    if ($id && $merek_id && $nama_model) {
        $stmt = mysqli_prepare($conn, "UPDATE master_model SET merek_id = ?, nama_model = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "isi", $merek_id, $nama_model, $id);
        mysqli_stmt_execute($stmt);
        $_SESSION['success'] = "Model berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Data tidak valid.";
    }
}
header("Location: index.php");
exit;
