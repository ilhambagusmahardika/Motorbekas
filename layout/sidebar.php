<?php
$currentPage = $currentPage ?? '';
?>
<aside class="sidebar">
  <div class="sidebar-brand">
    <h2><i class="ph ph-motorcycle" style="color:var(--accent-primary);"></i> JualMotor</h2>
    <span>Sistem Jual Beli Motor</span>
  </div>

  <ul class="sidebar-nav">
    <li>
      <a href="../beranda/index.php" class="<?= $currentPage === 'beranda' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-house"></i></span> Beranda
      </a>
    </li>
    <li>
      <a href="../pembelian/index.php" class="<?= $currentPage === 'pembelian' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-shopping-cart"></i></span> Pembelian Motor
      </a>
    </li>
    <li>
      <a href="../inventori/index.php" class="<?= $currentPage === 'inventori' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-wrench"></i></span> Inventori Barang
      </a>
    </li>
    <li>
      <a href="../stok/index.php" class="<?= $currentPage === 'stok' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-package"></i></span> Stok Unit
      </a>
    </li>
    <li>
      <a href="../transaksi/index.php" class="<?= $currentPage === 'transaksi' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-wallet"></i></span> Transaksi
      </a>
    </li>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <hr style="border-color: rgba(255,255,255,0.1); margin: 15px 0;">
    <li>
      <a href="../report/index.php" class="<?= $currentPage === 'report' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-chart-bar"></i></span> Laporan Keuangan
      </a>
    </li>
    <li>
      <a href="../master_plat/index.php" class="<?= $currentPage === 'master_plat' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-tag"></i></span> Master Plat
      </a>
    </li>
    <li>
      <a href="../master_motor/index.php" class="<?= $currentPage === 'master_motor' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-motorcycle"></i></span> Master Motor
      </a>
    </li>
    <li>
      <a href="../users/index.php" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
        <span class="icon"><i class="ph ph-users"></i></span> Manajemen Karyawan
      </a>
    </li>
    <?php endif; ?>
  </ul>

  <div class="sidebar-footer">
    <div style="margin-bottom: 10px; padding: 0 10px; font-size: 13px; color: #a0aec0; display: flex; align-items: center; gap: 8px;">
      <span style="width:30px; height:30px; border-radius:50%; background:var(--primary); display:grid; place-items:center; color:white; font-weight:bold;">
        <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
      </span>
      <div>
        <div style="font-weight:600; color:white;"><?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?></div>
        <div style="font-size:11px;"><?= ucfirst($_SESSION['role'] ?? 'Staff') ?></div>
      </div>
    </div>
    <a href="../auth/logout.php" style="background: rgba(239,68,68,0.1); color: #ef4444;">
      <span class="icon"><i class="ph ph-sign-out"></i></span> Logout
    </a>
  </div>
</aside>

<main class="main-content">