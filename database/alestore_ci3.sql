-- AleStore / AlexaStore database schema for CodeIgniter 3
-- Target DB: MySQL/MariaDB
-- Default CI3 config uses database name: alestore
-- Default admin seed:
--   email    : admin@alestore.test
--   password : password

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `alestore`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `alestore`;

DROP TABLE IF EXISTS `warranty_claims`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `digital_accounts`;
DROP TABLE IF EXISTS `digital_product_variations`;
DROP TABLE IF EXISTS `digital_products`;
DROP TABLE IF EXISTS `expire_durations`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `shopee_stores`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'staff',
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `customers_name_index` (`name`),
  KEY `customers_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `shopee_stores` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shop_name` VARCHAR(255) NOT NULL,
  `platform` VARCHAR(100) NOT NULL DEFAULT 'Shopee',
  `description` TEXT DEFAULT NULL,
  `admin_fee_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `shop_id` VARCHAR(255) NOT NULL,
  `access_token` VARCHAR(255) DEFAULT NULL,
  `refresh_token` VARCHAR(255) DEFAULT NULL,
  `partner_key` TEXT DEFAULT NULL,
  `partner_id` TEXT DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'active',
  `token_expire_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shopee_stores_shop_id_unique` (`shop_id`),
  KEY `shopee_stores_shop_name_index` (`shop_name`),
  KEY `shopee_stores_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `digital_products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `account_type` VARCHAR(50) NOT NULL DEFAULT 'private',
  `method` VARCHAR(50) NOT NULL DEFAULT 'credentials',
  `max_slot` INT UNSIGNED NOT NULL DEFAULT 1,
  `hpp` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `digital_products_name_index` (`name`),
  KEY `digital_products_method_index` (`method`),
  KEY `digital_products_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `digital_product_variations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `digital_product_id` BIGINT UNSIGNED NOT NULL,
  `label` VARCHAR(255) NOT NULL,
  `sale_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `hpp` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `digital_product_variations_product_index` (`digital_product_id`),
  KEY `digital_product_variations_label_index` (`label`),
  CONSTRAINT `digital_product_variations_product_fk`
    FOREIGN KEY (`digital_product_id`) REFERENCES `digital_products` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `digital_accounts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `digital_product_id` BIGINT UNSIGNED DEFAULT NULL,
  `digital_product_variation_id` BIGINT UNSIGNED DEFAULT NULL,
  `shopee_store_id` BIGINT UNSIGNED DEFAULT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `variation` VARCHAR(255) DEFAULT NULL,
  `account_type` VARCHAR(50) NOT NULL DEFAULT 'private',
  `email` VARCHAR(255) DEFAULT NULL,
  `password` TEXT DEFAULT NULL,
  `extra_info` TEXT DEFAULT NULL,
  `method` VARCHAR(50) NOT NULL DEFAULT 'credentials',
  `max_slot` INT UNSIGNED NOT NULL DEFAULT 1,
  `used_slot` INT UNSIGNED NOT NULL DEFAULT 0,
  `status` VARCHAR(50) NOT NULL DEFAULT 'available',
  `hpp` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `notes` TEXT DEFAULT NULL,
  `expired_at` TIMESTAMP NULL DEFAULT NULL,
  `sold_at` TIMESTAMP NULL DEFAULT NULL,
  `notification_resolved_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `digital_accounts_product_fk_index` (`digital_product_id`),
  KEY `digital_accounts_variation_fk_index` (`digital_product_variation_id`),
  KEY `digital_accounts_store_fk_index` (`shopee_store_id`),
  KEY `digital_accounts_product_name_index` (`product_name`),
  KEY `digital_accounts_status_index` (`status`),
  KEY `digital_accounts_expired_at_index` (`expired_at`),
  CONSTRAINT `digital_accounts_product_fk`
    FOREIGN KEY (`digital_product_id`) REFERENCES `digital_products` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `digital_accounts_variation_fk`
    FOREIGN KEY (`digital_product_variation_id`) REFERENCES `digital_product_variations` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `digital_accounts_store_fk`
    FOREIGN KEY (`shopee_store_id`) REFERENCES `shopee_stores` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shopee_order_id` VARCHAR(255) DEFAULT NULL,
  `shopee_store_id` BIGINT UNSIGNED DEFAULT NULL,
  `customer_id` BIGINT UNSIGNED DEFAULT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `order_type` VARCHAR(50) NOT NULL DEFAULT 'private',
  `product_name` VARCHAR(255) DEFAULT NULL,
  `variation` VARCHAR(255) DEFAULT NULL,
  `buyer_email` VARCHAR(255) DEFAULT NULL,
  `account_username` VARCHAR(255) DEFAULT NULL,
  `assigned_digital_account_id` BIGINT UNSIGNED DEFAULT NULL,
  `account_password` TEXT DEFAULT NULL,
  `account_max_user` INT UNSIGNED NOT NULL DEFAULT 1,
  `expired_at` TIMESTAMP NULL DEFAULT NULL,
  `total` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `orders_shopee_store_fk_index` (`shopee_store_id`),
  KEY `orders_customer_fk_index` (`customer_id`),
  KEY `orders_user_fk_index` (`user_id`),
  KEY `orders_order_id_index` (`shopee_order_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_created_at_index` (`created_at`),
  KEY `orders_expired_at_index` (`expired_at`),
  CONSTRAINT `orders_shopee_store_fk`
    FOREIGN KEY (`shopee_store_id`) REFERENCES `shopee_stores` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `orders_customer_fk`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `orders_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `warranty_claims` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED DEFAULT NULL,
  `shopee_store_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_code` VARCHAR(255) DEFAULT NULL,
  `buyer_name` VARCHAR(255) NOT NULL,
  `reason` VARCHAR(255) NOT NULL,
  `extra_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `notes` TEXT DEFAULT NULL,
  `claimed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `warranty_claims_order_fk_index` (`order_id`),
  KEY `warranty_claims_store_fk_index` (`shopee_store_id`),
  KEY `warranty_claims_status_index` (`status`),
  KEY `warranty_claims_claimed_at_index` (`claimed_at`),
  CONSTRAINT `warranty_claims_order_fk`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `warranty_claims_store_fk`
    FOREIGN KEY (`shopee_store_id`) REFERENCES `shopee_stores` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `expire_durations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(255) NOT NULL,
  `days` INT UNSIGNED NOT NULL,
  `is_default` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `expire_durations_default_index` (`is_default`, `days`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `admin_name` VARCHAR(255) DEFAULT NULL,
  `admin_role` VARCHAR(50) DEFAULT NULL,
  `action` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_fk_index` (`user_id`),
  KEY `activity_logs_created_at_index` (`created_at`),
  CONSTRAINT `activity_logs_user_fk`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users`
  (`name`, `email`, `role`, `password`, `created_at`, `updated_at`)
VALUES
  ('Administrator', 'admin@alestore.test', 'admin', '$2y$10$iX5R0dXcwF00xi0q5RLWQ.NzCePFcDaVcukB1y3WFV.CzFO465C76', NOW(), NOW());

INSERT INTO `expire_durations`
  (`label`, `days`, `is_default`, `created_at`, `updated_at`)
VALUES
  ('14 Days', 14, 0, NOW(), NOW()),
  ('1 Bulan', 30, 1, NOW(), NOW()),
  ('3 Bulan', 90, 0, NOW(), NOW()),
  ('Lifetime', 3650, 0, NOW(), NOW());

INSERT INTO `shopee_stores`
  (`shop_name`, `platform`, `description`, `admin_fee_percentage`, `shop_id`, `status`, `created_at`, `updated_at`)
VALUES
  ('Demo Store', 'Manual', 'Toko manual default untuk input awal.', 0.00, 'manual-demo-store', 'active', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
