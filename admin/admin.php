<?php

include '../logic/logsistem.php';
include '../config/koneksi.php';
include '../components/auth_check.php';


if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}



$ai = new AiEngine($koneksi);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console</title>
     <link rel="stylesheet" href="../assets/style.css">

    <style>
           body { font-family: 'Segoe UI', Roboto, sans-serif; }
        
      
        @media print {
           
            aside, nav, .btn-aksi, .ai-alert-section, .no-print {
                display: none !important;
            }
            
         
            body, main { 
                background: white; 
                height: auto; 
                overflow: visible; 
                margin: 0; padding: 0;
            }
            
            
            .kop-surat {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid black;
                padding-bottom: 10px;
            }

          
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid black !important; padding: 8px; color: black !important; }
            th { background-color: #f0f0f0 !important; }
            
           
            .card-tabel { shadow: none; border: none; }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-700 flex h-screen overflow-hidden">

    <?php include '../components/sideadmin.php'; ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        
        <?php 
            $pageTitle = "Dashboard Admin"; 
            include '../components/navbar.php'; 
        ?>

        <div class="kop-surat hidden p-4">
            <h1 class="text-2xl font-bold uppercase">Laporan Stok Barang</h1>
            <h2 class="text-xl">Alfamart KW Super - Cabang Pemalang</h2>
            <p class="text-xs mt-2">Dicetak: <?= date('d F Y') ?></p>
        </div>

        <div class="flex-1 overflow-y-auto p-8">
            
            <div class="ai-alert-section grid grid-cols-1 gap-4 mb-8">
                <?php
                $cek_barang = mysqli_query($koneksi, "SELECT id, nama_barang, stok FROM barang");
                $ada_warning = false;

                while ($b = mysqli_fetch_assoc($cek_barang)) {
                    $analisa = $ai->cekStokKritis($b['id']);
                    if ($analisa != null && $analisa['level'] == 'danger') {
                        $ada_warning = true;
                        echo '
                        <div class="bg-white border-l-4 border-red-500 rounded-xl shadow-sm p-6 flex items-start transition hover:shadow-md">
                            <div class="p-3 bg-red-50 rounded-full text-red-500 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <h3 class="font-bold text-gray-800 text-lg">Critical Stock Alert</h3>
                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded font-bold">WARNING</span>
                                </div>
                                <p class="text-gray-600 mt-1 mb-3 leading-relaxed">'.$analisa['pesan'].'</p>
                                
                                <button onclick="bukaModalRestock('.$b['id'].', \''.$b['nama_barang'].'\', '.$b['stok'].')" class="bg-red-600 text-white text-xs font-bold px-4 py-2 rounded hover:bg-red-700 transition flex items-center">
                                    + Restock Sekarang
                                </button>
                            </div>
                        </div>
                        ';
                    }
                }
                
                if (!$ada_warning) {
                    echo '<div class="bg-green-50 border border-green-200 rounded-xl p-6 flex items-center shadow-sm"><div class="text-green-500 mr-3"></div><div class="text-green-700"><b>System Healthy:</b> Semua stok aman terkendali.</div></div>';
                }
                ?>
            </div>
      <div class="mt-8 text-center">
        <?php include '../components/footer.php'; ?>
    </div>
            <div class="card-tabel bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 no-print">
                    <h3 class="font-bold text-gray-700 text-lg">Data Inventaris Real-time</h3>
                    <button onclick="window.print()" class="text-blue-600 hover:text-blue-800 text-sm font-bold flex items-center bg-white px-3 py-2 rounded-lg border border-blue-200 shadow-sm transition">
                        Cetak Laporan
                    </button>
                </div>
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-white">
                            <th class="px-6 py-4">Nama Produk</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-center">Status Stok</th>
                            <th class="px-6 py-4 text-right">Harga</th>
                            <th class="px-6 py-4 text-center no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php
                        $q = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY stok ASC");
                        while ($row = mysqli_fetch_assoc($q)) :
                            $stok = $row['stok'];
                            if($stok <= 0) $status_cls = "bg-red-100 text-red-700";
                            elseif($stok < 20) $status_cls = "bg-yellow-100 text-yellow-700";
                            else $status_cls = "bg-green-100 text-green-700";
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-700"><?= $row['nama_barang'] ?></td>
                            <td class="px-6 py-4 text-gray-500"><?= $row['kategori'] ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?= $status_cls ?>">
                                    <?= $stok ?> Unit
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-600">Rp <?= number_format($row['harga']) ?></td>
                            <td class="px-6 py-4 text-center no-print">
                                <button onclick="bukaModalRestock(<?= $row['id'] ?>, '<?= $row['nama_barang'] ?>', <?= $row['stok'] ?>)" class="text-blue-600 hover:text-blue-800 font-bold text-xs border border-blue-200 px-2 py-1 rounded">
                                    + Stok
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalRestock" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white w-96 rounded-2xl shadow-2xl p-6 transform transition-all scale-100">
            <h3 class="text-xl font-bold text-gray-800 mb-1">Restock Barang</h3>
            <p class="text-sm text-gray-500 mb-4">Menambah stok untuk: <b id="namaBarangModal" class="text-blue-600">...</b></p>
            
            <form action="" method="POST">
                <input type="hidden" name="id_barang" id="idBarangModal">
                
                <div class="bg-blue-50 p-3 rounded-lg mb-4 text-sm flex justify-between items-center border border-blue-100">
                    <div class="text-center">
                        <span class="block text-gray-400 text-xs">Awal</span>
                        <span id="stokAwalText" class="font-bold text-gray-700 text-lg">0</span>
                    </div>
                    <div class="text-gray-400 font-bold">+</div>
                    <div class="text-center">
                        <span class="block text-gray-400 text-xs">Tambah</span>
                        <span id="stokInputText" class="font-bold text-blue-600 text-lg">0</span>
                    </div>
                    <div class="text-gray-400 font-bold">=</div>
                    <div class="text-center">
                        <span class="block text-gray-400 text-xs">Total</span>
                        <span id="stokTotalText" class="font-bold text-green-600 text-lg">0</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jumlah Tambahan</label>
                    <input type="number" name="tambah_stok" id="inputTambahStok" 
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-lg" 
                        placeholder="0" required min="1" onkeyup="hitungTotal()">
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="tutupModal()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg font-bold hover:bg-gray-200">Batal</button>
                    <button type="submit" name="btn_simpan_stok" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg shadow-blue-200">
                        Simpan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let stokSaatIni = 0;

        function bukaModalRestock(id, nama, stok) {
            document.getElementById('modalRestock').classList.remove('hidden');
            document.getElementById('idBarangModal').value = id;
            document.getElementById('namaBarangModal').innerText = nama;
            
          
            stokSaatIni = parseInt(stok);
            document.getElementById('stokAwalText').innerText = stokSaatIni;
            
          
            document.getElementById('inputTambahStok').value = '';
            document.getElementById('stokInputText').innerText = '0';
            document.getElementById('stokTotalText').innerText = stokSaatIni;
        }

        function hitungTotal() {
            let inputVal = document.getElementById('inputTambahStok').value;
            let tambah = inputVal ? parseInt(inputVal) : 0;
            let total = stokSaatIni + tambah;
            
            document.getElementById('stokInputText').innerText = tambah;
            document.getElementById('stokTotalText').innerText = total;
        }

        function tutupModal() {
            document.getElementById('modalRestock').classList.add('hidden');
        }
    </script>

</body>
</html>