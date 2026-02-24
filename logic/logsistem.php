<?php


class AiEngine {
    private $db;

    public function __construct($koneksi) {
        $this->db = $koneksi;
    }

    
    public function scanKeranjang($keranjang_session) {
        if (empty($keranjang_session)) return null;


        $daftar_barang = array_keys($keranjang_session);
        $daftar_barang = array_reverse($daftar_barang); 

        foreach ($daftar_barang as $id_barang) {
          
            $saran = $this->cariPasangan($id_barang, $keranjang_session);
            
          
       
            if ($saran) {
                return $saran;
            }
        }
        
        return null;
    }

    private function cariPasangan($id_barang_input, $keranjang_saat_ini) {

        $query = "
            SELECT b.id, b.nama_barang, COUNT(*) as frekuensi
            FROM detail_transaksi d1
            JOIN detail_transaksi d2 ON d1.id_transaksi = d2.id_transaksi
            JOIN barang b ON d2.id_barang = b.id
            WHERE d1.id_barang = '$id_barang_input' 
            AND d2.id_barang != '$id_barang_input'
            GROUP BY d2.id_barang, b.id, b.nama_barang 
            ORDER BY frekuensi DESC
            LIMIT 1
        ";

        $result = mysqli_query($this->db, $query);
        
        
        if (!$result) return null;

        $data = mysqli_fetch_assoc($result);

        if ($data) { 
            $id_saran = $data['id'];

         
            if (!isset($keranjang_saat_ini[$id_saran])) {
                return [
                    'status' => true,
                    'pesan' => "Pelanggan beli <b>" . $this->getNamaBarang($id_barang_input) . "</b>. <br>Biasanya bisa di komboin <b>" . $data['nama_barang'] . "</b> coba tawarkan",
                    'nama_barang' => $data['nama_barang']
                ];
            }
        }
        return null;
    }

    
    private function getNamaBarang($id) {
        $q = mysqli_query($this->db, "SELECT nama_barang FROM barang WHERE id='$id'");
        $d = mysqli_fetch_assoc($q);
        return $d ? $d['nama_barang'] : 'Produk';
    }

   
    public function cekStokKritis($id_barang) {
        $qBarang = mysqli_query($this->db, "SELECT nama_barang, stok FROM barang WHERE id = '$id_barang'");
        $barang = mysqli_fetch_assoc($qBarang);

        if ($barang['stok'] > 20) return null;

        $qLaku = mysqli_query($this->db, "
            SELECT SUM(qty) as total_laku 
            FROM detail_transaksi d
            JOIN transaksi t ON d.id_transaksi = t.id
            WHERE d.id_barang = '$id_barang' 
            AND t.tgl_transaksi >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $dataLaku = mysqli_fetch_assoc($qLaku);
        $totalLaku = $dataLaku['total_laku'] ? $dataLaku['total_laku'] : 0; 
        $rataPerHari = $totalLaku / 7;

        if ($rataPerHari <= 0) return null;

        $sisaHari = $barang['stok'] / $rataPerHari;
        
        $teksWaktu = ($sisaHari < 1) ? "kurang dari 1 hari" : round($sisaHari) . " hari";

        if ($barang['stok'] <= 0) {
             return ['level' => 'danger', 'pesan' => "<b>PERINGATAN:</b> Stok <b>{$barang['nama_barang']}</b> SUDAH HABIS. Restock!"];
        } elseif ($sisaHari < 3) {
            return ['level' => 'danger', 'pesan' => " <b>PERINGATAN AI:</b> Stok <b>{$barang['nama_barang']}</b> sisa {$barang['stok']}. Habis dalam <b>$teksWaktu</b>."];
        }
        return null;
    }
}
?>