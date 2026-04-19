<?php
include "config/database.php";

// 1. Alter Users Table
// mysqli_query($conn, "ALTER TABLE users ADD COLUMN role ENUM('admin', 'staff') DEFAULT 'admin' AFTER password");

// 2. Create Master Plat
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS master_plat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prefix VARCHAR(3) NOT NULL,
    suffix VARCHAR(1) NULL,
    wilayah VARCHAR(50) NOT NULL,
    kota VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 3. Create Master Merek
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS master_merek (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_merek VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 4. Create Master Model
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS master_model (
    id INT AUTO_INCREMENT PRIMARY KEY,
    merek_id INT NOT NULL,
    nama_model VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (merek_id) REFERENCES master_merek(id) ON DELETE CASCADE
)");

// ---- INSERT DATA MEREK & MODEL ----
$dataModel = [
  "Yamaha" => ["Mio","NMAX","R15","R25","Aerox","Vixion","Jupiter","Xeon","Fino","MT-25","XSR 155","WR 155","Lexi","Fazzio","Gear","FreeGo"],
  "Honda" => ["Beat","Vario","PCX","CBR150","CBR250","Scoopy","Genio","Revo","Supra","ADV","Stylo","CB150","Tiger","Megapro","Verza","CRF","BeAT Street"],
  "Suzuki" => ["Satria","Nex","GSX","Address","Smash","Spin","Shogun","Thunder","Burgman"],
  "Kawasaki" => ["Ninja","Z250","W175","KLX","Versys","Z125","ZX-25R","Eliminator"],
  "Vespa" => ["Sprint","Primavera","GTS","S 125","LX 125"],
  "Viar" => ["Cross X","Vortex","Star NX","Q1"],
  "TVS" => ["Apache","Neo","Dazz","XL100"],
  "Benelli" => ["Panarea","Motobi","TNT","502C","Leoncino"]
];

mysqli_query($conn, "TRUNCATE TABLE master_model");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($conn, "TRUNCATE TABLE master_merek");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

foreach ($dataModel as $merek => $models) {
    $stmt = mysqli_prepare($conn, "INSERT INTO master_merek (nama_merek) VALUES (?)");
    mysqli_stmt_bind_param($stmt, "s", $merek);
    mysqli_stmt_execute($stmt);
    $merek_id = mysqli_insert_id($conn);
    
    foreach ($models as $model) {
        $stmt2 = mysqli_prepare($conn, "INSERT INTO master_model (merek_id, nama_model) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt2, "is", $merek_id, $model);
        mysqli_stmt_execute($stmt2);
    }
}

// ---- INSERT PLAT LAIN ----
mysqli_query($conn, "TRUNCATE TABLE master_plat");

$platLain = [
  'A' => ['w'=>'Banten', 'k'=>'Serang / Cilegon'],
  'B' => ['w'=>'DKI Jakarta', 'k'=>'Jakarta'],
  'D' => ['w'=>'Jawa Barat', 'k'=>'Bandung'],
  'E' => ['w'=>'Jawa Barat', 'k'=>'Cirebon'],
  'F' => ['w'=>'Jawa Barat', 'k'=>'Bogor'],
  'AB' => ['w'=>'DI Yogyakarta', 'k'=>'Yogyakarta'],
  'AG' => ['w'=>'Jawa Timur', 'k'=>'Kediri'],
  'AE' => ['w'=>'Jawa Timur', 'k'=>'Madiun'],
  'L' => ['w'=>'Jawa Timur', 'k'=>'Surabaya'],
  'M' => ['w'=>'Jawa Timur', 'k'=>'Madura'],
  'N' => ['w'=>'Jawa Timur', 'k'=>'Malang'],
  'P' => ['w'=>'Jawa Timur', 'k'=>'Jember'],
  'S' => ['w'=>'Jawa Timur', 'k'=>'Bojonegoro'],
  'W' => ['w'=>'Jawa Timur', 'k'=>'Sidoarjo'],
  'T' => ['w'=>'Jawa Barat', 'k'=>'Subang / Purwakarta'],
  'Z' => ['w'=>'Jawa Barat', 'k'=>'Garut / Tasikmalaya'],
  'DA' => ['w'=>'Kalimantan Selatan', 'k'=>'Banjarmasin'],
  'DB' => ['w'=>'Sulawesi Utara', 'k'=>'Manado'],
  'DD' => ['w'=>'Sulawesi Selatan', 'k'=>'Makassar'],
  'DK' => ['w'=>'Bali', 'k'=>'Denpasar'],
  'DN' => ['w'=>'Sulawesi Tengah', 'k'=>'Palu'],
  'DR' => ['w'=>'NTB', 'k'=>'Mataram'],
  'DH' => ['w'=>'NTT', 'k'=>'Kupang'],
  'KB' => ['w'=>'Kalimantan Barat', 'k'=>'Pontianak'],
  'KH' => ['w'=>'Kalimantan Tengah', 'k'=>'Palangkaraya'],
  'KT' => ['w'=>'Kalimantan Timur', 'k'=>'Samarinda'],
  'KU' => ['w'=>'Kalimantan Utara', 'k'=>'Tanjung Selor'],
  'BA' => ['w'=>'Sumatera Barat', 'k'=>'Padang'],
  'BB' => ['w'=>'Sumatera Utara', 'k'=>'Tapanuli'],
  'BD' => ['w'=>'Bengkulu', 'k'=>'Bengkulu'],
  'BE' => ['w'=>'Lampung', 'k'=>'Bandar Lampung'],
  'BG' => ['w'=>'Sumatera Selatan', 'k'=>'Palembang'],
  'BH' => ['w'=>'Jambi', 'k'=>'Jambi'],
  'BK' => ['w'=>'Sumatera Utara', 'k'=>'Medan'],
  'BL' => ['w'=>'Aceh', 'k'=>'Banda Aceh'],
  'BM' => ['w'=>'Riau', 'k'=>'Pekanbaru'],
  'BN' => ['w'=>'Bangka Belitung', 'k'=>'Pangkal Pinang'],
  'BP' => ['w'=>'Kepulauan Riau', 'k'=>'Batam'],
  'DE' => ['w'=>'Maluku', 'k'=>'Ambon'],
  'DG' => ['w'=>'Maluku Utara', 'k'=>'Ternate'],
  'PA' => ['w'=>'Papua', 'k'=>'Jayapura'],
  'PB' => ['w'=>'Papua Barat', 'k'=>'Manokwari'],
  'DC' => ['w'=>'Sulawesi Barat', 'k'=>'Mamuju'],
  'DL' => ['w'=>'Sulawesi Tenggara', 'k'=>'Kendari'],
  'DM' => ['w'=>'Gorontalo', 'k'=>'Gorontalo']
];

foreach ($platLain as $prefix => $data) {
    $stmt = mysqli_prepare($conn, "INSERT INTO master_plat (prefix, wilayah, kota) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $prefix, $data['w'], $data['k']);
    mysqli_stmt_execute($stmt);
}

// ---- INSERT PLAT JATENG DENGAN SUFFIX ----
$platJateng = [
  'G' => [
    'w' => 'Jawa Tengah',
    'samsat' => [
      'Kota Pekalongan' => ['A','H','S'],
      'Kabupaten Pekalongan' => ['B','K','O','T'],
      'Kabupaten Batang' => ['C','L','V','X'],
      'Kabupaten Pemalang' => ['D','I','M','W'],
      'Kota Tegal' => ['E','N','Y'],
      'Kabupaten Tegal' => ['F','P','Q','Z'],
      'Kabupaten Brebes' => ['G','J','R','U']
    ]
  ],
  'H' => [
    'w' => 'Jawa Tengah',
    'samsat' => [
      'Kota Semarang' => ['A','F','G','H','P','Q','R','S','W','X','Y','Z'],
      'Kota Salatiga' => ['B','K','O','T'],
      'Kabupaten Semarang' => ['C','I','L','V'],
      'Kabupaten Kendal' => ['D','M','U'],
      'Kabupaten Demak' => ['E','J','N']
    ]
  ],
  'K' => [
    'w' => 'Jawa Tengah',
    'samsat' => [
      'Kabupaten Pati' => ['A','G','H','S','U'],
      'Kabupaten Kudus' => ['B','K','O','R','T'],
      'Kabupaten Jepara' => ['C','L','Q','V'],
      'Kabupaten Rembang' => ['D','I','M','W'],
      'Kabupaten Blora' => ['E','N','X','Y'],
      'Kabupaten Grobogan' => ['F','J','P','Z']
    ]
  ],
  'R' => [
    'w' => 'Jawa Tengah',
    'samsat' => [
      'Kabupaten Banyumas' => ['A','E','G','H','J','S','X'],
      'Kabupaten Cilacap' => ['B','F','K','N','P','R','T'],
      'Kabupaten Purbalingga' => ['C','L','Q','U','V','Z'],
      'Kabupaten Banjarnegara' => ['D','I','M','O','W','Y']
    ]
  ],
  'AA' => [
    'w' => 'Jawa Tengah',
    'samsat' => [
      'Kota Magelang' => ['A','H','S','U'],
      'Kabupaten Magelang' => ['B','G','K','O','T'],
      'Kabupaten Purworejo' => ['C','L','V','Q'],
      'Kabupaten Kebumen' => ['D','J','M','W'],
      'Kabupaten Temanggung' => ['E','N','Y'],
      'Kabupaten Wonosobo' => ['F','P','Z']
    ]
  ],
  'AD' => [
    'w' => 'Jawa Tengah',
    'samsat' => [
      'Kota Surakarta' => ['A','H','S','U'],
      'Kabupaten Sukoharjo' => ['B','K','O','T'],
      'Kabupaten Klaten' => ['C','L','J','Q','V'],
      'Kabupaten Boyolali' => ['D','M','W'],
      'Kabupaten Sragen' => ['E','N','Y'],
      'Kabupaten Karanganyar' => ['F','P','Z'],
      'Kabupaten Wonogiri' => ['G','I','R']
    ]
  ]
];

foreach ($platJateng as $prefix => $data) {
    $wilayah = $data['w'];
    foreach ($data['samsat'] as $kota => $suffixes) {
        foreach ($suffixes as $suffix) {
            $stmt = mysqli_prepare($conn, "INSERT INTO master_plat (prefix, suffix, wilayah, kota) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $prefix, $suffix, $wilayah, $kota);
            mysqli_stmt_execute($stmt);
        }
    }
}

echo "Migrasi database berhasil diselesaikan! Data Plat dan Motor sudah masuk tabel master.";
?>
