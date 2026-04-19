<?php
session_start();
include "../config/database.php";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Prepared statement to prevent SQL injection
$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['login'] = true;
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'] ?? 'admin'; // Fallback admin jika kosong
    header("Location: ../beranda/index.php");
} else {
    $_SESSION['error'] = "Username atau password salah!";
    header("Location: login.php");
}
exit;