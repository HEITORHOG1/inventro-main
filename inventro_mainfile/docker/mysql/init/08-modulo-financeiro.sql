-- ============================================
-- MÓDULO FINANCEIRO - CONTAS A PAGAR E RECEBER
-- Arquivo: 08-modulo-financeiro.sql
-- Data: 2025-12-27
-- Descrição: Cria tabelas para gestão financeira brasileira
-- ============================================

SET sql_mode = '';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

USE inventro_db;

SELECT '========================================' AS '';
SELECT '💰 Criando módulo financeiro...' AS 'Status';
SELECT '========================================' AS '';

-- --------------------------------------------------------
-- 1. TABELA DE CATEGORIAS FINANCEIRAS
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `categorias_financeiras` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `tipo` ENUM('despesa','receita','ambos') DEFAULT 'ambos',
  `cor` VARCHAR(7) DEFAULT '#007bff',
  `icone` VARCHAR(50) DEFAULT 'fa-money-bill',
  `status` INT(2) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir categorias padrão
INSERT INTO `categorias_financeiras` (`nome`, `tipo`, `cor`, `icone`) VALUES
('Fornecedores', 'despesa', '#dc3545', 'fa-truck'),
('Aluguel', 'despesa', '#fd7e14', 'fa-home'),
('Energia Elétrica', 'despesa', '#ffc107', 'fa-bolt'),
('Água', 'despesa', '#17a2b8', 'fa-tint'),
('Telefone/Internet', 'despesa', '#6610f2', 'fa-phone'),
('Salários', 'despesa', '#e83e8c', 'fa-users'),
('Impostos', 'despesa', '#6c757d', 'fa-file-invoice'),
('Frete', 'despesa', '#20c997', 'fa-shipping-fast'),
('Manutenção', 'despesa', '#795548', 'fa-wrench'),
('Vendas de Produtos', 'receita', '#28a745', 'fa-shopping-cart'),
('Serviços', 'receita', '#007bff', 'fa-concierge-bell'),
('Outras Despesas', 'despesa', '#adb5bd', 'fa-ellipsis-h'),
('Outras Receitas', 'receita', '#adb5bd', 'fa-ellipsis-h')
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);

-- --------------------------------------------------------
-- 2. TABELA CONTAS A PAGAR
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `contas_pagar` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(20) NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `tipo` ENUM('compra','despesa','outro') DEFAULT 'despesa',
  `categoria_id` INT(11) DEFAULT NULL,
  `fornecedor_id` VARCHAR(20) DEFAULT NULL,
  `purchase_id` VARCHAR(100) DEFAULT NULL,
  `valor_original` DECIMAL(10,2) NOT NULL,
  `valor_pago` DECIMAL(10,2) DEFAULT 0.00,
  `data_emissao` DATE NOT NULL,
  `data_vencimento` DATE NOT NULL,
  `data_pagamento` DATE DEFAULT NULL,
  `forma_pagamento` ENUM('dinheiro','pix','cartao_debito','cartao_credito','boleto','transferencia','cheque') DEFAULT NULL,
  `banco_id` VARCHAR(50) DEFAULT NULL,
  `parcela_atual` INT(11) DEFAULT 1,
  `total_parcelas` INT(11) DEFAULT 1,
  `status` ENUM('aberto','parcial','pago','cancelado','vencido') DEFAULT 'aberto',
  `observacao` TEXT,
  `created_by` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_codigo` (`codigo`),
  INDEX `idx_vencimento` (`data_vencimento`),
  INDEX `idx_fornecedor` (`fornecedor_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_categoria` (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. TABELA CONTAS A RECEBER
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `contas_receber` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(20) NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `tipo` ENUM('venda','servico','fiado','outro') DEFAULT 'venda',
  `categoria_id` INT(11) DEFAULT NULL,
  `cliente_id` VARCHAR(20) DEFAULT NULL,
  `invoice_id` VARCHAR(20) DEFAULT NULL,
  `valor_original` DECIMAL(10,2) NOT NULL,
  `valor_recebido` DECIMAL(10,2) DEFAULT 0.00,
  `data_emissao` DATE NOT NULL,
  `data_vencimento` DATE NOT NULL,
  `data_recebimento` DATE DEFAULT NULL,
  `forma_pagamento` ENUM('dinheiro','pix','cartao_debito','cartao_credito','boleto','transferencia','cheque','fiado') DEFAULT NULL,
  `banco_id` VARCHAR(50) DEFAULT NULL,
  `parcela_atual` INT(11) DEFAULT 1,
  `total_parcelas` INT(11) DEFAULT 1,
  `status` ENUM('aberto','parcial','recebido','cancelado','vencido') DEFAULT 'aberto',
  `observacao` TEXT,
  `created_by` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_codigo` (`codigo`),
  INDEX `idx_vencimento` (`data_vencimento`),
  INDEX `idx_cliente` (`cliente_id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_categoria` (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. TABELA DE BAIXAS FINANCEIRAS (HISTÓRICO DE PAGAMENTOS/RECEBIMENTOS)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `baixas_financeiras` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('pagar','receber') NOT NULL,
  `conta_id` INT(11) NOT NULL,
  `valor` DECIMAL(10,2) NOT NULL,
  `data_baixa` DATE NOT NULL,
  `forma_pagamento` ENUM('dinheiro','pix','cartao_debito','cartao_credito','boleto','transferencia','cheque') NOT NULL,
  `banco_id` VARCHAR(50) DEFAULT NULL,
  `observacao` TEXT,
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_tipo_conta` (`tipo`, `conta_id`),
  INDEX `idx_data` (`data_baixa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. ADICIONAR TRADUÇÕES PARA O MÓDULO FINANCEIRO
-- --------------------------------------------------------

INSERT INTO `language` (`phrase`, `english`, `portugues`) VALUES
-- Menu e títulos
('financeiro', 'Financial', 'Financeiro'),
('contas_a_pagar', 'Accounts Payable', 'Contas a Pagar'),
('contas_a_receber', 'Accounts Receivable', 'Contas a Receber'),
('nova_conta_pagar', 'New Payable', 'Nova Conta a Pagar'),
('nova_conta_receber', 'New Receivable', 'Nova Conta a Receber'),
('dashboard_financeiro', 'Financial Dashboard', 'Dashboard Financeiro'),

-- Campos
('codigo', 'Code', 'Código'),
('valor_original', 'Original Value', 'Valor Original'),
('valor_pago', 'Paid Value', 'Valor Pago'),
('valor_recebido', 'Received Value', 'Valor Recebido'),
('valor_pendente', 'Pending Value', 'Valor Pendente'),
('data_emissao', 'Issue Date', 'Data de Emissão'),
('data_vencimento', 'Due Date', 'Data de Vencimento'),
('data_pagamento', 'Payment Date', 'Data do Pagamento'),
('data_recebimento', 'Receipt Date', 'Data do Recebimento'),
('forma_pagamento', 'Payment Method', 'Forma de Pagamento'),
('parcela', 'Installment', 'Parcela'),
('parcela_atual', 'Current Installment', 'Parcela Atual'),
('total_parcelas', 'Total Installments', 'Total de Parcelas'),
('observacao', 'Notes', 'Observação'),
('categoria', 'Category', 'Categoria'),

-- Status
('status_aberto', 'Open', 'Aberto'),
('status_parcial', 'Partial', 'Parcial'),
('status_pago', 'Paid', 'Pago'),
('status_recebido', 'Received', 'Recebido'),
('status_cancelado', 'Cancelled', 'Cancelado'),
('status_vencido', 'Overdue', 'Vencido'),

-- Tipos
('tipo_compra', 'Purchase', 'Compra'),
('tipo_despesa', 'Expense', 'Despesa'),
('tipo_venda', 'Sale', 'Venda'),
('tipo_servico', 'Service', 'Serviço'),
('tipo_fiado', 'Credit', 'Fiado'),
('tipo_outro', 'Other', 'Outro'),

-- Formas de pagamento
('dinheiro', 'Cash', 'Dinheiro'),
('pix', 'PIX', 'PIX'),
('cartao_debito', 'Debit Card', 'Cartão de Débito'),
('cartao_credito', 'Credit Card', 'Cartão de Crédito'),
('boleto', 'Bank Slip', 'Boleto'),
('transferencia', 'Transfer', 'Transferência'),
('cheque', 'Check', 'Cheque'),

-- Ações
('dar_baixa', 'Mark as Paid', 'Dar Baixa'),
('baixa_pagamento', 'Payment', 'Baixa de Pagamento'),
('baixa_recebimento', 'Receipt', 'Baixa de Recebimento'),
('registrar_baixa', 'Register Payment', 'Registrar Baixa'),
('historico_baixas', 'Payment History', 'Histórico de Baixas'),

-- Dashboard
('total_a_pagar', 'Total Payable', 'Total a Pagar'),
('total_a_receber', 'Total Receivable', 'Total a Receber'),
('vencidos', 'Overdue', 'Vencidos'),
('vence_hoje', 'Due Today', 'Vence Hoje'),
('vence_semana', 'Due This Week', 'Vence Esta Semana'),
('vence_mes', 'Due This Month', 'Vence Este Mês'),
('proximos_vencimentos', 'Upcoming Due Dates', 'Próximos Vencimentos'),
('fluxo_caixa', 'Cash Flow', 'Fluxo de Caixa'),

-- Filtros
('filtrar_por_status', 'Filter by Status', 'Filtrar por Status'),
('filtrar_por_vencimento', 'Filter by Due Date', 'Filtrar por Vencimento'),
('todos_status', 'All Status', 'Todos os Status'),
('apenas_abertos', 'Only Open', 'Apenas Abertos'),
('apenas_vencidos', 'Only Overdue', 'Apenas Vencidos'),

-- Mensagens
('conta_pagar_salva', 'Payable account saved successfully', 'Conta a pagar salva com sucesso'),
('conta_receber_salva', 'Receivable account saved successfully', 'Conta a receber salva com sucesso'),
('baixa_registrada', 'Payment registered successfully', 'Baixa registrada com sucesso'),
('conta_cancelada', 'Account cancelled successfully', 'Conta cancelada com sucesso'),
('valor_maior_pendente', 'Value cannot be greater than pending amount', 'Valor não pode ser maior que o pendente'),
('selecione_forma_pagamento', 'Select payment method', 'Selecione a forma de pagamento')

ON DUPLICATE KEY UPDATE `portugues` = VALUES(`portugues`);

-- --------------------------------------------------------
-- 6. CRIAR VIEWS PARA CONSULTAS OTIMIZADAS
-- --------------------------------------------------------

-- View de Contas a Pagar com dados do fornecedor
CREATE OR REPLACE VIEW `vw_contas_pagar` AS
SELECT 
    cp.*,
    (cp.valor_original - cp.valor_pago) AS valor_pendente,
    s.name AS fornecedor_nome,
    s.mobile AS fornecedor_telefone,
    cf.nome AS categoria_nome,
    cf.cor AS categoria_cor,
    CASE 
        WHEN cp.status = 'pago' THEN 'pago'
        WHEN cp.data_vencimento < CURDATE() AND cp.status != 'pago' THEN 'vencido'
        WHEN cp.data_vencimento = CURDATE() THEN 'hoje'
        WHEN cp.data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 'semana'
        ELSE 'normal'
    END AS situacao
FROM contas_pagar cp
LEFT JOIN supplier_tbl s ON s.supplier_id = cp.fornecedor_id
LEFT JOIN categorias_financeiras cf ON cf.id = cp.categoria_id;

-- View de Contas a Receber com dados do cliente
CREATE OR REPLACE VIEW `vw_contas_receber` AS
SELECT 
    cr.*,
    (cr.valor_original - cr.valor_recebido) AS valor_pendente,
    c.name AS cliente_nome,
    c.mobile AS cliente_telefone,
    cf.nome AS categoria_nome,
    cf.cor AS categoria_cor,
    CASE 
        WHEN cr.status = 'recebido' THEN 'recebido'
        WHEN cr.data_vencimento < CURDATE() AND cr.status != 'recebido' THEN 'vencido'
        WHEN cr.data_vencimento = CURDATE() THEN 'hoje'
        WHEN cr.data_vencimento <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 'semana'
        ELSE 'normal'
    END AS situacao
FROM contas_receber cr
LEFT JOIN customer_tbl c ON c.customerid = cr.cliente_id
LEFT JOIN categorias_financeiras cf ON cf.id = cr.categoria_id;

-- View de resumo financeiro
CREATE OR REPLACE VIEW `vw_resumo_financeiro` AS
SELECT 
    'pagar' AS tipo,
    COUNT(*) AS total_contas,
    SUM(CASE WHEN status IN ('aberto', 'parcial') THEN valor_original - valor_pago ELSE 0 END) AS total_pendente,
    SUM(CASE WHEN status IN ('aberto', 'parcial') AND data_vencimento < CURDATE() THEN valor_original - valor_pago ELSE 0 END) AS total_vencido,
    SUM(CASE WHEN status IN ('aberto', 'parcial') AND data_vencimento = CURDATE() THEN valor_original - valor_pago ELSE 0 END) AS total_hoje,
    SUM(CASE WHEN status IN ('aberto', 'parcial') AND data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN valor_original - valor_pago ELSE 0 END) AS total_semana,
    SUM(CASE WHEN status IN ('aberto', 'parcial') AND MONTH(data_vencimento) = MONTH(CURDATE()) AND YEAR(data_vencimento) = YEAR(CURDATE()) THEN valor_original - valor_pago ELSE 0 END) AS total_mes
FROM contas_pagar
UNION ALL
SELECT 
    'receber' AS tipo,
    COUNT(*) AS total_contas,
    SUM(CASE WHEN status IN ('aberto', 'parcial') THEN valor_original - valor_recebido ELSE 0 END) AS total_pendente,
    SUM(CASE WHEN status IN ('aberto', 'parcial') AND data_vencimento < CURDATE() THEN valor_original - valor_recebido ELSE 0 END) AS total_vencido,
    SUM(CASE WHEN status IN ('aberto', 'parcial') AND data_vencimento = CURDATE() THEN valor_original - valor_recebido ELSE 0 END) AS total_hoje,
    SUM(CASE WHEN status IN ('aberto', 'parcial') AND data_vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN valor_original - valor_recebido ELSE 0 END) AS total_semana,
    SUM(CASE WHEN status IN ('aberto', 'parcial') AND MONTH(data_vencimento) = MONTH(CURDATE()) AND YEAR(data_vencimento) = YEAR(CURDATE()) THEN valor_original - valor_recebido ELSE 0 END) AS total_mes
FROM contas_receber;

SELECT '✅ Módulo financeiro criado com sucesso!' AS 'Status';
