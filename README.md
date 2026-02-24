#  Aplikasi Kasir & POS Pintar (UKK PPLG)

Aplikasi Point of Sales (Kasir) berbasis Web Native yang dirancang untuk efisiensi transaksi, manajemen stok cerdas, dan pengalaman pelanggan modern.

Dibuat untuk memenuhi Uji Kompetensi Keahlian (UKK) Jurusan PPLG.

---

##  Fitur Unggulan (Key Features)

Aplikasi ini memiliki fitur di atas standar rata-rata:

### 1. Layar Pelanggan (Customer Display)
- **Real-time Mirroring:** Menampilkan isi keranjang belanja di layar kedua/monitor pelanggan secara otomatis tanpa refresh manual.
- **Lokasi File:** `halaman.php`

### 2. Barcode Scanner Support (Auto-Add Mode)
- **Smart Input:** Mendukung input via Barcode Scanner USB.
- **Auto-Logic:** Jika kode barcode unik terdeteksi, sistem otomatis memasukkan barang ke keranjang & mereset kolom pencarian (tanpa perlu klik tombol tambah).

### 3. AI Stock Detection (Admin)
- **Early Warning System:** Dashboard Admin otomatis mendeteksi barang yang stoknya menipis dan memberikan peringatan dini sebelum barang habis total.

### 4. Dashboard Owner & Laporan
- Grafik penjualan visual.
- Laporan omset dan barang terlaris untuk pengambilan keputusan bisnis.

### 5. Struk Thermal
- Desain struk belanja yang kompatibel dengan printer thermal 58mm.

---

## 🛠️ Teknologi yang Digunakan
- **Backend:** PHP Native (Versi 8.0+)
- **Frontend:** HTML5, CSS3, Tailwind CSS (via CDN/Local)
- **Database:** MySQL / MariaDB
- **Tools:** XAMPP, VS Code, Mermaid Diagram

---

##  Cara Instalasi

1. **Persiapan:**
   - Pastikan XAMPP (Apache & MySQL) sudah berjalan.
   - Copy folder `ukk_dean` ke dalam folder `htdocs`.

2. **Database:**
   - Buka `localhost/phpmyadmin`.
   - Buat database baru dengan nama **`db_kasir`**.
   - Import file `db_kasir.sql` yang ada di dalam folder proyek.

3. **Jalankan:**
   - Buka browser dan akses: `localhost/ukk_dean`

---

##  Akun Login (Default)

Berikut adalah akun untuk pengujian sistem:

| Role | Username | Password | Akses Fitur |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `admin` | Manajemen Barang, User, AI Warning |
| **Kasir** | `dean` | `123` | Transaksi POS, Cetak Struk, Scan Barcode |
| **Owner** | `den` | `123` | Laporan Keuangan, Grafik, Monitoring |

*(Catatan: Password mungkin berbeda jika hash di database diubah, silakan cek tabel `users`)*

---

##  Author

**Nama:** [Dean Jagadita Ahmad Monsi]
**Kelas:** XII PPLG
**Sekolah:** [SMKN 1 Pemalang]

---
*Created with ❤️ for UKK 2026*
