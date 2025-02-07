-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2025 at 04:29 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kepegawaian`
--

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--

CREATE TABLE `departemen` (
  `id_departemen` int(11) NOT NULL,
  `nama_departemen` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`id_departemen`, `nama_departemen`) VALUES
(1, 'SDM'),
(2, 'Teknologi Informasi'),
(3, 'Keuangan'),
(4, 'Pemasaran');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id_jabatan` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id_jabatan`, `nama_jabatan`) VALUES
(1, 'Manajer'),
(2, 'Staff Senior'),
(3, 'Staff Junior'),
(4, 'Magang'),
(5, 'Cleaning Service');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas_pegawai`
--

CREATE TABLE `log_aktivitas_pegawai` (
  `id_log` int(11) NOT NULL,
  `id_pegawai` int(11) DEFAULT NULL,
  `aksi` varchar(20) DEFAULT NULL,
  `tanggal_aksi` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas_pegawai`
--

INSERT INTO `log_aktivitas_pegawai` (`id_log`, `id_pegawai`, `aksi`, `tanggal_aksi`, `keterangan`) VALUES
(1, 1, 'UPDATE', '2025-02-04 15:25:01', 'Mengubah data pegawai: '),
(2, 2, 'INSERT', '2025-02-04 15:27:20', 'Menambah pegawai baru: Arif Julianto (Email: ariffjul43@gmail.com, Departemen: Teknologi Informasi, Jabatan: Staff Senior)'),
(3, 3, 'INSERT', '2025-02-06 11:50:02', 'Menambah pegawai baru: Supri anto (Email: supri@gmail.com, Departemen: SDM, Jabatan: Magang)'),
(4, 4, 'INSERT', '2025-02-07 14:12:31', 'Menambah pegawai baru: Edho setyo (Email: edho1@gmail.com, Departemen: Pemasaran, Jabatan: Staff Junior)');

-- --------------------------------------------------------

--
-- Table structure for table `pegawai`
--

CREATE TABLE `pegawai` (
  `id_pegawai` int(11) NOT NULL,
  `nama_depan` varchar(50) NOT NULL,
  `nama_belakang` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `id_departemen` int(11) DEFAULT NULL,
  `id_jabatan` int(11) DEFAULT NULL,
  `tanggal_bergabung` date NOT NULL,
  `gaji` decimal(10,2) NOT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pegawai`
--

INSERT INTO `pegawai` (`id_pegawai`, `nama_depan`, `nama_belakang`, `email`, `telepon`, `id_departemen`, `id_jabatan`, `tanggal_bergabung`, `gaji`, `status`) VALUES
(1, 'Aghus', 'yak', 'agus43@gmail.com', '0824814912', 4, 1, '2025-02-04', 5000000.00, 'aktif'),
(2, 'Arif', 'Julianto', 'ariffjul43@gmail.com', '087823330830', 2, 2, '2025-02-01', 10000000.00, 'aktif'),
(3, 'Supri', 'anto', 'supri@gmail.com', '087231231392', 1, 4, '2025-01-01', 3500000.00, 'aktif'),
(4, 'Edho', 'setyo', 'edho1@gmail.com', '0876545672', 4, 3, '2025-02-07', 3000000.00, 'aktif');

--
-- Triggers `pegawai`
--
DELIMITER $$
CREATE TRIGGER `log_pegawai_delete` BEFORE DELETE ON `pegawai` FOR EACH ROW BEGIN
    INSERT INTO log_aktivitas_pegawai (id_pegawai, aksi, keterangan)
    VALUES (OLD.id_pegawai, 'DELETE',
        CONCAT('Menghapus pegawai: ', OLD.nama_depan, ' ', OLD.nama_belakang,
        ' (Email: ', OLD.email,
        ', Departemen: ', (SELECT nama_departemen FROM departemen WHERE id_departemen = OLD.id_departemen),
        ', Jabatan: ', (SELECT nama_jabatan FROM jabatan WHERE id_jabatan = OLD.id_jabatan),
        ')')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_pegawai_insert` AFTER INSERT ON `pegawai` FOR EACH ROW BEGIN
    INSERT INTO log_aktivitas_pegawai (id_pegawai, aksi, keterangan)
    VALUES (NEW.id_pegawai, 'INSERT', 
        CONCAT('Menambah pegawai baru: ', NEW.nama_depan, ' ', NEW.nama_belakang,
        ' (Email: ', NEW.email,
        ', Departemen: ', (SELECT nama_departemen FROM departemen WHERE id_departemen = NEW.id_departemen),
        ', Jabatan: ', (SELECT nama_jabatan FROM jabatan WHERE id_jabatan = NEW.id_jabatan),
        ')')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_pegawai_update` AFTER UPDATE ON `pegawai` FOR EACH ROW BEGIN
    INSERT INTO log_aktivitas_pegawai (id_pegawai, aksi, keterangan)
    VALUES (NEW.id_pegawai, 'UPDATE',
        CONCAT('Mengubah data pegawai: ',
        CASE 
            WHEN OLD.nama_depan != NEW.nama_depan OR OLD.nama_belakang != NEW.nama_belakang
            THEN CONCAT('Nama dari "', OLD.nama_depan, ' ', OLD.nama_belakang, '" menjadi "', NEW.nama_depan, ' ', NEW.nama_belakang, '". ')
            ELSE ''
        END,
        CASE 
            WHEN OLD.email != NEW.email
            THEN CONCAT('Email dari "', OLD.email, '" menjadi "', NEW.email, '". ')
            ELSE ''
        END,
        CASE 
            WHEN OLD.id_departemen != NEW.id_departemen
            THEN CONCAT('Departemen dari "', 
                (SELECT nama_departemen FROM departemen WHERE id_departemen = OLD.id_departemen),
                '" menjadi "',
                (SELECT nama_departemen FROM departemen WHERE id_departemen = NEW.id_departemen),
                '". ')
            ELSE ''
        END,
        CASE 
            WHEN OLD.id_jabatan != NEW.id_jabatan
            THEN CONCAT('Jabatan dari "',
                (SELECT nama_jabatan FROM jabatan WHERE id_jabatan = OLD.id_jabatan),
                '" menjadi "',
                (SELECT nama_jabatan FROM jabatan WHERE id_jabatan = NEW.id_jabatan),
                '". ')
            ELSE ''
        END,
        CASE 
            WHEN OLD.gaji != NEW.gaji
            THEN CONCAT('Gaji dari ', OLD.gaji, ' menjadi ', NEW.gaji, '. ')
            ELSE ''
        END,
        CASE 
            WHEN OLD.status != NEW.status
            THEN CONCAT('Status dari "', OLD.status, '" menjadi "', NEW.status, '"')
            ELSE ''
        END)
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_pengguna` varchar(50) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `peran` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama_pengguna`, `kata_sandi`, `peran`) VALUES
(1, 'admin', '$2y$12$i7kCboQswIrKh38ItmKSOuh9D9M7h4Tq9tZtftqnyo1YGjuClDnem', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departemen`
--
ALTER TABLE `departemen`
  ADD PRIMARY KEY (`id_departemen`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indexes for table `log_aktivitas_pegawai`
--
ALTER TABLE `log_aktivitas_pegawai`
  ADD PRIMARY KEY (`id_log`);

--
-- Indexes for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD PRIMARY KEY (`id_pegawai`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_departemen` (`id_departemen`),
  ADD KEY `id_jabatan` (`id_jabatan`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `nama_pengguna` (`nama_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departemen`
--
ALTER TABLE `departemen`
  MODIFY `id_departemen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id_jabatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `log_aktivitas_pegawai`
--
ALTER TABLE `log_aktivitas_pegawai`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pegawai`
--
ALTER TABLE `pegawai`
  MODIFY `id_pegawai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pegawai`
--
ALTER TABLE `pegawai`
  ADD CONSTRAINT `pegawai_ibfk_1` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`),
  ADD CONSTRAINT `pegawai_ibfk_2` FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id_jabatan`),
  ADD CONSTRAINT `pegawai_ibfk_3` FOREIGN KEY (`id_pegawai`) REFERENCES `log_aktivitas_pegawai` (`id_log`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
