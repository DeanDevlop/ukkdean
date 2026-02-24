<?php

include '../components/auth_check.php';
include '../config/koneksi.php';

if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Barang Baru</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body { font-family: 'Segoe UI', Roboto, sans-serif; }
        .upload-area:hover { border-color: #3b82f6; background-color: #eff6ff; }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-700 flex h-screen overflow-hidden">

    <?php include '../components/sideadmin.php'; ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <?php 
            $pageTitle = "Form Input Produk"; 
            include '../components/navbar.php'; 
        ?>
      <div class="mt-8 text-center">
        <?php include '../components/footer.php'; ?>
    </div>
        <div class="flex-1 overflow-y-auto p-8">
            
            <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-10">
                <div class="px-8 py-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Tambahkan Produk</h3>
                            <p class="text-xs text-gray-400">Pastikan data produk sudah benar sebelum disimpan.</p>
                        </div>
                    </div>
                    
                </div>

                <div class="p-8">
                    <form action="../logic/tambah_barang.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Foto Produk (Opsional)</label>
                            <label class="upload-area flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 transition duration-300 group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-blue-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    <p class="mb-2 text-sm text-gray-500 group-hover:text-blue-600"><span class="font-bold">Klik upload</span> / drag & drop</p>
                                    <p class="text-xs text-gray-400" id="fileNameDisplay">JPG, PNG (Max 2MB)</p>
                                </div>
                                <input type="file" name="gambar_produk" class="hidden" accept="image/png, image/jpeg" onchange="previewFile(this)">
                            </label>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk</label>
                            <input type="text" name="nama_barang" class="w-full border border-gray-300 rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700 transition" placeholder="Contoh: Indomie Goreng" required>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                                <select name="kategori" class="w-full border border-gray-300 rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none bg-white font-bold text-gray-700 appearance-none">
                                    <option value="Makanan">Makanan</option>
                                    <option value="Minuman">Minuman</option>
                                    <option value="Bahan">Bahan Dapur</option>
                                    <option value="Cemilan">Cemilan</option>
                                    <option value="Peralatan">Peralatan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Harga Jual</label>
                                <div class="relative">
                                    
                                    <span class="absolute left-4 top-4 text-gray-500 font-bold">Rp</span>
                                    <input type="number" name="harga" class="w-full border border-gray-300 rounded-xl p-4 pl-12 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700 transition" placeholder="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Stok Awal</label>
                            <input type="number" name="stok_awal" class="w-full border border-gray-300 rounded-xl p-4 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700" placeholder="0" required>
                        </div>

                        <button type="submit" name="btn_tambah_barang" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex justify-center items-center">
                            SIMPAN PRODUK
                        </button>
                    </form>
                </div>
            </div>

            <div class="max-w-4xl mx-auto">
                <h3 class="font-bold text-gray-600 text-sm uppercase tracking-wider mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    5 Barang Terakhir Ditambahkan
                </h3>
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                            <tr>
                                <th class="px-6 py-4 border-b">Nama Barang</th>
                                <th class="px-6 py-4 border-b">Kategori</th>
                                <th class="px-6 py-4 border-b">Harga</th>
                                <th class="px-6 py-4 border-b text-center">Stok</th>
                                <th class="px-6 py-4 border-b text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php
                            
                            $qLast = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id DESC LIMIT 20");
                            
                            if(mysqli_num_rows($qLast) > 0) {
                                while($b = mysqli_fetch_assoc($qLast)):
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800 flex items-center gap-3">
                                    <?php if(!empty($b['gambar']) && file_exists("../assets/images/".$b['gambar'])): ?>
                                        <img src="../assets/images/<?= $b['gambar'] ?>" class="w-8 h-8 rounded-lg object-cover border border-gray-200">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No</div>
                                    <?php endif; ?>
                                    <?= $b['nama_barang'] ?>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs border font-bold">
                                        <?= $b['kategori'] ?>
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-blue-600 font-bold text-xs">Rp <?= number_format($b['harga']) ?></td>
                                <td class="px-6 py-4 text-center font-bold"><?= $b['stok'] ?></td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="view_edit_barang.php?id=<?= $b['id'] ?>" class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <a href="../logic/hapus_barang.php?id=<?= $b['id'] ?>" onclick="return confirm('Yakin ingin menghapus <?= $b['nama_barang'] ?>?')" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                                endwhile; 
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-6 text-gray-400'>Belum ada barang ditambahkan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        function previewFile(input) {
            var fileName = input.files[0].name;
            document.getElementById('fileNameDisplay').innerText = "File: " + fileName;
            document.getElementById('fileNameDisplay').classList.add('text-blue-600', 'font-bold');
        }
    </script>
</body>
</html>