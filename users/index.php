<?php
$pageTitle = "Manajemen Karyawan - JualMotor";
include "../config/database.php";
include "../layout/header.php";

// Cegah akses jika bukan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='glass-card fade-in text-center' style='padding:50px'><h2>Akses Ditolak!</h2><p>Hanya Admin yang dapat mengakses halaman ini.</p><a href='../beranda/index.php' class='btn btn-primary'>Kembali ke Beranda</a></div>");
}

include "../layout/sidebar.php";

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Fetch users
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY user_id ASC");
?>

<div class="page-header">
  <h2><i class="ph ph-users" style="font-size:24px; vertical-align:middle; margin-right:8px;"></i> Manajemen Karyawan</h2>
  <p>Tambah dan kelola akun akses aplikasi (Admin / Staff)</p>
</div>

<?php if ($success): ?>
  <div style="background:rgba(0,214,143,0.1);border:1px solid rgba(0,214,143,0.3);color:var(--accent-green);padding:12px 16px;border-radius:8px;margin-bottom:20px;">
    ✅ <?= htmlspecialchars($success) ?>
  </div>
<?php endif; ?>
<?php if ($error): ?>
  <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);color:#ef4444;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
    ❌ <?= htmlspecialchars($error) ?>
  </div>
<?php endif; ?>

<div class="glass-card fade-in">
  <h3><i class="ph ph-user-plus"></i> Tambah Pengguna Baru</h3>
  <form action="simpan.php" method="POST">
    <div class="form-grid">
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" placeholder="Budi Santoso" required>
      </div>
      <div class="form-group">
        <label>Username (Login)</label>
        <input type="text" name="username" placeholder="budi123" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
      </div>
      <div class="form-group">
        <label>Peran (Role)</label>
        <select name="role" required>
            <option value="staff">Staff (Hanya Input & Jual)</option>
            <option value="admin">Admin (Akses Penuh)</option>
        </select>
      </div>
    </div>
    <div class="mt-2">
      <button type="submit" class="btn btn-primary"><i class="ph ph-floppy-disk"></i> Simpan Akun</button>
    </div>
  </form>
</div>

<div class="glass-card mt-4">
  <h3>Daftar Karyawan Terdaftar</h3>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nama Lengkap</th>
          <th>Username</th>
          <th>Role</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($users) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($users)): ?>
          <tr>
            <td><?= $row['user_id'] ?></td>
            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td>
                <?php if ($row['role'] === 'admin'): ?>
                    <span class="badge badge-ready">Admin</span>
                <?php else: ?>
                    <span class="badge badge-new">Staff</span>
                <?php endif; ?>
            </td>
            <td>
              <a href="edit.php?id=<?= $row['user_id'] ?>" class="btn btn-primary btn-sm"><i class="ph ph-pencil-simple"></i></a>
              <?php if ($_SESSION['user_id'] !== $row['user_id'] && $row['user_id'] != 1): ?>
                <a href="hapus.php?id=<?= $row['user_id'] ?>" class="btn btn-danger btn-sm" onclick="confirmDelete(event, this.href, 'Hapus akun karyawan ini selamanya?')"><i class="ph ph-trash"></i></a>
              <?php elseif ($_SESSION['user_id'] == $row['user_id']) : ?>
                <span class="text-muted" style="font-size:12px;margin-left:5px"><i class="ph ph-user"></i> Anda</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center text-muted" style="padding:40px">Belum ada akun terdaftar</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "../layout/footer.php"; ?>
