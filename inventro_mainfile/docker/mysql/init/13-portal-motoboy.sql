-- =============================================
-- INVENTRO - Portal do Motoboy (Entregador)
-- Adiciona autenticação, taxa fixa, histórico de entregas
-- e novo status 'pronto_coleta' no fluxo de pedidos
-- =============================================

-- -----------------------------------------------
-- 1. Colunas novas na tabela entregadores
-- -----------------------------------------------

-- Senha para login no portal (bcrypt hash)
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS safe_add_col_13(IN p_table VARCHAR(64), IN p_column VARCHAR(64), IN p_definition TEXT)
BEGIN
    DECLARE col_exists INT DEFAULT 0;
    SELECT COUNT(*) INTO col_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table AND COLUMN_NAME = p_column;
    IF col_exists = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL safe_add_col_13('entregadores', 'senha', "VARCHAR(255) NULL DEFAULT NULL AFTER `telefone`");
CALL safe_add_col_13('entregadores', 'taxa_entrega_fixa', "DECIMAL(10,2) NOT NULL DEFAULT 5.00 AFTER `veiculo`");
CALL safe_add_col_13('entregadores', 'ultimo_login', "DATETIME NULL DEFAULT NULL AFTER `updated_at`");
CALL safe_add_col_13('entregadores', 'total_entregas', "INT NOT NULL DEFAULT 0 AFTER `ultimo_login`");
CALL safe_add_col_13('entregadores', 'total_ganhos', "DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `total_entregas`");

-- -----------------------------------------------
-- 2. Novo timestamp de status na tabela orders
-- -----------------------------------------------

CALL safe_add_col_13('orders', 'hora_pronto_coleta', "DATETIME NULL DEFAULT NULL AFTER `hora_preparando`");

-- -----------------------------------------------
-- 3. Tabela de histórico de entregas do motoboy
-- -----------------------------------------------

CREATE TABLE IF NOT EXISTS `entregador_entregas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `entregador_id` INT NOT NULL,
    `order_id` INT NOT NULL,
    `valor_ganho` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `aceito_em` DATETIME NOT NULL,
    `coletado_em` DATETIME NULL DEFAULT NULL,
    `entregue_em` DATETIME NULL DEFAULT NULL,
    `status` ENUM('aceito','coletado','entregue','cancelado') NOT NULL DEFAULT 'aceito',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`entregador_id`) REFERENCES `entregadores`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_order` (`order_id`),
    INDEX `idx_entregador_status` (`entregador_id`, `status`),
    INDEX `idx_entregador_data` (`entregador_id`, `aceito_em`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- 4. Config padrão da taxa do motoboy
-- -----------------------------------------------

INSERT INTO `cardapio_config` (`chave`, `valor`)
VALUES ('taxa_motoboy_fixa', '5.00')
ON DUPLICATE KEY UPDATE `chave` = `chave`;

-- -----------------------------------------------
-- 5. Cleanup
-- -----------------------------------------------

DROP PROCEDURE IF EXISTS `safe_add_col_13`;
