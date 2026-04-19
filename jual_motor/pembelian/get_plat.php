<?php
include "../config/database.php";
header('Content-Type: application/json');

$plat = $_GET['plat'] ?? '';
$plat = strtoupper(preg_replace('/\s+/', '', $plat)); 

if (!$plat) {
    echo json_encode(['status' => 'error']);
    exit;
}

if (preg_match('/^([A-Z]{1,2})(\d+)([A-Z]*)$/', $plat, $match)) {
    $prefix = $match[1];
    $angka = $match[2];
    $suffix = $match[3];
} else if (preg_match('/^([A-Z]{1,2})/', $plat, $match)) {
    $prefix = $match[1];
    $angka = '';
    $suffix = '';
} else {
    echo json_encode(['status' => 'error']);
    exit;
}

$suffixLetter = $suffix ? substr($suffix, -1) : '';
$res = null;

// Coba cari prioritas 1: Prefix + Suffix spesifik (Misal: G 1234 A -> Suffix A)
if ($suffixLetter) {
    $stmt = mysqli_prepare($conn, "SELECT wilayah, kota FROM master_plat WHERE prefix = ? AND suffix = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "ss", $prefix, $suffixLetter);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// Coba cari prioritas 2: Prefix saja (Ambil kota default/utama dari karesidenan tersebut tanpa peduli suffix)
if (!$res) {
    $stmt = mysqli_prepare($conn, "SELECT wilayah, kota FROM master_plat WHERE prefix = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $prefix);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// Coba cari prioritas 3: Jika prefix 2 huruf gak ketemu, coba 1 huruf pertamanya
if (!$res && strlen($prefix) == 2) {
    $prefix1 = substr($prefix, 0, 1);
    $stmt = mysqli_prepare($conn, "SELECT wilayah, kota FROM master_plat WHERE prefix = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $prefix1);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

if ($res) {
    // Parse response nama kota (hapus kata Kabupaten/Kota jika perlu)
    $namaKota = preg_replace('/^(Kabupaten |Kota )/i', '', $res['kota']);
    echo json_encode(['status'=>'success', 'wilayah'=>$res['wilayah'], 'kota'=>$namaKota, 'samsat'=>$res['kota']]);
} else {
    echo json_encode(['status'=>'error']);
}
?>
