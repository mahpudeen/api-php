-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 08, 2019 at 08:43 AM
-- Server version: 10.1.39-MariaDB
-- PHP Version: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `modal_antara`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `no_billing` varchar(85) DEFAULT NULL,
  `description` text,
  `qty` int(11) DEFAULT NULL,
  `base_price` int(11) DEFAULT NULL,
  `vat` int(11) DEFAULT NULL,
  `pph` int(11) DEFAULT NULL,
  `total_payment` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `no_billing`, `description`, `qty`, `base_price`, `vat`, `pph`, `total_payment`, `created_at`, `due_date`) VALUES
(1, '001/INV/MODALANTARA/09/2019', '<h3>API Service</h3> - September 2019', 14, 280000, 28000, 5600, 302400, '2019-09-05 21:40:51', '2019-09-20');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `nik` char(16) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `jenis_kelamin` varchar(15) DEFAULT NULL,
  `alamat` text,
  `provinsi` varchar(100) DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `kelurahan` varchar(100) DEFAULT NULL,
  `kode_pos` char(5) DEFAULT NULL,
  `tempat_lahir` varchar(75) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `no_telepon` varchar(25) DEFAULT NULL,
  `no_hp` varchar(25) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `agama` varchar(25) DEFAULT NULL,
  `status_pernikahan` varchar(20) DEFAULT NULL,
  `pendidikan` varchar(70) DEFAULT NULL,
  `bidang_pekerjaan` varchar(50) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `pendapatan` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `nik`, `nama`, `jenis_kelamin`, `alamat`, `provinsi`, `kota`, `kecamatan`, `kelurahan`, `kode_pos`, `tempat_lahir`, `tanggal_lahir`, `no_telepon`, `no_hp`, `email`, `agama`, `status_pernikahan`, `pendidikan`, `bidang_pekerjaan`, `pekerjaan`, `pendapatan`, `created_at`, `id_user`) VALUES
(1, '3506042602660001', 'Sulistyono', 'Laki-Laki', 'Jl. Raya - Dsn Purwokerto', 'JAWA TIMUR', 'Kabupaten Kediri', 'Ngadiluwih', 'Purwokerto', '64171', 'Kediri', '1966-02-26', '0329-2938192', '-', 'sulistyono@testsandboxing.com', 'Islam', 'Married', 'Sarjana', 'PNS', 'GURU', 84000000, '2019-08-22 10:37:38', 1),
(3, '3174054708010007', 'Gianny Priscilla', 'Perempuan', 'Jl. Masjid Cidodol', 'Jakarta Selatan', 'Kota Jakarta Selatan', 'Kebayoran Lama', 'Grogol Selatan', '12220', 'Jakarta', '2001-08-07', '-', '-', 'gianny@testsandboxing.com', 'Islam', 'Single', 'SMA', 'Lainnya', 'Lainnya', 3800000, '2019-08-22 15:20:33', 1),
(5, '3275030704960018', 'Raka Admiral Abdurrahman', 'Laki-Laki', 'Taman Wisma Asri Blok H 54 No. 24', 'Jawa Barat', 'Kota Bekasi', 'Bekasi Utara', 'Teluk Pucung', '17121', 'Bekasi', '1996-04-07', '-', '081380776620', 'raabdurrahman@fineoz.com', 'Islam', 'Single', 'S1/D4', 'Karyawan Swasta', 'Staff', 7000000, '2019-09-05 10:05:13', 3),
(6, '3276022212830006', 'Setya Bhakti Arumbudi', 'Laki-Laki', 'Jl. Singgalang No. 2', 'Jawa Barat', 'Kota Depok', 'Cimanggis', 'Mekarsari', '16452', 'Jakarta', '1983-12-22', '-', '081215077512', 'sbarumbudi@fineoz.com', 'Islam', 'Married', 'S2', 'Karyawan Swasta', 'Staff', 10000000, '2019-09-05 10:19:12', 3);

-- --------------------------------------------------------

--
-- Table structure for table `scoring`
--

CREATE TABLE `scoring` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tanggal_lahir` date NOT NULL,
  `pengalaman_kerja` int(11) NOT NULL,
  `jabatan` int(11) NOT NULL,
  `pendapatan` int(11) NOT NULL,
  `jumlah_tanggungan` int(11) NOT NULL,
  `pendidikan` int(11) NOT NULL,
  `kepemilikan_rumah` int(11) NOT NULL,
  `pinjaman` int(11) NOT NULL,
  `pengambilan_kredit` int(11) DEFAULT NULL,
  `tenor_bulanan` int(11) DEFAULT NULL,
  `value` float NOT NULL,
  `grading` varchar(50) DEFAULT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `scoring`
--

INSERT INTO `scoring` (`id`, `created_at`, `updated_at`, `tanggal_lahir`, `pengalaman_kerja`, `jabatan`, `pendapatan`, `jumlah_tanggungan`, `pendidikan`, `kepemilikan_rumah`, `pinjaman`, `pengambilan_kredit`, `tenor_bulanan`, `value`, `grading`, `id_customer`, `id_user`) VALUES
(1, '2019-08-22 03:53:05', '2019-09-03 03:19:22', '1996-04-07', 4, 9, 5000000, 2, 3, 1, 5000000, 1, 12, 85.834, 'Sangat Yakin', 1, 1),
(2, '2019-08-22 08:23:19', '2019-09-03 03:19:30', '2001-08-07', 1, 9, 3800000, 1, 4, 2, 1000000, 1, 1, 77.38, 'Cukup Yakin', 3, 1),
(3, '2019-08-27 06:55:02', '2019-09-03 03:19:32', '2001-08-07', 1, 9, 3800000, 1, 4, 2, 1000000, 1, 1, 77.38, 'Cukup Yakin', 3, 1),
(4, '2019-08-27 06:57:32', '2019-09-03 03:19:34', '2001-08-07', 1, 9, 3800000, 1, 4, 2, 1000000, 1, 1, 77.38, 'Cukup Yakin', 3, 1),
(5, '2019-09-04 13:19:41', '2019-09-04 13:19:41', '2001-08-07', 1, 9, 3800000, 1, 4, 2, 1000000, 1, 1, 94.909, 'Sangat Yakin', 1, 1),
(6, '2019-09-04 13:52:50', '2019-09-04 13:52:50', '2001-08-07', 1, 9, 3800000, 1, 4, 2, 1000000, 1, 1, 94.909, 'Sangat Yakin', 1, 1),
(7, '2019-09-05 03:20:39', '2019-09-05 03:20:39', '1983-12-22', 1, 5, 10000000, 3, 7, 1, 20000000, 1, 24, 21.45, 'Kurang Yakin', 6, 3),
(8, '2019-09-05 03:20:58', '2019-09-05 03:20:58', '1983-12-22', 1, 5, 10000000, 3, 7, 1, 10000000, 1, 24, 21.45, 'Kurang Yakin', 6, 3),
(9, '2019-09-05 03:21:12', '2019-09-05 03:21:12', '1983-12-22', 1, 5, 10000000, 3, 7, 1, 5000000, 1, 24, 25.082, 'Kurang Yakin', 6, 3),
(10, '2019-09-05 03:21:56', '2019-09-05 03:21:56', '1983-12-22', 1, 5, 3900000, 3, 7, 1, 5000000, 1, 24, 28.632, 'Kurang Yakin', 6, 3),
(11, '2019-09-05 03:22:24', '2019-09-05 03:22:24', '1983-12-22', 1, 5, 3900000, 1, 7, 1, 5000000, 1, 24, 27.945, 'Kurang Yakin', 6, 3),
(12, '2019-09-05 03:29:32', '2019-09-05 03:29:32', '1983-12-22', 1, 5, 15000000, 3, 7, 1, 5000000, 1, 24, 32.54, 'Kurang Yakin', 6, 3),
(13, '2019-09-05 03:30:03', '2019-09-05 03:30:03', '1983-12-22', 1, 5, 30000000, 3, 7, 1, 5000000, 1, 24, 32.542, 'Kurang Yakin', 6, 3),
(14, '2019-09-05 03:30:54', '2019-09-05 03:30:54', '1983-12-22', 1, 5, 30000000, 3, 7, 1, 1000000, 1, 24, 39.84, 'Kurang Yakin', 6, 3),
(15, '2019-09-05 03:32:07', '2019-09-05 03:32:07', '1983-12-22', 1, 5, 30000000, 1, 7, 1, 1000000, 1, 24, 39.475, 'Kurang Yakin', 6, 3),
(16, '2019-09-05 03:34:04', '2019-09-05 03:34:04', '1996-04-07', 1, 7, 4500000, 1, 6, 2, 13000000, 1, 12, 22.881, 'Kurang Yakin', 5, 3),
(17, '2019-09-05 03:34:27', '2019-09-05 03:34:27', '1996-04-07', 1, 7, 4500000, 1, 6, 2, 2000000, 1, 12, 36.77, 'Kurang Yakin', 5, 3),
(18, '2019-09-05 03:34:43', '2019-09-05 03:34:43', '1996-04-07', 1, 7, 4500000, 1, 6, 2, 500000, 1, 12, 38.554, 'Kurang Yakin', 5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `password` text,
  `api_key` text,
  `api_secret` text,
  `hit` int(11) DEFAULT NULL,
  `role` varchar(5) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expired_on` datetime DEFAULT NULL,
  `modified_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `nama`, `username`, `password`, `api_key`, `api_secret`, `hit`, `role`, `created_at`, `expired_on`, `modified_at`) VALUES
(1, 'Sandboxing', 'sandbox', 'sandboxpassword', '202636034c01b9eaea1ff55c19a1d913', NULL, NULL, 'user', '2019-08-22 10:18:49', '2019-09-22 10:18:49', '2019-09-06 10:51:33'),
(2, 'Admin Modal Antara', 'modalantara', 'passwordmodalantara', 'c67be1e16ca1b47d933a1336efb27c90', NULL, NULL, 'admin', '2019-09-05 09:42:03', '2019-10-05 09:42:03', '2019-09-05 09:42:15'),
(3, 'Sandboxing 2', 'sandbox2', 'sandboxpassword2', 'a5fb08399cbc58e1726b647d5bd62d14', NULL, NULL, 'user', '2019-09-05 10:04:44', '2019-10-05 10:04:44', '2019-09-05 10:04:44'),
(4, 'Sandboxing 3', 'sandbox3', 'sandboxpassword3', 'df2605ebec318ce6b08ccd85d0d14692', NULL, NULL, 'user', '2019-09-05 18:10:35', '2019-10-05 18:10:35', '2019-09-06 10:21:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scoring`
--
ALTER TABLE `scoring`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scoring`
--
ALTER TABLE `scoring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
