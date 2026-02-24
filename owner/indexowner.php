<?php
session_start();
include '../config/koneksi.php';
include '../components/auth_check.php';


if ($_SESSION['role'] != 'owner') {
    header("Location: ../login.php");
    exit;
}


$tgl_ini = date('Y-m-d');
$bln_ini = date('m');

$dHari = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE(tgl_transaksi) = '$tgl_ini'"));
$omset_hari = $dHari['total'] ?? 0;

$dBulan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi WHERE MONTH(tgl_transaksi) = '$bln_ini'"));
$omset_bulan = $dBulan['total'] ?? 0;


$dBarang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang"));
$total_barang = $dBarang['total'];


$jam_skrg = date('G');
$sapaan = ($jam_skrg < 11) ? "Selamat Pagi" : (($jam_skrg < 15) ? "Selamat Siang" : "Selamat Sore");


$qKritis = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang WHERE stok <= 10");
$dKritis = mysqli_fetch_assoc($qKritis);
$jml_kritis = $dKritis['total'];


if ($jml_kritis > 0) {
    $pesan_ai = "<b>PERHATIAN BOS!</b> Ada <b>$jml_kritis barang</b> yang stoknya menipis (di bawah 10). Cek tabel stok di bawah dan segera belanja ulang.";
    $icon_ai = '<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
    $bg_icon = "bg-red-100";
} elseif ($omset_hari > 1000000) {
    $pesan_ai = "Luar biasa! Omset hari ini tembus <b>Rp " . number_format($omset_hari/1000) . " Ribu</b>. Toko sedang ramai, pertahankan performa ini!";
    $icon_ai = '<svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>';
    $bg_icon = "bg-green-100";
} else {
    $pesan_ai = "Operasional toko berjalan normal. Stok aman dan kasir standby. Jangan lupa cek laporan berkala.";
}


$where_stok = "WHERE 1=1";
if (isset($_GET['cari_stok']) && !empty($_GET['cari_stok'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari_stok']);
    $where_stok .= " AND nama_barang LIKE '%$keyword%'";
}
if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $kat = mysqli_real_escape_string($koneksi, $_GET['kategori']);
    $where_stok .= " AND kategori = '$kat'";
}



$labels_tgl = []; $data_omset = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl_cek = date('Y-m-d', strtotime("-$i days"));
    $qChart = mysqli_query($koneksi, "SELECT SUM(total_bayar) as total FROM transaksi WHERE DATE(tgl_transaksi) = '$tgl_cek'");
    $dChart = mysqli_fetch_assoc($qChart);
    $labels_tgl[] = date('d M', strtotime($tgl_cek));
    $data_omset[] = $dChart['total'] ?? 0;
}

$labels_produk = []; $data_qty = [];
$qTop = mysqli_query($koneksi, "SELECT b.nama_barang, SUM(d.qty) as terjual FROM detail_transaksi d JOIN barang b ON d.id_barang = b.id GROUP BY d.id_barang ORDER BY terjual DESC LIMIT 5");
while($top = mysqli_fetch_assoc($qTop)){
    $labels_produk[] = $top['nama_barang'];
    $data_qty[] = $top['terjual'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Owner</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 h-screen flex overflow-hidden font-sans">

    <?php include '../components/sideowner.php'; ?>

    <main class="flex-1 flex flex-col h-full overflow-y-auto relative transition-all duration-300">
        
        <div class="bg-white px-8 py-5 border-b border-gray-200 flex justify-between items-center sticky top-0 z-10">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Dashboard Owner</h1>
                <p class="text-sm text-gray-400">Monitoring & Kontrol Bisnis</p>
            </div>
            <div class="bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-bold text-sm border border-blue-100 flex items-center shadow-sm">
               <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
               <?= date('d F Y') ?>
            </div>
        </div>

             <div class="mt-4 text-center">
        <?php include '../components/footer.php'; ?>
    </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between hover:border-blue-300 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Omset Hari Ini</p>
                        <h2 class="text-2xl font-bold text-gray-800">Rp <?= number_format($omset_hari) ?></h2>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between hover:border-blue-300 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Omset Bulan Ini</p>
                        <h2 class="text-2xl font-bold text-gray-800">Rp <?= number_format($omset_bulan) ?></h2>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg text-blue-600">
                       <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between hover:border-blue-300 transition">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total SKU Barang</p>
                        <h2 class="text-2xl font-bold text-gray-800"><?= $total_barang ?> Item</h2>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg> 
                        Tren Penjualan (7 Hari)
                    </h3>
                    <div class="relative h-64 w-full"><canvas id="chartOmset"></canvas></div>
                </div>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="font-bold text-gray-700 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg> 
                        Top 5 Produk
                    </h3>
                    <div class="relative h-64 w-full flex justify-center"><canvas id="chartProduk"></canvas></div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex flex-wrap justify-between items-center bg-gray-50 gap-2">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Pantau Stok
                        </h3>
                        
                        <form action="" method="GET" class="flex gap-2">
                            <select name="kategori" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded px-2 py-1 outline-none">
                                <option value="">Semua Kategori</option>
                                <option value="Makanan" <?= (isset($_GET['kategori']) && $_GET['kategori']=='Makanan') ? 'selected' : '' ?>>Makanan</option>
                                <option value="Minuman" <?= (isset($_GET['kategori']) && $_GET['kategori']=='Minuman') ? 'selected' : '' ?>>Minuman</option>
                                <option value="Bahan" <?= (isset($_GET['kategori']) && $_GET['kategori']=='Bahan') ? 'selected' : '' ?>>Bahan</option>
                                <option value="Peralatan" <?= (isset($_GET['kategori']) && $_GET['kategori']=='Peralatan') ? 'selected' : '' ?>>Peralatan</option>
                            </select>
                            <input type="text" name="cari_stok" placeholder="Cari..." value="<?= $_GET['cari_stok'] ?? '' ?>" class="text-xs border border-gray-300 rounded px-2 py-1 outline-none w-24">
                        </form>
                    </div>
                    
                    <div class="overflow-y-auto max-h-80"> 
                        <table class="w-full text-left">
                            <thead class="bg-white text-gray-500 text-xs uppercase font-bold sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 border-b bg-gray-50">Barang</th>
                                    <th class="px-6 py-3 border-b bg-gray-50 text-center">Sisa</th>
                                    <th class="px-6 py-3 border-b bg-gray-50 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php
                              
                              
                                $qStok = mysqli_query($koneksi, "SELECT * FROM barang $where_stok ORDER BY stok ASC LIMIT 10");
                                
                                if(mysqli_num_rows($qStok) > 0) {
                                    while($b = mysqli_fetch_assoc($qStok)):
                                        if($b['stok'] <= 10) { 
                                            $bg_stok = 'bg-red-50 text-red-700 border-red-200'; 
                                            $label = 'KRITIS';
                                        } elseif($b['stok'] <= 30) {
                                            $bg_stok = 'bg-yellow-50 text-yellow-700 border-yellow-200'; 
                                            $label = 'Waspada';
                                        } else {
                                            $bg_stok = 'bg-green-50 text-green-700 border-green-200';
                                            $label = 'Aman';
                                        }
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 font-medium text-gray-700">
                                        <?= $b['nama_barang'] ?>
                                        <div class="text-[10px] text-gray-400">Rp <?= number_format($b['harga']) ?> • <?= $b['kategori'] ?></div>
                                    </td>
                                    <td class="px-6 py-3 text-center font-bold text-gray-800"><?= $b['stok'] ?></td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="px-2 py-1 text-[10px] rounded-full border font-bold <?= $bg_stok ?>">
                                            <?= $label ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; 
                                } else {
                                    echo "<tr><td colspan='3' class='text-center py-4 text-gray-400 text-xs'>Barang tidak ditemukan</td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                             Akun Pegawai
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full ">
                            <thead class="bg-white text-gray-500 text-xs uppercase font-bold sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 border-b bg-gray-50 text-left">Nama</th>
                                    <th class="px-6 py-3 border-b bg-gray-50 text-right">Role</th>
                                   
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php
                                $qUser = mysqli_query($koneksi, "SELECT * FROM users WHERE role != 'owner' ORDER BY last_login DESC");
                                while($u = mysqli_fetch_assoc($qUser)):
                                   
                                ?>
                                <tr class="hover:bg-gray-50 ">
                                    <td class="px-6 py-3 font-bold text-gray-700 text-left">
                                        <?= $u['nama_lengkap'] ?>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <span class="px-2 py-1 text-[10px] font-bold text-gray-500 border rounded bg-gray-50"><?= strtoupper($u['role']) ?></span>
                                    </td>
                                  
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> </div>
            
    </main>

    <script>
        const ctxOmset = document.getElementById('chartOmset').getContext('2d');
        new Chart(ctxOmset, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels_tgl) ?>,
                datasets: [{
                    label: 'Omset (Rp)',
                    data: <?= json_encode($data_omset) ?>,
                    borderColor: '#3B82F6', backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2, tension: 0.4, fill: true, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#3B82F6'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { borderDash: [2, 4] }, ticks: { callback: function(value) { return 'Rp ' + value/1000 + 'k'; } } }, x: { grid: { display: false } } } }
        });

        const ctxProduk = document.getElementById('chartProduk').getContext('2d');
        new Chart(ctxProduk, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($labels_produk) ?>,
                datasets: [{
                    data: <?= json_encode($data_qty) ?>,
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                    borderWidth: 0, hoverOffset: 4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } } }, cutout: '70%' }
        });
    </script>
</body>
</html>