-- ==========================================================
-- Vyora MySQL Database Schema & Default Seed Data
-- MilesWeb / cPanel One-Click Import
-- ==========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Subscription Plans Table
DROP TABLE IF EXISTS `subscription_plans`;
CREATE TABLE `subscription_plans` (
  `id` CHAR(36) NOT NULL,
  `tier` VARCHAR(50) NOT NULL,
  `business_type` VARCHAR(50) NOT NULL DEFAULT 'RESTAURANT',
  `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `max_tables` INT NOT NULL DEFAULT 5,
  `max_rooms` INT NOT NULL DEFAULT 0,
  `features` JSON NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_plans_tier_business_unique` (`tier`, `business_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Owners (Restaurants, Hotels, Super Admin) Table
DROP TABLE IF EXISTS `owners`;
CREATE TABLE `owners` (
  `id` CHAR(36) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `restaurant_name` VARCHAR(255) NULL,
  `owner_name` VARCHAR(255) NULL,
  `phone` VARCHAR(50) NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'RESTAURANT_OWNER',
  `business_type` VARCHAR(50) NOT NULL DEFAULT 'RESTAURANT',
  `plan_id` CHAR(36) NULL,
  `partner_restaurant_id` CHAR(36) NULL,
  `subscription_status` VARCHAR(50) NOT NULL DEFAULT 'active',
  `show_on_landing` TINYINT(1) NOT NULL DEFAULT 0,
  `cuisine` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `owners_username_unique` (`username`),
  UNIQUE KEY `owners_email_unique` (`email`),
  KEY `owners_plan_id_foreign` (`plan_id`),
  KEY `owners_partner_restaurant_id_foreign` (`partner_restaurant_id`),
  CONSTRAINT `owners_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `owners_partner_restaurant_id_foreign` FOREIGN KEY (`partner_restaurant_id`) REFERENCES `owners` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Dining Tables Table
DROP TABLE IF EXISTS `tables`;
CREATE TABLE `tables` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `table_number` INT NOT NULL,
  `qr_code_data` TEXT NOT NULL,
  `qr_code_image_url` LONGTEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tables_owner_id_table_number_unique` (`owner_id`, `table_number`),
  KEY `tables_table_number_index` (`table_number`),
  CONSTRAINT `tables_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Hotel Room Categories Table
DROP TABLE IF EXISTS `room_categories`;
CREATE TABLE `room_categories` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `base_price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `max_occupancy` INT NOT NULL DEFAULT 2,
  `amenities` JSON NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_categories_owner_id_index` (`owner_id`),
  CONSTRAINT `room_categories_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Hotel Rooms Table
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `category_id` CHAR(36) NULL,
  `room_number` VARCHAR(50) NOT NULL,
  `floor` INT NOT NULL DEFAULT 1,
  `status` VARCHAR(50) NOT NULL DEFAULT 'vacant_clean',
  `qr_code_data` TEXT NULL,
  `qr_code_image_url` LONGTEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rooms_owner_id_room_number_unique` (`owner_id`, `room_number`),
  KEY `rooms_status_index` (`status`),
  CONSTRAINT `rooms_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rooms_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `room_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Hotel Guests Directory Table
DROP TABLE IF EXISTS `guests`;
CREATE TABLE `guests` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NULL,
  `email` VARCHAR(255) NULL,
  `id_type` VARCHAR(100) NULL,
  `id_number` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `guests_owner_id_index` (`owner_id`),
  KEY `guests_phone_index` (`phone`),
  CONSTRAINT `guests_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Hotel Bookings & Stays Table
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `room_id` CHAR(36) NOT NULL,
  `guest_id` CHAR(36) NOT NULL,
  `check_in` TIMESTAMP NOT NULL,
  `expected_check_out` TIMESTAMP NULL,
  `check_out` TIMESTAMP NULL,
  `room_rate` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `advance_deposit` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `status` VARCHAR(50) NOT NULL DEFAULT 'active',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bookings_status_index` (`status`),
  CONSTRAINT `bookings_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_guest_id_foreign` FOREIGN KEY (`guest_id`) REFERENCES `guests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Hotel Service & Housekeeping Requests Table
DROP TABLE IF EXISTS `hotel_service_requests`;
CREATE TABLE `hotel_service_requests` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `room_id` CHAR(36) NOT NULL,
  `booking_id` CHAR(36) NULL,
  `service_name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'housekeeping',
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL,
  `completed_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `hotel_service_requests_status_index` (`status`),
  CONSTRAINT `hotel_service_requests_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hotel_service_requests_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hotel_service_requests_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Menu Categories Table
DROP TABLE IF EXISTS `menu_categories`;
CREATE TABLE `menu_categories` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_categories_owner_id_index` (`owner_id`),
  CONSTRAINT `menu_categories_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Menu Items Table
DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `category_id` CHAR(36) NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `image_url` LONGTEXT NULL,
  `preparation_time` INT NOT NULL DEFAULT 15,
  `is_available` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_items_is_available_index` (`is_available`),
  CONSTRAINT `menu_items_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Orders Table
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` CHAR(36) NOT NULL,
  `owner_id` CHAR(36) NOT NULL,
  `table_number` INT NOT NULL DEFAULT 0,
  `room_id` CHAR(36) NULL,
  `room_number` VARCHAR(50) NULL,
  `hotel_id` CHAR(36) NULL,
  `hotel_name` VARCHAR(255) NULL,
  `order_type` VARCHAR(50) NOT NULL DEFAULT 'dine_in',
  `booking_id` CHAR(36) NULL,
  `billing_type` VARCHAR(50) NOT NULL DEFAULT 'folio',
  `fulfilled_by_id` CHAR(36) NULL,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `estimated_time` INT NOT NULL DEFAULT 15,
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL,
  `cancellation_reason` TEXT NULL,
  `completed_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `orders_owner_id_index` (`owner_id`),
  KEY `orders_hotel_id_index` (`hotel_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_order_type_index` (`order_type`),
  CONSTRAINT `orders_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `owners` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_fulfilled_by_id_foreign` FOREIGN KEY (`fulfilled_by_id`) REFERENCES `owners` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Order Items Table
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` CHAR(36) NOT NULL,
  `order_id` CHAR(36) NOT NULL,
  `menu_item_id` CHAR(36) NOT NULL,
  `menu_item_name` VARCHAR(255) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_index` (`order_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Hotel-Restaurant Multi-Partner Network Pivot Table
DROP TABLE IF EXISTS `hotel_partner_restaurants`;
CREATE TABLE `hotel_partner_restaurants` (
  `id` CHAR(36) NOT NULL,
  `hotel_id` CHAR(36) NOT NULL,
  `restaurant_id` CHAR(36) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hotel_restaurant_unique` (`hotel_id`, `restaurant_id`),
  CONSTRAINT `hpr_hotel_id_foreign` FOREIGN KEY (`hotel_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE,
  CONSTRAINT `hpr_restaurant_id_foreign` FOREIGN KEY (`restaurant_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Seed Plans
INSERT INTO `subscription_plans` (`id`, `tier`, `business_type`, `price`, `max_tables`, `max_rooms`, `features`) VALUES
('01a02d96-b3c8-72f5-9be4-153ec3d60001', 'FREE', 'RESTAURANT', 0.00, 5, 0, '["Basic QR ordering", "Up to 5 tables"]'),
('01a02d96-b3c8-72f5-9be4-153ec3d60002', 'PRO', 'RESTAURANT', 49.99, 25, 0, '["Up to 25 tables", "Priority support", "Analytics"]'),
('01a02d96-b3c8-72f5-9be4-153ec3d60003', 'PREMIUM', 'RESTAURANT', 99.99, 100, 0, '["Unlimited tables", "White-label branding", "API access"]'),
('01a02d96-b3c8-72f5-9be4-153ec3d60004', 'FREE', 'HOTEL', 0.00, 0, 10, '["Complete Hotel PMS", "Up to 10 rooms", "In-room QR services"]'),
('01a02d96-b3c8-72f5-9be4-153ec3d60005', 'PRO', 'COMBO', 79.99, 20, 30, '["Both Hotel PMS & Restaurant POS", "Up to 20 tables & 30 rooms", "Unified Folio & KDS"]');

-- 14. Seed Super Admin (admin123)
INSERT INTO `owners` (`id`, `username`, `email`, `password_hash`, `restaurant_name`, `owner_name`, `role`, `business_type`) VALUES
('01a02d96-b3c8-72f5-9be4-153ec3d60010', 'superadmin', 'admin@vyora.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'Vyora Platform', 'Platform Admin', 'SUPER_ADMIN', 'BOTH');

-- 15. Seed Demo Restaurant (SpiceHub / demo123)
INSERT INTO `owners` (`id`, `username`, `email`, `password_hash`, `restaurant_name`, `owner_name`, `cuisine`, `role`, `business_type`, `show_on_landing`, `plan_id`) VALUES
('01a02d96-b3c8-72f5-9be4-153ec3d60020', 'spicehub', 'spicehub@demo.com', '$2y$10$rN2hXkO.UaMzg7oF4t7fGe0yW2aOQhH1wLpUj0e9rQfXb1q.O2hKe', 'SpiceHub Dining', 'Sanjay Kapoor', 'Spice & Tandoor', 'RESTAURANT_OWNER', 'RESTAURANT', 1, '01a02d96-b3c8-72f5-9be4-153ec3d60001');

-- 16. Seed Demo Hotel (Grand Horizon / demo123)
INSERT INTO `owners` (`id`, `username`, `email`, `password_hash`, `restaurant_name`, `owner_name`, `cuisine`, `role`, `business_type`, `show_on_landing`, `plan_id`, `partner_restaurant_id`) VALUES
('01a02d96-b3c8-72f5-9be4-153ec3d60030', 'grandhorizon', 'hotel@demo.com', '$2y$10$rN2hXkO.UaMzg7oF4t7fGe0yW2aOQhH1wLpUj0e9rQfXb1q.O2hKe', 'Grand Horizon Hotel & Suites', 'Alexander Vance', 'Luxury Hospitality & In-Room Dining', 'RESTAURANT_OWNER', 'HOTEL', 1, '01a02d96-b3c8-72f5-9be4-153ec3d60005', '01a02d96-b3c8-72f5-9be4-153ec3d60020');

SET FOREIGN_KEY_CHECKS = 1;
