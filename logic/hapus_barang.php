<?php
include '../config/koneksi.php'; // Asumsi file koneksi.php berada satu tingkat di atas

// Pastikan request adalah GET dan parameter 'id' ada dan numerik
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id_brg = $_GET['id'];

    // Validasi bahwa ID adalah numerik untuk mencegah injeksi non-numerik
    if (!is_numeric($id_brg)) {
        die("ID barang tidak valid.");
    }

    // Menggunakan prepared statement untuk mencegah SQL Injection
    $stmt = mysqli_prepare($koneksi, "DELETE FROM barang WHERE id = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, "i", $id_brg); // 'i' menandakan integer

    if (mysqli_stmt_execute($stmt)) {
        // Redirect setelah berhasil menghapus
        header("Location: ../admin/view_tambah_barang.php?status=deleted");
        exit(); // Penting untuk menghentikan eksekusi skrip setelah redirect
    } else {
        // Log error secara internal dan tampilkan pesan generik ke pengguna
        error_log("Error deleting barang with ID " . $id_brg . ": " . mysqli_error($koneksi));
        echo "Terjadi kesalahan saat menghapus barang. Silakan coba lagi.";
    }

    mysqli_stmt_close($stmt);
} else {
    // Jika bukan request GET atau 'id' tidak ada
    die("Permintaan tidak valid.");
}

// Pastikan untuk menutup koneksi database di akhir skrip atau saat tidak lagi dibutuhkan
// mysqli_close($koneksi);
?>