<?php
session_start();
// Asumsi 'auth_check.php' ada di root atau path yang bisa diakses dan mengatur $_SESSION['user_id'] dan $_SESSION['role']
// Sesuaikan path jika diperlukan, misalnya: include '../auth_check.php';
include 'auth_check.php'; 

// --- START PATCH ---
// 1. Authorization Check: Hanya izinkan pengguna dengan peran 'admin' atau 'owner' untuk mengakses halaman ini.
//    Jika halaman ini dimaksudkan untuk pendaftaran publik, logika penetapan peran harus berbeda (misalnya, default ke 'user').
//    Mengingat peran 'admin', 'kasir', 'owner', ini kemungkinan besar adalah halaman manajemen pengguna internal.
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'owner')) {
    // Log upaya akses tidak sah
    error_log("Unauthorized access attempt to /user.php by user_id: " . ($_SESSION['user_id'] ?? 'N/A') . " with role: " . ($_SESSION['role'] ?? 'N/A'));
    // Alihkan ke halaman tidak berwenang atau tampilkan pesan error
    header("Location: /unauthorized.php"); // Pastikan halaman ini ada
    exit();
}
// --- END PATCH ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    
    // --- START PATCH ---
    // 2. Role Assignment Restriction: Mencegah penetapan peran langsung dari input pengguna tanpa validasi.
    //    Bahkan untuk pengguna yang berwenang (admin/owner), validasi peran yang disubmit terhadap daftar yang diizinkan.
    $submitted_role = $_POST['role'] ?? 'kasir'; // Default ke 'kasir' jika tidak disediakan
    $allowed_roles = ['admin', 'kasir', 'owner'];

    // Validasi peran yang disubmit. Jika tidak valid, default ke 'kasir'.
    if (!in_array($submitted_role, $allowed_roles)) {
        $submitted_role = 'kasir';
    }

    // Peran yang akan ditetapkan adalah peran yang telah divalidasi.
    // Pemeriksaan otorisasi awal memastikan hanya admin/owner yang dapat mencapai titik ini.
    $role_to_assign = $submitted_role;
    // --- END PATCH ---

    // Sanitasi input dasar (gunakan prepared statements untuk interaksi DB aktual)
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // --- Contoh Kode Interaksi Database (Ganti dengan kode DB aktual Anda) ---
    // Pastikan Anda menggunakan prepared statements untuk mencegah SQL Injection.
    // Contoh menggunakan PDO:
    /*
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=your_db", "your_user", "your_password");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)");
        $stmt->execute([
            'username' => $username,
            'password' => $hashed_password,
            'email' => $email,
            'role' => $role_to_assign // Gunakan peran yang telah divalidasi
        ]);
        // echo "User created successfully with role: " . $role_to_assign;
        header("Location: /user_list.php"); // Alihkan setelah berhasil
        exit();
    } catch (PDOException $e) {
        error_log("Database error creating user: " . $e->getMessage());
        echo "Error creating user.";
    }
    */
    // --- Akhir Contoh Kode Interaksi Database ---

    // Untuk tujuan demonstrasi:
    echo "User created: Username: $username, Email: $email, Role: $role_to_assign\n";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
</head>
<body>
    <h1>Create New User</h1>
    <form action="/user.php" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <!-- Pilihan peran hanya boleh terlihat/diedit oleh pengguna yang berwenang (admin/owner) -->
        <label for="role">Role:</label><br>
        <select id="role" name="role">
            <option value="kasir">Kasir</option>
            <option value="admin">Admin</option>
            <option value="owner">Owner</option>
        </select><br><br>
        
        <input type="submit" value="Create User">
    </form>
</body>
</html>