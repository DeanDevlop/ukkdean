<?php
include '../config/koneksi.php'; // Asumsi file koneksi.php berada satu tingkat di atas

// Validasi input ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID transaksi tidak valid.");
}
$id = $_GET['id'];

// --- Query untuk data transaksi utama ---
$stmt_transaksi = mysqli_prepare($koneksi, "SELECT * FROM transaksi WHERE id = ?");
if ($stmt_transaksi === false) {
    die("Error preparing statement for transaksi: " . mysqli_error($koneksi));
}
mysqli_stmt_bind_param($stmt_transaksi, "i", $id); // 'i' menandakan integer
mysqli_stmt_execute($stmt_transaksi);
$result_transaksi = mysqli_stmt_get_result($stmt_transaksi);
$transaksi = mysqli_fetch_assoc($result_transaksi);
mysqli_stmt_close($stmt_transaksi);

// Jika transaksi tidak ditemukan, mungkin tampilkan pesan error atau redirect
if (!$transaksi) {
    die("Transaksi tidak ditemukan.");
}

// --- Query untuk detail transaksi ---
$stmt_detail = mysqli_prepare($koneksi, "SELECT d.*, b.nama_barang, b.harga FROM detail_transaksi d JOIN barang b ON d.id_barang = b.id WHERE d.id_transaksi = ?");
if ($stmt_detail === false) {
    die("Error preparing statement for detail: " . mysqli_error($koneksi));
}
mysqli_stmt_bind_param($stmt_detail, "i", $id); // 'i' menandakan integer
mysqli_stmt_execute($stmt_detail);
$result_detail = mysqli_stmt_get_result($stmt_detail);
// Anda dapat mengulang $result_detail untuk menampilkan detail barang

// Contoh penggunaan (asumsi ada bagian HTML untuk menampilkan data)
// echo "<h1>Struk Transaksi #" . htmlspecialchars($transaksi['id']) . "</h1>";
// echo "<p>Tanggal: " . htmlspecialchars($transaksi['tanggal']) . "</p>";
// echo "<p>Total: " . htmlspecialchars($transaksi['total']) . "</p>
// echo "<h2>Detail Barang:</h2>";
// echo "<ul>";
// while ($row_detail = mysqli_fetch_assoc($result_detail)) {
//     echo "<li>" . htmlspecialchars($row_detail['nama_barang']) . " - " . htmlspecialchars($row_detail['jumlah']) . " x " . htmlspecialchars($row_detail['harga']) . "</li>";
// }
// echo "</ul>";

mysqli_stmt_close($stmt_detail);

// Pastikan untuk menutup koneksi database di akhir skrip atau saat tidak lagi dibutuhkan
// mysqli_close($koneksi);
?>