<?php
session_start(); // Pastikan sesi dimulai di awal skrip

// Ambil nilai dari sesi, berikan nilai default jika tidak ada
$nama_tampil = $_SESSION['nama'] ?? 'Guest';
$role_tampil = $_SESSION['role'] ?? '';

// Pastikan semua output ke HTML di-escape
$nama_tampil_escaped = htmlspecialchars($nama_tampil, ENT_QUOTES, 'UTF-8');
$role_tampil_escaped = htmlspecialchars($role_tampil, ENT_QUOTES, 'UTF-8');

?>
<nav class="navbar">
    <div class="navbar-brand">Aplikasi Kasir</div>
    <ul class="navbar-nav">
        <li class="nav-item"><a href="/" class="nav-link">Home</a></li>
        <?php if (isset($_SESSION['user_id'])): // Asumsi ada user_id di sesi untuk menandakan login ?>
            <li class="nav-item"><a href="/dashboard.php" class="nav-link">Dashboard</a></li>
            <?php if ($role_tampil_escaped === 'admin'): // Contoh menu khusus admin ?>
                <li class="nav-item"><a href="/admin/admin.php" class="nav-link">Admin Panel</a></li>
            <?php endif; ?>
            <li class="nav-item"><a href="/logout.php" class="nav-link">Logout</a></li>
        <?php else: ?>
            <li class="nav-item"><a href="/login.php" class="nav-link">Login</a></li>
            <li class="nav-item"><a href="/register.php" class="nav-link">Register</a></li>
        <?php endif; ?>
    </ul>
    <div class="navbar-info">
        <span>Welcome, <strong><?= $nama_tampil_escaped ?></strong> (<?= $role_tampil_escaped ?>)</span>
    </div>
</nav>

<style>
    /* Contoh styling sederhana untuk navbar */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #333;
        color: white;
        padding: 10px 20px;
    }
    .navbar-brand {
        font-weight: bold;
        font-size: 1.2em;
    }
    .navbar-nav {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
    }
    .nav-item {
        margin-left: 15px;
    }
    .nav-link {
        color: white;
        text-decoration: none;
    }
    .nav-link:hover {
        text-decoration: underline;
    }
    .navbar-info {
        font-size: 0.9em;
    }
</style>
