<?php
session_start();
if (!isset($_SESSION['login'])) {
  header("Location: ../auth/login.php"); exit;
}
define('NAMA_DEALER', 'Pelangi Motor Cilacap');
define('ALAMAT_DEALER', 'Jl. Penatusan, RT.07/RW.02, Adireja Wetan, Kec. Adipala, Kabupaten Cilacap, Jawa Tengah 53271');
define('TAGLINE_DEALER', 'Dealer Motor Bekas Terpercaya');
define('TELP_DEALER', '082364652829');

include "../config/database.php";

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: index.php"); exit; }

// Ambil data penjualan + motor
$query = "SELECT p.*, m.merek, m.model, m.warna, m.tahun_pembuatan, m.plat_nomor,
    m.no_rangka, m.no_mesin
  FROM penjualan p
  JOIN motor m ON p.motor_id = m.motor_id
  WHERE p.penjualan_id = $id";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
  header("Location: index.php"); exit;
}
$data = mysqli_fetch_assoc($result);

$noNota = 'NT-' . str_pad($id, 5, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nota Penjualan <?= $noNota ?> - <?= NAMA_DEALER ?></title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #f0f5fa;
      color: #1e293b;
      font-size: 13px;
      line-height: 1.6;
    }

    /* Toolbar - hanya tampil di layar */
    .toolbar {
      background: #1e3a5f;
      padding: 14px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }
    .toolbar-title {
      color: #fff;
      font-size: 14px;
      font-weight: 600;
    }
    .toolbar-title span {
      color: rgba(255,255,255,0.5);
      font-weight: 400;
      font-size: 12px;
      margin-left: 8px;
    }
    .toolbar-actions { display: flex; gap: 10px; }
    .btn-tool {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 18px;
      border: none;
      border-radius: 8px;
      font-family: inherit;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
    }
    .btn-print { background: #3b82f6; color: #fff; }
    .btn-print:hover { background: #2563eb; }
    .btn-back { background: rgba(255,255,255,0.1); color: #fff; }
    .btn-back:hover { background: rgba(255,255,255,0.2); }

    /* Nota container */
    .page-wrap {
      padding: 32px;
      display: flex;
      justify-content: center;
    }

    .nota {
      background: #fff;
      width: 100%;
      max-width: 720px;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.08);
      overflow: hidden;
    }

    /* Header nota */
    .nota-header {
      background: linear-gradient(135deg, #1e3a5f 0%, #3b82f6 100%);
      padding: 32px 36px;
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }
    .nota-brand h1 {
      font-size: 22px;
      font-weight: 800;
      letter-spacing: -0.5px;
    }
    .nota-brand p {
      font-size: 12px;
      color: rgba(255,255,255,0.65);
      margin-top: 2px;
    }
    .nota-meta { text-align: right; }
    .nota-meta .no-nota {
      font-size: 18px;
      font-weight: 700;
      letter-spacing: 1px;
    }
    .nota-meta .tgl-nota {
      font-size: 12px;
      color: rgba(255,255,255,0.65);
      margin-top: 4px;
    }
    .nota-badge {
      display: inline-block;
      background: rgba(255,255,255,0.2);
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      margin-top: 8px;
      letter-spacing: 0.5px;
    }

    /* Body nota */
    .nota-body { padding: 32px 36px; }

    /* Info pihak */
    .nota-parties {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      margin-bottom: 28px;
      padding-bottom: 24px;
      border-bottom: 1.5px dashed #e2e8f0;
    }
    .party-box h4 {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #94a3b8;
      margin-bottom: 8px;
    }
    .party-box .name {
      font-size: 15px;
      font-weight: 700;
      color: #1e293b;
    }
    .party-box p {
      font-size: 12px;
      color: #64748b;
      margin-top: 2px;
    }

    /* Detail motor */
    .section-title {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #94a3b8;
      margin-bottom: 12px;
    }

    .motor-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      overflow: hidden;
      margin-bottom: 24px;
    }
    .info-row {
      display: flex;
      padding: 10px 16px;
      border-bottom: 1px solid #e2e8f0;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label {
      width: 130px;
      color: #64748b;
      font-size: 12px;
      flex-shrink: 0;
    }
    .info-row .value {
      font-weight: 600;
      color: #1e293b;
      font-size: 12px;
    }

    /* Tabel rincian biaya */
    .biaya-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .biaya-table thead th {
      background: #f1f5f9;
      padding: 9px 14px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      color: #64748b;
      text-align: left;
      border-bottom: 2px solid #e2e8f0;
    }
    .biaya-table thead th:last-child { text-align: right; }
    .biaya-table tbody td {
      padding: 9px 14px;
      font-size: 12px;
      border-bottom: 1px solid #f1f5f9;
      color: #1e293b;
    }
    .biaya-table tbody td:last-child { text-align: right; font-weight: 600; }
    .biaya-table tfoot td {
      padding: 10px 14px;
      font-size: 13px;
    }
    .biaya-table tfoot tr.subtotal td { color: #64748b; }
    .biaya-table tfoot tr.total-modal td {
      font-weight: 700;
      background: #fef3c7;
      border-top: 1.5px solid #fde68a;
      color: #92400e;
    }
    .biaya-table tfoot tr.total-jual td {
      font-weight: 800;
      font-size: 15px;
      background: linear-gradient(135deg, #1e3a5f, #3b82f6);
      color: #fff;
    }

    /* Profit box */
    .profit-box {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px 20px;
      border-radius: 10px;
      margin-bottom: 28px;
    }
    .profit-box.positif { background: #ecfdf5; border: 1px solid #a7f3d0; }
    .profit-box.negatif { background: #fef2f2; border: 1px solid #fecaca; }
    .profit-box .icon { font-size: 28px; }
    .profit-box .label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .profit-box .amount {
      font-size: 22px;
      font-weight: 800;
    }
    .profit-box.positif .amount { color: #10b981; }
    .profit-box.negatif .amount { color: #ef4444; }

    /* Dijual melalui */
    .via-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #dbeafe;
      color: #1d4ed8;
      padding: 5px 14px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    /* Tanda tangan area */
    .ttd-section {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1.5px dashed #e2e8f0;
    }
    .ttd-box { text-align: center; }
    .ttd-box .ttd-label {
      font-size: 11px;
      color: #64748b;
      margin-bottom: 56px;
    }
    .ttd-box .ttd-line {
      border-top: 1.5px solid #cbd5e1;
      padding-top: 8px;
      font-size: 12px;
      font-weight: 600;
      color: #1e293b;
    }
    .ttd-box .ttd-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }

    /* Footer nota */
    .nota-footer {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      padding: 16px 36px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .nota-footer p { font-size: 11px; color: #94a3b8; }
    .nota-footer .powered { font-size: 11px; color: #cbd5e1; }

    /* Print styles */
    @media print {
      body { background: #fff; }
      .toolbar { display: none !important; }
      .page-wrap { padding: 0; }
      .nota {
        box-shadow: none;
        border-radius: 0;
        max-width: 100%;
      }
      @page { margin: 10mm; size: A5; }
    }
  </style>
</head>
<body>

<!-- Toolbar (layar saja) -->
<div class="toolbar">
  <div class="toolbar-title">
    Nota Penjualan
    <span><?= $noNota ?> — <?= htmlspecialchars($data['merek'] . ' ' . $data['model']) ?></span>
  </div>
  <div class="toolbar-actions">
    <a href="index.php" class="btn-tool btn-back">← Kembali</a>
    <button class="btn-tool btn-print" onclick="window.print()">🖨️ Cetak Nota</button>
  </div>
</div>

<div class="page-wrap">
  <div class="nota">

    <!-- Header -->
    <div class="nota-header">
      <div class="nota-brand">
        <h1>🏍️ <?= NAMA_DEALER ?></h1>
        <p><?= TAGLINE_DEALER ?></p>
        <p style="margin-top:4px;font-size:11px;color:rgba(255,255,255,0.5)"><?= ALAMAT_DEALER ?></p>
        <p style="margin-top:2px;font-size:12px;color:rgba(255,255,255,0.7)"> WA Telp. <?= TELP_DEALER ?></p>
      </div>
      <div class="nota-meta">
        <div class="no-nota"><?= $noNota ?></div>
        <div class="tgl-nota">Tanggal: <?= date('d F Y', strtotime($data['tgl_jual'])) ?></div>
        <div class="nota-badge">✅ TERJUAL</div>
      </div>
    </div>

    <!-- Body -->
    <div class="nota-body">

      <!-- Pihak penjual & pembeli -->
      <div class="nota-parties">
        <div class="party-box">
          <h4>Penjual</h4>
          <div class="name"><?= NAMA_DEALER ?></div>
          <p><?= TAGLINE_DEALER ?></p>
        </div>
        <div class="party-box">
          <h4>Pembeli</h4>
          <div class="name"><?= htmlspecialchars($data['nama_pembeli']) ?></div>
          <p>Dibeli via: <strong><?= htmlspecialchars($data['dijual_melalui']) ?></strong></p>
        </div>
      </div>

      <!-- Detail Motor -->
      <div class="section-title">Informasi Kendaraan</div>
      <div class="motor-info-grid">
        <div class="info-row">
          <span class="label">Merek / Model</span>
          <span class="value"><?= htmlspecialchars($data['merek'] . ' ' . $data['model']) ?></span>
        </div>
        <div class="info-row">
          <span class="label">Tahun</span>
          <span class="value"><?= $data['tahun_pembuatan'] ?></span>
        </div>
        <div class="info-row">
          <span class="label">Warna</span>
          <span class="value"><?= htmlspecialchars($data['warna']) ?></span>
        </div>
        <div class="info-row">
          <span class="label">Plat Nomor</span>
          <span class="value"><?= htmlspecialchars($data['plat_nomor']) ?></span>
        </div>
        <div class="info-row">
          <span class="label">No. Rangka</span>
          <span class="value"><?= htmlspecialchars($data['no_rangka']) ?></span>
        </div>
        <div class="info-row">
          <span class="label">No. Mesin</span>
          <span class="value"><?= htmlspecialchars($data['no_mesin']) ?></span>
        </div>
      </div>

      <!-- Harga Jual (customer view - tanpa info internal) -->
      <div class="section-title">Harga Transaksi</div>
      <div style="background:linear-gradient(135deg,#1e3a5f,#3b82f6);border-radius:12px;padding:28px 32px;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;">
        <div>
          <div style="color:rgba(255,255,255,0.65);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Harga Jual Kendaraan</div>
          <div style="color:#fff;font-size:28px;font-weight:800;letter-spacing:-0.5px;">Rp <?= number_format($data['harga_jual'], 0, ',', '.') ?></div>
          <div style="color:rgba(255,255,255,0.5);font-size:12px;margin-top:4px;">Tanggal transaksi: <?= date('d F Y', strtotime($data['tgl_jual'])) ?></div>
        </div>
        <div style="font-size:48px;opacity:0.3;">🏍️</div>
      </div>

      <!-- Keterangan tambahan -->
      <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;margin-bottom:28px;font-size:12px;color:#64748b;">
        <strong style="color:#1e293b;">Keterangan:</strong> Kendaraan dijual dalam kondisi sebagaimana adanya dan telah diperiksa oleh pembeli. Nota ini merupakan bukti sah jual beli yang mengikat kedua belah pihak.
      </div>

      <!-- Tanda Tangan -->
      <div class="ttd-section">
        <div class="ttd-box">
          <div class="ttd-label">Penjual,</div>
          <div class="ttd-line"><?= NAMA_DEALER ?></div>
          <div class="ttd-sub">Pihak I</div>
        </div>
        <div class="ttd-box">
          <div class="ttd-label">Pembeli,</div>
          <div class="ttd-line"><?= htmlspecialchars($data['nama_pembeli']) ?></div>
          <div class="ttd-sub">Pihak II</div>
        </div>
      </div>

    </div><!-- /nota-body -->

    <!-- Footer Nota -->
    <div class="nota-footer">
      <p>Nota ini merupakan bukti sah transaksi jual beli kendaraan bermotor.</p>
      <p class="powered"><?= NAMA_DEALER ?> © <?= date('Y') ?></p>
    </div>

  </div><!-- /nota -->
</div><!-- /page-wrap -->

</body>
</html>
