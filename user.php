<?php

$conn = mysqli_connect("localhost", "root", "", "db_kasir");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $role     = $_POST['role'];
    $created_at = date('Y-m-d H:i:s');
    $last_login = date('Y-m-d H:i:s');

    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password, role, nama_lengkap, created_at, last_login) 
            VALUES ('$username', '$password_hashed', '$role','$nama_lengkap', '$created_at', '$last_login')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Akun berhasil dibuat!');</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Akun Kasir</title>
</head>
<body>
    <h2>Form Pendaftaran User</h2>
    <form method="POST" action="">
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama_lengkap" required><br><br>

        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Role:</label><br>
        <select name="role">
            <option value="admin">Admin</option>
            <option value="kasir">Kasir</option>
              <option value="kasir">Owner</option>
        </select><br><br>

        <button type="submit">Daftar Akun</button>
    </form>
</body>
</html>
