-- MySQL schema for aplikasi_pinjaman (generated from migrations)
CREATE DATABASE IF NOT EXISTS `aplikasi_pinjaman` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `aplikasi_pinjaman`;

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','operator','viewer') NOT NULL DEFAULT 'operator',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `karyawans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nik` VARCHAR(255) NOT NULL,
  `nama` VARCHAR(255) NOT NULL,
  `jabatan` VARCHAR(255) DEFAULT NULL,
  `departemen` VARCHAR(255) DEFAULT NULL,
  `no_hp` VARCHAR(255) DEFAULT NULL,
  `no_wa` VARCHAR(255) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `jenis_kelamin` ENUM('L','P') DEFAULT NULL,
  `tanggal_masuk` DATE DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `karyawans_nik_unique` (`nik`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `periodes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(255) NOT NULL,
  `bulan` INT NOT NULL,
  `tahun` INT NOT NULL,
  `tanggal_mulai` DATE NOT NULL,
  `tanggal_selesai` DATE NOT NULL,
  `status` ENUM('aktif','selesai','draft') NOT NULL DEFAULT 'draft',
  `keterangan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pinjamans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_pinjaman` VARCHAR(255) NOT NULL,
  `karyawan_id` BIGINT UNSIGNED NOT NULL,
  `jumlah_pinjaman` DECIMAL(15,2) NOT NULL,
  `bunga_persen` DECIMAL(5,2) NOT NULL DEFAULT '0.00',
  `tenor` INT NOT NULL,
  `cicilan_per_bulan` DECIMAL(15,2) NOT NULL,
  `sisa_pinjaman` DECIMAL(15,2) NOT NULL,
  `tanggal_pinjaman` DATE NOT NULL,
  `keperluan` TEXT DEFAULT NULL,
  `status` ENUM('pending','disetujui','ditolak','lunas') NOT NULL DEFAULT 'pending',
  `disetujui_oleh` BIGINT UNSIGNED DEFAULT NULL,
  `tanggal_disetujui` TIMESTAMP NULL DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pinjamans_no_pinjaman_unique` (`no_pinjaman`),
  CONSTRAINT `pinjamans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `pinjamans_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `kasbons` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_kasbon` VARCHAR(255) NOT NULL,
  `karyawan_id` BIGINT UNSIGNED NOT NULL,
  `jumlah_kasbon` DECIMAL(15,2) NOT NULL,
  `sisa_kasbon` DECIMAL(15,2) NOT NULL,
  `tanggal_kasbon` DATE NOT NULL,
  `keperluan` TEXT DEFAULT NULL,
  `status` ENUM('pending','disetujui','ditolak','lunas') NOT NULL DEFAULT 'pending',
  `disetujui_oleh` BIGINT UNSIGNED DEFAULT NULL,
  `tanggal_disetujui` TIMESTAMP NULL DEFAULT NULL,
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kasbons_no_kasbon_unique` (`no_kasbon`),
  CONSTRAINT `kasbons_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `kasbons_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pinjaman_pembayarans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `pinjaman_id` BIGINT UNSIGNED NOT NULL,
  `pembayaran_ke` SMALLINT UNSIGNED NOT NULL,
  `jumlah_bayar` DECIMAL(15,2) NOT NULL,
  `tanggal_bayar` DATE NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pinjaman_pembayarans_pinjaman_id_pembayaran_ke_unique` (`pinjaman_id`,`pembayaran_ke`),
  CONSTRAINT `pinjaman_pembayarans_pinjaman_id_foreign` FOREIGN KEY (`pinjaman_id`) REFERENCES `pinjamans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `simpanans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id` BIGINT UNSIGNED NOT NULL,
  `simpanan_pokok` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `simpanan_wajib` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `simpanan_sukarela` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `total_simpanan` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `simpanans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `potongans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id` BIGINT UNSIGNED NOT NULL,
  `periode_id` BIGINT UNSIGNED NOT NULL,
  `potongan_pinjaman` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `potongan_kasbon` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `potongan_simpanan_wajib` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `potongan_simpanan_sukarela` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `potongan_lainnya` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `keterangan_lainnya` VARCHAR(255) DEFAULT NULL,
  `total_potongan` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `potongans_karyawan_id_periode_id_unique` (`karyawan_id`,`periode_id`),
  CONSTRAINT `potongans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `potongans_periode_id_foreign` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `riwayat_simpanans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id` BIGINT UNSIGNED NOT NULL,
  `periode_id` BIGINT UNSIGNED DEFAULT NULL,
  `jenis` ENUM('masuk','keluar') NOT NULL,
  `kategori` ENUM('pokok','wajib','sukarela') NOT NULL,
  `jumlah` DECIMAL(15,2) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `tanggal` DATE NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `riwayat_simpanans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `riwayat_simpanans_periode_id_foreign` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `import_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `tipe` VARCHAR(255) NOT NULL,
  `total_baris` INT NOT NULL DEFAULT 0,
  `berhasil` INT NOT NULL DEFAULT 0,
  `gagal` INT NOT NULL DEFAULT 0,
  `pesan_error` TEXT DEFAULT NULL,
  `status` ENUM('proses','selesai','gagal') NOT NULL DEFAULT 'proses',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `import_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `wa_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `karyawan_id` BIGINT UNSIGNED DEFAULT NULL,
  `no_tujuan` VARCHAR(255) NOT NULL,
  `pesan` TEXT NOT NULL,
  `status` ENUM('pending','terkirim','gagal') NOT NULL DEFAULT 'pending',
  `response` TEXT DEFAULT NULL,
  `tipe` VARCHAR(255) DEFAULT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `wa_logs_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `wa_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
