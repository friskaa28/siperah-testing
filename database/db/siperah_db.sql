-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2026 at 02:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siperah_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bagi_hasil`
--

CREATE TABLE `bagi_hasil` (
  `idbagi_hasil` bigint(20) UNSIGNED NOT NULL,
  `idproduksi` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `persentase_pemilik` decimal(5,2) DEFAULT 60.00,
  `persentase_pengelola` decimal(5,2) DEFAULT 40.00,
  `total_pendapatan` decimal(15,2) DEFAULT 0.00,
  `hasil_pemilik` decimal(15,2) GENERATED ALWAYS AS (`total_pendapatan` * `persentase_pemilik` / 100) STORED,
  `hasil_pengelola` decimal(15,2) GENERATED ALWAYS AS (`total_pendapatan` * `persentase_pengelola` / 100) STORED,
  `status` enum('pending','lunas','sebagian') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bagi_hasil`
--

INSERT INTO `bagi_hasil` (`idbagi_hasil`, `idproduksi`, `tanggal`, `persentase_pemilik`, `persentase_pengelola`, `total_pendapatan`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-12-16', 60.00, 40.00, 2250000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(2, 2, '2025-12-17', 60.00, 40.00, 2400000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(3, 3, '2025-12-18', 60.00, 40.00, 2100000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(4, 4, '2025-12-19', 60.00, 40.00, 2500000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(5, 5, '2025-12-20', 60.00, 40.00, 2300000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(6, 6, '2025-12-21', 60.00, 40.00, 2450000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(7, 7, '2025-12-22', 60.00, 40.00, 2200000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(8, 8, '2025-12-23', 60.00, 40.00, 2350000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(9, 9, '2025-12-24', 60.00, 40.00, 2550000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(10, 10, '2025-12-25', 60.00, 40.00, 2400000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(11, 11, '2025-12-16', 60.00, 40.00, 2600000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(12, 12, '2025-12-17', 60.00, 40.00, 2750000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(13, 13, '2025-12-18', 60.00, 40.00, 2400000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(14, 14, '2025-12-19', 60.00, 40.00, 2900000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(15, 15, '2025-12-20', 60.00, 40.00, 2500000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(16, 16, '2025-12-21', 60.00, 40.00, 2800000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(17, 17, '2025-12-22', 60.00, 40.00, 2450000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(18, 18, '2025-12-23', 60.00, 40.00, 2650000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(19, 19, '2025-12-24', 60.00, 40.00, 2850000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(20, 20, '2025-12-25', 60.00, 40.00, 2700000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(21, 21, '2025-12-16', 60.00, 40.00, 1900000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(22, 22, '2025-12-17', 60.00, 40.00, 2000000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(23, 23, '2025-12-18', 60.00, 40.00, 1800000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(24, 24, '2025-12-19', 60.00, 40.00, 2100000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(25, 25, '2025-12-20', 60.00, 40.00, 1950000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(26, 26, '2025-12-21', 60.00, 40.00, 2050000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(27, 27, '2025-12-22', 60.00, 40.00, 1850000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(28, 28, '2025-12-23', 60.00, 40.00, 2000000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(29, 29, '2025-12-24', 60.00, 40.00, 2150000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(30, 30, '2025-12-25', 60.00, 40.00, 2000000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(31, 31, '2025-12-16', 60.00, 40.00, 2900000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(32, 32, '2025-12-17', 60.00, 40.00, 3100000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(33, 33, '2025-12-18', 60.00, 40.00, 2750000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(34, 34, '2025-12-19', 60.00, 40.00, 3250000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(35, 35, '2025-12-20', 60.00, 40.00, 3000000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(36, 36, '2025-12-21', 60.00, 40.00, 3150000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(37, 37, '2025-12-22', 60.00, 40.00, 2850000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(38, 38, '2025-12-23', 60.00, 40.00, 3050000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(39, 39, '2025-12-24', 60.00, 40.00, 3200000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(40, 40, '2025-12-25', 60.00, 40.00, 3000000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(41, 41, '2025-12-16', 60.00, 40.00, 2050000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(42, 42, '2025-12-17', 60.00, 40.00, 2150000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(43, 43, '2025-12-18', 60.00, 40.00, 1950000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(44, 44, '2025-12-19', 60.00, 40.00, 2250000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(45, 45, '2025-12-20', 60.00, 40.00, 2100000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(46, 46, '2025-12-21', 60.00, 40.00, 2200000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(47, 47, '2025-12-22', 60.00, 40.00, 2000000.00, 'lunas', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(48, 48, '2025-12-23', 60.00, 40.00, 2150000.00, 'sebagian', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(49, 49, '2025-12-24', 60.00, 40.00, 2300000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(50, 50, '2025-12-25', 60.00, 40.00, 2150000.00, 'pending', '2025-12-25 13:00:46', '2025-12-25 13:00:46');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `distribusi`
--

CREATE TABLE `distribusi` (
  `iddistribusi` bigint(20) UNSIGNED NOT NULL,
  `idpeternak` bigint(20) UNSIGNED NOT NULL,
  `tujuan` varchar(100) NOT NULL,
  `volume` decimal(10,2) NOT NULL,
  `harga_per_liter` decimal(15,2) NOT NULL,
  `tanggal_kirim` date NOT NULL,
  `total_penjualan` decimal(15,2) GENERATED ALWAYS AS (`volume` * `harga_per_liter`) STORED,
  `status` enum('pending','terkirim','diterima','ditolak') DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `distribusi`
--

INSERT INTO `distribusi` (`iddistribusi`, `idpeternak`, `tujuan`, `volume`, `harga_per_liter`, `tanggal_kirim`, `status`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, 'Koperasi Kradinan', 45.00, 50000.00, '2025-12-16', 'diterima', 'Susu berkualitas baik', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(2, 1, 'Koperasi Kradinan', 48.00, 50000.00, '2025-12-17', 'diterima', 'Susu segar', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(3, 1, 'Koperasi Kradinan', 42.00, 50000.00, '2025-12-18', 'diterima', 'Produksi kurang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(4, 1, 'Koperasi Kradinan', 50.00, 50000.00, '2025-12-19', 'diterima', 'Susu berkualitas bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(5, 1, 'Koperasi Kradinan', 46.00, 50000.00, '2025-12-20', 'diterima', 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(6, 1, 'Koperasi Kradinan', 49.00, 50000.00, '2025-12-21', 'terkirim', 'Dalam perjalanan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(7, 1, 'Koperasi Kradinan', 44.00, 50000.00, '2025-12-22', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(8, 1, 'Koperasi Kradinan', 47.00, 50000.00, '2025-12-23', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(9, 1, 'Koperasi Kradinan', 51.00, 50000.00, '2025-12-24', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(10, 1, 'Koperasi Kradinan', 48.00, 50000.00, '2025-12-25', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(11, 2, 'Koperasi Kradinan', 52.00, 50000.00, '2025-12-16', 'diterima', 'Susu berkualitas baik', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(12, 2, 'Koperasi Kradinan', 55.00, 50000.00, '2025-12-17', 'diterima', 'Susu segar', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(13, 2, 'Koperasi Kradinan', 48.00, 50000.00, '2025-12-18', 'diterima', 'Produksi kurang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(14, 2, 'Koperasi Kradinan', 58.00, 50000.00, '2025-12-19', 'diterima', 'Susu berkualitas bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(15, 2, 'Koperasi Kradinan', 50.00, 50000.00, '2025-12-20', 'diterima', 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(16, 2, 'Koperasi Kradinan', 56.00, 50000.00, '2025-12-21', 'terkirim', 'Dalam perjalanan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(17, 2, 'Koperasi Kradinan', 49.00, 50000.00, '2025-12-22', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(18, 2, 'Koperasi Kradinan', 53.00, 50000.00, '2025-12-23', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(19, 2, 'Koperasi Kradinan', 57.00, 50000.00, '2025-12-24', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(20, 2, 'Koperasi Kradinan', 54.00, 50000.00, '2025-12-25', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(21, 3, 'Koperasi Kradinan', 38.00, 50000.00, '2025-12-16', 'diterima', 'Susu berkualitas baik', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(22, 3, 'Koperasi Kradinan', 40.00, 50000.00, '2025-12-17', 'diterima', 'Susu segar', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(23, 3, 'Koperasi Kradinan', 36.00, 50000.00, '2025-12-18', 'diterima', 'Produksi kurang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(24, 3, 'Koperasi Kradinan', 42.00, 50000.00, '2025-12-19', 'diterima', 'Susu berkualitas bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(25, 3, 'Koperasi Kradinan', 39.00, 50000.00, '2025-12-20', 'diterima', 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(26, 3, 'Koperasi Kradinan', 41.00, 50000.00, '2025-12-21', 'terkirim', 'Dalam perjalanan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(27, 3, 'Koperasi Kradinan', 37.00, 50000.00, '2025-12-22', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(28, 3, 'Koperasi Kradinan', 40.00, 50000.00, '2025-12-23', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(29, 3, 'Koperasi Kradinan', 43.00, 50000.00, '2025-12-24', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(30, 3, 'Koperasi Kradinan', 40.00, 50000.00, '2025-12-25', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(31, 4, 'Koperasi Kradinan', 58.00, 50000.00, '2025-12-16', 'diterima', 'Susu berkualitas baik', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(32, 4, 'Koperasi Kradinan', 62.00, 50000.00, '2025-12-17', 'diterima', 'Susu segar', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(33, 4, 'Koperasi Kradinan', 55.00, 50000.00, '2025-12-18', 'diterima', 'Produksi kurang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(34, 4, 'Koperasi Kradinan', 65.00, 50000.00, '2025-12-19', 'diterima', 'Susu berkualitas bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(35, 4, 'Koperasi Kradinan', 60.00, 50000.00, '2025-12-20', 'diterima', 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(36, 4, 'Koperasi Kradinan', 63.00, 50000.00, '2025-12-21', 'terkirim', 'Dalam perjalanan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(37, 4, 'Koperasi Kradinan', 57.00, 50000.00, '2025-12-22', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(38, 4, 'Koperasi Kradinan', 61.00, 50000.00, '2025-12-23', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(39, 4, 'Koperasi Kradinan', 64.00, 50000.00, '2025-12-24', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(40, 4, 'Koperasi Kradinan', 60.00, 50000.00, '2025-12-25', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(41, 5, 'Koperasi Kradinan', 41.00, 50000.00, '2025-12-16', 'diterima', 'Susu berkualitas baik', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(42, 5, 'Koperasi Kradinan', 43.00, 50000.00, '2025-12-17', 'diterima', 'Susu segar', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(43, 5, 'Koperasi Kradinan', 39.00, 50000.00, '2025-12-18', 'diterima', 'Produksi kurang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(44, 5, 'Koperasi Kradinan', 45.00, 50000.00, '2025-12-19', 'diterima', 'Susu berkualitas bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(45, 5, 'Koperasi Kradinan', 42.00, 50000.00, '2025-12-20', 'diterima', 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(46, 5, 'Koperasi Kradinan', 44.00, 50000.00, '2025-12-21', 'terkirim', 'Dalam perjalanan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(47, 5, 'Koperasi Kradinan', 40.00, 50000.00, '2025-12-22', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(48, 5, 'Koperasi Kradinan', 43.00, 50000.00, '2025-12-23', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(49, 5, 'Koperasi Kradinan', 46.00, 50000.00, '2025-12-24', 'pending', 'Menunggu pengambilan', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(50, 5, 'Koperasi Kradinan', 43.00, 50000.00, '2025-12-25', 'pending', 'Siap kirim', '2025-12-25 13:00:46', '2025-12-25 13:00:46');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `idlaporan` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `jenis_laporan` enum('mingguan','bulanan','custom') DEFAULT 'bulanan',
  `file_path` varchar(255) DEFAULT NULL,
  `tanggal_generate` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`idlaporan`, `iduser`, `periode`, `jenis_laporan`, `file_path`, `tanggal_generate`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-12', 'bulanan', '/storage/reports/admin_bulanan_202512.pdf', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(2, 1, '2025-11', 'bulanan', '/storage/reports/admin_bulanan_202511.pdf', '2025-11-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(3, 2, '2025-12', 'bulanan', '/storage/reports/pengelola_bulanan_202512.pdf', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(4, 2, '2025-W50', 'mingguan', '/storage/reports/pengelola_mingguan_2025W50.pdf', '2025-12-18 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(5, 3, '2025-12', 'bulanan', '/storage/reports/peternak1_bulanan_202512.pdf', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(6, 3, '2025-W50', 'mingguan', '/storage/reports/peternak1_mingguan_2025W50.pdf', '2025-12-18 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(7, 4, '2025-12', 'bulanan', '/storage/reports/peternak2_bulanan_202512.pdf', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(8, 4, '2025-W50', 'mingguan', '/storage/reports/peternak2_mingguan_2025W50.pdf', '2025-12-18 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(9, 5, '2025-12', 'bulanan', '/storage/reports/peternak3_bulanan_202512.pdf', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(10, 5, '2025-W50', 'mingguan', '/storage/reports/peternak3_mingguan_2025W50.pdf', '2025-12-18 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(11, 6, '2025-12', 'bulanan', '/storage/reports/peternak4_bulanan_202512.pdf', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(12, 6, '2025-W50', 'mingguan', '/storage/reports/peternak4_mingguan_2025W50.pdf', '2025-12-18 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(13, 7, '2025-12', 'bulanan', '/storage/reports/peternak5_bulanan_202512.pdf', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(14, 7, '2025-W50', 'mingguan', '/storage/reports/peternak5_mingguan_2025W50.pdf', '2025-12-18 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_01_01_000000_create_settings_table', 2),
(5, '2026_01_02_070557_add_waktu_setor_to_produksi_harian_table', 3),
(6, '2026_01_02_000000_update_peternak_and_create_slip_pembayaran', 4);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `idnotif` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pesan` text NOT NULL,
  `tipe` enum('info','success','warning','error') DEFAULT 'info',
  `kategori` enum('semua','jadwal','bagi_hasil') DEFAULT 'semua',
  `status_baca` enum('belum_baca','sudah_baca') DEFAULT 'belum_baca',
  `waktu_kirim` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`idnotif`, `iduser`, `judul`, `pesan`, `tipe`, `kategori`, `status_baca`, `waktu_kirim`, `created_at`, `updated_at`) VALUES
(1, 1, 'Selamat Datang Admin', 'Anda telah berhasil login ke sistem SIPERAH', 'success', 'semua', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(2, 1, 'Laporan Produksi Baru', 'Ada 50 data produksi baru telah tercatat hari ini', 'info', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(3, 1, 'Distribusi Selesai', 'Semua distribusi untuk hari ini telah diterima', 'success', 'semua', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(4, 2, 'Selamat Datang Pengelola', 'Anda telah berhasil login ke sistem SIPERAH', 'success', 'semua', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(5, 2, 'Produksi Tercatat', 'Produksi dari 5 peternak telah tercatat hari ini', 'info', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(6, 2, 'Bagi Hasil Dihitung', 'Bagi hasil untuk hari ini telah dihitung otomatis', 'success', 'bagi_hasil', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(7, 2, 'Status Distribusi Update', 'Ada 5 distribusi yang statusnya berubah menjadi terkirim', 'info', 'jadwal', 'belum_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(8, 3, 'Produksi Tercatat', 'Produksi Anda hari ini sebesar 48 liter telah tercatat', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(9, 3, 'Bagi Hasil Dihitung', 'Bagi hasil Anda dihitung otomatis sebesar Rp 2.400.000', 'success', 'bagi_hasil', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(10, 3, 'Distribusi Diterima', 'Susu Anda hari ini telah diterima oleh koperasi', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(11, 3, 'Reminder Input Produksi', 'Jangan lupa input produksi hari ini!', 'warning', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 19:43:41'),
(12, 4, 'Produksi Tercatat', 'Produksi Anda hari ini sebesar 54 liter telah tercatat', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(13, 4, 'Bagi Hasil Dihitung', 'Bagi hasil Anda dihitung otomatis sebesar Rp 2.700.000', 'success', 'bagi_hasil', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(14, 4, 'Distribusi Diterima', 'Susu Anda hari ini telah diterima oleh koperasi', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(15, 4, 'Target Tercapai', 'Produksi Anda mencapai target bulanan!', 'success', 'bagi_hasil', 'belum_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(16, 5, 'Produksi Tercatat', 'Produksi Anda hari ini sebesar 40 liter telah tercatat', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(17, 5, 'Bagi Hasil Dihitung', 'Bagi hasil Anda dihitung otomatis sebesar Rp 2.000.000', 'success', 'bagi_hasil', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(18, 5, 'Distribusi Diterima', 'Susu Anda hari ini telah diterima oleh koperasi', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(19, 5, 'Reminder Input Produksi', 'Jangan lupa input produksi hari ini!', 'warning', 'jadwal', 'belum_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(20, 6, 'Produksi Tercatat', 'Produksi Anda hari ini sebesar 60 liter telah tercatat', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(21, 6, 'Bagi Hasil Dihitung', 'Bagi hasil Anda dihitung otomatis sebesar Rp 3.000.000', 'success', 'bagi_hasil', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(22, 6, 'Distribusi Diterima', 'Susu Anda hari ini telah diterima oleh koperasi', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(23, 6, 'Produksi Tinggi', 'Produksi Anda hari ini sangat tinggi! Congratulations!', 'success', 'bagi_hasil', 'belum_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(24, 7, 'Produksi Tercatat', 'Produksi Anda hari ini sebesar 43 liter telah tercatat', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(25, 7, 'Bagi Hasil Dihitung', 'Bagi hasil Anda dihitung otomatis sebesar Rp 2.150.000', 'success', 'bagi_hasil', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(26, 7, 'Distribusi Diterima', 'Susu Anda hari ini telah diterima oleh koperasi', 'success', 'jadwal', 'sudah_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(27, 7, 'Reminder Input Produksi', 'Jangan lupa input produksi hari ini!', 'warning', 'jadwal', 'belum_baca', '2025-12-25 13:00:46', '2025-12-25 13:00:46', '2025-12-25 13:00:46');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peternak`
--

CREATE TABLE `peternak` (
  `idpeternak` bigint(20) UNSIGNED NOT NULL,
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `nama_peternak` varchar(100) NOT NULL,
  `no_peternak` varchar(20) DEFAULT NULL,
  `kelompok` varchar(50) DEFAULT NULL,
  `jumlah_sapi` int(11) DEFAULT 0,
  `lokasi` varchar(255) DEFAULT NULL,
  `koperasi_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peternak`
--

INSERT INTO `peternak` (`idpeternak`, `iduser`, `nama_peternak`, `no_peternak`, `kelompok`, `jumlah_sapi`, `lokasi`, `koperasi_id`, `created_at`, `updated_at`) VALUES
(1, 3, 'Peternak Satu - Profil', NULL, NULL, 10, 'Kradinan Utara', 'KOP001', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(2, 4, 'Peternak Dua - Profil', NULL, NULL, 12, 'Kradinan Tengah', 'KOP002', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(3, 5, 'Peternak Tiga - Profil', NULL, NULL, 8, 'Kradinan Selatan', 'KOP003', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(4, 6, 'Peternak Empat - Profil', NULL, NULL, 15, 'Kradinan Barat', 'KOP004', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(5, 7, 'Peternak Lima - Profil', NULL, NULL, 9, 'Kradinan Timur', 'KOP005', '2025-12-25 13:00:46', '2025-12-25 13:00:46');

-- --------------------------------------------------------

--
-- Table structure for table `produksi_harian`
--

CREATE TABLE `produksi_harian` (
  `idproduksi` bigint(20) UNSIGNED NOT NULL,
  `idpeternak` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_setor` enum('pagi','sore') DEFAULT NULL,
  `jumlah_susu_liter` decimal(10,2) NOT NULL,
  `biaya_pakan` decimal(15,2) DEFAULT 0.00,
  `biaya_tenaga` decimal(15,2) DEFAULT 0.00,
  `biaya_operasional` decimal(15,2) DEFAULT 0.00,
  `total_biaya` decimal(15,2) GENERATED ALWAYS AS (`biaya_pakan` + `biaya_tenaga` + `biaya_operasional`) STORED,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produksi_harian`
--

INSERT INTO `produksi_harian` (`idproduksi`, `idpeternak`, `tanggal`, `waktu_setor`, `jumlah_susu_liter`, `biaya_pakan`, `biaya_tenaga`, `biaya_operasional`, `foto_bukti`, `catatan`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-12-16', NULL, 45.00, 50000.00, 30000.00, 20000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(2, 1, '2025-12-17', NULL, 48.00, 52000.00, 30000.00, 20000.00, NULL, 'Produksi meningkat', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(3, 1, '2025-12-18', NULL, 42.00, 48000.00, 30000.00, 20000.00, NULL, 'Sapi kurang sehat', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(4, 1, '2025-12-19', NULL, 50.00, 55000.00, 30000.00, 20000.00, NULL, 'Produksi tinggi', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(5, 1, '2025-12-20', NULL, 46.00, 51000.00, 30000.00, 20000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(6, 1, '2025-12-21', NULL, 49.00, 54000.00, 30000.00, 20000.00, NULL, 'Produksi bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(7, 1, '2025-12-22', NULL, 44.00, 49000.00, 30000.00, 20000.00, NULL, 'Produksi sedang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(8, 1, '2025-12-23', NULL, 47.00, 52000.00, 30000.00, 20000.00, NULL, 'Produksi bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(9, 1, '2025-12-24', NULL, 51.00, 56000.00, 30000.00, 20000.00, NULL, 'Produksi meningkat', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(10, 1, '2025-12-25', NULL, 48.00, 53000.00, 30000.00, 20000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(11, 2, '2025-12-16', NULL, 52.00, 55000.00, 35000.00, 22000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(12, 2, '2025-12-17', NULL, 55.00, 57000.00, 35000.00, 22000.00, NULL, 'Produksi tinggi', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(13, 2, '2025-12-18', NULL, 48.00, 52000.00, 35000.00, 22000.00, NULL, 'Produksi menurun', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(14, 2, '2025-12-19', NULL, 58.00, 60000.00, 35000.00, 22000.00, NULL, 'Produksi sangat tinggi', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(15, 2, '2025-12-20', NULL, 50.00, 54000.00, 35000.00, 22000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(16, 2, '2025-12-21', NULL, 56.00, 58000.00, 35000.00, 22000.00, NULL, 'Produksi bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(17, 2, '2025-12-22', NULL, 49.00, 52000.00, 35000.00, 22000.00, NULL, 'Produksi sedang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(18, 2, '2025-12-23', NULL, 53.00, 55000.00, 35000.00, 22000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(19, 2, '2025-12-24', NULL, 57.00, 59000.00, 35000.00, 22000.00, NULL, 'Produksi tinggi', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(20, 2, '2025-12-25', NULL, 54.00, 56000.00, 35000.00, 22000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(21, 3, '2025-12-16', NULL, 38.00, 42000.00, 28000.00, 18000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(22, 3, '2025-12-17', NULL, 40.00, 44000.00, 28000.00, 18000.00, NULL, 'Produksi meningkat', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(23, 3, '2025-12-18', NULL, 36.00, 40000.00, 28000.00, 18000.00, NULL, 'Produksi menurun', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(24, 3, '2025-12-19', NULL, 42.00, 46000.00, 28000.00, 18000.00, NULL, 'Produksi bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(25, 3, '2025-12-20', NULL, 39.00, 43000.00, 28000.00, 18000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(26, 3, '2025-12-21', NULL, 41.00, 45000.00, 28000.00, 18000.00, NULL, 'Produksi bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(27, 3, '2025-12-22', NULL, 37.00, 41000.00, 28000.00, 18000.00, NULL, 'Produksi sedang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(28, 3, '2025-12-23', NULL, 40.00, 44000.00, 28000.00, 18000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(29, 3, '2025-12-24', NULL, 43.00, 47000.00, 28000.00, 18000.00, NULL, 'Produksi meningkat', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(30, 3, '2025-12-25', NULL, 40.00, 44000.00, 28000.00, 18000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(31, 4, '2025-12-16', NULL, 58.00, 62000.00, 40000.00, 25000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(32, 4, '2025-12-17', NULL, 62.00, 66000.00, 40000.00, 25000.00, NULL, 'Produksi tinggi', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(33, 4, '2025-12-18', NULL, 55.00, 59000.00, 40000.00, 25000.00, NULL, 'Produksi menurun', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(34, 4, '2025-12-19', NULL, 65.00, 69000.00, 40000.00, 25000.00, NULL, 'Produksi sangat tinggi', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(35, 4, '2025-12-20', NULL, 60.00, 64000.00, 40000.00, 25000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(36, 4, '2025-12-21', NULL, 63.00, 67000.00, 40000.00, 25000.00, NULL, 'Produksi bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(37, 4, '2025-12-22', NULL, 57.00, 61000.00, 40000.00, 25000.00, NULL, 'Produksi sedang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(38, 4, '2025-12-23', NULL, 61.00, 65000.00, 40000.00, 25000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(39, 4, '2025-12-24', NULL, 64.00, 68000.00, 40000.00, 25000.00, NULL, 'Produksi tinggi', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(40, 4, '2025-12-25', NULL, 60.00, 64000.00, 40000.00, 25000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(41, 5, '2025-12-16', NULL, 41.00, 45000.00, 29000.00, 19000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(42, 5, '2025-12-17', NULL, 43.00, 47000.00, 29000.00, 19000.00, NULL, 'Produksi meningkat', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(43, 5, '2025-12-18', NULL, 39.00, 43000.00, 29000.00, 19000.00, NULL, 'Produksi menurun', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(44, 5, '2025-12-19', NULL, 45.00, 49000.00, 29000.00, 19000.00, NULL, 'Produksi bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(45, 5, '2025-12-20', NULL, 42.00, 46000.00, 29000.00, 19000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(46, 5, '2025-12-21', NULL, 44.00, 48000.00, 29000.00, 19000.00, NULL, 'Produksi bagus', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(47, 5, '2025-12-22', NULL, 40.00, 44000.00, 29000.00, 19000.00, NULL, 'Produksi sedang', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(48, 5, '2025-12-23', NULL, 43.00, 47000.00, 29000.00, 19000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(49, 5, '2025-12-24', NULL, 46.00, 50000.00, 29000.00, 19000.00, NULL, 'Produksi meningkat', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(50, 5, '2025-12-25', NULL, 43.00, 47000.00, 29000.00, 19000.00, NULL, 'Produksi normal', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(51, 1, '2026-01-05', 'pagi', 10.50, 0.00, 0.00, 0.00, NULL, NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07'),
(52, 2, '2026-01-05', 'pagi', 10.50, 0.00, 0.00, 0.00, NULL, NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07'),
(53, 3, '2026-01-05', 'pagi', 10.50, 0.00, 0.00, 0.00, NULL, NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07'),
(54, 4, '2026-01-05', 'pagi', 10.50, 0.00, 0.00, 0.00, NULL, NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07'),
(55, 5, '2026-01-05', 'pagi', 10.50, 0.00, 0.00, 0.00, NULL, NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('IOhTVW66sj9xQgc7KFIySm1NwcVfr5Ej0JlQGZS0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib1gwYUluUm95RVFjNFNVMlRpeUVvNW9nU3F5ZGRLQllNR3V5NVVaSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1767629460),
('jO8uGFgafOGyOl1ojSSYGWro8CPDPJwQPPQya14f', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRHB0OVpXMnN1MmVxY0xoTWthcE54dHZHM2RkT0N4SUNYdWFmQkFtMiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQtcGVuZ2Vsb2xhIjtzOjU6InJvdXRlIjtzOjE5OiJkYXNoYm9hcmQucGVuZ2Vsb2xhIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1767627049),
('Wp26fPT2Y6JaPsQ1eAFKU9qlaDnF8kByiBHFrrDx', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQUh0bHNpa0lCbXVNWjRuUTZubmR0OXFBYUo1RERVQ1JKcnRjdWozbiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9kdWtzaS9pbnB1dCI7czo1OiJyb3V0ZSI7czoxNToicHJvZHVrc2kuY3JlYXRlIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1767623308);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'feature_produksi', '1', 'Fitur Input Produksi untuk Peternak', NULL, NULL),
(2, 'feature_distribusi', '1', 'Fitur Input Distribusi untuk Peternak', NULL, NULL),
(3, 'feature_notifikasi', '1', 'Fitur Notifikasi untuk Semua User', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slip_pembayaran`
--

CREATE TABLE `slip_pembayaran` (
  `idslip` bigint(20) UNSIGNED NOT NULL,
  `idpeternak` bigint(20) UNSIGNED NOT NULL,
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `jumlah_susu` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_pembayaran` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_shr` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_hutang_bl_ll` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_pakan_a` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_pakan_b` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_vitamix` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_konsentrat` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_skim` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_ib_keswan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_susu_a` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_kas_bon` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_pakan_b_2` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_sp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_karpet` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_vaksin` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_lain_lain` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_potongan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sisa_pembayaran` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `tanggal_bayar` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slip_pembayaran`
--

INSERT INTO `slip_pembayaran` (`idslip`, `idpeternak`, `bulan`, `tahun`, `jumlah_susu`, `harga_satuan`, `total_pembayaran`, `potongan_shr`, `potongan_hutang_bl_ll`, `potongan_pakan_a`, `potongan_pakan_b`, `potongan_vitamix`, `potongan_konsentrat`, `potongan_skim`, `potongan_ib_keswan`, `potongan_susu_a`, `potongan_kas_bon`, `potongan_pakan_b_2`, `potongan_sp`, `potongan_karpet`, `potongan_vaksin`, `potongan_lain_lain`, `total_potongan`, `sisa_pembayaran`, `status`, `tanggal_bayar`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2026, 10.50, 50000.00, 525000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 525000.00, 'pending', NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07'),
(2, 2, 1, 2026, 10.50, 50000.00, 525000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 525000.00, 'pending', NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07'),
(3, 3, 1, 2026, 10.50, 50000.00, 525000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 525000.00, 'pending', NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07'),
(4, 4, 1, 2026, 10.50, 50000.00, 525000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 525000.00, 'pending', NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07'),
(5, 5, 1, 2026, 10.50, 50000.00, 525000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 525000.00, 'pending', NULL, '2026-01-05 08:13:07', '2026-01-05 08:13:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `iduser` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('peternak','pengelola','admin') NOT NULL DEFAULT 'peternak',
  `nohp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`iduser`, `nama`, `email`, `password`, `role`, `nohp`, `alamat`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin SIPERAH', 'admin@siperah.com', '$2y$12$eLgXGlDVfLKHtoVShC0w9ex5k2k70tBB315DtAbeS1tOS1zQiDceS', 'admin', '081234567890', 'Bandung, West Java', 'aktif', '2025-12-25 13:00:46', '2025-12-25 08:40:54'),
(2, 'Pengelola Koperasi', 'pengelola@siperah.com', '$2y$12$eLgXGlDVfLKHtoVShC0w9ex5k2k70tBB315DtAbeS1tOS1zQiDceS', 'pengelola', '081234567891', 'Desa Kradinan, Bandung', 'aktif', '2025-12-25 13:00:46', '2025-12-25 16:07:09'),
(3, 'Peternak Satu', 'peternak1@siperah.com', '$2y$12$eLgXGlDVfLKHtoVShC0w9ex5k2k70tBB315DtAbeS1tOS1zQiDceS', 'peternak', '081234567892', 'Kradinan, Bandung', 'aktif', '2025-12-25 13:00:46', '2025-12-25 16:07:18'),
(4, 'Peternak Dua', 'peternak2@siperah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'peternak', '081234567893', 'Kradinan, Bandung', 'aktif', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(5, 'Peternak Tiga', 'peternak3@siperah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'peternak', '081234567894', 'Kradinan, Bandung', 'aktif', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(6, 'Peternak Empat', 'peternak4@siperah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'peternak', '081234567895', 'Kradinan, Bandung', 'aktif', '2025-12-25 13:00:46', '2025-12-25 13:00:46'),
(7, 'Peternak Lima', 'peternak5@siperah.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'peternak', '081234567896', 'Kradinan, Bandung', 'aktif', '2025-12-25 13:00:46', '2025-12-25 13:00:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bagi_hasil`
--
ALTER TABLE `bagi_hasil`
  ADD PRIMARY KEY (`idbagi_hasil`),
  ADD UNIQUE KEY `idproduksi` (`idproduksi`),
  ADD KEY `idx_idproduksi` (`idproduksi`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `distribusi`
--
ALTER TABLE `distribusi`
  ADD PRIMARY KEY (`iddistribusi`),
  ADD KEY `idx_idpeternak` (`idpeternak`),
  ADD KEY `idx_tanggal_kirim` (`tanggal_kirim`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`idlaporan`),
  ADD KEY `idx_iduser` (`iduser`),
  ADD KEY `idx_jenis_laporan` (`jenis_laporan`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`idnotif`),
  ADD KEY `idx_iduser` (`iduser`),
  ADD KEY `idx_status_baca` (`status_baca`),
  ADD KEY `idx_kategori` (`kategori`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `peternak`
--
ALTER TABLE `peternak`
  ADD PRIMARY KEY (`idpeternak`),
  ADD UNIQUE KEY `iduser` (`iduser`),
  ADD KEY `idx_iduser` (`iduser`);

--
-- Indexes for table `produksi_harian`
--
ALTER TABLE `produksi_harian`
  ADD PRIMARY KEY (`idproduksi`),
  ADD KEY `idx_idpeternak` (`idpeternak`),
  ADD KEY `idx_tanggal` (`tanggal`),
  ADD KEY `idx_peternak_tanggal` (`idpeternak`,`tanggal`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `slip_pembayaran`
--
ALTER TABLE `slip_pembayaran`
  ADD PRIMARY KEY (`idslip`),
  ADD KEY `slip_pembayaran_idpeternak_foreign` (`idpeternak`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`iduser`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bagi_hasil`
--
ALTER TABLE `bagi_hasil`
  MODIFY `idbagi_hasil` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `distribusi`
--
ALTER TABLE `distribusi`
  MODIFY `iddistribusi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `idlaporan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `idnotif` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `peternak`
--
ALTER TABLE `peternak`
  MODIFY `idpeternak` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produksi_harian`
--
ALTER TABLE `produksi_harian`
  MODIFY `idproduksi` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `slip_pembayaran`
--
ALTER TABLE `slip_pembayaran`
  MODIFY `idslip` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `iduser` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bagi_hasil`
--
ALTER TABLE `bagi_hasil`
  ADD CONSTRAINT `bagi_hasil_ibfk_1` FOREIGN KEY (`idproduksi`) REFERENCES `produksi_harian` (`idproduksi`) ON DELETE CASCADE;

--
-- Constraints for table `distribusi`
--
ALTER TABLE `distribusi`
  ADD CONSTRAINT `distribusi_ibfk_1` FOREIGN KEY (`idpeternak`) REFERENCES `peternak` (`idpeternak`) ON DELETE CASCADE;

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `users` (`iduser`) ON DELETE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `users` (`iduser`) ON DELETE CASCADE;

--
-- Constraints for table `peternak`
--
ALTER TABLE `peternak`
  ADD CONSTRAINT `peternak_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `users` (`iduser`) ON DELETE CASCADE;

--
-- Constraints for table `produksi_harian`
--
ALTER TABLE `produksi_harian`
  ADD CONSTRAINT `produksi_harian_ibfk_1` FOREIGN KEY (`idpeternak`) REFERENCES `peternak` (`idpeternak`) ON DELETE CASCADE;

--
-- Constraints for table `slip_pembayaran`
--
ALTER TABLE `slip_pembayaran`
  ADD CONSTRAINT `slip_pembayaran_idpeternak_foreign` FOREIGN KEY (`idpeternak`) REFERENCES `peternak` (`idpeternak`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
