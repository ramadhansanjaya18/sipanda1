-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 01 Jan 2026 pada 18.10
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipanda_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `balita`
--

CREATE TABLE `balita` (
  `id_balita` int(11) NOT NULL,
  `nama_balita` varchar(100) NOT NULL,
  `nik_balita` varchar(16) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `nama_orang_tua` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT NULL,
  `id_kader_pendaftar` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `balita`
--

INSERT INTO `balita` (`id_balita`, `nama_balita`, `nik_balita`, `tanggal_lahir`, `jenis_kelamin`, `nama_orang_tua`, `alamat`, `foto_profil`, `id_kader_pendaftar`, `is_active`, `created_at`) VALUES
(15, 'Muhammad Ardhan Syahputra', '3212181309230002', '2023-09-13', 'Laki-laki', 'Etin Supriatin', 'blok celo RT.21/RW.05', NULL, 2, 0, '2025-10-28 16:31:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `imunisasi`
--

CREATE TABLE `imunisasi` (
  `id_imunisasi` int(11) NOT NULL,
  `id_balita` int(11) NOT NULL,
  `tanggal_imunisasi` date NOT NULL,
  `jenis_vaksin` varchar(100) NOT NULL,
  `catatan` text DEFAULT NULL,
  `status_validasi` enum('Belum Divalidasi','Valid','Tidak Valid') DEFAULT 'Belum Divalidasi',
  `id_kader` int(11) DEFAULT NULL,
  `id_bidan_validator` int(11) DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `imunisasi`
--

INSERT INTO `imunisasi` (`id_imunisasi`, `id_balita`, `tanggal_imunisasi`, `jenis_vaksin`, `catatan`, `status_validasi`, `id_kader`, `id_bidan_validator`, `validated_at`, `created_at`) VALUES
(8, 15, '2025-10-28', 'Polio', NULL, 'Valid', 2, NULL, '2025-10-30 20:11:19', '2025-12-09 15:39:35'),
(9, 15, '2025-11-01', 'Polio', NULL, 'Valid', 3, NULL, '2025-11-06 18:26:40', '2025-12-09 15:39:35'),
(14, 15, '2025-12-05', 'BCG', NULL, 'Valid', 3, NULL, '2025-12-13 11:24:08', '2025-12-09 15:39:35'),
(15, 15, '2025-12-06', 'polio', NULL, 'Valid', 3, NULL, '2025-12-13 11:24:03', '2025-12-09 15:39:35'),
(16, 15, '2025-12-09', 'Polio', NULL, 'Valid', 3, NULL, '2025-12-13 09:07:03', '2025-12-09 15:39:35'),
(19, 15, '2025-12-13', 'DPT', NULL, 'Valid', 3, NULL, '2025-12-13 11:23:45', '2025-12-13 07:10:13'),
(20, 15, '2025-12-17', 'Polio', NULL, 'Valid', NULL, NULL, '2025-12-17 15:15:56', '2025-12-17 14:54:30'),
(21, 15, '2025-12-22', 'Polio', NULL, 'Tidak Valid', 3, NULL, '2025-12-22 08:37:07', '2025-12-22 07:33:26'),
(22, 15, '2025-12-22', 'DPT', NULL, 'Valid', 3, 1, '2025-12-22 11:14:55', '2025-12-22 07:41:37'),
(23, 15, '2025-12-22', 'Polio', NULL, 'Valid', 3, 1, '2025-12-22 11:14:51', '2025-12-22 11:01:58'),
(24, 15, '2025-12-22', 'Polio', NULL, 'Belum Divalidasi', 3, NULL, NULL, '2025-12-22 13:16:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `judul_kegiatan` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_kegiatan` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `judul_kegiatan`, `deskripsi`, `tanggal_kegiatan`, `created_at`) VALUES
(24, 'Posyandu', NULL, '2025-12-23', '2025-12-22 13:23:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id_pemeriksaan` int(11) NOT NULL,
  `id_balita` int(11) NOT NULL,
  `tanggal_periksa` date NOT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL COMMENT 'dalam KG',
  `tinggi_badan` decimal(5,2) DEFAULT NULL COMMENT 'dalam CM',
  `lingkar_kepala` decimal(5,2) DEFAULT NULL COMMENT 'dalam CM',
  `catatan` text DEFAULT NULL,
  `status_validasi` enum('Belum Divalidasi','Valid','Tidak Valid') DEFAULT 'Belum Divalidasi',
  `id_kader` int(11) DEFAULT NULL,
  `id_bidan_validator` int(11) DEFAULT NULL,
  `validated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pemeriksaan`
--

INSERT INTO `pemeriksaan` (`id_pemeriksaan`, `id_balita`, `tanggal_periksa`, `berat_badan`, `tinggi_badan`, `lingkar_kepala`, `catatan`, `status_validasi`, `id_kader`, `id_bidan_validator`, `validated_at`, `created_at`) VALUES
(14, 15, '2025-10-28', 8.50, 70.20, 45.00, NULL, 'Valid', 2, 1, '2025-10-29 19:47:26', '2025-10-28 16:46:39'),
(15, 15, '2025-11-28', 9.50, 75.00, 45.00, NULL, 'Valid', 2, 1, '2025-10-29 19:47:22', '2025-10-29 15:52:00'),
(16, 15, '2025-12-29', 10.00, 80.00, 50.00, NULL, 'Valid', 2, 1, '2025-10-29 19:47:18', '2025-10-29 16:20:44'),
(17, 15, '2025-11-05', 10.00, 85.00, 50.00, NULL, 'Valid', 3, NULL, '2025-11-06 07:23:39', '2025-10-30 21:22:18'),
(21, 15, '2025-12-06', 9.00, 80.00, 45.00, NULL, 'Valid', 3, NULL, '2025-12-13 09:06:57', '2025-12-06 14:32:11'),
(24, 15, '2025-12-22', 8.00, 70.00, 45.00, NULL, 'Tidak Valid', 3, NULL, '2025-12-22 08:36:58', '2025-12-22 07:33:41'),
(25, 15, '2025-12-22', 8.00, 70.00, 45.00, NULL, 'Valid', 3, NULL, '2025-12-22 08:37:11', '2025-12-22 07:41:52'),
(26, 15, '2025-12-22', 8.50, 70.20, 45.00, NULL, 'Valid', 3, 1, '2025-12-22 11:14:48', '2025-12-22 11:02:13'),
(27, 15, '2025-12-22', 8.50, 70.20, 50.00, NULL, 'Belum Divalidasi', 3, NULL, NULL, '2025-12-22 13:09:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('kader','bidan') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `created_at`, `is_active`) VALUES
(1, 'Umel', 'bidan_umel', '$2y$10$p.s/dLWitvVNNrdsPHc1vOSDr6w3a8E3IIDDaMwQAO3HlFO33WP1q', 'bidan', '2025-10-21 14:46:41', 1),
(2, 'Umroh', 'kader_umroh', 'kadersipanda', 'kader', '2025-10-21 14:46:41', 1),
(3, 'Adelita Nur Mu\'izah', 'kader_adel', '$2y$10$tgBPbE14e0asEQTkX6yJwOR0xt.GaGNQO3nNrJjGv5.DGcKOj5Tiu', 'kader', '2025-10-21 14:46:41', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `balita`
--
ALTER TABLE `balita`
  ADD PRIMARY KEY (`id_balita`),
  ADD UNIQUE KEY `nik_balita` (`nik_balita`),
  ADD KEY `id_kader_pendaftar` (`id_kader_pendaftar`);

--
-- Indeks untuk tabel `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD PRIMARY KEY (`id_imunisasi`),
  ADD KEY `id_balita` (`id_balita`),
  ADD KEY `id_kader` (`id_kader`),
  ADD KEY `id_bidan_validator` (`id_bidan_validator`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`);

--
-- Indeks untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id_pemeriksaan`),
  ADD KEY `id_balita` (`id_balita`),
  ADD KEY `id_kader` (`id_kader`),
  ADD KEY `id_bidan_validator` (`id_bidan_validator`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `balita`
--
ALTER TABLE `balita`
  MODIFY `id_balita` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT untuk tabel `imunisasi`
--
ALTER TABLE `imunisasi`
  MODIFY `id_imunisasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id_pemeriksaan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `balita`
--
ALTER TABLE `balita`
  ADD CONSTRAINT `balita_ibfk_1` FOREIGN KEY (`id_kader_pendaftar`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD CONSTRAINT `fk_imunisasi_balita` FOREIGN KEY (`id_balita`) REFERENCES `balita` (`id_balita`) ON DELETE CASCADE,
  ADD CONSTRAINT `imunisasi_ibfk_1` FOREIGN KEY (`id_balita`) REFERENCES `balita` (`id_balita`) ON DELETE CASCADE,
  ADD CONSTRAINT `imunisasi_ibfk_2` FOREIGN KEY (`id_kader`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `imunisasi_ibfk_3` FOREIGN KEY (`id_bidan_validator`) REFERENCES `users` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD CONSTRAINT `fk_pemeriksaan_balita` FOREIGN KEY (`id_balita`) REFERENCES `balita` (`id_balita`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`id_balita`) REFERENCES `balita` (`id_balita`) ON DELETE CASCADE,
  ADD CONSTRAINT `pemeriksaan_ibfk_2` FOREIGN KEY (`id_kader`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `pemeriksaan_ibfk_3` FOREIGN KEY (`id_bidan_validator`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
