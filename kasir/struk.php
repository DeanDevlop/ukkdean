<?php
include '../config/koneksi.php';
include '../components/auth_check.php'; 


$id = $_GET['id'];


$query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id = '$id'");
$trx   = mysqli_fetch_assoc($query);


$id_user = $trx['id_user'];
$qUser   = mysqli_query($koneksi, "SELECT nama_lengkap FROM users WHERE id='$id_user'");
$dUser   = mysqli_fetch_assoc($qUser);
$nama_kasir = $dUser['nama_lengkap'] ?? 'Admin';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #<?= $id ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; font-size: 12px; max-width: 300px; margin: 0 auto; color: #000; }
        .header { text-align: center; margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .info { margin-bottom: 10px; }
        .item { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .total-section { border-top: 1px dashed #000; margin-top: 10px; padding-top: 5px; }
        .footer { text-align: center; margin-top: 15px; font-size: 10px; }
        .btn-print { margin-top: 20px; display: block; width: 100%; padding: 10px; background: #000; color: #fff; text-align: center; text-decoration: none; font-weight: bold; cursor: pointer; }
        
        @media print {
            .btn-print, .no-print { display: none; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2 style="margin:0;">Toko Dean Super</h2>
        <p style="margin:2px;">JL. SUGIHWARAS NO. 45, PEMALANG</p>
        <p style="margin:0;">NPWP: 01.234.567.8-000</p>
    </div>

    <div class="info">
        <div>Bon: <?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></div>
        <div>Kasir: <?= $nama_kasir ?></div>
        <div><?= date('d.m.y - H:i', strtotime($trx['tgl_transaksi'])) ?></div>
    </div>

    <div style="border-bottom: 1px dashed #000; margin-bottom: 5px;"></div>

    <?php
    $qDetail = mysqli_query($koneksi, "SELECT d.*, b.nama_barang 
                                       FROM detail_transaksi d 
                                       JOIN barang b ON d.id_barang = b.id 
                                       WHERE d.id_transaksi = '$id'");
    while ($item = mysqli_fetch_assoc($qDetail)) {
    ?>
    <div style="margin-bottom: 5px;">
        <div style="font-weight:bold;"><?= strtoupper($item['nama_barang']) ?></div>
        <div class="item">
            <span><?= $item['qty'] ?> x <?= number_format($item['subtotal'] / $item['qty'], 0, ',', '.') ?></span>
            <span><?= number_format($item['subtotal'], 0, ',', '.') ?></span>
        </div>
    </div>
    <?php } ?>
    
    <div class="total-section">
        <div class="item" style="font-weight: bold; font-size: 14px;">
            <span>Total :</span>
            <span><?= number_format($trx['total_bayar'], 0, ',', '.') ?></span>
        </div>
        
        <div class="item">
            <span>Tunai :</span>
            <span><?= number_format($trx['bayar'], 0, ',', '.') ?></span>
        </div>
        <div class="item">
            <span>Kembali :</span>
            <span><?= number_format($trx['kembalian'], 0, ',', '.') ?></span>
        </div>
    </div>

    <div style="text-align: center; margin-top: 10px;">
        PPN TERMASUK<br>
        LAYANAN KONSUMEN SMS:<br>
        0896-6564-0209
    </div>

    <div class="footer">
        *** TERIMA KASIH ***<br>
        SELAMAT BELANJA KEMBALI
    </div>

    <a href="index.php" class="btn-print">KEMBALI KE KASIR</a>

</body>
</html>