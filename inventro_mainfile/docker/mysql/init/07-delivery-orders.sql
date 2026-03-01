-- ============================================
-- INVENTRO - SISTEMA DE ENTREGAS E PEDIDOS
-- Arquivo: 07-delivery-orders.sql
-- Descrição: Tabelas para delivery e pedidos online
-- Execução: Automática via docker-entrypoint-initdb.d
-- ============================================

SET sql_mode = '';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

USE inventro_db;

SELECT '========================================' AS '';
SELECT '🚚 Criando sistema de entregas...' AS 'Status';
SELECT '========================================' AS '';

-- ============================================
-- 1. TABELA DE ZONAS DE ENTREGA (BAIRROS)
-- ============================================

CREATE TABLE IF NOT EXISTS `delivery_zones` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL COMMENT 'Nome do bairro/região',
    `taxa` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Taxa de entrega (0 = grátis)',
    `tempo_min` INT(11) DEFAULT 20 COMMENT 'Tempo mínimo de entrega (minutos)',
    `tempo_max` INT(11) DEFAULT 40 COMMENT 'Tempo máximo de entrega (minutos)',
    `ativo` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. TABELA DE PEDIDOS
-- ============================================

CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(20) NOT NULL COMMENT 'Número do pedido (#0001)',
    `cliente_nome` VARCHAR(100) NOT NULL,
    `cliente_telefone` VARCHAR(20) NOT NULL,
    `cliente_endereco` TEXT NOT NULL,
    `cliente_complemento` VARCHAR(255) DEFAULT NULL,
    `zona_id` INT(11) DEFAULT NULL COMMENT 'FK para delivery_zones',
    `zona_nome` VARCHAR(100) DEFAULT NULL COMMENT 'Nome da zona no momento do pedido',
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `taxa_entrega` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `desconto` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `forma_pagamento` VARCHAR(20) NOT NULL COMMENT 'dinheiro/cartao/pix',
    `troco_para` DECIMAL(10,2) DEFAULT NULL COMMENT 'Valor do troco (se dinheiro)',
    `tipo_checkout` VARCHAR(20) NOT NULL DEFAULT 'site' COMMENT 'whatsapp/site',
    `status` VARCHAR(20) NOT NULL DEFAULT 'pendente' COMMENT 'pendente/confirmado/preparando/saiu_entrega/entregue/cancelado',
    `observacao` TEXT DEFAULT NULL COMMENT 'Observações do cliente',
    `cpf_nota` VARCHAR(14) DEFAULT NULL COMMENT 'CPF para nota fiscal',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `order_number_unique` (`order_number`),
    KEY `idx_status` (`status`),
    KEY `idx_created` (`created_at`),
    KEY `idx_zona` (`zona_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TABELA DE ITENS DO PEDIDO
-- ============================================

CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) NOT NULL COMMENT 'FK para orders',
    `product_id` INT(11) NOT NULL COMMENT 'FK para product_tbl',
    `product_name` VARCHAR(255) NOT NULL COMMENT 'Nome do produto no momento',
    `quantity` INT(11) NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10,2) NOT NULL COMMENT 'Preço unitário no momento',
    `total_price` DECIMAL(10,2) NOT NULL COMMENT 'Preço total (qty * unit)',
    PRIMARY KEY (`id`),
    KEY `idx_order` (`order_id`),
    KEY `idx_product` (`product_id`),
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. CONFIGURAÇÕES DO CARDÁPIO/DELIVERY
-- ============================================

CREATE TABLE IF NOT EXISTS `cardapio_config` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `chave` VARCHAR(50) NOT NULL UNIQUE,
    `valor` TEXT,
    `descricao` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir configurações padrão
INSERT INTO `cardapio_config` (`chave`, `valor`, `descricao`) VALUES
('taxa_entrega', '5.00', 'Taxa de entrega fixa'),
('tempo_medio_entrega', '45', 'Tempo médio de entrega em minutos'),
('horario_abertura', '00:00', 'Horário de abertura'),
('horario_fechamento', '23:59', 'Horário de fechamento'),
('dias_funcionamento', '0,1,2,3,4,5,6', 'Dias da semana (0=dom, 6=sab)'),
('pedido_minimo', '0.00', 'Valor mínimo do pedido'),
('aceita_dinheiro', '1', 'Aceita pagamento em dinheiro'),
('aceita_cartao', '1', 'Aceita cartão na entrega'),
('aceita_pix', '1', 'Aceita Pix'),
('pix_chave', '', 'Chave Pix para pagamento'),
('pix_tipo', 'telefone', 'Tipo da chave Pix (telefone/cpf/cnpj/email/aleatoria)'),
('whatsapp_numero', '24999998888', 'Número WhatsApp para pedidos'),
('entrega_ativa', '1', 'Delivery ativo'),
('retirada_ativa', '1', 'Retirada no local ativa'),
('mensagem_confirmacao', 'Seu pedido foi recebido! Em breve entraremos em contato.', 'Mensagem de confirmação')
ON DUPLICATE KEY UPDATE `valor` = VALUES(`valor`);

-- ============================================
-- 5. DADOS DE EXEMPLO - ZONAS DE ENTREGA
-- ============================================

INSERT INTO `delivery_zones` (`nome`, `taxa`, `tempo_min`, `tempo_max`, `ativo`) VALUES
('Centro', 0.00, 15, 25, 1),
('Quitandinha', 4.00, 15, 30, 1),
('Valparaiso', 5.00, 20, 35, 1),
('Alto da Serra', 5.00, 20, 35, 1),
('Castelania', 6.00, 25, 40, 1),
('Morin', 4.00, 15, 30, 1),
('Coronel Veiga', 5.00, 20, 35, 1),
('Itaipava', 8.00, 35, 50, 1),
('Bingen', 4.00, 15, 30, 1),
('Simerinha', 6.00, 25, 40, 1),
('Mosela', 5.00, 20, 35, 1),
('Retiro', 3.00, 10, 20, 1)
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);

-- ============================================
-- 6. ADICIONAR MENUS DO ADMIN
-- ============================================

-- Menu principal de Delivery
INSERT INTO `sec_menu_item` (`menu_id`, `menu_title`, `page_url`, `module`, `parent_menu`, `status`, `createby`, `createdate`) VALUES
(200, 'Delivery', 'delivery', 'delivery', 0, 1, 1, NOW()),
(201, 'Taxas de Entrega', 'delivery/zones', 'delivery', 200, 1, 1, NOW()),
(202, 'Pedidos Online', 'delivery/orders', 'delivery', 200, 1, 1, NOW()),
(203, 'Configurações', 'delivery/config', 'delivery', 200, 1, 1, NOW())
ON DUPLICATE KEY UPDATE `menu_title` = VALUES(`menu_title`);

-- ============================================
-- 7. TRADUÇÕES
-- ============================================

INSERT INTO `language` (`phrase`, `english`, `portugues`) VALUES
('delivery', 'Delivery', 'Entrega'),
('delivery_zones', 'Delivery Zones', 'Zonas de Entrega'),
('delivery_fee', 'Delivery Fee', 'Taxa de Entrega'),
('free_delivery', 'Free Delivery', 'Entrega Grátis'),
('delivery_time', 'Delivery Time', 'Tempo de Entrega'),
('min_order', 'Minimum Order', 'Pedido Mínimo'),
('online_orders', 'Online Orders', 'Pedidos Online'),
('order_number', 'Order Number', 'Número do Pedido'),
('order_status', 'Order Status', 'Status do Pedido'),
('pending', 'Pending', 'Pendente'),
('confirmed', 'Confirmed', 'Confirmado'),
('preparing', 'Preparing', 'Preparando'),
('out_for_delivery', 'Out for Delivery', 'Saiu para Entrega'),
('delivered', 'Delivered', 'Entregue'),
('cancelled', 'Cancelled', 'Cancelado'),
('payment_method', 'Payment Method', 'Forma de Pagamento'),
('cash', 'Cash', 'Dinheiro'),
('card', 'Card', 'Cartão'),
('pix', 'Pix', 'Pix'),
('change_for', 'Change for', 'Troco para'),
('observations', 'Observations', 'Observações'),
('checkout', 'Checkout', 'Finalizar Pedido'),
('place_order', 'Place Order', 'Fazer Pedido'),
('order_confirmed', 'Order Confirmed', 'Pedido Confirmado'),
('order_summary', 'Order Summary', 'Resumo do Pedido'),
('delivery_address', 'Delivery Address', 'Endereço de Entrega'),
('select_neighborhood', 'Select Neighborhood', 'Selecione o Bairro'),
('your_order', 'Your Order', 'Seu Pedido'),
('add_zone', 'Add Zone', 'Adicionar Zona'),
('edit_zone', 'Edit Zone', 'Editar Zona'),
('zone_name', 'Zone Name', 'Nome da Zona'),
('minutes', 'minutes', 'minutos'),
('cardapio_config', 'Menu Settings', 'Configurações do Cardápio')
ON DUPLICATE KEY UPDATE `portugues` = VALUES(`portugues`);

SELECT '✅ Sistema de entregas criado!' AS 'Status';
