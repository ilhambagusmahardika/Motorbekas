-- ================================================
-- Database: db_jual_motor
-- Aplikasi Jual Beli Motor Bekas
-- ================================================

CREATE DATABASE IF NOT EXISTS `db_jual_motor`;
USE `db_jual_motor`;

-- ================================================
-- Tabel Users
-- ================================================
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default user: admin / admin123
INSERT INTO `users` (`username`, `password`, `nama`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- ================================================
-- Tabel Motor
-- ================================================
CREATE TABLE IF NOT EXISTS `motor` (
  `motor_id` INT AUTO_INCREMENT PRIMARY KEY,
  `tgl_pembelian` DATE NOT NULL,
  `nama_penjual` VARCHAR(100) NOT NULL,
  `merek` VARCHAR(50) NOT NULL,
  `model` VARCHAR(50) NOT NULL,
  `warna` VARCHAR(30) NOT NULL,
  `tahun_pembuatan` YEAR NOT NULL,
  `nama_bpkb` VARCHAR(100) NOT NULL,
  `plat_nomor` VARCHAR(20) NOT NULL,
  `asal_wilayah` VARCHAR(50),
  `asal_kota` VARCHAR(50),
  `samsat_terdaftar` VARCHAR(100),
  `no_rangka` VARCHAR(50) NOT NULL,
  `no_mesin` VARCHAR(50) NOT NULL,
  `pajak_berlaku` DATE NOT NULL,
  `foto_motor` VARCHAR(255),
  `harga_beli` BIGINT NOT NULL DEFAULT 0,
  `status_motor` ENUM('Dibeli','Siap Jual','Terjual') NOT NULL DEFAULT 'Dibeli',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ================================================
-- Tabel Perbaikan
-- ================================================
CREATE TABLE IF NOT EXISTS `perbaikan` (
  `perbaikan_id` INT AUTO_INCREMENT PRIMARY KEY,
  `motor_id` INT NOT NULL,
  `biaya` BIGINT NOT NULL DEFAULT 0,
  `keterangan` TEXT,
  `tgl_perbaikan` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`motor_id`) REFERENCES `motor`(`motor_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ================================================
-- Tabel Penjualan
-- ================================================
CREATE TABLE IF NOT EXISTS `penjualan` (
  `penjualan_id` INT AUTO_INCREMENT PRIMARY KEY,
  `motor_id` INT NOT NULL,
  `tgl_jual` DATE NOT NULL,
  `nama_pembeli` VARCHAR(100) NOT NULL,
  `harga_jual` BIGINT NOT NULL DEFAULT 0,
  `dijual_melalui` VARCHAR(50),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`motor_id`) REFERENCES `motor`(`motor_id`) ON DELETE CASCADE
) ENGINE=InnoDB;
