<?php
include '../config/koneksi.php'; // Asumsi file koneksi.php berada satu tingkat di atas

// Asumsi Anda memiliki query untuk mengambil data riwayat transaksi
// Contoh query (sesuaikan dengan struktur database Anda)
$query_riwayat = "SELECT t.id, t.transaction_number, u.nama_lengkap, t.tanggal, t.total FROM transaksi t JOIN users u ON t.id_user = u.id ORDER BY t.tanggal DESC";
$result_riwayat = mysqli_query($koneksi, $query_riwayat);

$riwayat_list = [];
if ($result_riwayat) {
    while ($row = mysqli_fetch_assoc($result_riwayat)) {
        $riwayat_list[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
</head>
<body>
    <h1>Riwayat Transaksi</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Nomor Transaksi</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($riwayat_list)): ?>
            <tr>
                <td colspan="6">Tidak ada riwayat transaksi.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($riwayat_list as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['transaction_number'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['tanggal'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['total'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <a href="#">Detail</a>
                    <a href="../logic/hapus_transaksi.php?id=<?= (int)$row['id'] ?>" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
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