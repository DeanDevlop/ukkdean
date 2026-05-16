<?php
// Pastikan kredensial diambil dari variabel lingkungan untuk keamanan
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'app_user'; // Gunakan user dengan hak akses terbatas
$pass = getenv('DB_PASS') ?: 'S3cur3P@ssw0rd!'; // Gunakan kata sandi yang kuat
$db   = getenv('DB_NAME') ?: 'db_kasir';

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    // Log error secara aman dan tampilkan pesan generik kepada pengguna
    error_log("Koneksi Gagal: " . mysqli_connect_error());
    die("Terjadi masalah koneksi database. Silakan coba lagi nanti.");
}

$prefix = "#TRX-";
$timestamp = date('Ymd'); 
$random = rand(100, 999);
$nomor_final = $prefix . $timestamp . $random;

?>