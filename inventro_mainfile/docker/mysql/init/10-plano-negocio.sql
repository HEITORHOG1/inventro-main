-- ============================================
-- INVENTRO - MIGRAÇÃO: Plano de Negócio
-- Arquivo: 10-plano-negocio.sql
-- Descrição: Adiciona coluna plano_negocio na tabela setting
-- ============================================

USE inventro_db;

ALTER TABLE `setting`
    ADD COLUMN `plano_negocio` VARCHAR(50) NOT NULL DEFAULT 'mercado_completo'
    AFTER `timezone`;

-- Garante que registros existentes tenham o valor correto
UPDATE `setting` SET `plano_negocio` = 'mercado_completo' WHERE `plano_negocio` = '' OR `plano_negocio` IS NULL;

-- Frases i18n para a UI
INSERT INTO `language` (`phrase`, `english`, `portugues`) VALUES
    ('plano_negocio', 'Business Plan', 'Plano de Negócio'),
    ('plano_negocio_descricao', 'Select which plan best fits your store. This controls which modules are available.', 'Selecione o plano que melhor se adapta à sua loja. Isso controla quais módulos estão disponíveis.'),
    ('modulo_nao_disponivel', 'This module is not available in your current plan.', 'Este módulo não está disponível no seu plano atual.')
ON DUPLICATE KEY UPDATE `phrase` = VALUES(`phrase`);

SELECT 'Plano de negocio migration complete' AS 'Status';
