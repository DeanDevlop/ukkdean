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

// Contoh data untuk modal (misalnya, item pertama dari daftar)
$b = $barang_list[0] ?? ['id' => 0, 'nama_barang' => '', 'kategori' => ''];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Daftar Barang</h1>
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
            <?php foreach ($barang_list as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['nama_barang'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['harga'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['stok_awal'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <!-- Menggunakan json_encode untuk konteks JavaScript -->
                    <button onclick="bukaModalRestock(<?= (int)$row['id'] ?>, <?= json_encode($row['nama_barang']) ?>, <?= json_encode($row['kategori']) ?>)">Restock</button>
                    <a href="#">Edit</a>
                    <a href="#">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Contoh Modal Restock (asumsi ada modal di halaman ini) -->
    <div id="modalRestock" style="display:none;">
        <h2>Restock Barang</h2>
        <p>ID: <span id="modalItemId"></span></p>
        <p>Nama Barang: <span id="modalItemName"></span></p>
        <p>Kategori: <span id="modalItemCategory"></span></p>
        <button onclick="tutupModalRestock()">Tutup</button>
    </div>

    <script>
        function bukaModalRestock(id, nama_barang, kategori) {
            document.getElementById('modalItemId').innerText = id;
            document.getElementById('modalItemName').innerText = nama_barang;
            document.getElementById('modalItemCategory').innerText = kategori;
            document.getElementById('modalRestock').style.display = 'block';
        }

        function tutupModalRestock() {
            document.getElementById('modalRestock').style.display = 'none';
        }
    </script>
</body>
</html>
<?php
mysqli_close($koneksi);
?>