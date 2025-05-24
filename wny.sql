-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2025 at 07:30 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wny`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian`
--

CREATE TABLE `detail_pembelian` (
  `id` int NOT NULL,
  `pembelian_id` int DEFAULT NULL,
  `produk_id` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `harga` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int NOT NULL,
  `nama_kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(1, 'Minyak'),
(2, 'Beras'),
(3, 'Gas'),
(4, 'Mie'),
(5, 'Sabun'),
(6, 'Bumbu');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int NOT NULL,
  `session_id` varchar(255) NOT NULL COMMENT 'Untuk menyimpan ID Sesi PHP',
  `produk_id` int NOT NULL,
  `jumlah` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id` int NOT NULL,
  `tanggal` datetime DEFAULT CURRENT_TIMESTAMP,
  `total` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `kategori_id` int DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `deskripsi` text,
  `gambar_produk` varchar(255) DEFAULT NULL,
  `stok` int DEFAULT NULL,
  `is_popular` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `kategori_id`, `harga`, `deskripsi`, `gambar_produk`, `stok`, `is_popular`, `created_at`, `updated_at`) VALUES
(1, 'Minyakita', 1, 36000, 'Minyakita adalah minyak goreng kemasan bersubsidi yang jernih, sehat, terjangkau, dan cocok digunakan untuk menggoreng maupun menumis kebutuhan masakan sehari-hari.', 'asset/images/minyak.png', NULL, 1, '2025-05-24 00:22:39', '2025-05-24 00:24:30'),
(2, 'Beras', 2, 15000, 'Beras 5 kg adalah bahan pangan pokok yang bergizi, pulen, wangi, dan mudah dimasak untuk memenuhi kebutuhan konsumsi rumah tangga sehari-hari.', 'asset/images/Beras.png', NULL, 1, '2025-05-24 00:22:39', '2025-05-24 00:24:30'),
(3, 'Gas LPG 3kg', 3, 20000, 'Gas LPG 3 kg adalah bahan bakar memasak bersubsidi yang ringan, praktis, hemat, dan mudah digunakan untuk kebutuhan rumah tangga sehari-hari.', 'asset/images/Gas.png', NULL, 1, '2025-05-24 00:22:39', '2025-05-24 00:24:30'),
(4, 'Indomie', 4, 3500, 'Indomie adalah mie instan favorit masyarakat Indonesia dengan berbagai varian rasa yang lezat dan mudah disiapkan untuk sajian cepat dan praktis.', 'asset/images/Indomie.png', NULL, 1, '2025-05-24 00:22:39', '2025-05-24 00:24:30'),
(5, 'Mie Sedaap', 4, 3000, 'Mie Sedaap menawarkan rasa gurih dan kenyal yang nikmat, cocok untuk disantap kapan saja sebagai pilihan mie instan berkualitas.', 'asset/images/miesedaap.png', NULL, 1, '2025-05-24 00:22:39', '2025-05-24 00:24:30'),
(6, 'Supermie', 4, 3000, 'Supermie adalah mie instan klasik dengan cita rasa khas dan tekstur lembut, ideal untuk menu harian yang praktis dan mengenyangkan.', 'asset/images/supermi.png', NULL, 0, '2025-05-24 00:22:39', '2025-05-24 00:22:39'),
(7, 'Pop Mie', 4, 6000, 'Pop Mie adalah mie instan dalam cup yang praktis, cukup diseduh air panas dan siap disantap kapan saja, cocok untuk aktivitas sibuk.', 'asset/images/popmie.png', NULL, 0, '2025-05-24 00:22:39', '2025-05-24 00:22:39'),
(8, 'Sunlight', 5, 5000, 'Sunlight adalah sabun pencuci piring dengan formula aktif penghilang lemak, membuat peralatan makan bersih, higienis, dan harum.', 'asset/images/Sunlight.png', NULL, 0, '2025-05-24 00:22:39', '2025-05-24 00:22:39'),
(9, 'Sasa', 6, 2000, 'Sasa adalah penyedap rasa MSG yang memberikan cita rasa gurih dan lezat pada berbagai masakan rumah tangga.', 'asset/images/sasa.png', NULL, 0, '2025-05-24 00:22:39', '2025-05-24 00:22:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembelian_id` (`pembelian_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_id` (`produk_id`),
  ADD KEY `idx_session_id` (`session_id`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `detail_pembelian_ibfk_1` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`),
  ADD CONSTRAINT `detail_pembelian_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
