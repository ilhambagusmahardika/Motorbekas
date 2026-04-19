<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['login'])) {
  header("Location: ../auth/login.php"); exit;
}
// Determine active page for sidebar highlight
$currentPage = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? 'Jual Motor' ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="../assets/js/app.js" defer></script>
</head>
<body>
<div class="app-layout">