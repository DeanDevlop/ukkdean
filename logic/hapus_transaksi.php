<?php
include '../config/koneksi.php';


if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];

   
    $query_detail = "DELETE FROM detail_transaksi WHERE id_transaksi = '$id_transaksi'";
    mysqli_query($koneksi, $query_detail);

  
    $query_utama = "DELETE FROM transaksi WHERE id = '$id_transaksi'";
    
    if (mysqli_query($koneksi, $query_utama)) {
       
        echo "<script>alert('Data transaksi berhasil dihapus!'); window.location.href='../admin/riwayat.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location.href='../admin/riwayat.php';</script>";
    }

} else {
   
    header("Location: ../admin/riwayat.php");
}
?>