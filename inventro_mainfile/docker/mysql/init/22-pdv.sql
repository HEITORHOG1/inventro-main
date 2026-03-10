-- =============================================
-- 22-pdv.sql — PDV (Frente de Caixa) — Fase 1
-- =============================================
-- Cria tabelas e campos necessários para o módulo PDV.
-- Referência: docs/PDV-NFC-e-PROPOSTA.md seção 9.7
--
-- Tabelas novas: pdv_terminal, pdv_caixa, pdv_caixa_mov,
--   pdv_fiado, pdv_fiado_pagamento, invoice_payment,
--   pdv_venda_suspensa, pdv_audit_log
--
-- ALTERs: product_tbl, invoice_tbl, invoice_details,
--   pdv_caixa, customer_tbl, user, product_return

-- =========================================
-- 1. Configuração do terminal PDV
-- =========================================
CREATE TABLE IF NOT EXISTS `pdv_terminal` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `numero` VARCHAR(10) NOT NULL UNIQUE COMMENT 'Ex: 001, 002, 003',
    `nome` VARCHAR(100) NOT NULL COMMENT 'Ex: Caixa Principal, Caixa Rápido',
    `serie_nfce` VARCHAR(5) DEFAULT '1',
    `ambiente` ENUM('homologacao','producao') DEFAULT 'homologacao',
    `impressora` VARCHAR(255) NULL COMMENT 'Nome da impressora térmica',
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 2. Controle de caixa (abertura/fechamento)
-- =========================================
CREATE TABLE IF NOT EXISTS `pdv_caixa` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `terminal_id` INT NOT NULL COMMENT 'FK → pdv_terminal.id',
    `operador_id` INT NOT NULL COMMENT 'FK → user.id',
    `valor_abertura` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `valor_fechamento` DECIMAL(10,2) NULL,
    `valor_contado` DECIMAL(10,2) NULL COMMENT 'Dinheiro contado no fechamento',
    `diferenca` DECIMAL(10,2) NULL COMMENT '+ sobra, - falta',
    `total_vendas` DECIMAL(10,2) NULL COMMENT 'Snapshot total vendas ao fechar',
    `qtd_vendas` INT NULL COMMENT 'Qtd vendas no turno',
    `aberto_em` DATETIME NOT NULL,
    `fechado_em` DATETIME NULL,
    `status` ENUM('aberto','fechado') DEFAULT 'aberto',
    `observacao` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal`(`id`),
    FOREIGN KEY (`operador_id`) REFERENCES `user`(`id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_terminal_status` (`terminal_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 3. Movimentações do caixa
-- =========================================
CREATE TABLE IF NOT EXISTS `pdv_caixa_mov` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `caixa_id` INT NOT NULL COMMENT 'FK → pdv_caixa.id',
    `tipo` ENUM('suprimento','sangria','venda','cancelamento','troca_operador','devolucao') NOT NULL,
    `valor` DECIMAL(10,2) NOT NULL,
    `forma_pagamento` ENUM('dinheiro','pix','debito','credito','fiado','misto') NULL COMMENT 'Preenchido quando tipo=venda',
    `invoice_id` INT NULL COMMENT 'FK → invoice_tbl.id (quando tipo=venda ou cancelamento)',
    `descricao` VARCHAR(255) NULL,
    `operador_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`caixa_id`) REFERENCES `pdv_caixa`(`id`),
    FOREIGN KEY (`operador_id`) REFERENCES `user`(`id`),
    INDEX `idx_tipo` (`tipo`),
    INDEX `idx_caixa_tipo` (`caixa_id`, `tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 4. Pendências de fiado
-- =========================================
CREATE TABLE IF NOT EXISTS `pdv_fiado` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL COMMENT 'FK → invoice_tbl.id',
    `customer_id` INT NOT NULL COMMENT 'FK → customer_tbl.id (PK inteira)',
    `valor` DECIMAL(10,2) NOT NULL,
    `valor_pago` DECIMAL(10,2) DEFAULT 0.00,
    `status` ENUM('pendente','parcial','quitado','cancelado') DEFAULT 'pendente',
    `vencimento` DATE NULL,
    `quitado_em` DATETIME NULL,
    `operador_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoice_tbl`(`id`),
    FOREIGN KEY (`operador_id`) REFERENCES `user`(`id`),
    INDEX `idx_customer_status` (`customer_id`, `status`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 5. Pagamentos parciais do fiado
-- =========================================
CREATE TABLE IF NOT EXISTS `pdv_fiado_pagamento` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `fiado_id` INT NOT NULL COMMENT 'FK → pdv_fiado.id',
    `valor` DECIMAL(10,2) NOT NULL,
    `forma_pagamento` ENUM('dinheiro','pix','debito','credito') NOT NULL,
    `operador_id` INT NOT NULL,
    `observacao` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`fiado_id`) REFERENCES `pdv_fiado`(`id`),
    FOREIGN KEY (`operador_id`) REFERENCES `user`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 6. Formas de pagamento split (invoice_payment)
-- =========================================
CREATE TABLE IF NOT EXISTS `invoice_payment` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL COMMENT 'FK → invoice_tbl.id',
    `forma` ENUM('dinheiro','pix','debito','credito','fiado') NOT NULL,
    `valor` DECIMAL(10,2) NOT NULL COMMENT 'Valor pago nesta forma',
    `bandeira` VARCHAR(50) NULL COMMENT 'Visa, Master, Elo (quando cartão)',
    `nsu` VARCHAR(50) NULL COMMENT 'NSU da transação TEF',
    `autorizacao` VARCHAR(50) NULL COMMENT 'Código de autorização TEF',
    `troco` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Troco dado (só para dinheiro)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoice_tbl`(`id`),
    INDEX `idx_invoice` (`invoice_id`),
    INDEX `idx_forma` (`forma`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 7. Vendas suspensas (hold sale)
-- =========================================
CREATE TABLE IF NOT EXISTS `pdv_venda_suspensa` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `terminal_id` INT NOT NULL COMMENT 'FK → pdv_terminal.id',
    `caixa_id` INT NOT NULL COMMENT 'FK → pdv_caixa.id',
    `operador_id` INT NOT NULL COMMENT 'FK → user.id',
    `itens` JSON NOT NULL COMMENT 'Array de itens [{product_id, ean, nome, qtd, preco, desconto}]',
    `cpf_cliente` VARCHAR(14) NULL,
    `customer_id` INT NULL,
    `total` DECIMAL(10,2) NOT NULL,
    `motivo` VARCHAR(100) NULL COMMENT 'Ex: cliente foi buscar carteira',
    `status` ENUM('suspensa','recuperada','expirada') DEFAULT 'suspensa',
    `suspensa_em` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `recuperada_em` DATETIME NULL,
    `expires_at` DATETIME NOT NULL COMMENT 'Expira em 2h (auto-cancela)',
    FOREIGN KEY (`terminal_id`) REFERENCES `pdv_terminal`(`id`),
    FOREIGN KEY (`caixa_id`) REFERENCES `pdv_caixa`(`id`),
    FOREIGN KEY (`operador_id`) REFERENCES `user`(`id`),
    INDEX `idx_terminal_status` (`terminal_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 8. Log de auditoria do PDV
-- =========================================
CREATE TABLE IF NOT EXISTS `pdv_audit_log` (
    `id` BIGINT AUTO_INCREMENT PRIMARY KEY,
    `terminal_id` INT NOT NULL,
    `caixa_id` INT NULL COMMENT 'NULL se ação fora de caixa aberto',
    `operador_id` INT NOT NULL,
    `acao` VARCHAR(50) NOT NULL COMMENT 'login, abertura, venda, cancelamento, desconto, etc.',
    `entidade` VARCHAR(50) NULL COMMENT 'invoice, pdv_caixa, product, etc.',
    `entidade_id` INT NULL COMMENT 'ID do registro afetado',
    `detalhes` JSON NULL COMMENT 'Dados adicionais em JSON',
    `ip` VARCHAR(45) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_terminal_data` (`terminal_id`, `created_at`),
    INDEX `idx_operador_data` (`operador_id`, `created_at`),
    INDEX `idx_acao` (`acao`),
    INDEX `idx_entidade` (`entidade`, `entidade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- 9. ALTERs em tabelas existentes
-- =========================================

-- 9a. product_tbl: EAN/GTIN + estoque_minimo + pesável
ALTER TABLE `product_tbl`
    ADD COLUMN `ean_gtin` VARCHAR(14) NULL COMMENT 'Código de barras EAN-8/EAN-13/GTIN-14' AFTER `product_code`,
    ADD COLUMN `estoque_minimo` INT DEFAULT 0 COMMENT 'Alerta quando estoque abaixo deste valor' AFTER `ean_gtin`,
    ADD COLUMN `pesavel` TINYINT(1) DEFAULT 0 COMMENT '1 = vendido por peso (balança)' AFTER `estoque_minimo`,
    ADD COLUMN `codigo_balanca` VARCHAR(5) NULL COMMENT 'Código interno da balança (5 dígitos)' AFTER `pesavel`,
    ADD COLUMN `tipo_barcode_balanca` ENUM('peso','preco') DEFAULT 'peso' COMMENT 'Como interpretar o barcode da balança' AFTER `codigo_balanca`,
    ADD INDEX `idx_ean_gtin` (`ean_gtin`);

-- 9b. invoice_tbl: vincular ao terminal PDV + desconto
ALTER TABLE `invoice_tbl`
    ADD COLUMN `terminal_id` INT NULL COMMENT 'FK → pdv_terminal.id (NULL = venda pelo POS antigo)' AFTER `id`,
    ADD COLUMN `caixa_id` INT NULL COMMENT 'FK → pdv_caixa.id (sessão de caixa)' AFTER `terminal_id`,
    ADD COLUMN `desconto_total` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Desconto aplicado na venda toda' AFTER `total_amount`,
    ADD COLUMN `desconto_autorizado_por` INT NULL COMMENT 'FK → user.id (supervisor que autorizou)' AFTER `desconto_total`,
    ADD INDEX `idx_terminal` (`terminal_id`);

-- 9c. invoice_details: desconto por item + descrição genérica
ALTER TABLE `invoice_details`
    ADD COLUMN `desconto_pct` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Desconto % aplicado neste item',
    ADD COLUMN `desconto_valor` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Valor do desconto neste item',
    ADD COLUMN `descricao_manual` VARCHAR(255) NULL COMMENT 'Descrição para item genérico (product_id NULL)';

-- 9d. customer_tbl: limite de crédito
ALTER TABLE `customer_tbl`
    ADD COLUMN `limite_credito` DECIMAL(10,2) DEFAULT 0.00 COMMENT '0 = usa padrão do sistema',
    ADD COLUMN `fiado_bloqueado` TINYINT(1) DEFAULT 0 COMMENT '1 = bloqueado pelo admin';

-- 9e. user: matrícula para login no PDV
ALTER TABLE `user`
    ADD COLUMN `matricula` VARCHAR(20) NULL COMMENT 'Login do PDV (alternativa ao email)' AFTER `email`,
    ADD UNIQUE INDEX `idx_matricula` (`matricula`);

-- 9f. product_return: vincular ao terminal
ALTER TABLE `product_return`
    ADD COLUMN `terminal_id` INT NULL COMMENT 'FK → pdv_terminal.id (NULL = devolução via POS antigo)';

-- =========================================
-- 10. Dados iniciais
-- =========================================

-- Menu PDV no sidebar admin
INSERT INTO `sec_menu_item` (`menu_title`, `page_url`, `module`, `parent_menu`, `status`, `createby`, `createdate`)
SELECT 'PDV / Caixa', 'pdv', 'pdv', NULL, 1, 1, NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `sec_menu_item` WHERE `module` = 'pdv');

-- Role "Operador de Caixa"
INSERT INTO `sec_role_tbl` (`role_name`, `role_description`, `create_by`, `date_time`, `role_status`)
SELECT 'Operador de Caixa', 'Operador do PDV — acesso apenas à frente de caixa', 1, NOW(), 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `sec_role_tbl` WHERE `role_name` = 'Operador de Caixa');

-- Terminal padrão (Caixa 001)
INSERT INTO `pdv_terminal` (`numero`, `nome`)
SELECT '001', 'Caixa Principal'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `pdv_terminal` WHERE `numero` = '001');

-- =========================================
-- Fim
-- =========================================
SELECT '✅ 22-pdv.sql — PDV Fase 1 aplicado com sucesso!' AS 'Status';
