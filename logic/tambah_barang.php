<?php
include '../config/koneksi.php'; // Asumsi file koneksi.php berada satu tingkat di atas

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi dan sanitasi input menggunakan filter_input
    $nama_barang = filter_input(INPUT_POST, 'nama_barang', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    $kategori = filter_input(INPUT_POST, 'kategori', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    $harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_FLOAT);
    $stok_awal = filter_input(INPUT_POST, 'stok_awal', FILTER_VALIDATE_INT);

    // Periksa apakah validasi gagal untuk input numerik atau string kosong
    if ($nama_barang === false || $nama_barang === null || $nama_barang === '' ||
        $kategori === false || $kategori === null || $kategori === '' ||
        $harga === false || $harga === null ||
        $stok_awal === false || $stok_awal === null) {
        die("Input tidak valid. Pastikan semua kolom terisi dan harga/stok adalah angka.");
    }

    // Menggunakan prepared statement untuk mencegah SQL Injection
    $stmt = mysqli_prepare($koneksi, "INSERT INTO barang (nama_barang, kategori, harga, stok_awal) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . mysqli_error($koneksi));
    }

    // 's' untuk string, 'd' untuk double (float), 'i' untuk integer
    mysqli_stmt_bind_param($stmt, "ssdi", $nama_barang, $kategori, $harga, $stok_awal);

    if (mysqli_stmt_execute($stmt)) {
        // Redirect setelah berhasil menambahkan
        header("Location: ../admin/view_tambah_barang.php?status=added");
        exit(); // Penting untuk menghentikan eksekusi skrip setelah redirect
    } else {
        // Log error secara internal dan tampilkan pesan generik ke pengguna
        error_log("Error adding barang: " . mysqli_error($koneksi));
        echo "Terjadi kesalahan saat menambahkan barang. Silakan coba lagi.";
    }

    mysqli_stmt_close($stmt);
} else {
    // Jika bukan request POST
    die("Metode permintaan tidak valid.");
}

// Pastikan untuk menutup koneksi database di akhir skrip atau saat tidak lagi dibutuhkan
// mysqli_close($koneksi);
?>