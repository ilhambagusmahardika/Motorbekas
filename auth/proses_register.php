<?php
session_start();
$_SESSION['error'] = "Akses ditolak. Fitur register publik dinonaktifkan.";
header("Location: login.php");
exit;
?>
