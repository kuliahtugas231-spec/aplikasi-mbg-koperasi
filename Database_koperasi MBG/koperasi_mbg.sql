-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2026 at 09:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `koperasi_mbg`
--

-- --------------------------------------------------------

--
-- Table structure for table `dompet`
--

CREATE TABLE `dompet` (
  `id_dompet` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `saldo` decimal(12,2) DEFAULT 0.00,
  `status_dompet` enum('Aktif','Ditangguhkan') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dompet`
--

INSERT INTO `dompet` (`id_dompet`, `id_user`, `saldo`, `status_dompet`) VALUES
(1, 3, 1200000000.00, 'Aktif'),
(2, 2, 0.00, 'Aktif'),
(5, 21, 1500000000.00, 'Aktif'),
(6, 22, 750000000.00, 'Aktif'),
(7, 23, 900000000.00, 'Aktif'),
(8, 24, 850000000.00, 'Aktif'),
(9, 25, 1100000000.00, 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `escrow`
--

CREATE TABLE `escrow` (
  `id_escrow` int(11) NOT NULL,
  `id_pesanan` int(11) DEFAULT NULL,
  `jumlah_dana` int(11) DEFAULT NULL,
  `status` enum('Ditahan','Dicairkan','Dikembalikan') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `escrow`
--

INSERT INTO `escrow` (`id_escrow`, `id_pesanan`, `jumlah_dana`, `status`, `created_at`) VALUES
(1, 1, 171000, 'Ditahan', '2026-06-10 18:10:46');

-- --------------------------------------------------------

--
-- Table structure for table `katalog_bahan`
--

CREATE TABLE `katalog_bahan` (
  `id_bahan` int(11) NOT NULL,
  `id_user_petani` int(11) NOT NULL,
  `nama_bahan` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(12,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `katalog_bahan`
--

INSERT INTO `katalog_bahan` (`id_bahan`, `id_user_petani`, `nama_bahan`, `deskripsi`, `harga`, `stok`, `satuan`, `gambar`, `created_at`) VALUES
(4, 6, 'tomat Buat / Tomat Kebun (Beef Tomato)', 'Deskripsi: Ini adalah jenis tomat yang ukurannya paling besar, bulat, dan berdaging tebal dengan biji yang relatif sedikit.\r\n\r\nKegunaan: Karena dagingnya padat dan tidak terlalu berair, tomat ini sangat cocok untuk diiris sebagai isian burger, sandwich, atau salad.', 9000.00, 200, 'Kg', NULL, '2026-06-10 18:09:21'),
(6, 4, 'Bawang Merah Lokal', 'Bawang merah kupas berkualitas tinggi dengan aroma kuat.', 28000.00, 200, 'Kg', NULL, '2026-06-17 19:05:52'),
(7, 5, 'Sayur Kangkung Segar', 'Kangkung hidroponik segar tanpa pestisida kimia berbahaya.', 7000.00, 300, 'Kg', NULL, '2026-06-17 19:05:52'),
(8, 5, 'Sayur Sawi Hijau', 'Sawi hijau pilihan, kaya vitamin untuk pelengkap menu sayur anak sekolah.', 8000.00, 250, 'Kg', NULL, '2026-06-17 19:05:52'),
(9, 6, 'Tomat Buat / Tomat Kebun', 'Tomat buah berukuran besar, daging tebal, dan segar.', 9000.00, 19, 'Kg', NULL, '2026-06-17 19:05:52'),
(10, 6, 'Wortel Lokal Manis', 'Wortel manis segar kaya vitamin A, sangat baik untuk sup anak-anak.', 11000.00, 180, 'Kg', NULL, '2026-06-17 19:05:52'),
(11, 7, 'Pisang Ambon', 'Pisang Ambon matang alami, manis, siap disajikan sebagai buah pencuci mulut.', 15000.00, 120, 'Kg', NULL, '2026-06-17 19:05:52'),
(12, 8, 'Jeruk Berastagi Madu', 'Jeruk lokal manis segar dengan kandungan air melimpah.', 22000.00, 140, 'Kg', NULL, '2026-06-17 19:05:52'),
(13, 9, 'Daging Ayam Broiler Segar', 'Daging ayam potong segar harian, bersih higienis dan bersertifikat halal.', 34000.00, 400, 'Kg', NULL, '2026-06-17 19:05:52'),
(14, 10, 'Daging Sapi Has Dalam', 'Daging sapi segar kualitas prima tanpa lemak berlebih.', 125000.00, 80, 'Kg', NULL, '2026-06-17 19:05:52'),
(15, 11, 'Ikan Lele Hidup Segar', 'Ikan lele budidaya air tawar, kondisi segar kaya protein.', 24000.00, 150, 'Kg', NULL, '2026-06-17 19:05:52'),
(16, 11, 'Ikan Kembung Segar', 'Ikan kembung tangkapan laut harian, tinggi kandungan omega-3.', 32000.00, 100, 'Kg', NULL, '2026-06-17 19:05:52'),
(17, 12, 'Tahu Putih Sutra', 'Tahu putih lembut berbahan kedelai murni non-GMO tanpa pengawet.', 12000.00, 350, 'Papan', NULL, '2026-06-17 19:05:52'),
(18, 13, 'Tempe Murni Higienis', 'Tempe bungkus daun dan plastik cetak padat, kaya protein nabati.', 10000.00, 300, 'Kg', NULL, '2026-06-17 19:05:52'),
(19, 14, 'Kentang Dieng Super', 'Kentang berukuran besar dan padat, cocok untuk pelengkap karbohidrat sup.', 16000.00, 200, 'Kg', NULL, '2026-06-17 19:05:52'),
(20, 15, 'Jagung Manis Pipil', 'Jagung manis segar pilihan untuk campuran sayur bening atau bakwan.', 9000.00, 220, 'Kg', NULL, '2026-06-17 19:05:52'),
(21, 16, 'Suku Sapi Murni Pasteurasi', 'Susu sapi murni segar kualitas premium standar gizi nasional.', 18000.00, 500, 'Liter', NULL, '2026-06-17 19:05:52'),
(22, 4, 'Cabai Merah Keriting', 'Cabai merah segar pilihan langsung dari petani lokal, cocok untuk bumbu dapur umum.', 35000.00, 150, 'Kg', NULL, '2026-06-17 19:09:58'),
(23, 4, 'Bawang Merah Lokal', 'Bawang merah kupas berkualitas tinggi dengan aroma kuat.', 28000.00, 200, 'Kg', NULL, '2026-06-17 19:09:58'),
(24, 4, 'Cabai Hijau Besar', 'Cabai hijau segar, tidak terlalu pedas, sangat cocok untuk tumisan dapur umum.', 22000.00, 100, 'Kg', NULL, '2026-06-17 19:09:58'),
(25, 4, 'Cabai Rawit Setan', 'Cabai rawit merah super pedas untuk kebutuhan pembuatan sambal kuah.', 45000.00, 80, 'Kg', NULL, '2026-06-17 19:09:58'),
(26, 5, 'Sayur Kangkung Segar', 'Kangkung hidroponik segar tanpa pestisida kimia berbahaya.', 7000.00, 300, 'Kg', NULL, '2026-06-17 19:09:58'),
(27, 5, 'Sayur Sawi Hijau', 'Sawi hijau pilihan, kaya vitamin untuk pelengkap menu sayur anak sekolah.', 8000.00, 250, 'Kg', NULL, '2026-06-17 19:09:58'),
(28, 5, 'Bayam Hijau Ikat', 'Bayam segar kaya zat besi, sangat baik untuk menu sayur bening anak-anak.', 6000.00, 180, 'Kg', NULL, '2026-06-17 19:09:58'),
(29, 6, 'Tomat Buat / Tomat Kebun', 'Tomat buah berukuran besar, daging tebal, dan segar.', 9000.00, 19, 'Kg', NULL, '2026-06-17 19:09:58'),
(30, 6, 'Wortel Lokal Manis', 'Wortel manis segar kaya vitamin A, sangat baik untuk sup anak-anak.', 11000.00, 180, 'Kg', NULL, '2026-06-17 19:09:58'),
(31, 6, 'Kubis / Kol Putih', 'Kol putih segar dan renyah, pelengkap sempurna untuk menu sayur sop.', 7500.00, 150, 'Kg', NULL, '2026-06-17 19:09:58'),
(32, 7, 'Pisang Ambon', 'Pisang Ambon matang alami, manis, siap disajikan sebagai buah pencuci mulut.', 15000.00, 120, 'Kg', NULL, '2026-06-17 19:09:58'),
(33, 7, 'Pisang Raja', 'Pisang raja pilihan dengan tekstur legit, kaya nutrisi.', 18000.00, 90, 'Kg', NULL, '2026-06-17 19:09:58'),
(34, 7, 'Pisang Kepok', 'Pisang kepok segar, bisa dikonsumsi langsung atau diolah untuk camilan sehat.', 12000.00, 100, 'Kg', NULL, '2026-06-17 19:09:58'),
(35, 8, 'Jeruk Berastagi Madu', 'Jeruk lokal manis segar dengan kandungan air melimpah.', 22000.00, 140, 'Kg', NULL, '2026-06-17 19:09:58'),
(36, 8, 'Jeruk Nipis Peras', 'Jeruk nipis segar untuk menghilangkan bau amis pada ikan dan penambah kesegaran masakan.', 15000.00, 60, 'Kg', NULL, '2026-06-17 19:09:58'),
(37, 9, 'Daging Ayam Broiler Segar', 'Daging ayam potong segar harian, bersih higienis dan bersertifikat halal.', 34000.00, 400, 'Kg', NULL, '2026-06-17 19:09:58'),
(38, 9, 'Telur Ayam Negeri', 'Telur ayam segar pilihan sumber protein tinggi untuk menu harian.', 26000.00, 500, 'Kg', NULL, '2026-06-17 19:09:58');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `id_dapur` int(11) DEFAULT NULL,
  `jumlah_beli` int(11) NOT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `status_logistik` enum('Pending','Verified','Diterima Gudang','Selesai') DEFAULT 'Pending',
  `status_pembayaran` enum('Belum Lunas','Lunas') DEFAULT 'Belum Lunas',
  `catatan_qc` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_produk`, `id_dapur`, `jumlah_beli`, `total_harga`, `status_logistik`, `status_pembayaran`, `catatan_qc`, `created_at`) VALUES
(1, 4, 3, 19, 171000.00, 'Pending', '', NULL, '2026-06-10 18:10:46');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_saldo`
--

CREATE TABLE `riwayat_saldo` (
  `id_riwayat` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `tipe` enum('Top-Up','Pembayaran','Penerimaan','Refund') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Petani','Koperasi','Dapur') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `nama_lengkap`, `created_at`) VALUES
(2, 'koperasi', 'koperasi1', 'Koperasi', 'Koperasi MBG Pusat', '2026-06-06 16:43:42'),
(3, 'dapur1', 'dapur1', 'Dapur', 'Dapur Umum Sejahtera', '2026-06-06 16:43:42'),
(4, 'petanicabe', 'petani123', 'Petani', 'Bpk. Ahmad Junaedi (Cabai & Bawang)', '2026-06-09 17:59:41'),
(5, 'petanikangkung', 'petani123', 'Petani', 'Ibu Siti Aminah (Sayur Hijau)', '2026-06-09 17:59:41'),
(6, 'petanitomat', 'petani123', 'Petani', 'Bpk. Wayan Sudarta (Tomat & Wortel)', '2026-06-09 17:59:41'),
(7, 'petanipisang', 'petani123', 'Petani', 'Bpk. Gede Bagus (Buah Pisang Lokal)', '2026-06-09 17:59:41'),
(8, 'petanijeruk', 'petani123', 'Petani', 'Ibu Ketut Lestari (Jeruk Berastagi)', '2026-06-09 17:59:41'),
(9, 'peternakayam', 'petani123', 'Petani', 'Bpk. H. Mahmud (Daging Ayam Broiler)', '2026-06-09 17:59:41'),
(10, 'peternaksapi', 'petani123', 'Petani', 'Bpk. Joko Susilo (Daging Sapi Segar)', '2026-06-09 17:59:41'),
(11, 'nelayanikan', 'petani123', 'Petani', 'Bpk. Rusdianto (Ikan Kembung & Lele)', '2026-06-09 17:59:41'),
(12, 'petanitahu', 'petani123', 'Petani', 'Ibu Maryam (Produsen Tahu Kedelai)', '2026-06-09 17:59:41'),
(13, 'petanitempe', 'petani123', 'Petani', 'Bpk. slamet (Pengrajin Tempe Murni)', '2026-06-09 17:59:41'),
(14, 'petanikentang', 'petani123', 'Petani', 'Bpk. Dedi Suhendar (Kentang Dieng)', '2026-06-09 17:59:41'),
(15, 'petanijagung', 'petani123', 'Petani', 'Ibu Neneng Hasanah (Jagung Manis)', '2026-06-09 17:59:41'),
(16, 'peternaksusu', 'petani123', 'Petani', 'Koperasi Susu Makmur (Susu Sapi Murni)', '2026-06-09 17:59:41'),
(17, 'koperasipusat', 'koperasi123', 'Koperasi', 'Koperasi MBG Pusat (Admin Utama)', '2026-06-09 18:04:47'),
(18, 'kud_makmur', 'koperasi123', 'Koperasi', 'KUD Makmur Jaya (Wilayah Utara)', '2026-06-09 18:04:47'),
(19, 'kud_sejahtera', 'koperasi123', 'Koperasi', 'KUD Sejahtera Bersama (Wilayah Selatan)', '2026-06-09 18:04:47'),
(20, 'kud_tani', 'koperasi123', 'Koperasi', 'Koperasi Unit Desa Tani Mulia', '2026-06-09 18:04:47'),
(21, 'dapur_kec_utama', 'dapur123', 'Dapur', 'Dapur Umum Kecamatan Pusat', '2026-06-09 18:05:02'),
(22, 'dapur_sekolah1', 'dapur123', 'Dapur', 'Dapur Umum Klaster SD Pertiwi', '2026-06-09 18:05:02'),
(23, 'dapur_sekolah2', 'dapur123', 'Dapur', 'Dapur Umum Klaster SMP Negeri 1', '2026-06-09 18:05:02'),
(24, 'dapur_wil_timur', 'dapur123', 'Dapur', 'Dapur Umum Komunitas Wilayah Timur', '2026-06-09 18:05:02'),
(25, 'dapur_sehat_mbg', 'dapur123', 'Dapur', 'Dapur Umum Yayasan Gizi Sehat', '2026-06-09 18:05:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dompet`
--
ALTER TABLE `dompet`
  ADD PRIMARY KEY (`id_dompet`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `escrow`
--
ALTER TABLE `escrow`
  ADD PRIMARY KEY (`id_escrow`);

--
-- Indexes for table `katalog_bahan`
--
ALTER TABLE `katalog_bahan`
  ADD PRIMARY KEY (`id_bahan`),
  ADD KEY `id_user_petani` (`id_user_petani`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indexes for table `riwayat_saldo`
--
ALTER TABLE `riwayat_saldo`
  ADD PRIMARY KEY (`id_riwayat`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dompet`
--
ALTER TABLE `dompet`
  MODIFY `id_dompet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `escrow`
--
ALTER TABLE `escrow`
  MODIFY `id_escrow` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `katalog_bahan`
--
ALTER TABLE `katalog_bahan`
  MODIFY `id_bahan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `riwayat_saldo`
--
ALTER TABLE `riwayat_saldo`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dompet`
--
ALTER TABLE `dompet`
  ADD CONSTRAINT `dompet_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `katalog_bahan`
--
ALTER TABLE `katalog_bahan`
  ADD CONSTRAINT `katalog_bahan_ibfk_1` FOREIGN KEY (`id_user_petani`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
