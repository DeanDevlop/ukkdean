-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2026 at 07:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `nama_barang`, `harga`, `stok`, `gambar`, `kategori`) VALUES
(1, 'Indomie Goreng', 3500, 62, NULL, 'Makanan'),
(2, 'Telur Ayam', 2000, 23, NULL, 'Bahan'),
(3, 'Minyak Goreng', 14000, 84, NULL, 'Bahan'),
(4, 'Kecap Manis', 10000, 91, NULL, 'Bumbu'),
(5, 'Roti Tawar', 12000, 32, NULL, 'Makanan'),
(6, 'Selai Coklat', 15000, 41, NULL, 'Makanan'),
(7, 'Kopi Hitam', 5000, 94, NULL, 'Minuman'),
(8, 'Gula Pasir', 12000, 26, NULL, 'Bumbu'),
(9, 'Susu UHT', 6000, 93, NULL, 'Minuman'),
(10, 'Biskuit Kelapa', 8000, 53, NULL, 'Cemilan'),
(11, 'Sabun Mandi', 4000, 90, NULL, 'Peralatan'),
(12, 'Shampo', 1000, 94, NULL, 'Peralatan'),
(13, 'Pasta Gigi', 12000, 89, NULL, 'Peralatan'),
(14, 'Sikat Gigi', 5000, 93, NULL, 'Peralatan'),
(15, 'Air Mineral', 3000, 95, NULL, 'Minuman'),
(16, 'Teh Kotak', 4000, 90, NULL, 'Minuman'),
(17, 'Chiki Balls', 2000, 97, NULL, 'Cemilan'),
(18, 'Kacang Garuda', 9000, 99, NULL, 'Cemilan'),
(19, 'Tepung Terigu', 11000, 90, NULL, 'Bahan'),
(20, 'Margarin', 7000, 88, NULL, 'Bahan'),
(21, 'woy', 3000, 1, 'barang_697c22790e70b.jpg', 'Peralatan');

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id` int(11) NOT NULL,
  `id_transaksi` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `subtotal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id`, `id_transaksi`, `id_barang`, `qty`, `subtotal`) VALUES
(7, 4, 1, 1, 3500),
(8, 4, 2, 1, 2000),
(9, 5, 1, 1, 3500),
(10, 5, 2, 1, 2000),
(11, 5, 4, 1, 10000),
(12, 5, 5, 1, 12000),
(13, 5, 6, 2, 30000),
(14, 5, 7, 1, 5000),
(15, 5, 10, 1, 8000);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `tgl_transaksi` datetime DEFAULT NULL,
  `total_bayar` int(11) DEFAULT NULL,
  `bayar` decimal(10,2) DEFAULT 0.00,
  `kembalian` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_user`, `tgl_transaksi`, `total_bayar`, `bayar`, `kembalian`) VALUES
(4, 5, '2026-01-30 05:53:11', 5500, 100000.00, 94500.00),
(5, 4, '2026-01-30 07:07:16', 70500, 100000.00, 29500.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','kasir','owner') DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama_lengkap`, `created_at`, `last_login`, `last_logout`) VALUES
(4, 'den', '$2y$10$XQ6rUpcA2nWEved3TiJ4t.Aq46DQXylix6J8o.6JA1pMPj49/3nNG', 'owner', 'owner', '2026-01-30 05:06:14', '2026-01-30 05:06:14', NULL),
(5, 'dean', '$2y$10$5UiCCgEtPaLf2DAsAEoPQu303al1n0ACMpY/u9NnFbYncM/C8kSd2', 'kasir', 'dean', '2026-01-30 05:33:19', '2026-01-30 05:33:19', NULL),
(6, 'admin', '$2y$10$YEdyootAzde.xCMIFpQ93e22b/5bgnEEez6EfPgsXBpsbw2EM4Qdu', 'admin', 'admin', '2026-01-30 05:33:28', '2026-01-30 05:33:28', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
