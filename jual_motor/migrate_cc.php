<?php
/**
 * Migrasi: Tambah kolom cc (kapasitas mesin) ke tabel motor
 * Jalankan sekali saja di browser: http://localhost/jual_motor/migrate_cc.php
 */
include "config/database.php";

$results = [];

// Cek apakah kolom cc sudah ada
$check = mysqli_query($conn, "SHOW COLUMNS FROM motor LIKE 'cc'");
if (mysqli_num_rows($check) === 0) {
    $r = mysqli_query($conn, "ALTER TABLE motor ADD COLUMN cc VARCHAR(10) NULL DEFAULT NULL AFTER model");
    $results[] = $r ? "✅ Kolom 'cc' berhasil ditambahkan ke tabel motor." : "❌ Gagal: " . mysqli_error($conn);
} else {
    $results[] = "ℹ️ Kolom 'cc' sudah ada, tidak perlu ditambahkan.";
}

echo "<pre style='font-family:monospace;padding:20px;background:#f8fafc;border-radius:8px'>";
echo "<strong>Migrasi CC Motor</strong>\n\n";
foreach ($results as $r) echo $r . "\n";
echo "\n<a href='pembelian/index.php'>→ Buka Pembelian</a>";
echo "</pre>";
?>
