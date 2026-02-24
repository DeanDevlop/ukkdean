<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include '../config/koneksi.php';

$total_bayar = 0;

if (!empty($_SESSION['keranjang'])) {
    
    $keranjang = array_reverse($_SESSION['keranjang'], true);

    echo '<table class="w-full text-left">';
    echo '<thead class="text-gray-400 border-b border-gray-200 text-sm uppercase">
            <tr>
                <th class="py-3 font-semibold">Nama Barang</th>
                <th class="py-3 font-semibold text-center">Qty</th>
                <th class="py-3 font-semibold text-right">Subtotal</th>
            </tr>
          </thead>';
    echo '<tbody class="divide-y divide-gray-100">';

    foreach ($keranjang as $id_brg => $qty) {
        $q = mysqli_query($koneksi, "SELECT * FROM barang WHERE id='$id_brg'");
        $d = mysqli_fetch_assoc($q);
        $subtotal = $d['harga'] * $qty;
        $total_bayar += $subtotal;
?>
    <tr class="group animate-pulse-once"> <td class="py-4 align-top">
            <p class="font-bold text-gray-800 text-lg"><?= $d['nama_barang'] ?></p>
            <p class="text-gray-400 text-sm">@ Rp <?= number_format($d['harga']) ?></p>
        </td>
        <td class="py-4 text-center align-top">
            <span class="bg-gray-100 text-gray-700 font-bold px-3 py-1 rounded-lg text-sm">
                <?= $qty ?>x
            </span>
        </td>
        <td class="py-4 text-right font-bold text-gray-800 text-lg align-top">
            Rp <?= number_format($subtotal) ?>
        </td>
    </tr>
<?php 
    } 
    echo '</tbody></table>';
} else {
   
    echo '
    <div class="flex flex-col items-center justify-center h-full text-gray-400 mt-20">
        <div class="w-24 h-24 mb-4 bg-gray-100 rounded-full flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="text-xl font-medium text-gray-500">Siap Melayani Pelanggan</p>
      
    </div>';
}
?>

<input type="hidden" id="server_total" value="Rp <?= number_format($total_bayar) ?>">