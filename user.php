<?php
// user.php
// Pastikan koneksi database sudah diinisialisasi, contoh: $pdo = new PDO(...);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi dan validasi input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password_raw = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW); // Ambil password mentah untuk hashing

    if (empty($username) || empty($password_raw)) {
        http_response_code(400);
        die("Nama pengguna dan kata sandi wajib diisi.");
    }

    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);
    
    // PERBAIKAN KRITIS: Jangan ambil 'role' dari input pengguna untuk pendaftaran publik.
    // Tetapkan peran default yang tidak memiliki hak istimewa.
    $role = 'user'; // Hardcode peran default

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password_hashed, $role]);
        http_response_code(201);
        echo "Pengguna berhasil dibuat dengan peran: " . htmlspecialchars($role);
    } catch (PDOException $e) {
        // Catat kesalahan dan berikan pesan generik
        error_log("Kesalahan database saat membuat pengguna: " . $e->getMessage());
        http_response_code(500);
        echo "Terjadi kesalahan saat membuat pengguna. Silakan coba lagi.";
    }
} else {
    // Opsional: Tampilkan formulir untuk permintaan GET
    // header('Content-Type: text/html');
    // echo "<form method='POST'>...<input type='text' name='username'>...<input type='password' name='password'>...<button type='submit'>Daftar</button></form>";
    http_response_code(405);
    echo "Metode tidak diizinkan.";
}
?>