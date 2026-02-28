-- =============================================
-- 09-ifood-local.sql
-- Inventro iFood Local — Novas features
-- Rodar APÓS 08-modulo-financeiro.sql
-- =============================================

SET sql_mode = '';
SET NAMES utf8mb4;
USE inventro_db;

-- =============================================
-- Helper: procedure para ADD COLUMN seguro (MySQL 8.0 não suporta IF NOT EXISTS)
-- =============================================
DROP PROCEDURE IF EXISTS `safe_add_column`;
DELIMITER //
CREATE PROCEDURE `safe_add_column`(
    IN p_table VARCHAR(64),
    IN p_column VARCHAR(64),
    IN p_definition TEXT
)
BEGIN
    DECLARE col_exists INT DEFAULT 0;
    SELECT COUNT(*) INTO col_exists
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND COLUMN_NAME = p_column;
    IF col_exists = 0 THEN
        SET @ddl = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

-- =============================================
-- 1. Disponibilidade de produto no cardápio
-- =============================================
CALL safe_add_column('product_tbl', 'disponivel_cardapio', "TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=visível no cardápio, 0=oculto/esgotado'");
CALL safe_add_column('product_tbl', 'ordem_exibicao', "INT NOT NULL DEFAULT 0 COMMENT 'Ordem de exibição no cardápio (menor = primeiro)'");

-- =============================================
-- 2. Colunas extras na tabela orders
-- =============================================

-- Timestamps de cada mudança de status
CALL safe_add_column('orders', 'hora_confirmado', 'DATETIME NULL DEFAULT NULL');
CALL safe_add_column('orders', 'hora_preparando', 'DATETIME NULL DEFAULT NULL');
CALL safe_add_column('orders', 'hora_saiu_entrega', 'DATETIME NULL DEFAULT NULL');
CALL safe_add_column('orders', 'hora_entregue', 'DATETIME NULL DEFAULT NULL');

-- Pagamento confirmado (manual ou webhook)
CALL safe_add_column('orders', 'pagamento_confirmado', 'TINYINT(1) NOT NULL DEFAULT 0');

-- Entregador atribuído ao pedido
CALL safe_add_column('orders', 'entregador_id', 'INT NULL DEFAULT NULL');
CALL safe_add_column('orders', 'entregador_nome', 'VARCHAR(100) NULL DEFAULT NULL');

-- Avaliação do cliente
CALL safe_add_column('orders', 'avaliacao_nota', "TINYINT NULL DEFAULT NULL COMMENT '1 a 5 estrelas'");
CALL safe_add_column('orders', 'avaliacao_comentario', 'TEXT NULL DEFAULT NULL');

-- Cupom de desconto usado
CALL safe_add_column('orders', 'cupom_codigo', 'VARCHAR(20) NULL DEFAULT NULL');
CALL safe_add_column('orders', 'desconto_cupom', 'DECIMAL(10,2) NOT NULL DEFAULT 0.00');

-- Tipo de entrega (entrega ou retirada no local)
CALL safe_add_column('orders', 'tipo_entrega', "VARCHAR(20) NOT NULL DEFAULT 'entrega' COMMENT 'entrega ou retirada'");

-- Limpar helper
DROP PROCEDURE IF EXISTS `safe_add_column`;

-- =============================================
-- 3. Config: loja pausada
-- =============================================
INSERT INTO `cardapio_config` (`chave`, `valor`, `descricao`) VALUES
    ('loja_pausada', '0', 'Se 1, loja não aceita pedidos temporariamente')
ON DUPLICATE KEY UPDATE `descricao` = VALUES(`descricao`);

-- =============================================
-- 4. Tabela de entregadores
-- =============================================
CREATE TABLE IF NOT EXISTS `entregadores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `telefone` VARCHAR(20) NOT NULL,
    `veiculo` ENUM('moto', 'bicicleta', 'carro', 'a_pe') NOT NULL DEFAULT 'moto',
    `status` ENUM('disponivel', 'em_entrega', 'indisponivel') NOT NULL DEFAULT 'disponivel',
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ativo` (`ativo`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. Tabela de cupons de desconto
-- =============================================
CREATE TABLE IF NOT EXISTS `cupons_desconto` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(20) NOT NULL,
    `tipo` ENUM('percentual', 'valor_fixo', 'frete_gratis') NOT NULL,
    `valor` DECIMAL(10,2) NOT NULL DEFAULT 0.00
        COMMENT 'Percentual (ex: 10.00 = 10%) ou valor fixo em R$',
    `valor_minimo_pedido` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `uso_maximo` INT NULL DEFAULT NULL
        COMMENT 'NULL = ilimitado',
    `uso_atual` INT NOT NULL DEFAULT 0,
    `validade_inicio` DATE NULL DEFAULT NULL,
    `validade_fim` DATE NULL DEFAULT NULL,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_codigo` (`codigo`),
    INDEX `idx_ativo` (`ativo`),
    INDEX `idx_validade` (`validade_inicio`, `validade_fim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. Traduções PT-BR (INSERT seguro — sem UNIQUE KEY em phrase)
-- =============================================
DROP PROCEDURE IF EXISTS `safe_insert_lang`;
DELIMITER //
CREATE PROCEDURE `safe_insert_lang`(IN p_phrase VARCHAR(100), IN p_english VARCHAR(255), IN p_portugues TEXT)
BEGIN
    DECLARE cnt INT DEFAULT 0;
    SELECT COUNT(*) INTO cnt FROM `language` WHERE `phrase` = p_phrase;
    IF cnt = 0 THEN
        INSERT INTO `language` (`phrase`, `english`, `portugues`) VALUES (p_phrase, p_english, p_portugues);
    ELSE
        UPDATE `language` SET `portugues` = p_portugues WHERE `phrase` = p_phrase;
    END IF;
END //
DELIMITER ;

CALL safe_insert_lang('entregadores', 'Delivery Drivers', 'Entregadores');
CALL safe_insert_lang('novo_entregador', 'New Driver', 'Novo Entregador');
CALL safe_insert_lang('editar_entregador', 'Edit Driver', 'Editar Entregador');
CALL safe_insert_lang('veiculo', 'Vehicle', 'Veículo');
CALL safe_insert_lang('cupons_desconto', 'Discount Coupons', 'Cupons de Desconto');
CALL safe_insert_lang('novo_cupom', 'New Coupon', 'Novo Cupom');
CALL safe_insert_lang('editar_cupom', 'Edit Coupon', 'Editar Cupom');
CALL safe_insert_lang('codigo_cupom', 'Coupon Code', 'Código do Cupom');
CALL safe_insert_lang('tipo_desconto', 'Discount Type', 'Tipo de Desconto');
CALL safe_insert_lang('valor_minimo', 'Minimum Value', 'Valor Mínimo');
CALL safe_insert_lang('uso_maximo', 'Max Uses', 'Uso Máximo');
CALL safe_insert_lang('validade', 'Validity', 'Validade');
CALL safe_insert_lang('disponivel', 'Available', 'Disponível');
CALL safe_insert_lang('esgotado', 'Out of Stock', 'Esgotado');
CALL safe_insert_lang('pausar_loja', 'Pause Store', 'Pausar Loja');
CALL safe_insert_lang('retomar_loja', 'Resume Store', 'Retomar Loja');
CALL safe_insert_lang('loja_pausada', 'Store Paused', 'Loja Pausada');
CALL safe_insert_lang('acompanhar_pedido', 'Track Order', 'Acompanhar Pedido');
CALL safe_insert_lang('pedido_minimo_msg', 'Minimum order', 'Pedido mínimo');
CALL safe_insert_lang('loja_fechada', 'Store Closed', 'Loja Fechada');
CALL safe_insert_lang('kanban', 'Kanban Board', 'Painel Kanban');
CALL safe_insert_lang('retirada_loja', 'Pickup at Store', 'Retirada na Loja');
CALL safe_insert_lang('avaliacao', 'Rating', 'Avaliação');
CALL safe_insert_lang('repetir_pedido', 'Repeat Order', 'Repetir Pedido');
CALL safe_insert_lang('frete_gratis', 'Free Shipping', 'Frete Grátis');
CALL safe_insert_lang('percentual', 'Percentage', 'Percentual');
CALL safe_insert_lang('valor_fixo', 'Fixed Value', 'Valor Fixo');
CALL safe_insert_lang('confirmar_pagamento', 'Confirm Payment', 'Confirmar Pagamento');
CALL safe_insert_lang('atribuir_entregador', 'Assign Driver', 'Atribuir Entregador');
CALL safe_insert_lang('enviar_whatsapp', 'Send via WhatsApp', 'Enviar via WhatsApp');
CALL safe_insert_lang('pedido_recebido', 'Order Received', 'Pedido Recebido');
CALL safe_insert_lang('pedido_confirmado', 'Order Confirmed', 'Pedido Confirmado');
CALL safe_insert_lang('pedido_preparando', 'Preparing', 'Preparando');
CALL safe_insert_lang('saiu_entrega', 'Out for Delivery', 'Saiu para Entrega');
CALL safe_insert_lang('pedido_entregue', 'Delivered', 'Entregue');
CALL safe_insert_lang('pronto_retirada', 'Ready for Pickup', 'Pronto para Retirada');
CALL safe_insert_lang('moto', 'Motorcycle', 'Moto');
CALL safe_insert_lang('bicicleta', 'Bicycle', 'Bicicleta');
CALL safe_insert_lang('carro', 'Car', 'Carro');
CALL safe_insert_lang('a_pe', 'On Foot', 'A Pé');
CALL safe_insert_lang('pix', 'PIX', 'PIX');
CALL safe_insert_lang('cartao_debito', 'Debit Card', 'Cartão Débito');
CALL safe_insert_lang('cartao_credito', 'Credit Card', 'Cartão Crédito');
CALL safe_insert_lang('fiado', 'Store Credit', 'Fiado');
CALL safe_insert_lang('copiar_pix', 'Copy PIX Code', 'Copiar Código PIX');
CALL safe_insert_lang('qrcode_pix', 'PIX QR Code', 'QR Code PIX');
CALL safe_insert_lang('instalar_app', 'Install App', 'Instalar App');
CALL safe_insert_lang('notificacao_sonora', 'Sound Notification', 'Notificação Sonora');
CALL safe_insert_lang('impressao_automatica', 'Auto Print', 'Impressão Automática');

DROP PROCEDURE IF EXISTS `safe_insert_lang`;

-- =============================================
-- 7. Menus para novas features
-- =============================================

-- Verificar se menu Kanban já existe antes de inserir
INSERT INTO `sec_menu_item` (`menu_title`, `page_url`, `module`, `parent_menu`, `status`, `createby`, `createdate`)
SELECT 'Kanban', 'delivery/orders/kanban', 'delivery', 200, 1, 1, NOW()
FROM DUAL WHERE NOT EXISTS (
    SELECT 1 FROM `sec_menu_item` WHERE `page_url` = 'delivery/orders/kanban'
);

INSERT INTO `sec_menu_item` (`menu_title`, `page_url`, `module`, `parent_menu`, `status`, `createby`, `createdate`)
SELECT 'Entregadores', 'delivery/entregadores', 'delivery', 200, 1, 1, NOW()
FROM DUAL WHERE NOT EXISTS (
    SELECT 1 FROM `sec_menu_item` WHERE `page_url` = 'delivery/entregadores'
);

INSERT INTO `sec_menu_item` (`menu_title`, `page_url`, `module`, `parent_menu`, `status`, `createby`, `createdate`)
SELECT 'Cupons', 'delivery/cupons', 'delivery', 200, 1, 1, NOW()
FROM DUAL WHERE NOT EXISTS (
    SELECT 1 FROM `sec_menu_item` WHERE `page_url` = 'delivery/cupons'
);

-- =============================================
-- 8. Permissões para role admin (role_id=1)
-- =============================================
INSERT INTO `sec_role_permission` (`role_id`, `menu_id`, `can_access`, `can_create`, `can_edit`, `can_delete`, `createby`, `createdate`)
SELECT 1, mi.menu_id, 1, 1, 1, 1, 1, NOW()
FROM `sec_menu_item` mi
WHERE mi.page_url IN ('delivery/orders/kanban', 'delivery/entregadores', 'delivery/cupons')
AND NOT EXISTS (
    SELECT 1 FROM `sec_role_permission` rp
    WHERE rp.role_id = 1 AND rp.menu_id = mi.menu_id
);
