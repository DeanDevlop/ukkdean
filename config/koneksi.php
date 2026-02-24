<?php
$host = "localhost";
$user = "root";
$pass = "";
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