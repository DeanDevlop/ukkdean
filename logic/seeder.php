<?php

$conn = mysqli_connect("localhost", "root", "", "db_kasir");


$items = [
    ['Indomie Goreng', 3500, 'Makanan'], ['Telur Ayam', 2000, 'Bahan'], 
    ['Minyak Goreng', 14000, 'Bahan'], ['Kecap Manis', 10000, 'Bumbu'],
    ['Roti Tawar', 12000, 'Makanan'], ['Selai Coklat', 15000, 'Makanan'],
    ['Kopi Hitam', 5000, 'Minuman'], ['Gula Pasir', 12000, 'Bumbu'],
    ['Susu UHT', 6000, 'Minuman'], ['Biskuit Kelapa', 8000, 'Cemilan'],
    ['Sabun Mandi', 4000, 'Peralatan'], ['Shampo', 1000, 'Peralatan'],
    ['Pasta Gigi', 12000, 'Peralatan'], ['Sikat Gigi', 5000, 'Peralatan'],
    ['Air Mineral', 3000, 'Minuman'], ['Teh Kotak', 4000, 'Minuman'],
    ['Chiki Balls', 2000, 'Cemilan'], ['Kacang Garuda', 9000, 'Cemilan'],
    ['Tepung Terigu', 11000, 'Bahan'], ['Margarin', 7000, 'Bahan']
];


mysqli_query($conn, "TRUNCATE TABLE barang");
mysqli_query($conn, "TRUNCATE TABLE transaksi");
mysqli_query($conn, "TRUNCATE TABLE detail_transaksi");

foreach ($items as $item) {
    mysqli_query($conn, "INSERT INTO barang (nama_barang, harga, stok, kategori) VALUES ('$item[0]', '$item[1]', 100, '$item[2]')");
}

echo "✅ Barang berhasil direset.<br>";

for ($i = 1; $i <= 100; $i++) {
   
    $hari_lalu = rand(0, 30);
    $tgl = date('Y-m-d H:i:s', strtotime("-$hari_lalu days"));
    
   
    mysqli_query($conn, "INSERT INTO transaksi (tgl_transaksi, total_bayar) VALUES ('$tgl', 0)");
    $id_transaksi = mysqli_insert_id($conn);
    
   
    $skenario = rand(1, 10);
    $barang_beli = [];

    if ($skenario <= 4) { 
       
        $barang_beli[] = 1; 
        $barang_beli[] = 2; 
    } elseif ($skenario <= 7) {
        
        $barang_beli[] = 5; 
        $barang_beli[] = 6; 
    } else {
       
        $barang_beli[] = rand(1, 20);
        $barang_beli[] = rand(1, 20);
    }

   
    $total = 0;
    foreach ($barang_beli as $id_brg) {
       
        $qHarga = mysqli_query($conn, "SELECT harga FROM barang WHERE id = $id_brg");
        $dHarga = mysqli_fetch_assoc($qHarga);
        $harga = $dHarga['harga'];
        $qty = rand(1, 3);
        $subtotal = $harga * $qty;
        
        mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_barang, qty, subtotal) VALUES ('$id_transaksi', '$id_brg', '$qty', '$subtotal')");
        
      
        mysqli_query($conn, "UPDATE barang SET stok = stok - $qty WHERE id = $id_brg");
        $total += $subtotal;
    }

  
    mysqli_query($conn, "UPDATE transaksi SET total_bayar = $total WHERE id = $id_transaksi");
}

echo "✅ 100 Data Transaksi Dummt & Pola AI berhasil dibuat!";
?>