<?php
session_start();
if (!isset($_SESSION['login'])) {
  header("Location: ../auth/login.php"); exit;
}
include "../config/database.php";

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
  // Delete foto file if exists
  $motor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto_motor FROM motor WHERE motor_id=$id"));
  if ($motor && $motor['foto_motor'] && file_exists($motor['foto_motor'])) {
    unlink($motor['foto_motor']);
  }
  $stmt = mysqli_prepare($conn, "DELETE FROM motor WHERE motor_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $_SESSION['success'] = "Data motor berhasil dihapus!";
}
header("Location: index.php");
exit;
