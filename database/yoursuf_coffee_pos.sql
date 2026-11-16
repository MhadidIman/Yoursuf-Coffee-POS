-- =============================================================================
-- DATABASE DUMP SIAP IMPORT PHPMYADMIN (CPANEL DOMAINESIA)
-- Aplikasi : Yoursuf Coffee POS & Manajemen Keuangan (Laravel 13)
-- Zona Waktu: Asia/Jakarta (WIB)
-- Isi Data  : 1 Akun Pemilik, 4 Kategori, 19 Menu Asli Yoursuf Coffee,
--             7 Stok Bahan Utama (Cup 16oz & Lid 100 pcs), 0 Transaksi & 0 Pengeluaran
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 1. Tabel `users`
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Pemilik Yoursuf Coffee', 'owner@yoursufcoffee.com', NULL, '$2y$12$9Q6nkoccif3.VFYWw3tJVO9j6eAQR7zdAVsNKy0SjlfcbxwUN2u.6', NULL, NOW(), NOW());

-- -----------------------------------------------------------------------------
-- 2. Tabel `password_reset_tokens` & `sessions`
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Tabel `cache` & `cache_locks`
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. Tabel `jobs`, `job_batches`, `failed_jobs`
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. Tabel `categories` (4 Kategori Utama Yoursuf Coffee)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Milk Based Series', 'milk-based-series', NOW(), NOW()),
(2, 'Non-Coffee', 'non-coffee', NOW(), NOW()),
(3, 'Mocktail Series', 'mocktail-series', NOW(), NOW()),
(4, 'Americano Series', 'americano-series', NOW(), NOW());

-- -----------------------------------------------------------------------------
-- 6. Tabel `products` (19 Menu Asli Yoursuf Coffee + Estimasi HPP 50%)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` int NOT NULL,
  `cost_price` int NOT NULL DEFAULT '0',
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `cost_price`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 1, 'Butterscotch Latte', 'espresso, magic milk, and butterscotch syrup', 22000, 11000, 1, NOW(), NOW()),
(2, 1, 'Huzzelnutt Latte', 'espresso, magic milk, and huzzelnutt syrup', 22000, 11000, 1, NOW(), NOW()),
(3, 1, 'Pandan Latte', 'espresso, magic milk, and pandan syrup', 22000, 11000, 1, NOW(), NOW()),
(4, 1, 'Caramel Latte', 'espresso, magic milk, and caramel syrup', 22000, 11000, 1, NOW(), NOW()),
(5, 1, 'Salted Caramel Latte', 'espresso, magic milk, and salted caramel syrup', 22000, 11000, 1, NOW(), NOW()),
(6, 1, 'Palm Sugar Latte', 'espresso, magic milk, and palm sugar syrup', 22000, 11000, 1, NOW(), NOW()),
(7, 1, 'Vanilla Latte', 'espresso, magic milk, and vanilla syrup', 22000, 11000, 1, NOW(), NOW()),
(8, 1, 'Vanilla Marie Latte', 'espresso, magic milk, vanilla syrup, and marie biscuit', 25000, 12500, 1, NOW(), NOW()),
(9, 2, 'Matcha Latte', 'matcha, magic milk, & sweetened condensed milk', 22000, 11000, 1, NOW(), NOW()),
(10, 2, 'Red Velvet Latte', 'red velvet, magic milk, & sweetened condensed milk', 21000, 10500, 1, NOW(), NOW()),
(11, 2, 'Matcha Barry', 'matcha, magic milk, and barry syrup', 25000, 12500, 1, NOW(), NOW()),
(12, 2, 'Dark Chocolatte Latte', 'dark chocolatte, magic milk, & sweetened condensed milk', 22000, 11000, 1, NOW(), NOW()),
(13, 2, 'Taro Latte', 'Taro, magic milk, & sweetened condensed milk', 22000, 11000, 1, NOW(), NOW()),
(14, 2, 'Caramela', 'non-coffee caramel milk blend', 21000, 10500, 1, NOW(), NOW()),
(15, 2, 'Romansa Regal', 'magic milk and regal biscuit blend', 21000, 10500, 1, NOW(), NOW()),
(16, 3, 'Lemonade', 'espresso, soda water, lemon syrup, and dried lemon', 22000, 11000, 1, NOW(), NOW()),
(17, 3, 'Sammint', 'espresso with secreat syrup', 22000, 11000, 1, NOW(), NOW()),
(18, 3, 'Smriwing', 'espresso, secreat syrup, and dried lemon', 22000, 11000, 1, NOW(), NOW()),
(19, 4, 'Regular Americano', 'espresso and water', 20000, 10000, 1, NOW(), NOW());

-- -----------------------------------------------------------------------------
-- 7. Tabel `orders` & `order_items` (Kosong / Bersih 0 Transaksi)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_type` enum('takeaway','delivery') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'takeaway',
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_wa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_fee` int NOT NULL DEFAULT '0',
  `discount_amount` int NOT NULL DEFAULT '0',
  `use_lid` tinyint(1) NOT NULL DEFAULT '1',
  `total_amount` int NOT NULL,
  `payment_method` enum('cash','qris') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `cash_received` int DEFAULT NULL,
  `change_amount` int DEFAULT NULL,
  `status` enum('masuk','dibuat','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'masuk',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int NOT NULL,
  `cost_price` int NOT NULL DEFAULT '0',
  `quantity` int NOT NULL DEFAULT '1',
  `item_notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. Tabel `expenses` (Kosong / Bersih 0 Pengeluaran)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `expenses`;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `expense_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. Tabel `ingredients` (Stok Bahan Utama & Kemasan Otomatis)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ingredients`;
CREATE TABLE `ingredients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pcs',
  `current_stock` int NOT NULL DEFAULT '0',
  `min_stock_alert` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ingredients` (`id`, `name`, `unit`, `current_stock`, `min_stock_alert`, `created_at`, `updated_at`) VALUES
(1, 'Cup 16oz', 'pcs', 100, 25, NOW(), NOW()),
(2, 'Lid (Tutup Cup)', 'pcs', 100, 25, NOW(), NOW()),
(3, 'Espresso Beans', 'gr', 1500, 300, NOW(), NOW()),
(4, 'Magic Milk', 'ml', 6000, 1500, NOW(), NOW()),
(5, 'Sweetened Condensed Milk', 'gr', 2000, 500, NOW(), NOW()),
(6, 'Syrup Variants (Butterscotch, Caramel, Pandan, Vanilla)', 'ml', 2500, 500, NOW(), NOW()),
(7, 'Non-Coffee Powder (Matcha, Red Velvet, Choco, Taro)', 'gr', 1000, 250, NOW(), NOW());

-- -----------------------------------------------------------------------------
-- 10. Tabel `migrations`
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_11_11_000001_create_categories_table', 1),
(5, '2026_11_11_000002_create_products_table', 1),
(6, '2026_11_11_000003_create_orders_table', 1),
(7, '2026_11_11_000004_create_order_items_table', 1),
(8, '2026_11_11_000005_create_expenses_table', 1),
(9, '2026_11_11_000006_create_ingredients_table', 1),
(10, '2026_11_11_000007_add_discount_and_lid_to_orders_table', 1);

SET FOREIGN_KEY_CHECKS = 1;
