<?php
session_start();
include '../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (empty($_SESSION['keranjang'])) {
    header("Location: ../kasir/index.php");
    exit;
}


$id_user = $_SESSION['id_user'] ?? 1; 
$uang_bayar_string = $_POST['bayar_tunai'];
$uang_bayar = (int) str_replace('.', '', $uang_bayar_string); 

$total_belanja = 0;
foreach ($_SESSION['keranjang'] as $id_brg => $qty) {
    $q = mysqli_query($koneksi, "SELECT harga FROM barang WHERE id='$id_brg'");
    $d = mysqli_fetch_assoc($q);
    $total_belanja += ($d['harga'] * $qty);
}


if ($uang_bayar < $total_belanja) {
    echo "<script>alert('Uang tidak cukup!'); window.history.back();</script>";
    exit;
}

$kembalian = $uang_bayar - $total_belanja;
$tgl_jam = date('Y-m-d H:i:s');


$query_last_id = mysqli_query($koneksi, "SELECT id FROM transaksi ORDER BY id DESC LIMIT 1");
$last_data = mysqli_fetch_assoc($query_last_id);
$next_id = $last_data ? $last_data['id'] + 1 : 1;
$nomor_transaksi = "#TRX-" . date('Ymd') . str_pad($next_id, 4, '0', STR_PAD_LEFT);


$query = "INSERT INTO transaksi (transaction_number, id_user, tgl_transaksi, total_bayar, bayar, kembalian) 
          VALUES ('$nomor_transaksi', '$id_user', '$tgl_jam', '$total_belanja', '$uang_bayar', '$kembalian')";

if (mysqli_query($koneksi, $query)) {
    $id_transaksi = mysqli_insert_id($koneksi); 

   
    foreach ($_SESSION['keranjang'] as $id_brg => $qty) {
        $q = mysqli_query($koneksi, "SELECT harga, stok FROM barang WHERE id='$id_brg'");
        $d = mysqli_fetch_assoc($q);
        
        $harga_saat_ini = $d['harga'];
        $subtotal = $harga_saat_ini * $qty;

        mysqli_query($koneksi, "INSERT INTO detail_transaksi (id_transaksi, id_barang, qty, subtotal) 
                               VALUES ('$id_transaksi', '$id_brg', '$qty', '$subtotal')");

        $stok_baru = $d['stok'] - $qty;
        mysqli_query($koneksi, "UPDATE barang SET stok = $stok_baru WHERE id='$id_brg'");
    }

    unset($_SESSION['keranjang']);
    header("Location: ../kasir/struk.php?id=$id_transaksi");
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>