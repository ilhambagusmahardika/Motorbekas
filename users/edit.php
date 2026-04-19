<?php
$pageTitle = "Edit Pengguna - JualMotor";
include "../config/database.php";
include "../layout/header.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='glass-card fade-in text-center' style='padding:50px'><h2>Akses Ditolak!</h2></div>");
}

include "../layout/sidebar.php";

$id = $_GET['id'] ?? 0;
// Jangan izinkan edit superadmin sembarangan jika bukan dirinya sendiri (asumsikan user_id 1 = superadmin)
if ($id == 1 && $_SESSION['user_id'] != 1) {
    echo "<div class='glass-card fade-in text-center'>Akses Ditolak ke Akun Utama!</div>";
    include "../layout/footer.php";
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$user = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$user) {
    echo "<div class='glass-card'>Karyawan tidak ditemukan.</div>";
    include "../layout/footer.php";
    exit;
}
?>

<div class="page-header">
  <h2>✏️ Edit Karyawan</h2>
  <p>Perbarui data atau password hak akses pengguna</p>
</div>

<div class="glass-card fade-in" style="max-width: 500px;">
  <form action="update.php" method="POST">
    <input type="hidden" name="id" value="<?= $user['user_id'] ?>">
    <div class="form-group">
      <label>Nama Lengkap</label>
      <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
    </div>
    <div class="form-group">
      <label>Username (Login)</label>
      <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required minlength="3">
    </div>
    <div class="form-group">
      <label>Peran (Role Akses)</label>
      <select name="role" required <?= ($user['user_id'] == 1) ? 'disabled' : '' ?>>
        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin (Akses Penuh)</option>
        <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff (Kasir / Operasional)</option>
      </select>
      <?php if ($user['user_id'] == 1): ?>
        <input type="hidden" name="role" value="admin">
        <small class="text-orange">Peran akun utama tidak bisa diubah.</small>
      <?php endif; ?>
    </div>
    <div class="form-group">
      <label>Password Baru</label>
      <input type="password" name="password" placeholder="Isi hanya jika ingin mengganti password lama">
      <small class="text-muted">Kosongkan jika tidak ingin merubah password saat ini.</small>
    </div>
    
    <div class="mt-3" style="display:flex;gap:12px">
      <a href="index.php" class="btn btn-outline">Batal</a>
      <button type="submit" class="btn btn-primary">💾 Simpan Karyawan</button>
    </div>
  </form>
</div>

<?php include "../layout/footer.php"; ?>
