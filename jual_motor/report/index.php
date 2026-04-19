<?php
$pageTitle = "Laporan Keuangan - JualMotor";
include "../config/database.php";
include "../layout/header.php";

// Cegah akses langsung via URL jika bukan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='glass-card fade-in text-center' style='padding:50px; margin:20px'><h2>Akses Ditolak! 🔒</h2><p>Hanya Admin yang dapat mengakses laporan keuangan toko.</p><a href='../beranda/index.php' class='btn btn-primary'>Kembali ke Beranda</a></div>");
}

include "../layout/sidebar.php";

// === 1. Kumpulkan Data Untuk Buku Kas (Cash Flow) ===
$arusKas = [];
$saldo = 0;
$totalPemasukan = 0;
$totalPengeluaran = 0;

// A. Pengeluaran: Pembelian Motor
$qBeli = mysqli_query($conn, "SELECT m.tgl_pembelian as tgl, m.merek, m.model, m.plat_nomor, m.harga_beli as keluar FROM motor m");
while($r = mysqli_fetch_assoc($qBeli)) {
    $arusKas[] = [
        'tanggal' => strtotime($r['tgl']),
        'jenis' => 'Pengeluaran',
        'kategori' => 'Pembelian Motor',
        'keterangan' => "Beli motor {$r['merek']} {$r['model']} ({$r['plat_nomor']})",
        'masuk' => 0,
        'keluar' => $r['keluar']
    ];
}

// B. Pengeluaran: Biaya Perbaikan & Pajak
$qPerbaikan = mysqli_query($conn, "SELECT p.tgl_perbaikan as tgl, p.keterangan, p.biaya as keluar, m.merek, m.model, m.plat_nomor FROM perbaikan p JOIN motor m ON p.motor_id = m.motor_id");
while($r = mysqli_fetch_assoc($qPerbaikan)) {
    $arusKas[] = [
        'tanggal' => strtotime($r['tgl']),
        'jenis' => 'Pengeluaran',
        'kategori' => 'Biaya Tambahan',
        'keterangan' => "{$r['keterangan']} - {$r['merek']} {$r['model']} ({$r['plat_nomor']})",
        'masuk' => 0,
        'keluar' => $r['keluar']
    ];
}

// C. Pemasukan: Penjualan Motor
$qJual = mysqli_query($conn, "SELECT p.tgl_jual as tgl, p.harga_jual as masuk, m.merek, m.model, m.plat_nomor FROM penjualan p JOIN motor m ON p.motor_id = m.motor_id");
while($r = mysqli_fetch_assoc($qJual)) {
    $arusKas[] = [
        'tanggal' => strtotime($r['tgl']),
        'jenis' => 'Pemasukan',
        'kategori' => 'Penjualan Motor',
        'keterangan' => "Jual motor {$r['merek']} {$r['model']} ({$r['plat_nomor']})",
        'masuk' => $r['masuk'],
        'keluar' => 0
    ];
}

// Urutkan array berdasarkan tanggal (terlama ke terbaru) untuk menghitung Saldo berurutan
usort($arusKas, function($a, $b) {
    return $a['tanggal'] <=> $b['tanggal'];
});

// Hitung saldo
foreach($arusKas as $k => $v) {
    $saldo += ($v['masuk'] - $v['keluar']);
    $arusKas[$k]['saldo'] = $saldo;
    $totalPemasukan += $v['masuk'];
    $totalPengeluaran += $v['keluar'];
}

// Balikkan array dari terbaru ke terlama untuk tampilan tabel
$arusKas = array_reverse($arusKas);

// === 2. Data Aset Motor / Inventori Aktif ===
$modalBelumKembali = mysqli_query($conn, "
  SELECT m.*, 
    COALESCE((SELECT SUM(biaya) FROM perbaikan WHERE motor_id = m.motor_id), 0) as total_perbaikan
  FROM motor m 
  WHERE m.status_motor != 'Terjual' 
  ORDER BY m.created_at DESC
");
$totalAsetModal = 0;

?>

<div class="page-header">
  <h2><i class="ph ph-wallet" style="font-size:24px; vertical-align:middle; margin-right:8px;"></i> Laporan Keuangan (Buku Kas)</h2>
  <p>Transparansi arus kas uang masuk dan uang keluar toko</p>
</div>

<!-- Summary Cards Arus Kas -->
<div class="summary-cards" style="grid-template-columns: repeat(3, 1fr);">
  <div class="card-summary green">
    <div class="card-icon">💵</div>
    <div class="card-value">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></div>
    <div class="card-label">Total Uang Masuk</div>
  </div>
  <div class="card-summary red">
    <div class="card-icon">💸</div>
    <div class="card-value">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></div>
    <div class="card-label">Total Uang Keluar (Beli & Perbaikan)</div>
  </div>
  <div class="card-summary <?= $saldo >= 0 ? 'blue' : 'orange' ?>">
    <div class="card-icon">💰</div>
    <div class="card-value">Rp <?= number_format($saldo, 0, ',', '.') ?></div>
    <div class="card-label">Saldo Kas Saat Ini</div>
  </div>
</div>

<div class="glass-card" style="margin-bottom:30px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h3>📖 Catatan Arus Kas (Buku Kas)</h3>
        <button class="btn btn-outline" onclick="window.print()">🖨️ Cetak Laporan</button>
    </div>
    <div class="table-wrapper text-sm">
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background:var(--bg-hover);">
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th style="text-align:right">Uang Masuk</th>
                    <th style="text-align:right">Uang Keluar</th>
                    <th style="text-align:right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($arusKas)): ?>
                    <tr><td colspan="6" class="empty-state text-center" style="padding:30px">Belum ada transaksi keuangan.</td></tr>
                <?php else: ?>
                    <?php foreach($arusKas as $kas): ?>
                        <tr>
                            <td width="100"><?= date('d/m/Y', $kas['tanggal']) ?></td>
                            <td width="130">
                                <?php if($kas['jenis'] == 'Pemasukan'): ?>
                                    <span class="badge badge-ready"><?= $kas['kategori'] ?></span>
                                <?php else: ?>
                                    <span class="badge badge-sold"><?= $kas['kategori'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($kas['keterangan']) ?></td>
                            <td style="text-align:right; color:var(--accent-green)">
                                <?= $kas['masuk'] > 0 ? '+ Rp ' . number_format($kas['masuk'],0,',','.') : '-' ?>
                            </td>
                            <td style="text-align:right; color:var(--accent-red)">
                                <?= $kas['keluar'] > 0 ? '- Rp ' . number_format($kas['keluar'],0,',','.') : '-' ?>
                            </td>
                            <td style="text-align:right; font-weight:bold; color:var(--text-primary)">
                                Rp <?= number_format($kas['saldo'],0,',','.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="page-header mt-5">
  <h2><i class="ph ph-package" style="font-size:24px; vertical-align:middle; margin-right:8px;"></i> Laporan Aset / Nilai Inventori</h2>
  <p>Daftar modal uang yang masih mengendap dalam bentuk motor (belum terjual)</p>
</div>

<div class="glass-card">
  <div class="table-wrapper">
    <table id="asetTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Motor</th>
          <th>Plat Nomor</th>
          <th>Status</th>
          <th style="text-align:right">Harga Beli</th>
          <th style="text-align:right">Biaya Perbaikan</th>
          <th style="text-align:right">Total Modal Bersih</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($modalBelumKembali) > 0): $no=1; ?>
          <?php while ($r = mysqli_fetch_assoc($modalBelumKembali)):
            $modal = $r['harga_beli'] + $r['total_perbaikan'];
            $totalAsetModal += $modal;
          ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><strong><?= htmlspecialchars($r['merek'].' '.$r['model']) ?></strong></td>
            <td><?= htmlspecialchars($r['plat_nomor']) ?></td>
            <td>
              <?php
                if ($r['status_motor'] === 'Dibeli') echo '<span class="badge badge-new">Di Inventori</span>';
                else echo '<span class="badge badge-ready">Siap Jual</span>';
              ?>
            </td>
            <td style="text-align:right">Rp <?= number_format($r['harga_beli'],0,',','.') ?></td>
            <td style="text-align:right" class="text-orange">Rp <?= number_format($r['total_perbaikan'],0,',','.') ?></td>
            <td style="text-align:right" class="fw-bold">Rp <?= number_format($modal,0,',','.') ?></td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7" class="empty-state text-center" style="padding:30px">Semua motor sudah terjual, tidak ada aset mengendap 🎉</td></tr>
        <?php endif; ?>
      </tbody>
      <?php if(mysqli_num_rows($modalBelumKembali) > 0): ?>
      <tfoot>
        <tr style="background:var(--bg-hover);">
            <td colspan="6" style="text-align:right; font-weight:bold; font-size:16px; padding:15px;">TOTAL UANG MENGENDAP DI MOTOR:</td>
            <td style="text-align:right; font-weight:bold; font-size:16px; color:var(--accent-blue);">Rp <?= number_format($totalAsetModal,0,',','.') ?></td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>

<?php include "../layout/footer.php"; ?>