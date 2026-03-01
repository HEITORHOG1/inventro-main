-- =============================================
-- INVENTRO - Traduções e ajustes do módulo Delivery
-- =============================================

DELIMITER //
CREATE PROCEDURE IF NOT EXISTS safe_insert_lang_11(IN p_phrase VARCHAR(255), IN p_english TEXT, IN p_portugues TEXT)
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

-- Labels do sidebar
CALL safe_insert_lang_11('pedidos_online', 'Online Orders', 'Pedidos Online');
CALL safe_insert_lang_11('kanban_pedidos', 'Orders Kanban', 'Painel Kanban');
CALL safe_insert_lang_11('zonas_entrega', 'Delivery Zones', 'Zonas de Entrega');
CALL safe_insert_lang_11('config_delivery', 'Delivery Settings', 'Configurações Delivery');

-- Labels gerais usados em formulários delivery
CALL safe_insert_lang_11('nome', 'Name', 'Nome');
CALL safe_insert_lang_11('telefone', 'Phone', 'Telefone');
CALL safe_insert_lang_11('select', 'Select', 'Selecione');
CALL safe_insert_lang_11('back', 'Back', 'Voltar');
CALL safe_insert_lang_11('edit', 'Edit', 'Editar');
CALL safe_insert_lang_11('nome_placeholder', 'Full name', 'Nome completo');
CALL safe_insert_lang_11('entregador', 'Driver', 'Entregador');
CALL safe_insert_lang_11('detalhes_pedido', 'Order Details', 'Detalhes do Pedido');
CALL safe_insert_lang_11('entregador_em_entrega', 'Driver is on delivery', 'Entregador em entrega no momento');

-- Labels de veículos
CALL safe_insert_lang_11('veiculo_moto', 'Motorcycle', 'Moto');
CALL safe_insert_lang_11('veiculo_bicicleta', 'Bicycle', 'Bicicleta');
CALL safe_insert_lang_11('veiculo_carro', 'Car', 'Carro');
CALL safe_insert_lang_11('veiculo_a_pe', 'On foot', 'A Pé');

DROP PROCEDURE IF EXISTS `safe_insert_lang_11`;
