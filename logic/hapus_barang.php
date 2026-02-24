<?php
include '../config/koneksi.php';


if (isset($_GET['id'])) {
    $id_brg = $_GET['id'];

  
    $query_utama = "DELETE FROM barang WHERE id = '$id_brg'";
    
    if (mysqli_query($koneksi, $query_utama)) {
       
        echo "<script>alert('Data barang berhasil dihapus!'); window.location.href='../admin/view_tambah_barang.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location.href='../admin/view_tambah_barang.php';</script>";
    }

} else {
   
    header("Location: ../admin/riwayat.php");
}