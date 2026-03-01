-- =============================================
-- Tabela de refresh tokens para API JWT
-- =============================================
-- Armazena refresh tokens (hash SHA256) para renovação de access tokens.
-- Access tokens são JWT stateless (não armazenados no banco).

CREATE TABLE IF NOT EXISTS `api_tokens` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL COMMENT 'ID do usuário (entregador ou admin)',
    `user_type` VARCHAR(20) NOT NULL DEFAULT 'motoboy' COMMENT 'Tipo: motoboy, admin',
    `refresh_token` VARCHAR(64) NOT NULL COMMENT 'SHA256 hash do refresh token',
    `expires_at` DATETIME NOT NULL COMMENT 'Expiração do refresh token',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_user` (`user_id`, `user_type`),
    INDEX `idx_refresh` (`refresh_token`),
    INDEX `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela de rate limiting para API
-- =============================================
-- Registra cada request para controle de limites por IP/token.
-- Limpeza automática de registros antigos via cron ou na própria request.

CREATE TABLE IF NOT EXISTS `api_rate_limits` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `identifier` VARCHAR(128) NOT NULL COMMENT 'IP ou user_ID',
    `endpoint_type` VARCHAR(20) NOT NULL DEFAULT 'public' COMMENT 'Tipo: public, auth, login',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_lookup` (`identifier`, `endpoint_type`, `created_at`),
    INDEX `idx_cleanup` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tabela de device tokens para push notifications (FCM)
-- =============================================
-- Preparação para fase 4: push notifications via Firebase.

CREATE TABLE IF NOT EXISTS `device_tokens` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `user_type` VARCHAR(20) NOT NULL DEFAULT 'motoboy',
    `fcm_token` VARCHAR(255) NOT NULL COMMENT 'Firebase Cloud Messaging token',
    `platform` VARCHAR(10) NOT NULL DEFAULT 'android' COMMENT 'android, ios, web',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `idx_fcm_unique` (`fcm_token`),
    INDEX `idx_user` (`user_id`, `user_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
