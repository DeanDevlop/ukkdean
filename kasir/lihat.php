<?php
session_start();
include '../config/koneksi.php';
include '../logic/logsistem.php';
include '../components/auth_check.php';

if ($_SESSION['role'] != 'kasir') {
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
    <title>Lihat Stok</title>
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

    <?php include '../components/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        
        <?php 
            $pageTitle = "Lihat Stok Barang & Harga"; 
            include '../components/navbar.php'; 
        ?>


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
                        <div class="bg-white   rounded-xl shadow-sm p-6 flex items-start transition hover:shadow-md">
                            <div class="p-3 bg-red-50 rounded-full text-red-500 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between">
                                    <h3 class="font-bold text-gray-800 text-lg">Critical Stock Alert</h3>
                                    <span class="bg-red-100 text-red-700 text-xs px-2 py-1 rounded font-bold">WARNING</span>
                                </div>
                                <p class="text-gray-600 mt-1 mb-3 leading-relaxed">'.$analisa['pesan'].'</p>
                                
                              
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
                    <h3 class="font-bold text-gray-700 text-lg">Stok & Harga Barang</h3>
                   
                </div>
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-white">
                            <th class="px-6 py-4">Nama Produk</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-center">Status Stok</th>
                            <th class="px-6 py-4 text-right">Harga</th>
                          
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
                          
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>


    </div>
   </div>


</body>
</html>