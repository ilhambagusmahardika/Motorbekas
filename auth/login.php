<?php
session_start();
if (isset($_SESSION['login'])) {
  header("Location: ../beranda/index.php"); exit;
}
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - JualMotor</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
  <div class="login-card fade-in">
    <div class="logo">
      <h1>🏍️ JualMotor</h1>
      <p>Sistem Jual Beli Motor Bekas</p>
    </div>

    <?php if ($error): ?>
      <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">
      <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Masukkan username" required autofocus>
      </div>
      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn-login">Masuk</button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--text-secondary);">
      Belum punya akun? <a href="https://wa.me/6282322491723" target="_blank" style="color:var(--accent-primary);font-weight:600;text-decoration:none;">Hubungi Administrator</a>
    </p>
  </div>
</div>
</body>
</html>