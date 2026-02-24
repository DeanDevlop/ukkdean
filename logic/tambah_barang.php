<?php
include '../config/koneksi.php';

if (isset($_POST['btn_tambah_barang'])) {
    $nama     = $_POST['nama_barang'];
    $kategori = $_POST['kategori'];
    $harga    = $_POST['harga'];
    $stok     = $_POST['stok_awal'];
    
 
    $gambar_db = null; 

    
    if (isset($_FILES['gambar_produk']) && $_FILES['gambar_produk']['error'] === UPLOAD_ERR_OK) {
        
        $file_tmp   = $_FILES['gambar_produk']['tmp_name'];
        $file_name  = $_FILES['gambar_produk']['name'];
        $file_size  = $_FILES['gambar_produk']['size'];
        $file_parts = pathinfo($file_name);
        $file_ext   = strtolower($file_parts['extension']);

        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        $max_size    = 2 * 1024 * 1024; 

        if (in_array($file_ext, $allowed_ext) && $file_size <= $max_size) {
            
            $nama_file_baru = 'barang_' . uniqid() . '.' . $file_ext;
            
            
            $lokasi_simpan = '../assets/images/' . $nama_file_baru;
            
           
            if (move_uploaded_file($file_tmp, $lokasi_simpan)) {
               
                $gambar_db = $nama_file_baru;
            } else {
                echo "<script>alert('Gagal memindahkan file gambar!'); window.history.back();</script>";
                exit;
            }
        } else {
             echo "<script>alert('Format gambar tidak sesuai atau ukuran terlalu besar (Max 2MB)!'); window.history.back();</script>";
             exit;
        }
    }
  

    if (!empty($nama) && $harga > 0) {
        
        $query = "INSERT INTO barang (nama_barang, kategori, harga, stok, gambar) VALUES ('$nama', '$kategori', '$harga', '$stok', '$gambar_db')";
        
        if (mysqli_query($koneksi, $query)) {
           
            header("Location: ../admin/admin.php");
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
        
    } else {
        header("Location: ../admin/admin.php");
    }
} else {
    header("Location: ../admin/admin.php");
}
?>