<?php
include "../config/database.php";
header('Content-Type: application/json');

$merek = $_GET['merek'] ?? '';
$stmt = mysqli_prepare($conn, "SELECT nama_model FROM master_model mm JOIN master_merek m ON mm.merek_id = m.id WHERE m.nama_merek = ? ORDER BY mm.nama_model ASC");
mysqli_stmt_bind_param($stmt, "s", $merek);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$models = [];
while ($row = mysqli_fetch_assoc($result)) {
    $models[] = $row['nama_model'];
}

echo json_encode($models);
?>
