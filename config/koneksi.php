<?php
$host = "localhost";
$user = "app_user"; // Ganti dengan pengguna basis data khusus aplikasi
$pass = "S3cur3P@ssw0rd!"; // Ganti dengan kata sandi yang kuat dan unik
$db   = "db_kasir";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

$prefix = "#TRX-";
$timestamp = date('Ymd'); 
$random = rand(100, 999);
$nomor_final = $prefix . $timestamp . $random;

?>