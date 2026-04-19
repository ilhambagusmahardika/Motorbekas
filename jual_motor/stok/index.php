<?php
$pageTitle = "Stok Unit - JualMotor";
include "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

// Get motors ready for sale
$motors = mysqli_query($conn, "SELECT m.*, 
  COALESCE((SELECT SUM(biaya) FROM perbaikan WHERE motor_id = m.motor_id), 0) as total_perbaikan
  FROM motor m WHERE m.status_motor = 'Siap Jual' ORDER BY m.created_at DESC");
$totalStok = mysqli_num_rows($motors);
?>

<div class="page-header">
  <h2> Stok Unit</h2>
  <p>Daftar motor yang siap dijual</p>
</div>

<div class="summary-cards" style="margin-bottom:24px">
  <div class="card-summary cyan fade-in">
    <div class="card-icon"> </div>
    <div class="card-value"><?= $totalStok ?></div>
    <div class="card-label">Total Motor Siap Jual</div>
  </div>
</div>

<div class="glass-card fade-in">
  <h3> Motor Tersedia</h3>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Foto</th>
          <th>Merek / Model</th>
          <th>Warna</th>
          <th>Tahun</th>
          <th>Plat Nomor</th>
          <th>Asal</th>
          <th>Harga Beli</th>
          <th>Biaya Perbaikan</th>
          <th>Harga Modal</th>
          <th>Pajak s/d</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($totalStok > 0): $no=1; ?>
          <?php while ($row = mysqli_fetch_assoc($motors)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td>
              <?php if ($row['foto_motor'] && file_exists($row['foto_motor'])): ?>
                <img src="../pembelian/<?= $row['foto_motor'] ?>" alt="foto">
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($row['merek']) ?></strong><br><small class="text-muted"><?= htmlspecialchars($row['model']) ?></small></td>
            <td><?= htmlspecialchars($row['warna']) ?></td>
            <td><?= $row['tahun_pembuatan'] ?></td>
            <td><strong><?= htmlspecialchars($row['plat_nomor']) ?></strong></td>
            <td><small><?= htmlspecialchars($row['asal_kota']) ?></small></td>
            <td>Rp <?= number_format($row['harga_beli'],0,',','.') ?></td>
            <td class="text-orange">Rp <?= number_format($row['total_perbaikan'],0,',','.') ?></td>
            <td class="fw-bold text-green">Rp <?= number_format($row['harga_beli']+$row['total_perbaikan'],0,',','.') ?></td>
            <td>
              <?php 
                $pajak = strtotime($row['pajak_berlaku']);
                $now = time();
                $class = $pajak < $now ? 'text-red' : 'text-green';
              ?>
              <span class="<?= $class ?>"><?= date('d/m/Y', $pajak) ?></span>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="11" class="text-center text-muted" style="padding:40px">
            <div class="empty-state">
              <div class="icon">📭</div>
              <p>Belum ada motor siap jual<br><small>Tandai motor "Siap Jual" di menu Inventori</small></p>
            </div>
          </td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include "../layout/footer.php"; ?>