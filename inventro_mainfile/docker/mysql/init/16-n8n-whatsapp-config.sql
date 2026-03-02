-- =============================================
-- 16: Configuracoes n8n + WhatsApp Business API
-- =============================================
-- Todas as configs ficam na cardapio_config (key-value)
-- Configuraveis via tela admin em Delivery > WhatsApp & Automacao
-- INSERT IGNORE para ser idempotente (pode rodar multiplas vezes)

-- Database separado para o servico n8n (usado se profile n8n ativado)
CREATE DATABASE IF NOT EXISTS inventro_n8n
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON inventro_n8n.* TO 'inventro_user'@'%';
FLUSH PRIVILEGES;

USE inventro_db;

-- === AUTOMACAO n8n ===
INSERT IGNORE INTO cardapio_config (chave, valor, descricao) VALUES
('n8n_ativo',           '0', 'Ativar integracao n8n para notificacoes automaticas'),
('n8n_webhook_url',     'http://n8n:5678/webhook/inventro', 'URL interna do webhook n8n'),
('n8n_webhook_secret',  '', 'Chave secreta para autenticar webhooks'),
('n8n_status',          'nao_configurado', 'Status da conexao: nao_configurado|conectado|erro');

-- === WHATSAPP BUSINESS API ===
INSERT IGNORE INTO cardapio_config (chave, valor, descricao) VALUES
('whatsapp_api_ativa',        '0', 'Ativar envio automatico via WhatsApp Business API'),
('whatsapp_api_phone_id',     '', 'Phone Number ID (painel Meta Developers)'),
('whatsapp_api_token',        '', 'Token de acesso permanente (System User Token)'),
('whatsapp_api_business_id',  '', 'WhatsApp Business Account ID (WABA ID)'),
('whatsapp_api_status',       'nao_configurado', 'Status: nao_configurado|conectado|erro'),
('whatsapp_api_ultimo_teste', '', 'Data/hora do ultimo teste bem sucedido');

-- === SETUP WIZARD ===
INSERT IGNORE INTO cardapio_config (chave, valor, descricao) VALUES
('setup_whatsapp_passo', '0', 'Passo atual do assistente de configuracao (0=nao iniciou, 5=concluido)');

-- === NOTIFICACOES: quais eventos disparam mensagem ===
INSERT IGNORE INTO cardapio_config (chave, valor, descricao) VALUES
('notif_pedido_criado_cliente', '1', 'Notificar cliente quando pedido e criado'),
('notif_pedido_criado_motoboy', '1', 'Notificar motoboys quando novo pedido chega'),
('notif_status_confirmado',     '1', 'Notificar cliente quando pedido confirmado'),
('notif_status_preparando',     '1', 'Notificar cliente quando pedido em preparo'),
('notif_status_pronto',         '1', 'Notificar cliente quando pedido pronto p/ coleta'),
('notif_status_saiu_entrega',   '1', 'Notificar cliente quando motoboy saiu'),
('notif_status_entregue',       '1', 'Notificar cliente quando pedido entregue'),
('notif_status_cancelado',      '1', 'Notificar cliente quando pedido cancelado'),
('notif_cupom_fiscal',          '1', 'Enviar cupom fiscal (PDF) via WhatsApp'),
('notif_motoboy_atribuido',     '1', 'Notificar motoboy quando atribuido a pedido'),
('notif_resumo_diario',         '0', 'Enviar resumo diario para admin'),
('notif_resumo_horario',        '23:00', 'Horario do envio do resumo diario'),
('notif_admin_telefone',        '', 'Telefone do admin para receber resumo e alertas');

-- === NOMES DOS TEMPLATES DO WHATSAPP (cadastrados pelo admin no Meta Business) ===
INSERT IGNORE INTO cardapio_config (chave, valor, descricao) VALUES
('wpp_template_pedido_criado',     'pedido_confirmado',          'Nome do template Meta: pedido recebido'),
('wpp_template_pedido_confirmado', 'pedido_confirmado',          'Nome do template Meta: pedido confirmado'),
('wpp_template_pedido_preparando', 'pedido_preparando',          'Nome do template Meta: em preparo'),
('wpp_template_pedido_pronto',     'pedido_pronto',              'Nome do template Meta: pronto p/ coleta'),
('wpp_template_pedido_saiu',       'pedido_saiu_entrega',        'Nome do template Meta: saiu entrega'),
('wpp_template_pedido_entregue',   'pedido_entregue',            'Nome do template Meta: entregue'),
('wpp_template_pedido_cancelado',  'pedido_cancelado',           'Nome do template Meta: cancelado'),
('wpp_template_motoboy_novo',      'motoboy_novo_pedido',        'Nome do template Meta: novo pedido p/ motoboy'),
('wpp_template_motoboy_atribuido', 'motoboy_entrega_atribuida',  'Nome do template Meta: entrega atribuida'),
('wpp_template_cupom_fiscal',      'cupom_fiscal',               'Nome do template Meta: envio cupom fiscal');

-- === TRADUCAO PARA O MENU ===
INSERT IGNORE INTO language (phrase, english, portugues)
VALUES ('whatsapp_automacao', 'WhatsApp & Automation', 'WhatsApp & Automacao');
