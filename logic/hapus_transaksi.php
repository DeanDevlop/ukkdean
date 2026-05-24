<?php
include '../config/koneksi.php'; // Asumsi file koneksi.php berada satu tingkat di atas

// Pastikan request adalah GET dan parameter 'id' ada dan numerik
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];

    // Validasi bahwa ID adalah numerik
    if (!is_numeric($id_transaksi)) {
        die("ID transaksi tidak valid.");
    }

    // Memulai transaksi untuk memastikan atomicity (kedua DELETE berhasil atau gagal bersama)
    mysqli_begin_transaction($koneksi);

    try {
        // --- Hapus dari detail_transaksi terlebih dahulu (karena kemungkinan foreign key) ---
        $stmt_detail = mysqli_prepare($koneksi, "DELETE FROM detail_transaksi WHERE id_transaksi = ?");
        if ($stmt_detail === false) {
            throw new Exception("Error preparing statement for detail_transaksi: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_detail, "i", $id_transaksi); // 'i' menandakan integer
        mysqli_stmt_execute($stmt_detail);
        mysqli_stmt_close($stmt_detail);

        // --- Hapus dari transaksi ---
        $stmt_transaksi = mysqli_prepare($koneksi, "DELETE FROM transaksi WHERE id = ?");
        if ($stmt_transaksi === false) {
            throw new Exception("Error preparing statement for transaksi: " . mysqli_error($koneksi));
        }
        mysqli_stmt_bind_param($stmt_transaksi, "i", $id_transaksi); // 'i' menandakan integer
        mysqli_stmt_execute($stmt_transaksi);
        mysqli_stmt_close($stmt_transaksi);

        // Commit transaksi jika semua operasi berhasil
        mysqli_commit($koneksi);

        // Redirect setelah berhasil menghapus
        header("Location: ../admin/riwayat.php?status=deleted");
        exit(); // Penting untuk menghentikan eksekusi skrip setelah redirect

    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($koneksi);
        // Log error secara internal dan tampilkan pesan generik ke pengguna
        error_log("Error deleting transaction with ID " . $id_transaksi . ": " . $e->getMessage());
        echo "Terjadi kesalahan saat menghapus transaksi. Silakan coba lagi.";
    }
} else {
    // Jika bukan request GET atau 'id' tidak ada
    die("Permintaan tidak valid.");
}

// Pastikan untuk menutup koneksi database di akhir skrip atau saat tidak lagi dibutuhkan
// mysqli_close($koneksi);
?>