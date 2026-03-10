-- =============================================
-- 25-pdv-product-fields.sql — Phase 11: Product & User form fields
-- =============================================
-- Adds missing indexes from Phase 11 requirements.
-- Columns were already created in 22-pdv.sql.
-- This migration only adds the unique index on codigo_balanca
-- that was not included in the original PDV migration.

-- Unique index on codigo_balanca (safe: check before creating)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_tbl' AND INDEX_NAME = 'idx_codigo_balanca');
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `product_tbl` ADD UNIQUE INDEX `idx_codigo_balanca` (`codigo_balanca`)',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Make ean_gtin unique (the index from 22-pdv is not unique)
-- Drop the non-unique index first if it exists, then recreate as unique
-- Using a procedure to handle the conditional drop safely
DELIMITER //
CREATE PROCEDURE _fix_ean_index()
BEGIN
    DECLARE idx_exists INT DEFAULT 0;
    DECLARE uniq_exists INT DEFAULT 0;

    -- Check if non-unique idx_ean_gtin exists
    SELECT COUNT(*) INTO idx_exists
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'product_tbl'
      AND INDEX_NAME = 'idx_ean_gtin'
      AND NON_UNIQUE = 1;

    -- Check if unique idx_ean_gtin_unique already exists
    SELECT COUNT(*) INTO uniq_exists
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'product_tbl'
      AND INDEX_NAME = 'idx_ean_gtin_unique';

    IF idx_exists > 0 AND uniq_exists = 0 THEN
        ALTER TABLE `product_tbl` DROP INDEX `idx_ean_gtin`;
        ALTER TABLE `product_tbl` ADD UNIQUE INDEX `idx_ean_gtin` (`ean_gtin`);
    ELSEIF idx_exists = 0 AND uniq_exists = 0 THEN
        -- No index at all, create unique
        ALTER TABLE `product_tbl` ADD UNIQUE INDEX `idx_ean_gtin` (`ean_gtin`);
    END IF;
END //
DELIMITER ;

CALL _fix_ean_index();
DROP PROCEDURE IF EXISTS _fix_ean_index;

SELECT 'Phase 11: PDV product fields indexes applied' AS 'Status';
