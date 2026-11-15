/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `company_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `company_address` varchar(500) DEFAULT NULL,
  `logo_path` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_settings_user_id_unique` (`user_id`),
  CONSTRAINT `company_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `import_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `tipe` varchar(191) NOT NULL COMMENT 'karyawan, pinjaman, kasbon, dll',
  `total_baris` int(11) NOT NULL DEFAULT 0,
  `berhasil` int(11) NOT NULL DEFAULT 0,
  `gagal` int(11) NOT NULL DEFAULT 0,
  `pesan_error` text DEFAULT NULL,
  `status` enum('proses','selesai','gagal') NOT NULL DEFAULT 'proses',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `import_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `import_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `karyawans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `karyawans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `nik` varchar(191) NOT NULL COMMENT 'Nomor Induk Karyawan',
  `nama` varchar(191) NOT NULL,
  `jabatan` varchar(191) DEFAULT NULL,
  `no_hp` varchar(191) DEFAULT NULL,
  `no_wa` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `karyawans_nik_unique` (`nik`),
  UNIQUE KEY `karyawans_user_id_nik_unique` (`user_id`,`nik`),
  CONSTRAINT `karyawans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kasbons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kasbons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `no_kasbon` varchar(191) NOT NULL,
  `karyawan_id` bigint(20) unsigned NOT NULL,
  `jumlah_kasbon` decimal(15,2) NOT NULL,
  `sisa_kasbon` decimal(15,2) NOT NULL,
  `tanggal_kasbon` date NOT NULL,
  `keperluan` text DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak','lunas') NOT NULL DEFAULT 'pending',
  `disetujui_oleh` bigint(20) unsigned DEFAULT NULL,
  `tanggal_disetujui` timestamp NULL DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kasbons_no_kasbon_unique` (`no_kasbon`),
  UNIQUE KEY `kasbons_user_id_no_kasbon_unique` (`user_id`,`no_kasbon`),
  KEY `kasbons_karyawan_id_foreign` (`karyawan_id`),
  KEY `kasbons_disetujui_oleh_foreign` (`disetujui_oleh`),
  CONSTRAINT `kasbons_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kasbons_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`),
  CONSTRAINT `kasbons_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `periodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `periodes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `nama` varchar(191) NOT NULL COMMENT 'Misal: Januari 2026',
  `bulan` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` enum('aktif','selesai','draft') NOT NULL DEFAULT 'draft',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `periodes_user_id_bulan_tahun_unique` (`user_id`,`bulan`,`tahun`),
  CONSTRAINT `periodes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pinjaman_pembayarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pinjaman_pembayarans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pinjaman_id` bigint(20) unsigned NOT NULL,
  `pembayaran_ke` smallint(5) unsigned NOT NULL,
  `jumlah_bayar` decimal(15,2) NOT NULL,
  `tanggal_bayar` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pinjaman_pembayarans_pinjaman_id_pembayaran_ke_unique` (`pinjaman_id`,`pembayaran_ke`),
  CONSTRAINT `pinjaman_pembayarans_pinjaman_id_foreign` FOREIGN KEY (`pinjaman_id`) REFERENCES `pinjamans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pinjamans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pinjamans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `no_pinjaman` varchar(191) NOT NULL,
  `karyawan_id` bigint(20) unsigned NOT NULL,
  `jumlah_pinjaman` decimal(15,2) NOT NULL,
  `bunga_persen` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tenor` int(11) NOT NULL COMMENT 'Jumlah cicilan bulan',
  `cicilan_per_bulan` decimal(15,2) NOT NULL,
  `sisa_pinjaman` decimal(15,2) NOT NULL,
  `tanggal_pinjaman` date NOT NULL,
  `keperluan` text DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak','lunas') NOT NULL DEFAULT 'pending',
  `disetujui_oleh` bigint(20) unsigned DEFAULT NULL,
  `tanggal_disetujui` timestamp NULL DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pinjamans_no_pinjaman_unique` (`no_pinjaman`),
  UNIQUE KEY `pinjamans_user_id_no_pinjaman_unique` (`user_id`,`no_pinjaman`),
  KEY `pinjamans_karyawan_id_foreign` (`karyawan_id`),
  KEY `pinjamans_disetujui_oleh_foreign` (`disetujui_oleh`),
  CONSTRAINT `pinjamans_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pinjamans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`),
  CONSTRAINT `pinjamans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `potongans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `potongans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `karyawan_id` bigint(20) unsigned NOT NULL,
  `periode_id` bigint(20) unsigned NOT NULL,
  `potongan_pinjaman` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_kasbon` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_simpanan_wajib` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_simpanan_sukarela` decimal(15,2) NOT NULL DEFAULT 0.00,
  `potongan_lainnya` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan_lainnya` varchar(191) DEFAULT NULL,
  `total_potongan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `potongans_karyawan_id_periode_id_unique` (`karyawan_id`,`periode_id`),
  UNIQUE KEY `potongans_user_id_karyawan_id_periode_id_unique` (`user_id`,`karyawan_id`,`periode_id`),
  KEY `potongans_periode_id_foreign` (`periode_id`),
  CONSTRAINT `potongans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`),
  CONSTRAINT `potongans_periode_id_foreign` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`),
  CONSTRAINT `potongans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `riwayat_simpanans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `riwayat_simpanans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `karyawan_id` bigint(20) unsigned NOT NULL,
  `periode_id` bigint(20) unsigned DEFAULT NULL,
  `jenis` enum('masuk','keluar') NOT NULL COMMENT 'masuk = setor, keluar = tarik',
  `kategori` enum('pokok','wajib','sukarela') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `riwayat_simpanans_karyawan_id_foreign` (`karyawan_id`),
  KEY `riwayat_simpanans_periode_id_foreign` (`periode_id`),
  KEY `riwayat_simpanans_user_id_foreign` (`user_id`),
  CONSTRAINT `riwayat_simpanans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`),
  CONSTRAINT `riwayat_simpanans_periode_id_foreign` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_simpanans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `simpanans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `simpanans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `karyawan_id` bigint(20) unsigned NOT NULL,
  `simpanan_pokok` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Simpanan pokok satu kali saat masuk',
  `simpanan_wajib` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Simpanan wajib per bulan',
  `simpanan_sukarela` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Simpanan sukarela per bulan',
  `total_simpanan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `simpanans_user_id_karyawan_id_unique` (`user_id`,`karyawan_id`),
  KEY `simpanans_karyawan_id_foreign` (`karyawan_id`),
  CONSTRAINT `simpanans_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`),
  CONSTRAINT `simpanans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `username` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `role` enum('admin','operator','viewer') NOT NULL DEFAULT 'operator',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `wa_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `karyawan_id` bigint(20) unsigned DEFAULT NULL,
  `no_tujuan` varchar(191) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('pending','terkirim','gagal') NOT NULL DEFAULT 'pending',
  `response` text DEFAULT NULL,
  `tipe` varchar(191) DEFAULT NULL COMMENT 'slip, notifikasi, dll',
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wa_logs_karyawan_id_foreign` (`karyawan_id`),
  KEY `wa_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `wa_logs_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `wa_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2026_08_09_000001_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2026_08_09_000002_create_karyawans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_08_09_000003_create_periodes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_08_09_000004_create_pinjamans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_08_09_000005_create_kasbons_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_08_09_000006_create_simpanans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_08_09_000007_create_potongans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_08_09_000008_create_riwayat_simpanans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_08_09_000009_create_import_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_08_09_000010_create_wa_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_08_19_000001_add_email_to_karyawans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_08_19_000002_add_user_id_to_all_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_08_19_000003_add_user_id_to_periodes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_10_24_000001_drop_departemen_from_karyawans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_10_31_000001_remove_salary_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_10_31_000002_add_interest_rate_to_pinjamans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_10_31_000003_create_pinjaman_pembayarans_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_10_31_000004_create_company_settings_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_10_31_000005_add_company_address_to_company_settings_table',3);
