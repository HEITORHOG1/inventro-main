-- =============================================
-- 23-pdv-fiado.sql — PDV Fiado/Crediário — Fase 8
-- =============================================
-- Ajustes necessários para suportar fiado completo no PDV.
-- Tabelas pdv_fiado e pdv_fiado_pagamento já existem em 22-pdv.sql.
-- Este script adiciona campos e tipos faltantes.

-- 1. Adicionar 'recebimento_fiado' e 'fechamento' ao ENUM tipo em pdv_caixa_mov
ALTER TABLE `pdv_caixa_mov`
    MODIFY COLUMN `tipo` ENUM('suprimento','sangria','venda','cancelamento','troca_operador','devolucao','recebimento_fiado','fechamento') NOT NULL;

-- 2. Adicionar caixa_id na pdv_fiado_pagamento (para vincular ao caixa)
ALTER TABLE `pdv_fiado_pagamento`
    ADD COLUMN `caixa_id` INT NULL COMMENT 'FK -> pdv_caixa.id' AFTER `operador_id`;

-- 3. Adicionar saldo computado como coluna na pdv_fiado para facilitar queries
ALTER TABLE `pdv_fiado`
    ADD COLUMN `saldo` DECIMAL(10,2) GENERATED ALWAYS AS (`valor` - `valor_pago`) VIRTUAL AFTER `valor_pago`;

-- Fim
SELECT 'OK 23-pdv-fiado.sql applied' AS 'Status';
