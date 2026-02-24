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
$saran_ai = null;


if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $qStok = mysqli_query($koneksi, "SELECT stok FROM barang WHERE id='$id'");
    $dStok = mysqli_fetch_assoc($qStok);
    if ($dStok['stok'] > 0) {
        $qty_di_keranjang = isset($_SESSION['keranjang'][$id]) ? $_SESSION['keranjang'][$id] : 0;
        if ($qty_di_keranjang < $dStok['stok']) {
            if (isset($_SESSION['keranjang'][$id])) $_SESSION['keranjang'][$id]++;
            else $_SESSION['keranjang'][$id] = 1;
        }
    }
}


if (isset($_GET['kurang'])) {
    $id = (int)$_GET['kurang'];
    if (isset($_SESSION['keranjang'][$id])) {
        if ($_SESSION['keranjang'][$id] > 1) $_SESSION['keranjang'][$id]--;
        else unset($_SESSION['keranjang'][$id]);
    }
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    unset($_SESSION['keranjang'][$id]);
}

if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}


$where_clause = "WHERE 1=1";

if (isset($_GET['cari']) && !empty($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);

    $where_clause .= " AND (nama_barang LIKE '%$keyword%' OR id = '$keyword')";
}

if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $kat = mysqli_real_escape_string($koneksi, $_GET['kategori']);
    $where_clause .= " AND kategori = '$kat'";
}


$query = mysqli_query($koneksi, "SELECT * FROM barang $where_clause");


if (isset($_GET['cari']) && mysqli_num_rows($query) == 1) {
    $item_otomatis = mysqli_fetch_assoc($query);
    $id_auto = $item_otomatis['id'];


    if ($item_otomatis['stok'] > 0) {

        $stok_db = $item_otomatis['stok'];
        $qty_skrg = isset($_SESSION['keranjang'][$id_auto]) ? $_SESSION['keranjang'][$id_auto] : 0;

        if ($qty_skrg < $stok_db) {
            if (!isset($_SESSION['keranjang'][$id_auto])) {
                $_SESSION['keranjang'][$id_auto] = 1;
            } else {
                $_SESSION['keranjang'][$id_auto]++;
            }
        }


        header("Location: index.php");
        exit;
    }
}


$keranjang_sekarang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
$saran_ai = $ai->scanKeranjang($keranjang_sekarang);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="icon" href="../assets/images/logo_toko.png" type="image/png">
    <title>Kasir - Point of Sale</title>
</head>

<body class="bg-gray-100 text-gray-800 h-screen flex overflow-hidden">

    <?php include '../components/sidebar.php'; ?>

    <main class="flex-1 flex flex-col h-full overflow-hidden relative transition-all duration-300" id="mainArea">
        <?php
        $pageTitle = "Point of Sale";
        include '../components/navbar.php';
        ?>

        <div class="px-8 pt-6 pb-2">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-wrap gap-4 justify-between items-center">

                <form action="" method="GET" class="flex flex-1 gap-4 items-center">

                    <div class="relative flex-1 max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </span>

                        <input type="text" name="cari"
                            id="searchBar"
                            value="<?= isset($_GET['cari']) ? $_GET['cari'] : '' ?>"
                            class="w-full py-2 pl-10 pr-4 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-mono"
                            placeholder="Scan Barcode / Cari..."

                            autocomplete="off"
                            onblur="this.focus()">
                    </div>

                    <select name="kategori" onchange="this.form.submit()" class="py-2 px-4 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer hidden md:block">
                        <option value="">Semua Kategori</option>
                        <option value="Makanan" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Makanan') ? 'selected' : '' ?>>Makanan</option>
                        <option value="Minuman" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Minuman') ? 'selected' : '' ?>>Minuman</option>
                        <option value="Bahan" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Bahan') ? 'selected' : '' ?>>Bahan Dapur</option>
                        <option value="Cemilan" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Cemilan') ? 'selected' : '' ?>>Cemilan</option>
                        <option value="Peralatan" <?= (isset($_GET['kategori']) && $_GET['kategori'] == 'Peralatan') ? 'selected' : '' ?>>Peralatan</option>
                    </select>

                    <?php if (isset($_GET['cari']) || isset($_GET['kategori'])): ?>
                        <a href="index.php" class="text-red-500 hover:text-red-700 text-sm font-bold flex items-center">
                            <svg class="w-6 h-6  dark:text-white text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16 10 3-3m0 0-3-3m3 3H5v3m3 4-3 3m0 0 3 3m-3-3h14v-3" />
                            </svg>

                        </a>
                    <?php endif; ?>
                </form>

                <a href="../halaman.php" target="_blank" class="hidden md:flex items-center space-x-2 bg-indigo-50 text-indigo-600 px-3 py-2 rounded-lg hover:bg-indigo-100 transition font-bold text-sm border border-indigo-100" title="Buka di Layar Kedua">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span>Layar</span>
                </a>

                <button onclick="toggleSidebar()" class="flex items-center space-x-2 bg-blue-100 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-200 transition font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>(<?= count($_SESSION['keranjang'] ?? []) ?>)</span>
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-8 pt-2">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <?php

                if (isset($_GET['cari']) && mysqli_num_rows($query) == 0) {
                    echo '
                    <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                        <div class="bg-red-50 text-red-500 p-4 rounded-full mb-3">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Barang Tidak Ditemukan</h3>
                        <p class="text-gray-500">Kode Barcode/Nama "<b>' . htmlspecialchars($_GET['cari']) . '</b>" tidak ada di sistem.</p>
                        <a href="index.php" class="mt-4 px-4 py-2 bg-gray-200 rounded-lg font-bold text-gray-600 hover:bg-gray-300 transition">Reset Pencarian</a>
                    </div>';
                } elseif (mysqli_num_rows($query) > 0) {
                    while ($item = mysqli_fetch_assoc($query)) :
                        $habis = ($item['stok'] <= 0);

                        $class_habis = $habis ? "grayscale opacity-75 cursor-not-allowed" : "hover:shadow-lg hover:border-blue-400 cursor-pointer transform hover:-translate-y-1";
                        $link = $habis ? "#" : "index.php?add=" . $item['id'] . (isset($_GET['cari']) ? "&cari=" . $_GET['cari'] : "") . (isset($_GET['kategori']) ? "&kategori=" . $_GET['kategori'] : "");

                        $gambar = $item['gambar'];
                        $path_gambar = "../assets/images/" . $gambar;
                        $pakai_gambar = !empty($gambar) && file_exists($path_gambar);
                ?>

                        <a href="<?= $link ?>" class="group bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-200 overflow-hidden flex flex-col h-full <?= $class_habis ?>">
                            <div class="h-32 w-full bg-gray-100 relative overflow-hidden">
                                <?php if ($pakai_gambar) : ?>
                                    <img src="<?= $path_gambar ?>" alt="<?= $item['nama_barang'] ?>" class="w-full h-full object-cover transition duration-300">
                                <?php else : ?>
                                    <div class="w-full h-full flex items-center justify-center text-blue-200 group-hover:bg-blue-50 transition">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>

                                <span class="absolute top-2 right-2 bg-white bg-opacity-90 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm">
                                    <?= $item['kategori'] ?>
                                </span>
                            </div>

                            <div class="p-3 flex flex-col flex-1 justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm leading-tight mb-1 line-clamp-2 h-10 group-hover:text-blue-600 transition">
                                        <?= $item['nama_barang'] ?>
                                    </h3>
                                </div>

                                <div class="flex justify-between items-end mt-2 border-t border-gray-100 pt-2">
                                    <div>
                                        <p class="text-xs text-gray-400">Harga</p>
                                        <p class="text-blue-600 font-bold">Rp <?= number_format($item['harga']) ?></p>
                                    </div>
                                    <div class="text-right">
                                        <?php if ($habis): ?>
                                            <span class="text-[10px] font-bold text-white bg-red-500 px-2 py-1 rounded">HABIS</span>
                                        <?php else: ?>
                                            <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded group-hover:bg-blue-100 group-hover:text-blue-600 transition">Stok: <?= $item['stok'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                <?php
                    endwhile;
                }
                ?>
            </div>


        </div>
    </main>

    <aside id="cartSidebar" class="w-96 bg-white border-l border-gray-200 flex flex-col shadow-2xl z-20 transition-all duration-300 transform translate-x-0 fixed right-0 h-full">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Current Order
            </h2>
            <button onclick="toggleSidebar()" class="text-gray-400 hover:text-red-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <?php
            $total_bayar = 0;
            if (!empty($_SESSION['keranjang'])) :
                foreach ($_SESSION['keranjang'] as $id_brg => $qty) :
                    $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id='$id_brg'");
                    $d = mysqli_fetch_assoc($q);
                    $subtotal = $d['harga'] * $qty;
                    $total_bayar += $subtotal;
            ?>
                    <div class="flex justify-between items-center group bg-gray-50 p-2 rounded-lg border border-gray-100 shadow-sm">
                        <div class="flex-1">
                            <div class="text-sm font-bold text-gray-800"><?= $d['nama_barang'] ?></div>
                            <div class="text-xs text-gray-400">Rp <?= number_format($d['harga']) ?></div>
                        </div>

                        <div class="flex items-center space-x-2 bg-white rounded-lg border border-gray-200">
                            <a href="index.php?kurang=<?= $id_brg ?>" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-red-50 hover:text-red-500 font-bold transition">-</a>
                            <span class="text-sm font-bold w-6 text-center"><?= $qty ?></span>
                            <a href="index.php?add=<?= $id_brg ?>" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-blue-50 hover:text-blue-500 font-bold transition">+</a>
                        </div>

                        <div class="text-sm font-bold text-gray-700 w-24 text-right">Rp <?= number_format($subtotal) ?></div>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>

        <?php if ($saran_ai) : ?>
            <div class="px-6 pb-4">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-4 relative overflow-hidden shadow-sm">
                    <div class="flex items-start relative z-10">
                        <div class="text-blue-600 mr-3 mt-1"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19,9l1.25-2.75L23,5l-2.75-1.25L19,1l-1.25,2.75L15,5l2.75,1.25L19,9z M11.5,9.5L9,4L6.5,9.5L1,12l5.5,2.5L9,20l2.5-5.5L17,12L11.5,9.5z" />
                            </svg></div>
                        <div>
                            <h3 class="text-xs font-bold text-blue-800 uppercase tracking-wide mb-1">Smart Suggestion</h3>
                            <p class="text-sm text-gray-700 leading-snug mb-3"><?= $saran_ai['pesan'] ?></p>
                            <?php
                            $nama_rek = $saran_ai['nama_barang'];
                            $qRek = mysqli_query($koneksi, "SELECT id FROM barang WHERE nama_barang = '$nama_rek'");
                            $dRek = mysqli_fetch_assoc($qRek);
                            ?>
                            <?php if ($dRek): ?>
                                <a href="index.php?add=<?= $dRek['id'] ?>" class="inline-flex items-center justify-center w-full py-2 bg-white border border-blue-200 rounded-lg text-blue-700 text-xs font-bold hover:bg-blue-50 transition shadow-sm">+ Tambahkan Item</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="p-6 bg-white border-t border-gray-100">
            <div class="flex justify-between items-end mb-4">
                <span class="text-gray-500 text-sm font-medium">Total Tagihan</span>
                <span class="text-3xl font-bold text-gray-800">Rp <?= number_format($total_bayar ?? 0) ?></span>
            </div>

            <form action="../logic/proses_bayar.php" method="POST" onsubmit="return bersihkanRupiah()">
                <div class="mb-3 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold">Rp</span>
                    </div>
                    <input type="text" id="input_tunai" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-bold text-gray-700 text-lg shadow-sm"
                        placeholder="0" onkeyup="formatRupiah(this)">
                    <input type="hidden" name="bayar_tunai" id="bayar_tunai_asli">
                </div>

                <button type="submit" class="flex items-center justify-center w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200 hover:-translate-y-1 transform">
                    BAYAR & CETAK
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('cartSidebar');
            const main = document.getElementById('mainArea');
            if (sidebar.classList.contains('translate-x-full')) {
                sidebar.classList.remove('translate-x-full');
                main.classList.add('mr-96');
            } else {
                sidebar.classList.add('translate-x-full');
                main.classList.remove('mr-96');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('cartSidebar');
            const main = document.getElementById('mainArea');
            sidebar.classList.remove('translate-x-full');
            main.classList.add('mr-96');
        });

        function formatRupiah(input) {
            let angka = input.value.replace(/\D/g, '');
            let formatted = new Intl.NumberFormat('id-ID').format(angka);
            input.value = formatted;
            document.getElementById('bayar_tunai_asli').value = angka;
        }

        function bersihkanRupiah() {
            let total = <?= $total_bayar ?>;
            let bayar = document.getElementById('bayar_tunai_asli').value;
            if (bayar < total) {
                alert("Uang tunai kurang! Total: " + total + ", Bayar: " + bayar);
                return false;
            }
            return true;
        }
    </script>
</body>

</html>