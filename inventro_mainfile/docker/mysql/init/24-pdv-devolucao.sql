-- ============================================================
-- Fase 9: Devolução / Troca — schema additions
-- ============================================================

-- Add invoice_detail_id and motivo columns to return_details
ALTER TABLE `return_details`
    ADD COLUMN `invoice_detail_id` INT UNSIGNED NULL DEFAULT NULL AFTER `return_id`,
    ADD COLUMN `motivo` VARCHAR(30) NULL DEFAULT NULL AFTER `amount`;

-- Index for faster lookups when checking already-returned quantities
ALTER TABLE `return_details`
    ADD INDEX `idx_return_details_invoice_detail` (`invoice_detail_id`);
