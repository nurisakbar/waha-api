-- ============================================
-- Database Schema untuk SaaS WhatsApp API
-- Database: MySQL
-- Framework: Laravel
-- ============================================

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NULL DEFAULT NULL,
  `avatar` VARCHAR(255) NULL DEFAULT NULL,
  `role` ENUM('super_admin', 'admin', 'user') NOT NULL DEFAULT 'user',
  `subscription_plan_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `subscription_status` ENUM('active', 'cancelled', 'expired', 'trial') NOT NULL DEFAULT 'trial',
  `trial_ends_at` TIMESTAMP NULL DEFAULT NULL,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  `remember_token` VARCHAR(100) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_subscription_plan` (`subscription_plan_id`),
  INDEX `idx_subscription_status` (`subscription_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. PASSWORD RESET TOKENS TABLE
-- ============================================
CREATE TABLE `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. SESSIONS TABLE (Laravel Sessions)
-- ============================================
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. PLANS TABLE (Subscription Plans)
-- ============================================
CREATE TABLE `plans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NULL DEFAULT NULL,
  `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
  `billing_period` ENUM('monthly', 'yearly') NOT NULL DEFAULT 'monthly',
  `sessions_limit` INT NOT NULL DEFAULT 1,
  `messages_per_month` INT NULL DEFAULT NULL COMMENT 'NULL = unlimited',
  `api_rate_limit` INT NOT NULL DEFAULT 100 COMMENT 'requests per minute',
  `webhook_limit` INT NOT NULL DEFAULT 1,
  `features` JSON NULL DEFAULT NULL,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `is_featured` BOOLEAN NOT NULL DEFAULT FALSE,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_slug` (`slug`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. SUBSCRIPTIONS TABLE
-- ============================================
CREATE TABLE `subscriptions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `plan_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('active', 'cancelled', 'expired', 'past_due') NOT NULL DEFAULT 'active',
  `current_period_start` TIMESTAMP NOT NULL,
  `current_period_end` TIMESTAMP NOT NULL,
  `cancel_at_period_end` BOOLEAN NOT NULL DEFAULT FALSE,
  `cancelled_at` TIMESTAMP NULL DEFAULT NULL,
  `trial_ends_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_plan_id` (`plan_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`plan_id`) REFERENCES `plans`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. WHATSAPP_SESSIONS TABLE
-- ============================================
CREATE TABLE `whatsapp_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `session_name` VARCHAR(255) NOT NULL,
  `session_id` VARCHAR(255) NOT NULL UNIQUE COMMENT 'WAHA session ID',
  `status` ENUM('pairing', 'connected', 'disconnected', 'failed') NOT NULL DEFAULT 'pairing',
  `qr_code` TEXT NULL DEFAULT NULL,
  `qr_code_expires_at` TIMESTAMP NULL DEFAULT NULL,
  `device_info` JSON NULL DEFAULT NULL,
  `waha_instance_url` VARCHAR(255) NULL DEFAULT NULL,
  `last_activity_at` TIMESTAMP NULL DEFAULT NULL,
  `connected_at` TIMESTAMP NULL DEFAULT NULL,
  `disconnected_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. MESSAGES TABLE
-- ============================================
CREATE TABLE `messages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `session_id` BIGINT UNSIGNED NOT NULL,
  `whatsapp_message_id` VARCHAR(255) NULL DEFAULT NULL,
  `from_number` VARCHAR(20) NOT NULL,
  `to_number` VARCHAR(20) NOT NULL,
  `message_type` ENUM('text', 'image', 'video', 'audio', 'document', 'location', 'contact', 'sticker', 'voice') NOT NULL DEFAULT 'text',
  `content` TEXT NULL DEFAULT NULL,
  `media_url` VARCHAR(500) NULL DEFAULT NULL,
  `media_mime_type` VARCHAR(100) NULL DEFAULT NULL,
  `media_size` BIGINT NULL DEFAULT NULL,
  `caption` TEXT NULL DEFAULT NULL,
  `status` ENUM('pending', 'sent', 'delivered', 'read', 'failed') NOT NULL DEFAULT 'pending',
  `direction` ENUM('incoming', 'outgoing') NOT NULL,
  `error_message` TEXT NULL DEFAULT NULL,
  `sent_at` TIMESTAMP NULL DEFAULT NULL,
  `delivered_at` TIMESTAMP NULL DEFAULT NULL,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_from_number` (`from_number`),
  INDEX `idx_to_number` (`to_number`),
  INDEX `idx_whatsapp_message_id` (`whatsapp_message_id`),
  INDEX `idx_direction` (`direction`),
  INDEX `idx_status` (`status`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`session_id`) REFERENCES `whatsapp_sessions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. WEBHOOKS TABLE
-- ============================================
CREATE TABLE `webhooks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `session_id` BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'NULL = all sessions',
  `name` VARCHAR(255) NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `events` JSON NOT NULL COMMENT 'Array of event types: message, status, session, etc.',
  `secret` VARCHAR(255) NULL DEFAULT NULL,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `last_triggered_at` TIMESTAMP NULL DEFAULT NULL,
  `failure_count` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_session_id` (`session_id`),
  INDEX `idx_is_active` (`is_active`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`session_id`) REFERENCES `whatsapp_sessions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. WEBHOOK_LOGS TABLE
-- ============================================
CREATE TABLE `webhook_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `webhook_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(100) NOT NULL,
  `payload` JSON NOT NULL,
  `response_status` INT NULL DEFAULT NULL,
  `response_body` TEXT NULL DEFAULT NULL,
  `error_message` TEXT NULL DEFAULT NULL,
  `attempt_number` INT NOT NULL DEFAULT 1,
  `triggered_at` TIMESTAMP NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_webhook_id` (`webhook_id`),
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_triggered_at` (`triggered_at`),
  FOREIGN KEY (`webhook_id`) REFERENCES `webhooks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. API_KEYS TABLE
-- ============================================
CREATE TABLE `api_keys` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `key` VARCHAR(64) NOT NULL UNIQUE,
  `key_prefix` VARCHAR(8) NOT NULL COMMENT 'First 8 chars for display',
  `last_used_at` TIMESTAMP NULL DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_key` (`key`),
  INDEX `idx_is_active` (`is_active`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. API_USAGE_LOGS TABLE
-- ============================================
CREATE TABLE `api_usage_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `api_key_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `endpoint` VARCHAR(255) NOT NULL,
  `method` VARCHAR(10) NOT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `status_code` INT NOT NULL,
  `response_time` INT NOT NULL COMMENT 'in milliseconds',
  `request_size` INT NULL DEFAULT NULL,
  `response_size` INT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_api_key_id` (`api_key_id`),
  INDEX `idx_endpoint` (`endpoint`),
  INDEX `idx_status_code` (`status_code`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`api_key_id`) REFERENCES `api_keys`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. USAGE_STATISTICS TABLE
-- ============================================
CREATE TABLE `usage_statistics` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `messages_sent` INT NOT NULL DEFAULT 0,
  `messages_received` INT NOT NULL DEFAULT 0,
  `api_calls` INT NOT NULL DEFAULT 0,
  `webhook_calls` INT NOT NULL DEFAULT 0,
  `storage_used` BIGINT NOT NULL DEFAULT 0 COMMENT 'in bytes',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_date` (`user_id`, `date`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_date` (`date`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. INVOICES TABLE
-- ============================================
CREATE TABLE `invoices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `subscription_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `amount` DECIMAL(10, 2) NOT NULL,
  `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
  `status` ENUM('draft', 'pending', 'paid', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
  `due_date` TIMESTAMP NULL DEFAULT NULL,
  `paid_at` TIMESTAMP NULL DEFAULT NULL,
  `payment_method` VARCHAR(50) NULL DEFAULT NULL,
  `payment_reference` VARCHAR(255) NULL DEFAULT NULL,
  `notes` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_subscription_id` (`subscription_id`),
  INDEX `idx_invoice_number` (`invoice_number`),
  INDEX `idx_status` (`status`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 14. NOTIFICATIONS TABLE
-- ============================================
CREATE TABLE `notifications` (
  `id` CHAR(36) NOT NULL,
  `type` VARCHAR(255) NOT NULL,
  `notifiable_type` VARCHAR(255) NOT NULL,
  `notifiable_id` BIGINT UNSIGNED NOT NULL,
  `data` TEXT NOT NULL,
  `read_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_notifiable` (`notifiable_type`, `notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 15. FAILED_JOBS TABLE (Laravel Queue)
-- ============================================
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL UNIQUE,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 16. JOBS TABLE (Laravel Queue)
-- ============================================
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT UNSIGNED NULL DEFAULT NULL,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_queue` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 17. CACHE TABLE (Laravel Cache)
-- ============================================
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`),
  INDEX `idx_expiration` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEFAULT DATA SEEDING
-- ============================================

-- Insert default subscription plans
INSERT INTO `plans` (`name`, `slug`, `description`, `price`, `currency`, `billing_period`, `sessions_limit`, `messages_per_month`, `api_rate_limit`, `webhook_limit`, `features`, `is_active`, `is_featured`, `sort_order`) VALUES
('Free', 'free', 'Perfect for testing and small projects', 0.00, 'USD', 'monthly', 1, 100, 10, 1, '["Basic messaging", "1 session", "100 messages/month", "Basic support"]', TRUE, FALSE, 1),
('Basic', 'basic', 'For small businesses', 9.99, 'USD', 'monthly', 3, 1000, 50, 3, '["All Free features", "3 sessions", "1,000 messages/month", "Email support"]', TRUE, TRUE, 2),
('Pro', 'pro', 'For growing businesses', 29.99, 'USD', 'monthly', 10, 10000, 200, 10, '["All Basic features", "10 sessions", "10,000 messages/month", "Priority support", "Advanced analytics"]', TRUE, TRUE, 3),
('Enterprise', 'enterprise', 'For large organizations', 99.99, 'USD', 'monthly', 50, NULL, 1000, 50, '["All Pro features", "50 sessions", "Unlimited messages", "Dedicated support", "Custom integrations", "SLA guarantee"]', TRUE, FALSE, 4);

-- ============================================
-- INDEXES OPTIMIZATION
-- ============================================

-- Additional composite indexes for common queries
CREATE INDEX `idx_messages_user_session` ON `messages` (`user_id`, `session_id`, `created_at`);
CREATE INDEX `idx_messages_user_date` ON `messages` (`user_id`, `created_at`);
CREATE INDEX `idx_sessions_user_status` ON `whatsapp_sessions` (`user_id`, `status`);

-- ============================================
-- END OF SCHEMA
-- ============================================

