-- ============================================================
-- 18 - Portal do Cliente: Login, Dashboard, Password Reset
-- ============================================================

SET sql_mode = '';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- 1. Adicionar colunas de autenticacao ao customer_tbl
-- MySQL 5.7 nao suporta ADD COLUMN IF NOT EXISTS, usar PREPARE/EXECUTE
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'password_hash');
SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `customer_tbl` ADD COLUMN `password_hash` VARCHAR(255) NULL DEFAULT NULL COMMENT ''bcrypt hash para login no portal do cliente'' AFTER `email`',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'email_verified_at');
SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `customer_tbl` ADD COLUMN `email_verified_at` DATETIME NULL DEFAULT NULL COMMENT ''Timestamp de verificacao do email'' AFTER `password_hash`',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'last_login');
SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `customer_tbl` ADD COLUMN `last_login` DATETIME NULL DEFAULT NULL AFTER `email_verified_at`',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Permitir NULL na coluna email (original era NOT NULL com '' como default)
ALTER TABLE `customer_tbl` MODIFY COLUMN `email` VARCHAR(255) NULL DEFAULT NULL;

-- Limpar emails vazios para permitir UNIQUE (MySQL permite multiplos NULLs)
UPDATE `customer_tbl` SET `email` = NULL WHERE `email` = '' OR TRIM(`email`) = '';

-- Index UNIQUE no email (ignora NULLs)
-- Proteger contra erro se index ja existe
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND INDEX_NAME = 'uk_customer_email');
SET @sql = IF(@idx_exists = 0,
  'ALTER TABLE `customer_tbl` ADD UNIQUE INDEX `uk_customer_email` (`email`)',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Tabela de tokens para redefinir senha
CREATE TABLE IF NOT EXISTS `customer_password_resets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `token` VARCHAR(64) NOT NULL COMMENT 'SHA-256 hash do token real',
  `expires_at` DATETIME NOT NULL,
  `used_at` DATETIME NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_token` (`token`),
  INDEX `idx_customer` (`customer_id`),
  INDEX `idx_expires` (`expires_at`),
  CONSTRAINT `fk_pwd_reset_customer` FOREIGN KEY (`customer_id`)
    REFERENCES `customer_tbl` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Rate limiting de login
CREATE TABLE IF NOT EXISTS `customer_login_attempts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `attempted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `success` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `idx_email_time` (`email`, `attempted_at`),
  INDEX `idx_ip_time` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Adicionar customer_id na tabela orders
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'customer_id');
SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `orders` ADD COLUMN `customer_id` INT(11) NULL DEFAULT NULL COMMENT ''FK customer_tbl.id (NULL para pedidos sem login)'' AFTER `order_number`',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index (proteger se ja existe)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND INDEX_NAME = 'idx_customer');
SET @sql = IF(@idx_exists = 0,
  'ALTER TABLE `orders` ADD INDEX `idx_customer` (`customer_id`)',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 5. Backfill: vincular pedidos existentes aos clientes pelo telefone
UPDATE `orders` o
  INNER JOIN `customer_tbl` c
    ON REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(o.cliente_telefone, '(', ''), ')', ''), ' ', ''), '-', ''), '+', '')
       = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(c.mobile, '(', ''), ')', ''), ' ', ''), '-', ''), '+', '')
SET o.customer_id = c.id
WHERE o.customer_id IS NULL
  AND c.mobile IS NOT NULL
  AND c.mobile != ''
  AND LENGTH(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(c.mobile, '(', ''), ')', ''), ' ', ''), '-', ''), '+', '')) >= 8;

-- 6. Traducoes
INSERT INTO `language` (`phrase`, `english`, `portugues`) VALUES
('minha_conta',           'My Account',                    'Minha Conta'),
('entrar',                'Sign In',                       'Entrar'),
('criar_conta',           'Create Account',                'Criar Conta'),
('esqueci_senha',         'Forgot Password',               'Esqueci minha senha'),
('redefinir_senha',       'Reset Password',                'Redefinir Senha'),
('nova_senha',            'New Password',                  'Nova Senha'),
('confirmar_senha',       'Confirm Password',              'Confirmar Senha'),
('meus_pedidos',          'My Orders',                     'Meus Pedidos'),
('meu_perfil',            'My Profile',                    'Meu Perfil'),
('pagamentos',            'Payments',                      'Pagamentos'),
('sair',                  'Sign Out',                      'Sair'),
('conta_criada',          'Account created successfully',  'Conta criada com sucesso'),
('email_ja_cadastrado',   'Email already registered',      'E-mail ja cadastrado'),
('senha_incorreta',       'Incorrect password',            'Senha incorreta'),
('email_nao_encontrado',  'Email not found',               'E-mail nao encontrado'),
('link_enviado',          'Reset link sent to your email', 'Link enviado para seu e-mail'),
('token_invalido',        'Invalid or expired token',      'Token invalido ou expirado'),
('senha_alterada',        'Password changed successfully', 'Senha alterada com sucesso'),
('perfil_atualizado',     'Profile updated',               'Perfil atualizado'),
('nenhum_pedido',         'No orders yet',                 'Nenhum pedido ainda'),
('alterar_senha',         'Change Password',               'Alterar Senha'),
('senha_atual',           'Current Password',              'Senha Atual'),
('dados_pessoais',        'Personal Info',                 'Dados Pessoais'),
('limite_tentativas',     'Too many attempts, try again later', 'Muitas tentativas, aguarde 15 minutos'),
('bem_vindo',             'Welcome',                       'Bem-vindo'),
('repetir_pedido',        'Reorder',                       'Repetir Pedido'),
('ja_tem_conta',          'Already have an account?',      'Ja tem conta?'),
('nao_tem_conta',         'No account yet?',               'Ainda nao tem conta?'),
('total_pedidos',         'Total Orders',                  'Total de Pedidos'),
('total_gasto',           'Total Spent',                   'Total Gasto'),
('pedido_detalhes',       'Order Details',                 'Detalhes do Pedido'),
('acompanhar_pedido',     'Track Order',                   'Acompanhar Pedido'),
('historico_pedidos',     'Order History',                 'Historico de Pedidos'),
('ver_todos',             'View All',                      'Ver Todos')
ON DUPLICATE KEY UPDATE `portugues` = VALUES(`portugues`);
