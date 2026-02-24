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
    <title>Riwayat Transaksi</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>body { font-family: 'Segoe UI', Roboto, sans-serif; }</style>
</head>
<body class="bg-gray-50 font-sans text-gray-700 flex h-screen overflow-hidden">

    <?php include '../components/sideadmin.php'; ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        
        <?php 
            $pageTitle = "Riwayat Penjualan"; 
            include '../components/navbar.php'; 
        ?>
      <div class="mt-8 text-center">
        <?php include '../components/footer.php'; ?>
    </div>
        <div class="flex-1 overflow-y-auto p-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Data Transaksi Masuk</h3>
                        <p class="text-xs text-gray-400">Daftar semua transaksi yang berhasil.</p>
                    </div>
                   
                </div>

                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100 bg-gray-50">
                            <th class="px-6 py-4">No. Transaksi</th>
                            <th class="px-6 py-4">Tanggal & Jam</th>
                            <th class="px-6 py-4">Kasir Bertugas</th>
                            <th class="px-6 py-4 text-right">Total Belanja</th>
                            <th class="px-6 py-4 text-right">Bayar</th>
                            
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php
                       
                        $query = "SELECT t.*, u.nama_lengkap 
                                  FROM transaksi t 
                                  LEFT JOIN users u ON t.id_user = u.id 
                                  ORDER BY t.tgl_transaksi DESC";
                        
                        $result = mysqli_query($koneksi, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) :
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                           <td class="px-6 py-4 font-bold text-blue-600">
    <?= $row['transaction_number'] ?>
</td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= date('d M Y, H:i', strtotime($row['tgl_transaksi'])) ?> WIB
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-bold border border-blue-100">
                                    <?= $row['nama_lengkap'] ?: 'Admin/Unknown' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-700">
                                Rp <?= number_format($row['total_bayar']) ?>
                            </td>
                            <td class="px-6 py-4 text-right text-green-600 font-medium">
                                Rp <?= number_format($row['bayar']) ?>
                            </td>
                            
                        </tr>
                        <?php 
                            endwhile; 
                        } else {
                            echo '<tr><td colspan="6" class="text-center py-8 text-gray-400">Belum ada riwayat transaksi.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>