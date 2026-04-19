<?php
$conn = mysqli_connect("localhost", "root", "", "db_jual_motor");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>