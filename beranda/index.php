<?php
$pageTitle = "Beranda - JualMotor";
include "../config/database.php";
include "../layout/header.php";
include "../layout/sidebar.php";

// Query summary data
$totalDibeli = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM motor WHERE MONTH(tgl_pembelian) = MONTH(CURDATE()) AND YEAR(tgl_pembelian) = YEAR(CURDATE())"))['c'];
$totalInventori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM motor WHERE status_motor='Dibeli'"))['c'];
$totalStok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM motor WHERE status_motor='Siap Jual'"))['c'];
$totalTerjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM penjualan WHERE MONTH(tgl_jual) = MONTH(CURDATE()) AND YEAR(tgl_jual) = YEAR(CURDATE())"))['c'];

// Profit calculation
$profitQuery = mysqli_query($conn, "
  SELECT 
    COALESCE(SUM(p.harga_jual), 0) as total_jual,
    COALESCE(SUM(m.harga_beli), 0) as total_beli,
    COALESCE((SELECT SUM(biaya) FROM perbaikan WHERE motor_id IN (SELECT motor_id FROM penjualan WHERE MONTH(tgl_jual) = MONTH(CURDATE()) AND YEAR(tgl_jual) = YEAR(CURDATE()))), 0) as total_perbaikan
  FROM penjualan p 
  JOIN motor m ON p.motor_id = m.motor_id
  WHERE MONTH(p.tgl_jual) = MONTH(CURDATE()) AND YEAR(p.tgl_jual) = YEAR(CURDATE())
");
$profit = mysqli_fetch_assoc($profitQuery);
$totalProfit = ($profit['total_jual'] ?? 0) - ($profit['total_beli'] ?? 0) - ($profit['total_perbaikan'] ?? 0);

// Pajak reminder - motor yang pajaknya < 30 hari lagi
$pajakHabis = mysqli_query($conn, "SELECT motor_id, merek, model, plat_nomor, pajak_berlaku FROM motor WHERE status_motor != 'Terjual' AND pajak_berlaku <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) ORDER BY pajak_berlaku ASC");
$pajakCount = mysqli_num_rows($pajakHabis);

// Recent motors
$recentMotors = mysqli_query($conn, "SELECT * FROM motor ORDER BY created_at DESC LIMIT 5");

// Monthly profit data for chart
$monthlyData = mysqli_query($conn, "
  SELECT 
    DATE_FORMAT(p.tgl_jual, '%Y-%m') as bulan,
    SUM(p.harga_jual) as jual,
    SUM(m.harga_beli) as beli,
    COALESCE(SUM((SELECT COALESCE(SUM(biaya),0) FROM perbaikan WHERE motor_id = m.motor_id)), 0) as perbaikan
  FROM penjualan p
  JOIN motor m ON p.motor_id = m.motor_id
  GROUP BY DATE_FORMAT(p.tgl_jual, '%Y-%m')
  ORDER BY bulan DESC
  LIMIT 6
");
$chartLabels = [];
$chartProfit = [];
while ($md = mysqli_fetch_assoc($monthlyData)) {
  $chartLabels[] = date('M Y', strtotime($md['bulan'].'-01'));
  $chartProfit[] = $md['jual'] - $md['beli'] - $md['perbaikan'];
}
$chartLabels = array_reverse($chartLabels);
$chartProfit = array_reverse($chartProfit);
?>

<div class="topbar">
  <div class="greeting">
    <h3>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?></h3>
    <span><?= strftime('%A, %d %B %Y') ?: date('l, d F Y') ?></span>
  </div>
  <div class="user-info">
    <div class="avatar"><?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?></div>
    <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
  </div>
</div>

<?php if ($pajakCount > 0): ?>
<div class="pajak-warning">
  ⚠️ <strong><?= $pajakCount ?> motor</strong> pajak akan/sudah habis dalam 30 hari:
  <?php mysqli_data_seek($pajakHabis, 0); while($pj = mysqli_fetch_assoc($pajakHabis)): ?>
    <span style="display:inline-block;background:#fff;border:1px solid #fde68a;padding:2px 8px;border-radius:4px;margin:3px 2px;font-size:12px">
      <strong><?= htmlspecialchars($pj['plat_nomor']) ?></strong> <?= htmlspecialchars($pj['merek'].' '.$pj['model']) ?> — <?= date('d/m/Y', strtotime($pj['pajak_berlaku'])) ?>
      <button type="button" onclick="bukaModalPajak(<?= $pj['motor_id'] ?>, '<?= htmlspecialchars($pj['plat_nomor']) ?>')" style="margin-left:5px;background:var(--accent-primary);color:#fff;border:none;padding:2px 6px;border-radius:3px;cursor:pointer;font-size:11px;"><i class="ph ph-wallet"></i> Bayar</button>
    </span>
  <?php endwhile; ?>
</div>
<?php endif; ?>

<div class="page-header">
  <h2>Dashboard</h2>
  <p>Ringkasan data jual beli motor</p>
</div>

<div class="summary-cards">
  <div class="card-summary blue">
    <div class="card-icon">🛒</div>
    <div class="card-value"><?= $totalDibeli ?></div>
    <div class="card-label">Dibeli Bulan Ini</div>
  </div>
  <div class="card-summary orange">
    <div class="card-icon">🔧</div>
    <div class="card-value"><?= $totalInventori ?></div>
    <div class="card-label">Di Inventori</div>
  </div>
  <div class="card-summary cyan">
    <div class="card-icon">📦</div>
    <div class="card-value"><?= $totalStok ?></div>
    <div class="card-label">Stok Siap Jual</div>
  </div>
  <div class="card-summary green">
    <div class="card-icon">✅</div>
    <div class="card-value"><?= $totalTerjual ?></div>
    <div class="card-label">Terjual Bulan Ini</div>
  </div>
  <div class="card-summary <?= $totalProfit >= 0 ? 'green' : 'red' ?>">
    <div class="card-icon">💰</div>
    <div class="card-value">Rp <?= number_format($totalProfit, 0, ',', '.') ?></div>
    <div class="card-label">Profit Bulan Ini</div>
  </div>
</div>

<?php if (!empty($chartLabels)): ?>
<div class="glass-card">
  <h3>Grafik Profit Bulanan</h3>
  <canvas id="profitChart" height="80"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
  const ctx = document.getElementById('profitChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($chartLabels) ?>,
      datasets: [{
        label: 'Profit (Rp)',
        data: <?= json_encode($chartProfit) ?>,
        backgroundColor: <?= json_encode(array_map(function($v){ return $v >= 0 ? 'rgba(59,130,246,0.7)' : 'rgba(239,68,68,0.7)'; }, $chartProfit)) ?>,
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              return 'Rp ' + ctx.raw.toLocaleString('id-ID');
            }
          }
        }
      },
      scales: {
        y: {
          ticks: {
            callback: function(val) { return 'Rp ' + (val/1000000).toFixed(1) + 'jt'; }
          },
          grid: { color: '#f1f5f9' }
        },
        x: { grid: { display: false } }
      }
    }
  });
</script>
<?php endif; ?>

<div class="glass-card">
  <h3>Motor Terbaru</h3>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Merek</th>
          <th>Model</th>
          <th>Plat Nomor</th>
          <th>Harga Beli</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($recentMotors) > 0): $no = 1; ?>
          <?php while ($row = mysqli_fetch_assoc($recentMotors)): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= date('d/m/Y', strtotime($row['tgl_pembelian'])) ?></td>
            <td><?= htmlspecialchars($row['merek']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><strong><?= htmlspecialchars($row['plat_nomor']) ?></strong></td>
            <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
            <td>
              <?php
                if ($row['status_motor'] === 'Dibeli') echo '<span class="badge badge-new">Dibeli</span>';
                elseif ($row['status_motor'] === 'Siap Jual') echo '<span class="badge badge-ready">Siap Jual</span>';
                else echo '<span class="badge badge-sold">Terjual</span>';
              ?>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center text-muted" style="padding:40px">Belum ada data motor</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<!-- Modal Bayar Pajak -->
<div class="modal-overlay" id="modalPajak">
  <div class="modal" style="max-width:400px">
    <button class="modal-close" onclick="document.getElementById('modalPajak').classList.remove('active')">&times;</button>
    <h3><i class="ph ph-receipt"></i> Perpanjang Pajak</h3>
    <p style="font-size:13px;color:var(--text-secondary);margin-bottom:15px">Bayar pajak untuk plat <strong><span id="pajakPlatDisplay"></span></strong>. Biaya akan ditambahkan ke total modal motor tersebut.</p>
    <form action="proses_pajak.php" method="POST">
      <input type="hidden" name="motor_id" id="pajakMotorId">
      <div class="form-group">
        <label>Pajak Berlaku Baru (S/d Tanggal)</label>
        <input type="date" name="pajak_baru" required>
      </div>
      <div class="form-group">
        <label>Biaya Pajak (Rp)</label>
        <input type="text" name="biaya_pajak" class="rupiah-input" placeholder="Contoh: 250.000" required autocomplete="off">
      </div>
      <div style="margin-top:20px">
        <button type="submit" class="btn btn-primary" style="width:100%"><i class="ph ph-check-circle"></i> Simpan & Bayar</button>
      </div>
    </form>
  </div>
</div>

<script>
function bukaModalPajak(id, plat) {
  document.getElementById('pajakMotorId').value = id;
  document.getElementById('pajakPlatDisplay').textContent = plat;
  document.getElementById('modalPajak').classList.add('active');
}
</script>

<?php include "../layout/footer.php"; ?>