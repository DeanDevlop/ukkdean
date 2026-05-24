<?php
include '../config/koneksi.php'; // Asumsi file koneksi.php berada satu tingkat di atas

// Asumsi Anda memiliki query untuk mengambil data barang
// Contoh query (sesuaikan dengan struktur database Anda)
$query_barang = "SELECT id, nama_barang, kategori, harga, stok_awal FROM barang";
$result_barang = mysqli_query($koneksi, $query_barang);

$barang_list = [];
if ($result_barang) {
    while ($row = mysqli_fetch_assoc($result_barang)) {
        $barang_list[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Barang</title>
</head>
<body>
    <h1>Daftar Barang</h1>
    <a href="#">Tambah Barang Baru</a>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok Awal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($barang_list)): ?>
            <tr>
                <td colspan="6">Tidak ada barang.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($barang_list as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['id'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($b['nama_barang'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($b['kategori'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($b['harga'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($b['stok_awal'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <a href="#">Edit</a>
                    <!-- Menggunakan json_encode untuk konteks JavaScript confirm() -->
                    <a href="../logic/hapus_barang.php?id=<?= (int)$b['id'] ?>" 
                       onclick="return confirm('Yakin ingin menghapus ' + <?= json_encode($b['nama_barang']) ?> + '?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php
mysqli_close($koneksi);
?>