-- ============================================
-- ADAPTAÇÕES PARA O MERCADO BRASILEIRO
-- Arquivo: 05-adaptacoes-brasil.sql
-- Descrição: Campos CPF/CNPJ, moeda Real, timezone
-- Execução: Automática via docker-entrypoint-initdb.d
-- ============================================

SET sql_mode = '';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

USE inventro_db;

SELECT '========================================' AS '';
SELECT '🇧🇷 Aplicando adaptações brasileiras...' AS 'Status';
SELECT '========================================' AS '';

-- --------------------------------------------------------
-- 1. ADICIONAR MOEDA REAL BRASILEIRO (garantir que existe)
-- --------------------------------------------------------

INSERT INTO `tbl_currency` (`currencyid`, `currencyname`, `curr_icon`, `curr_rate`, `position`) 
VALUES (5, 'Real', 'R$', '1.00', 0)
ON DUPLICATE KEY UPDATE `currencyname` = 'Real', `curr_icon` = 'R$';

-- Definir Real como moeda padrão e timezone do Brasil
UPDATE `setting` SET `currency` = '5', `timezone` = 'America/Sao_Paulo', `language` = 'portugues' WHERE `id` = 2;

-- --------------------------------------------------------
-- 2. ADICIONAR CAMPOS BRASILEIROS NA TABELA DE CLIENTES
-- --------------------------------------------------------

-- CPF
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'cpf') > 0,
    'SELECT 1',
    'ALTER TABLE `customer_tbl` ADD COLUMN `cpf` VARCHAR(14) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- CNPJ
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'cnpj') > 0,
    'SELECT 1',
    'ALTER TABLE `customer_tbl` ADD COLUMN `cnpj` VARCHAR(18) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- CEP
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'cep') > 0,
    'SELECT 1',
    'ALTER TABLE `customer_tbl` ADD COLUMN `cep` VARCHAR(9) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cidade
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'cidade') > 0,
    'SELECT 1',
    'ALTER TABLE `customer_tbl` ADD COLUMN `cidade` VARCHAR(100) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Estado
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'estado') > 0,
    'SELECT 1',
    'ALTER TABLE `customer_tbl` ADD COLUMN `estado` VARCHAR(2) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tipo Pessoa
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_tbl' AND COLUMN_NAME = 'tipo_pessoa') > 0,
    'SELECT 1',
    'ALTER TABLE `customer_tbl` ADD COLUMN `tipo_pessoa` CHAR(1) DEFAULT ''F'''
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- --------------------------------------------------------
-- 3. ADICIONAR CAMPOS BRASILEIROS NA TABELA DE FORNECEDORES
-- --------------------------------------------------------

-- CPF
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_tbl' AND COLUMN_NAME = 'cpf') > 0,
    'SELECT 1',
    'ALTER TABLE `supplier_tbl` ADD COLUMN `cpf` VARCHAR(14) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- CNPJ
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_tbl' AND COLUMN_NAME = 'cnpj') > 0,
    'SELECT 1',
    'ALTER TABLE `supplier_tbl` ADD COLUMN `cnpj` VARCHAR(18) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Inscrição Estadual
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_tbl' AND COLUMN_NAME = 'inscricao_estadual') > 0,
    'SELECT 1',
    'ALTER TABLE `supplier_tbl` ADD COLUMN `inscricao_estadual` VARCHAR(20) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- CEP
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_tbl' AND COLUMN_NAME = 'cep') > 0,
    'SELECT 1',
    'ALTER TABLE `supplier_tbl` ADD COLUMN `cep` VARCHAR(9) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Cidade
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_tbl' AND COLUMN_NAME = 'cidade') > 0,
    'SELECT 1',
    'ALTER TABLE `supplier_tbl` ADD COLUMN `cidade` VARCHAR(100) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Estado
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_tbl' AND COLUMN_NAME = 'estado') > 0,
    'SELECT 1',
    'ALTER TABLE `supplier_tbl` ADD COLUMN `estado` VARCHAR(2) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Razão Social
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_tbl' AND COLUMN_NAME = 'razao_social') > 0,
    'SELECT 1',
    'ALTER TABLE `supplier_tbl` ADD COLUMN `razao_social` VARCHAR(255) NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tipo Pessoa
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplier_tbl' AND COLUMN_NAME = 'tipo_pessoa') > 0,
    'SELECT 1',
    'ALTER TABLE `supplier_tbl` ADD COLUMN `tipo_pessoa` CHAR(1) DEFAULT ''J'''
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- --------------------------------------------------------
-- 4. ADICIONAR TRADUÇÕES DE CAMPOS BRASILEIROS
-- --------------------------------------------------------

INSERT INTO `language` (`phrase`, `english`, `portugues`) VALUES 
('cpf', 'CPF', 'CPF'),
('cnpj', 'CNPJ', 'CNPJ'),
('cep', 'Zip Code', 'CEP'),
('cidade', 'City', 'Cidade'),
('estado', 'State', 'Estado'),
('tipo_pessoa', 'Person Type', 'Tipo de Pessoa'),
('pessoa_fisica', 'Physical Person', 'Pessoa Física'),
('pessoa_juridica', 'Legal Person', 'Pessoa Jurídica'),
('inscricao_estadual', 'State Registration', 'Inscrição Estadual'),
('razao_social', 'Company Name', 'Razão Social'),
('buscar_cep', 'Search CEP', 'Buscar CEP'),
('cpf_invalido', 'Invalid CPF', 'CPF Inválido'),
('cnpj_invalido', 'Invalid CNPJ', 'CNPJ Inválido'),
('cpf_cnpj', 'CPF/CNPJ', 'CPF/CNPJ'),
('numero', 'Number', 'Número'),
('bairro', 'Neighborhood', 'Bairro'),
('complemento', 'Complement', 'Complemento'),
('uf', 'State', 'UF'),
('logradouro', 'Street', 'Logradouro')
ON DUPLICATE KEY UPDATE `portugues` = VALUES(`portugues`);

SELECT '✅ Adaptações brasileiras aplicadas!' AS 'Status';
