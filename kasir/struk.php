<?php
// kasir/struk.php
include '../auth_check.php'; // Diasumsikan ini memeriksa login pengguna dan mengatur $_SESSION['user_id'] dan $_SESSION['user_role']

// Pastikan pengguna telah login dan memiliki peran 'kasir'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'kasir') {
    header('Location: /login.php'); // Arahkan ke halaman login atau tampilkan kesalahan
    exit();
}

// Pastikan koneksi database sudah diinisialisasi, contoh: $pdo = new PDO(...);

// Sanitasi dan validasi input
$transaction_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$transaction_id) {
    http_response_code(400);
    die("ID transaksi tidak valid.");
}

$current_kasir_id = $_SESSION['user_id']; // Dapatkan ID kasir yang sedang login

// PERBAIKAN KRITIS: Tambahkan pemeriksaan otorisasi untuk memastikan transaksi milik kasir saat ini
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND kasir_id = ?");
$stmt->execute([$transaction_id, $current_kasir_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

if ($transaction) {
    // Tampilkan detail transaksi
    header('Content-Type: text/html');
    echo "<h1>Struk Transaksi #" . htmlspecialchars($transaction['id']) . "</h1>";
    echo "<p>Jumlah: " . htmlspecialchars($transaction['amount']) . "</p>";
    echo "<p>Tanggal: " . htmlspecialchars($transaction['transaction_date']) . "</p>";
    // ... tampilkan detail lainnya, pastikan semua output di-escape dengan htmlspecialchars() ...
} else {
    // PENTING: Jangan ungkapkan apakah transaksi ada tetapi milik pengguna lain.
    // Cukup katakan "tidak ditemukan" atau "Anda tidak memiliki akses".
    http_response_code(403); // Forbidden
    echo "Transaksi tidak ditemukan atau Anda tidak memiliki akses.";
}
?>