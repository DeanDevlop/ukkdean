<?php
session_start();
// Asumsi 'auth_check.php' ada di direktori induk atau path yang bisa diakses
// Sesuaikan path jika struktur direktori berbeda.
include '../../auth_check.php'; 

// --- START PATCH ---
// Pastikan pengguna telah login (ditangani oleh auth_check.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php"); // Alihkan ke halaman login jika tidak terautentikasi
    exit();
}

// Validasi parameter 'id': Pastikan ada dan merupakan angka.
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    // Log upaya akses tidak valid
    error_log("Invalid or missing 'id' parameter in /kasir/struk.php by user_id: " . $_SESSION['user_id']);
    header("Location: /kasir/dashboard.php"); // Alihkan ke halaman yang aman
    exit();
}

$transaction_id = (int)$_GET['id']; // Konversi ke integer untuk keamanan dan tipe data yang benar

// --- Contoh Kode Interaksi Database (Ganti dengan kode DB aktual Anda) ---
// Pastikan Anda menggunakan prepared statements untuk mencegah SQL Injection.
// Contoh menggunakan PDO:
/*
$pdo = null; // Inisialisasi PDO connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=your_db", "your_user", "your_password");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Terjadi kesalahan koneksi database.");
}
*/
// --- Akhir Contoh Kode Interaksi Database ---

// Ambil detail transaksi menggunakan prepared statement dan tambahkan pemeriksaan otorisasi.
// Logika otorisasi:
// 1. Jika pengguna adalah 'admin' atau 'owner', mereka dapat melihat transaksi apa pun.
// 2. Jika pengguna adalah 'kasir', mereka hanya dapat melihat transaksi yang mereka proses (asumsi ada kolom 'kasir_id' di tabel 'transaksi').
//    Sesuaikan logika ini berdasarkan aturan bisnis aktual Anda (misalnya, kasir dapat melihat semua transaksi di cabang mereka).

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

$sql = "SELECT * FROM transaksi WHERE id = :id";
$params = [':id' => $transaction_id];

if ($user_role === 'kasir') {
    // Asumsi kolom 'kasir_id' menyimpan ID kasir yang memproses transaksi
    $sql .= " AND kasir_id = :user_id";
    $params[':user_id'] = $user_id;
}
// Jika 'admin' atau 'owner', tidak perlu klausa WHERE tambahan untuk otorisasi.

$transaction = null;
/*
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database query error for transaction id $transaction_id: " . $e->getMessage());
    echo "Terjadi kesalahan saat mengambil data transaksi.";
    exit();
}
*/

// --- Simulasi data transaksi untuk demonstrasi ---
// Ganti dengan hasil query database aktual Anda
if ($transaction_id == 123 && ($user_role === 'admin' || $user_role === 'owner' || ($user_role === 'kasir' && $user_id == 1))) {
    $transaction = ['id' => 123, 'item' => 'Barang A', 'amount' => 10000, 'kasir_id' => 1, 'customer_name' => 'Pelanggan X', 'tanggal' => '2023-10-26'];
} elseif ($transaction_id == 456 && ($user_role === 'admin' || $user_role === 'owner' || ($user_role === 'kasir' && $user_id == 2))) {
    $transaction = ['id' => 456, 'item' => 'Barang B', 'amount' => 20000, 'kasir_id' => 2, 'customer_name' => 'Pelanggan Y', 'tanggal' => '2023-10-25'];
}
// --- Akhir Simulasi ---


if (!$transaction) {
    // Log upaya akses transaksi yang tidak sah atau tidak ada
    error_log("Unauthorized or non-existent transaction access attempt for id: $transaction_id by user_id: $user_id with role: $user_role");
    echo "Struk transaksi tidak ditemukan atau Anda tidak memiliki izin untuk melihatnya.";
    // header("Location: /kasir/dashboard.php"); // Alihkan ke halaman yang aman
    exit();
}

// Tampilkan detail transaksi
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi #<?php echo htmlspecialchars($transaction['id']); ?></title>
</head>
<body>
    <h1>Struk Transaksi #<?php echo htmlspecialchars($transaction['id']); ?></h1>
    <p><strong>Tanggal:</strong> <?php echo htmlspecialchars($transaction['tanggal']); ?></p>
    <p><strong>Item:</strong> <?php echo htmlspecialchars($transaction['item']); ?></p>
    <p><strong>Jumlah:</strong> Rp <?php echo number_format($transaction['amount'], 0, ',', '.'); ?></p>
    <p><strong>Kasir ID:</strong> <?php echo htmlspecialchars($transaction['kasir_id']); ?></p>
    <p><strong>Nama Pelanggan:</strong> <?php echo htmlspecialchars($transaction['customer_name']); ?></p>
    <p><a href="/kasir/dashboard.php">Kembali ke Dashboard</a></p>
</body>
</html>
<?php
// --- END PATCH ---
?>