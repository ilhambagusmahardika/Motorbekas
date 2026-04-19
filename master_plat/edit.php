<?php
$pageTitle = "Edit Plat Nomor - JualMotor";
include "../config/database.php";
include "../layout/header.php";

// Cegah akses jika bukan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='glass-card fade-in text-center' style='padding:50px'><h2>Akses Ditolak!</h2><p>Hanya Admin yang dapat mengakses halaman ini.</p></div>");
}

include "../layout/sidebar.php";

$id = $_GET['id'] ?? 0;
$stmt = mysqli_prepare($conn, "SELECT * FROM master_plat WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$plat = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$plat) {
    echo "<div class='glass-card'>Data Plat tidak ditemukan.</div>";
    include "../layout/footer.php";
    exit;
}
?>

<div class="page-header">
  <h2>✏️ Edit Plat Nomor</h2>
  <p>Perbarui data kode pelat dan wilayah</p>
</div>

<div class="glass-card fade-in" style="max-width: 600px;">
  <form action="update.php" method="POST">
    <input type="hidden" name="id" value="<?= $plat['id'] ?>">
    <div class="form-grid">
      <div class="form-group">
        <label>Prefix (Huruf Depan)</label>
        <input type="text" name="prefix" value="<?= htmlspecialchars($plat['prefix']) ?>" required style="text-transform:uppercase">
      </div>
      <div class="form-group">
        <label>Suffix (Huruf Belakang)</label>
        <input type="text" name="suffix" value="<?= htmlspecialchars($plat['suffix'] ?? '') ?>" style="text-transform:uppercase" placeholder="Kosongkan jika semua suffix">
      </div>
      <div class="form-group full-width">
        <label>Provinsi / Wilayah</label>
        <input type="text" name="wilayah" value="<?= htmlspecialchars($plat['wilayah']) ?>" required>
      </div>
      <div class="form-group full-width">
        <label>Kota / Kabupaten (Nama SAMSAT)</label>
        <input type="text" name="kota" value="<?= htmlspecialchars($plat['kota']) ?>" required>
      </div>
    </div>
    <div class="mt-3" style="display:flex;gap:12px">
      <a href="index.php" class="btn btn-outline">Batal</a>
      <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
    </div>
  </form>
</div>

<?php include "../layout/footer.php"; ?>
