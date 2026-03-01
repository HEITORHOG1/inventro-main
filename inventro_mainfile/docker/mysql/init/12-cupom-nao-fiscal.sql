-- =============================================
-- INVENTRO - Cupom Não-Fiscal (impressão térmica)
-- Adiciona CNPJ à loja e traduções necessárias
-- =============================================

-- Coluna CNPJ na tabela setting (safe add)
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS safe_add_col_12(
    IN tbl VARCHAR(64), IN col VARCHAR(64), IN col_def VARCHAR(255)
)
BEGIN
    DECLARE cnt INT DEFAULT 0;
    SELECT COUNT(*) INTO cnt FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = tbl AND column_name = col;
    IF cnt = 0 THEN
        SET @q = CONCAT('ALTER TABLE `', tbl, '` ADD COLUMN `', col, '` ', col_def);
        PREPARE stmt FROM @q;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL safe_add_col_12('setting', 'cnpj', 'VARCHAR(20) DEFAULT NULL AFTER `phone`');
DROP PROCEDURE IF EXISTS `safe_add_col_12`;

-- Traduções
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS safe_insert_lang_12(IN p_phrase VARCHAR(255), IN p_english TEXT, IN p_portugues TEXT)
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

CALL safe_insert_lang_12('cnpj', 'CNPJ', 'CNPJ');
CALL safe_insert_lang_12('cnpj_placeholder', 'XX.XXX.XXX/XXXX-XX', 'XX.XXX.XXX/XXXX-XX');

DROP PROCEDURE IF EXISTS `safe_insert_lang_12`;
