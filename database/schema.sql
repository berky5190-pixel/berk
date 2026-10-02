-- ============================================================================
-- DEMİRBAŞ VE ZİMMET YÖNETİM SİSTEMİ
-- VERİTABANI ŞEMASI (MySQL 8.0+ / MariaDB 10.5+)
-- Karakter Seti: utf8mb4 | Karşılaştırma: utf8mb4_unicode_ci
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. ROLLER (roles)
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL COMMENT 'Rol Adı: Yönetici, Birim Sorumlusu, Personel',
    `slug` VARCHAR(50) NOT NULL UNIQUE COMMENT 'admin, manager, user',
    `description` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. YETKİLER (permissions)
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `module` VARCHAR(50) NOT NULL COMMENT 'assets, assignments, stock, users, settings vb.',
    `description` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. ROL - YETKİ İLİŞKİSİ (role_permissions)
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
    `role_id` INT NOT NULL,
    `permission_id` INT NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. DEPARTMANLAR (departments)
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `status` ENUM('active', 'passive') DEFAULT 'active',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. LOKASYONLAR (locations)
DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `building` VARCHAR(100) NULL,
    `room_number` VARCHAR(50) NULL,
    `description` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. PERSONELLER (employees)
DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `department_id` INT NOT NULL,
    `registration_no` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Sicil No',
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NULL,
    `phone` VARCHAR(30) NULL,
    `title` VARCHAR(100) NOT NULL COMMENT 'Görev / Unvan',
    `hire_date` DATE NULL,
    `status` ENUM('active', 'passive') DEFAULT 'active',
    `photo_path` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_emp_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT,
    INDEX `idx_emp_status` (`status`),
    INDEX `idx_emp_reg` (`registration_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. KULLANICILAR (users)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `employee_id` INT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `status` ENUM('active', 'passive') DEFAULT 'active',
    `last_login_at` DATETIME NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_user_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
    INDEX `idx_user_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. DEMİRBAŞ KATEGORİLERİ (asset_categories)
DROP TABLE IF EXISTS `asset_categories`;
CREATE TABLE `asset_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `parent_id` INT NULL,
    `name` VARCHAR(100) NOT NULL,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_cat_parent` FOREIGN KEY (`parent_id`) REFERENCES `asset_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. DEMİRBAŞLAR (assets)
DROP TABLE IF EXISTS `assets`;
CREATE TABLE `assets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `location_id` INT NULL,
    `asset_code` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Demirbaş Kodu / Etiket No',
    `name` VARCHAR(150) NOT NULL,
    `brand` VARCHAR(100) NOT NULL,
    `model` VARCHAR(100) NOT NULL,
    `serial_number` VARCHAR(100) NULL,
    `barcode` VARCHAR(100) NULL,
    `purchase_date` DATE NULL,
    `purchase_price` DECIMAL(12,2) DEFAULT 0.00,
    `currency` VARCHAR(3) DEFAULT 'TRY',
    `warranty_start` DATE NULL,
    `warranty_end` DATE NULL,
    `status` ENUM('in_stock', 'assigned', 'in_use', 'defective', 'in_service', 'scrapped', 'lost') DEFAULT 'in_stock',
    `photo_path` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `created_by` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_asset_cat` FOREIGN KEY (`category_id`) REFERENCES `asset_categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_asset_loc` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_asset_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_asset_status` (`status`),
    INDEX `idx_asset_code` (`asset_code`),
    INDEX `idx_asset_serial` (`serial_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. ZİMMETLER (assignments)
DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `assignment_code` VARCHAR(50) NOT NULL UNIQUE,
    `employee_id` INT NOT NULL,
    `asset_id` INT NOT NULL,
    `assigned_by_user_id` INT NOT NULL,
    `received_by_name` VARCHAR(100) NULL,
    `assignment_date` DATE NOT NULL,
    `planned_return_date` DATE NULL,
    `actual_return_date` DATE NULL,
    `status` ENUM('active', 'returned', 'pending_return', 'lost', 'damaged') DEFAULT 'active',
    `return_condition` ENUM('good', 'damaged', 'defective', 'scrapped') NULL,
    `return_notes` TEXT NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_asgn_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_asgn_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_asgn_user` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_asgn_status` (`status`),
    INDEX `idx_asgn_date` (`assignment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. ZİMMET BELGELERİ (assignment_documents)
DROP TABLE IF EXISTS `assignment_documents`;
CREATE TABLE `assignment_documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `assignment_id` INT NOT NULL,
    `document_type` ENUM('initial_protocol', 'return_protocol', 'damage_report', 'service_slip', 'other') DEFAULT 'initial_protocol',
    `original_filename` VARCHAR(255) NOT NULL,
    `stored_filename` VARCHAR(255) NOT NULL UNIQUE,
    `file_path` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `file_size` INT NOT NULL,
    `sha256_hash` CHAR(64) NULL,
    `description` VARCHAR(255) NULL,
    `uploaded_by_user_id` INT NOT NULL,
    `uploaded_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_doc_asgn` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_doc_user` FOREIGN KEY (`uploaded_by_user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_doc_asgn` (`assignment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. STOK KARTLARI (stock_items)
DROP TABLE IF EXISTS `stock_items`;
CREATE TABLE `stock_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `location_id` INT NULL,
    `stock_code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `brand` VARCHAR(100) NULL,
    `model` VARCHAR(100) NULL,
    `unit` ENUM('piece', 'box', 'meter', 'kg', 'set') DEFAULT 'piece',
    `current_stock` INT DEFAULT 0,
    `min_stock` INT DEFAULT 5,
    `max_stock` INT DEFAULT 1000,
    `description` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_stock_cat` FOREIGN KEY (`category_id`) REFERENCES `asset_categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_stock_loc` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
    INDEX `idx_stock_code` (`stock_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. STOK HAREKETLERİ (stock_movements)
DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE `stock_movements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `stock_item_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `movement_type` ENUM('in', 'out', 'adjustment', 'return', 'assignment_out') NOT NULL,
    `quantity` INT NOT NULL,
    `previous_stock` INT NOT NULL,
    `new_stock` INT NOT NULL,
    `reference_type` VARCHAR(50) NULL,
    `reference_id` INT NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sm_item` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    INDEX `idx_sm_item` (`stock_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. BİLDİRİMLER (notifications)
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `type` ENUM('critical_stock', 'assignment_return_due', 'overdue_return', 'new_assignment', 'document_missing', 'system_alert') NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `link` VARCHAR(255) NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_notif_read` (`is_read`),
    INDEX `idx_notif_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. İŞLEM GEÇMİŞİ / AUDIT LOG (audit_logs)
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action` VARCHAR(50) NOT NULL,
    `module` VARCHAR(50) NOT NULL,
    `record_id` INT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` VARCHAR(255) NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    INDEX `idx_audit_module` (`module`),
    INDEX `idx_audit_action` (`action`),
    INDEX `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. SİSTEM AYARLARI (settings)
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(50) NOT NULL UNIQUE,
    `value` TEXT NULL,
    `group` VARCHAR(30) DEFAULT 'general',
    `description` VARCHAR(255) NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
