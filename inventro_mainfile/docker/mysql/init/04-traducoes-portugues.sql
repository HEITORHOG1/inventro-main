-- ============================================
-- INVENTRO - Traduções para Português Brasileiro (PT-BR)
-- Arquivo: 04-traducoes-portugues.sql
-- Descrição: Todas as traduções para português
-- Execução: Automática via docker-entrypoint-initdb.d
-- ============================================

SET sql_mode = '';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

USE inventro_db;

SELECT '========================================' AS '';
SELECT '🇧🇷 Aplicando traduções PT-BR...' AS 'Status';
SELECT '========================================' AS '';

-- ============================================
-- Módulos e Menus Principais
-- ============================================

UPDATE `language` SET `portugues` = 'Painel' WHERE `phrase` = 'dashboard';
UPDATE `language` SET `portugues` = 'Cliente' WHERE `phrase` = 'customer';
UPDATE `language` SET `portugues` = 'Fornecedor' WHERE `phrase` = 'supplier';
UPDATE `language` SET `portugues` = 'Item' WHERE `phrase` = 'item';
UPDATE `language` SET `portugues` = 'Fatura' WHERE `phrase` = 'invoice';
UPDATE `language` SET `portugues` = 'Compra' WHERE `phrase` = 'purchase';
UPDATE `language` SET `portugues` = 'Devolução' WHERE `phrase` = 'return';
UPDATE `language` SET `portugues` = 'Relatório' WHERE `phrase` = 'report';
UPDATE `language` SET `portugues` = 'Estoque' WHERE `phrase` = 'stock';
UPDATE `language` SET `portugues` = 'Banco' WHERE `phrase` = 'bank';
UPDATE `language` SET `portugues` = 'RH' WHERE `phrase` = 'hrm';
UPDATE `language` SET `portugues` = 'Contas' WHERE `phrase` = 'accounts';
UPDATE `language` SET `portugues` = 'Menu' WHERE `phrase` = 'menu';

-- ============================================
-- Submenus e Links
-- ============================================

UPDATE `language` SET `portugues` = 'Lista de Clientes' WHERE `phrase` = 'customer_list';
UPDATE `language` SET `portugues` = 'Extrato do Cliente' WHERE `phrase` = 'customer_ledger';
UPDATE `language` SET `portugues` = 'Lista de Fornecedores' WHERE `phrase` = 'supplier_list';
UPDATE `language` SET `portugues` = 'Extrato do Fornecedor' WHERE `phrase` = 'supplier_ledger';
UPDATE `language` SET `portugues` = 'Unidade' WHERE `phrase` = 'unit';
UPDATE `language` SET `portugues` = 'Categoria' WHERE `phrase` = 'category';
UPDATE `language` SET `portugues` = 'Adicionar Item' WHERE `phrase` = 'add_item';
UPDATE `language` SET `portugues` = 'Lista de Itens' WHERE `phrase` = 'item_list';
UPDATE `language` SET `portugues` = 'Adicionar Fatura' WHERE `phrase` = 'add_invoice';
UPDATE `language` SET `portugues` = 'Adicionar Fatura PDV' WHERE `phrase` = 'add_pos_invoice';
UPDATE `language` SET `portugues` = 'Lista de Faturas' WHERE `phrase` = 'invoice_list';
UPDATE `language` SET `portugues` = 'Nova Compra' WHERE `phrase` = 'new_purchase';
UPDATE `language` SET `portugues` = 'Lista de Compras' WHERE `phrase` = 'purchase_list';
UPDATE `language` SET `portugues` = 'Devolução de Cliente' WHERE `phrase` = 'customer_return';
UPDATE `language` SET `portugues` = 'Lista de Devoluções de Cliente' WHERE `phrase` = 'customer_return_list';
UPDATE `language` SET `portugues` = 'Devolução ao Fornecedor' WHERE `phrase` = 'supplier_return';
UPDATE `language` SET `portugues` = 'Lista de Devoluções ao Fornecedor' WHERE `phrase` = 'supplier_return_list';
UPDATE `language` SET `portugues` = 'Relatório de Compras' WHERE `phrase` = 'purchase_report';
UPDATE `language` SET `portugues` = 'Relatório de Vendas' WHERE `phrase` = 'sales_report';
UPDATE `language` SET `portugues` = 'Livro Caixa' WHERE `phrase` = 'cash_book';
UPDATE `language` SET `portugues` = 'Livro Bancário' WHERE `phrase` = 'bank_book';
UPDATE `language` SET `portugues` = 'Relatório de Estoque' WHERE `phrase` = 'stock_report';
UPDATE `language` SET `portugues` = 'Relatório de Estoque por Fornecedor' WHERE `phrase` = 'stock_report_supplier_wise';
UPDATE `language` SET `portugues` = 'Relatório de Estoque por Produto' WHERE `phrase` = 'stock_report_product_wise';
UPDATE `language` SET `portugues` = 'Extrato Bancário' WHERE `phrase` = 'bank_ledger';
UPDATE `language` SET `portugues` = 'Ajuste Bancário' WHERE `phrase` = 'bank_adjustment';
UPDATE `language` SET `portugues` = 'Departamento' WHERE `phrase` = 'department';
UPDATE `language` SET `portugues` = 'Cargo' WHERE `phrase` = 'designation';
UPDATE `language` SET `portugues` = 'Salário' WHERE `phrase` = 'salary';
UPDATE `language` SET `portugues` = 'Configuração de Salário' WHERE `phrase` = 'salary_setup';
UPDATE `language` SET `portugues` = 'Lista de Salários Gerados' WHERE `phrase` = 'salary_generat_list';
UPDATE `language` SET `portugues` = 'Presença' WHERE `phrase` = 'attendance';
UPDATE `language` SET `portugues` = 'Relatório de Presença' WHERE `phrase` = 'attendance_report';
UPDATE `language` SET `portugues` = 'Funcionário' WHERE `phrase` = 'employee';
UPDATE `language` SET `portugues` = 'Adicionar Funcionário' WHERE `phrase` = 'add_employee';
UPDATE `language` SET `portugues` = 'Gerenciar Funcionários' WHERE `phrase` = 'manage_employee';
UPDATE `language` SET `portugues` = 'Pagamento ou Recebimento' WHERE `phrase` = 'payment_or_receive';
UPDATE `language` SET `portugues` = 'Gerenciar Transações' WHERE `phrase` = 'manage_transaction';
UPDATE `language` SET `portugues` = 'Ajuste de Conta' WHERE `phrase` = 'account_adjustment';
UPDATE `language` SET `portugues` = 'Fechamento de Caixa' WHERE `phrase` = 'cash_closing';
UPDATE `language` SET `portugues` = 'Lista de Fechamentos' WHERE `phrase` = 'closing_list';
UPDATE `language` SET `portugues` = 'Adicionar Função' WHERE `phrase` = 'add_role';
UPDATE `language` SET `portugues` = 'Lista de Funções' WHERE `phrase` = 'role_list';
UPDATE `language` SET `portugues` = 'Atribuir Função' WHERE `phrase` = 'role_assign';
UPDATE `language` SET `portugues` = 'Lista de Funções Atribuídas' WHERE `phrase` = 'assigned_userrole_list';

-- ============================================
-- Labels e Campos de Formulário
-- ============================================

UPDATE `language` SET `portugues` = 'Nome' WHERE `phrase` = 'name';
UPDATE `language` SET `portugues` = 'E-mail' WHERE `phrase` = 'email';
UPDATE `language` SET `portugues` = 'Celular' WHERE `phrase` = 'mobile';
UPDATE `language` SET `portugues` = 'Telefone' WHERE `phrase` = 'phone';
UPDATE `language` SET `portugues` = 'Endereço' WHERE `phrase` = 'address';
UPDATE `language` SET `portugues` = 'Status' WHERE `phrase` = 'status';
UPDATE `language` SET `portugues` = 'Ação' WHERE `phrase` = 'action';
UPDATE `language` SET `portugues` = 'Ativo' WHERE `phrase` = 'active';
UPDATE `language` SET `portugues` = 'Inativo' WHERE `phrase` = 'inactive';
UPDATE `language` SET `portugues` = 'Nº' WHERE `phrase` = 'sl_no';
UPDATE `language` SET `portugues` = 'Adicionar' WHERE `phrase` = 'add';
UPDATE `language` SET `portugues` = 'Editar' WHERE `phrase` = 'edit';
UPDATE `language` SET `portugues` = 'Atualizar' WHERE `phrase` = 'update';
UPDATE `language` SET `portugues` = 'Excluir' WHERE `phrase` = 'delete';
UPDATE `language` SET `portugues` = 'Salvar' WHERE `phrase` = 'save';
UPDATE `language` SET `portugues` = 'Cancelar' WHERE `phrase` = 'cancel';
UPDATE `language` SET `portugues` = 'Fechar' WHERE `phrase` = 'close';
UPDATE `language` SET `portugues` = 'Limpar' WHERE `phrase` = 'reset';
UPDATE `language` SET `portugues` = 'Pesquisar' WHERE `phrase` = 'search';
UPDATE `language` SET `portugues` = 'Enviar' WHERE `phrase` = 'submit';
UPDATE `language` SET `portugues` = 'Visualizar' WHERE `phrase` = 'view';
UPDATE `language` SET `portugues` = 'Imprimir' WHERE `phrase` = 'print';
UPDATE `language` SET `portugues` = 'Baixar' WHERE `phrase` = 'download';
UPDATE `language` SET `portugues` = 'Enviar' WHERE `phrase` = 'upload';
UPDATE `language` SET `portugues` = 'Data' WHERE `phrase` = 'date';
UPDATE `language` SET `portugues` = 'Hora' WHERE `phrase` = 'time';
UPDATE `language` SET `portugues` = 'Descrição' WHERE `phrase` = 'description';
UPDATE `language` SET `portugues` = 'Comentário' WHERE `phrase` = 'comment';
UPDATE `language` SET `portugues` = 'Observação' WHERE `phrase` = 'note';
UPDATE `language` SET `portugues` = 'Selecionar' WHERE `phrase` = 'select';
UPDATE `language` SET `portugues` = 'Selecione...' WHERE `phrase` = 'select_one';
UPDATE `language` SET `portugues` = 'Sim' WHERE `phrase` = 'yes';
UPDATE `language` SET `portugues` = 'Não' WHERE `phrase` = 'no';
UPDATE `language` SET `portugues` = 'Todos' WHERE `phrase` = 'all';
UPDATE `language` SET `portugues` = 'Nenhum' WHERE `phrase` = 'none';

-- ============================================
-- Fornecedores
-- ============================================

UPDATE `language` SET `portugues` = 'Adicionar Fornecedor' WHERE `phrase` = 'supplier_add';
UPDATE `language` SET `portugues` = 'Editar Fornecedor' WHERE `phrase` = 'supplier_edit';
UPDATE `language` SET `portugues` = 'Nome do Fornecedor' WHERE `phrase` = 'supplier_name';
UPDATE `language` SET `portugues` = 'Importar CSV' WHERE `phrase` = 'import_csv';
UPDATE `language` SET `portugues` = 'Baixar Arquivo de Exemplo' WHERE `phrase` = 'download_sample_file';
UPDATE `language` SET `portugues` = 'Enviar Arquivo CSV' WHERE `phrase` = 'upload_csv_file';
UPDATE `language` SET `portugues` = 'Saldo Anterior' WHERE `phrase` = 'previous_balance';
UPDATE `language` SET `portugues` = 'Tipo de Recebimento' WHERE `phrase` = 'isreceipt';

-- ============================================
-- Clientes
-- ============================================

UPDATE `language` SET `portugues` = 'Adicionar Cliente' WHERE `phrase` = 'customer_add';
UPDATE `language` SET `portugues` = 'Editar Cliente' WHERE `phrase` = 'customer_edit';
UPDATE `language` SET `portugues` = 'Nome do Cliente' WHERE `phrase` = 'customer_name';
UPDATE `language` SET `portugues` = 'Total de Clientes' WHERE `phrase` = 'total_customer';

-- ============================================
-- Itens/Produtos
-- ============================================

UPDATE `language` SET `portugues` = 'Nome do Item' WHERE `phrase` = 'item_name';
UPDATE `language` SET `portugues` = 'Código do Item' WHERE `phrase` = 'item_code';
UPDATE `language` SET `portugues` = 'Preço do Item' WHERE `phrase` = 'item_price';
UPDATE `language` SET `portugues` = 'Preço de Venda' WHERE `phrase` = 'selling_price';
UPDATE `language` SET `portugues` = 'Preço de Venda' WHERE `phrase` = 'sale_price';
UPDATE `language` SET `portugues` = 'Preço de Compra' WHERE `phrase` = 'purchase_price';
UPDATE `language` SET `portugues` = 'Quantidade' WHERE `phrase` = 'quantity';
UPDATE `language` SET `portugues` = 'Qtd' WHERE `phrase` = 'qty';
UPDATE `language` SET `portugues` = 'Estoque' WHERE `phrase` = 'stock_qty';
UPDATE `language` SET `portugues` = 'Adicionar Categoria' WHERE `phrase` = 'add_category';
UPDATE `language` SET `portugues` = 'Nome da Categoria' WHERE `phrase` = 'category_name';
UPDATE `language` SET `portugues` = 'Adicionar Unidade' WHERE `phrase` = 'add_unit';
UPDATE `language` SET `portugues` = 'Nome da Unidade' WHERE `phrase` = 'unit_name';
UPDATE `language` SET `portugues` = 'Total de Itens' WHERE `phrase` = 'total_item';
UPDATE `language` SET `portugues` = 'Modelo do Item' WHERE `phrase` = 'item_model';

-- ============================================
-- Faturas e Vendas
-- ============================================

UPDATE `language` SET `portugues` = 'Número da Fatura' WHERE `phrase` = 'invoice_number';
UPDATE `language` SET `portugues` = 'Data da Fatura' WHERE `phrase` = 'invoice_date';
UPDATE `language` SET `portugues` = 'Total da Fatura' WHERE `phrase` = 'invoice_total';
UPDATE `language` SET `portugues` = 'Total de Faturas' WHERE `phrase` = 'total_invoice';
UPDATE `language` SET `portugues` = 'Subtotal' WHERE `phrase` = 'subtotal';
UPDATE `language` SET `portugues` = 'Desconto' WHERE `phrase` = 'discount';
UPDATE `language` SET `portugues` = 'Imposto' WHERE `phrase` = 'tax';
UPDATE `language` SET `portugues` = 'Total Geral' WHERE `phrase` = 'grand_total';
UPDATE `language` SET `portugues` = 'Valor Pago' WHERE `phrase` = 'paid_amount';
UPDATE `language` SET `portugues` = 'Valor Pendente' WHERE `phrase` = 'due_amount';
UPDATE `language` SET `portugues` = 'Método de Pagamento' WHERE `phrase` = 'payment_method';
UPDATE `language` SET `portugues` = 'Dinheiro' WHERE `phrase` = 'cash';
UPDATE `language` SET `portugues` = 'Cartão' WHERE `phrase` = 'card';
UPDATE `language` SET `portugues` = 'Cheque' WHERE `phrase` = 'cheque';
UPDATE `language` SET `portugues` = 'Transferência Bancária' WHERE `phrase` = 'bank_transfer';
UPDATE `language` SET `portugues` = 'ID da Fatura' WHERE `phrase` = 'invoice_id';
UPDATE `language` SET `portugues` = 'Detalhes da Fatura' WHERE `phrase` = 'invoice_details';
UPDATE `language` SET `portugues` = 'Desconto da Fatura' WHERE `phrase` = 'invoice_discount';

-- ============================================
-- Compras
-- ============================================

UPDATE `language` SET `portugues` = 'Número da Compra' WHERE `phrase` = 'purchase_number';
UPDATE `language` SET `portugues` = 'Data da Compra' WHERE `phrase` = 'purchase_date';
UPDATE `language` SET `portugues` = 'Total da Compra' WHERE `phrase` = 'purchase_total';
UPDATE `language` SET `portugues` = 'Total de Compras' WHERE `phrase` = 'total_purchase';
UPDATE `language` SET `portugues` = 'Criar Compra' WHERE `phrase` = 'create_purchase';
UPDATE `language` SET `portugues` = 'Editar Compra' WHERE `phrase` = 'edit_purchase';
UPDATE `language` SET `portugues` = 'ID da Compra' WHERE `phrase` = 'purchase_id';

-- ============================================
-- Dashboard e Relatórios
-- ============================================

UPDATE `language` SET `portugues` = 'Relatório de Compras e Vendas' WHERE `phrase` = 'purchase_and_sales_report';
UPDATE `language` SET `portugues` = 'Mais Informações' WHERE `phrase` = 'more_info';
UPDATE `language` SET `portugues` = 'Total' WHERE `phrase` = 'total';
UPDATE `language` SET `portugues` = 'Hoje' WHERE `phrase` = 'today';
UPDATE `language` SET `portugues` = 'Esta Semana' WHERE `phrase` = 'this_week';
UPDATE `language` SET `portugues` = 'Este Mês' WHERE `phrase` = 'this_month';
UPDATE `language` SET `portugues` = 'Este Ano' WHERE `phrase` = 'this_year';
UPDATE `language` SET `portugues` = 'Período' WHERE `phrase` = 'date_range';
UPDATE `language` SET `portugues` = 'De' WHERE `phrase` = 'from';
UPDATE `language` SET `portugues` = 'Até' WHERE `phrase` = 'to';
UPDATE `language` SET `portugues` = 'Filtrar' WHERE `phrase` = 'filter';
UPDATE `language` SET `portugues` = 'Data Inicial' WHERE `phrase` = 'from_date';
UPDATE `language` SET `portugues` = 'Data Final' WHERE `phrase` = 'to_date';
UPDATE `language` SET `portugues` = 'Buscar' WHERE `phrase` = 'find';

-- ============================================
-- Contas e Finanças
-- ============================================

UPDATE `language` SET `portugues` = 'Valor' WHERE `phrase` = 'amount';
UPDATE `language` SET `portugues` = 'Valor Recebido' WHERE `phrase` = 'received_amount';
UPDATE `language` SET `portugues` = 'Valor do Pagamento' WHERE `phrase` = 'payment_amount';
UPDATE `language` SET `portugues` = 'Saldo' WHERE `phrase` = 'balance';
UPDATE `language` SET `portugues` = 'Crédito' WHERE `phrase` = 'credit';
UPDATE `language` SET `portugues` = 'Débito' WHERE `phrase` = 'debit';
UPDATE `language` SET `portugues` = 'Transação' WHERE `phrase` = 'transaction';
UPDATE `language` SET `portugues` = 'Tipo de Transação' WHERE `phrase` = 'transaction_type';
UPDATE `language` SET `portugues` = 'Referência' WHERE `phrase` = 'reference';
UPDATE `language` SET `portugues` = 'Lucro' WHERE `phrase` = 'profit';
UPDATE `language` SET `portugues` = 'Prejuízo' WHERE `phrase` = 'loss';
UPDATE `language` SET `portugues` = 'Pago' WHERE `phrase` = 'paid';
UPDATE `language` SET `portugues` = 'Pendente' WHERE `phrase` = 'due';
UPDATE `language` SET `portugues` = 'Recebido' WHERE `phrase` = 'received';
UPDATE `language` SET `portugues` = 'Pagamento' WHERE `phrase` = 'payment';
UPDATE `language` SET `portugues` = 'Receber' WHERE `phrase` = 'receive';
UPDATE `language` SET `portugues` = 'Tipo de Pagamento' WHERE `phrase` = 'payment_type';
UPDATE `language` SET `portugues` = 'Pagamento em Dinheiro' WHERE `phrase` = 'cash_payment';
UPDATE `language` SET `portugues` = 'Pagamento Bancário' WHERE `phrase` = 'bank_payment';
UPDATE `language` SET `portugues` = 'Pagamento Pendente' WHERE `phrase` = 'due_payment';
UPDATE `language` SET `portugues` = 'Data de Pagamento' WHERE `phrase` = 'payment_date';
UPDATE `language` SET `portugues` = 'Desconto Total' WHERE `phrase` = 'total_discount';
UPDATE `language` SET `portugues` = 'Valor Total' WHERE `phrase` = 'total_amount';
UPDATE `language` SET `portugues` = 'Preço Total' WHERE `phrase` = 'total_price';

-- ============================================
-- Banco
-- ============================================

UPDATE `language` SET `portugues` = 'Nome do Banco' WHERE `phrase` = 'bank_name';
UPDATE `language` SET `portugues` = 'Número da Conta' WHERE `phrase` = 'account_number';
UPDATE `language` SET `portugues` = 'Nº da Conta' WHERE `phrase` = 'account_no';
UPDATE `language` SET `portugues` = 'Nome da Conta' WHERE `phrase` = 'account_name';
UPDATE `language` SET `portugues` = 'Adicionar Banco' WHERE `phrase` = 'add_bank';
UPDATE `language` SET `portugues` = 'Editar Banco' WHERE `phrase` = 'edit_bank';
UPDATE `language` SET `portugues` = 'Lista de Bancos' WHERE `phrase` = 'bank_list';
UPDATE `language` SET `portugues` = 'Nome da Agência' WHERE `phrase` = 'branch_name';

-- ============================================
-- RH - Recursos Humanos
-- ============================================

UPDATE `language` SET `portugues` = 'Nome do Departamento' WHERE `phrase` = 'department_name';
UPDATE `language` SET `portugues` = 'Nome do Cargo' WHERE `phrase` = 'designation_name';
UPDATE `language` SET `portugues` = 'Nome do Funcionário' WHERE `phrase` = 'employee_name';
UPDATE `language` SET `portugues` = 'Data de Admissão' WHERE `phrase` = 'joining_date';
UPDATE `language` SET `portugues` = 'Salário Básico' WHERE `phrase` = 'basic_salary';
UPDATE `language` SET `portugues` = 'Presente' WHERE `phrase` = 'present';
UPDATE `language` SET `portugues` = 'Ausente' WHERE `phrase` = 'absent';
UPDATE `language` SET `portugues` = 'Licença' WHERE `phrase` = 'leave';
UPDATE `language` SET `portugues` = 'Folha de Pagamento' WHERE `phrase` = 'payroll';
UPDATE `language` SET `portugues` = 'Gerar Salário' WHERE `phrase` = 'generate_salary';
UPDATE `language` SET `portugues` = 'Gerar' WHERE `phrase` = 'generate';
UPDATE `language` SET `portugues` = 'Mês' WHERE `phrase` = 'month';
UPDATE `language` SET `portugues` = 'Horário de Entrada' WHERE `phrase` = 'in_time';
UPDATE `language` SET `portugues` = 'Horário de Saída' WHERE `phrase` = 'out_time';
UPDATE `language` SET `portugues` = 'Tempo de Permanência' WHERE `phrase` = 'stay_time';
UPDATE `language` SET `portugues` = 'País' WHERE `phrase` = 'country';
UPDATE `language` SET `portugues` = 'Cidade' WHERE `phrase` = 'city';
UPDATE `language` SET `portugues` = 'CEP' WHERE `phrase` = 'zip';

-- ============================================
-- Funções e Permissões
-- ============================================

UPDATE `language` SET `portugues` = 'Nome da Função' WHERE `phrase` = 'role_name';
UPDATE `language` SET `portugues` = 'Descrição da Função' WHERE `phrase` = 'role_description';
UPDATE `language` SET `portugues` = 'Usuário' WHERE `phrase` = 'user';
UPDATE `language` SET `portugues` = 'Selecionar Usuário' WHERE `phrase` = 'select_user';
UPDATE `language` SET `portugues` = 'Selecionar Função' WHERE `phrase` = 'select_role';
UPDATE `language` SET `portugues` = 'Permissão' WHERE `phrase` = 'permission';
UPDATE `language` SET `portugues` = 'Função Atribuída' WHERE `phrase` = 'assigned_role';
UPDATE `language` SET `portugues` = 'Pode Criar' WHERE `phrase` = 'can_create';
UPDATE `language` SET `portugues` = 'Pode Ler' WHERE `phrase` = 'can_read';
UPDATE `language` SET `portugues` = 'Pode Editar' WHERE `phrase` = 'can_edit';
UPDATE `language` SET `portugues` = 'Pode Excluir' WHERE `phrase` = 'can_delete';
UPDATE `language` SET `portugues` = 'Selecionar/Desselecionar' WHERE `phrase` = 'select_deselect';
UPDATE `language` SET `portugues` = 'Permissão de Função' WHERE `phrase` = 'menu';

-- ============================================
-- Configurações
-- ============================================

UPDATE `language` SET `portugues` = 'Configurações da Aplicação' WHERE `phrase` = 'application_setting';
UPDATE `language` SET `portugues` = 'Configurações do Sistema' WHERE `phrase` = 'system_settings';
UPDATE `language` SET `portugues` = 'Configuração' WHERE `phrase` = 'setting';
UPDATE `language` SET `portugues` = 'Idioma' WHERE `phrase` = 'language';
UPDATE `language` SET `portugues` = 'Moeda' WHERE `phrase` = 'currency';
UPDATE `language` SET `portugues` = 'Símbolo da Moeda' WHERE `phrase` = 'currency_symbol';
UPDATE `language` SET `portugues` = 'Logo' WHERE `phrase` = 'logo';
UPDATE `language` SET `portugues` = 'Favicon' WHERE `phrase` = 'favicon';
UPDATE `language` SET `portugues` = 'Título' WHERE `phrase` = 'title';
UPDATE `language` SET `portugues` = 'Rodapé' WHERE `phrase` = 'footer';
UPDATE `language` SET `portugues` = 'Texto do Rodapé' WHERE `phrase` = 'footer_text';
UPDATE `language` SET `portugues` = 'Título da Aplicação' WHERE `phrase` = 'application_title';
UPDATE `language` SET `portugues` = 'Alinhamento do Site' WHERE `phrase` = 'site_align';
UPDATE `language` SET `portugues` = 'Esquerda para Direita' WHERE `phrase` = 'left_to_right';
UPDATE `language` SET `portugues` = 'Direita para Esquerda' WHERE `phrase` = 'right_to_left';

-- ============================================
-- Usuários e Login
-- ============================================

UPDATE `language` SET `portugues` = 'Adicionar Usuário' WHERE `phrase` = 'add_user';
UPDATE `language` SET `portugues` = 'Editar Usuário' WHERE `phrase` = 'edit_user';
UPDATE `language` SET `portugues` = 'Lista de Usuários' WHERE `phrase` = 'user_list';
UPDATE `language` SET `portugues` = 'Nome Completo' WHERE `phrase` = 'fullname';
UPDATE `language` SET `portugues` = 'Nome de Usuário' WHERE `phrase` = 'username';
UPDATE `language` SET `portugues` = 'Senha' WHERE `phrase` = 'password';
UPDATE `language` SET `portugues` = 'Confirmar Senha' WHERE `phrase` = 'confirm_password';
UPDATE `language` SET `portugues` = 'Nova Senha' WHERE `phrase` = 'new_password';
UPDATE `language` SET `portugues` = 'Entrar' WHERE `phrase` = 'login';
UPDATE `language` SET `portugues` = 'Sair' WHERE `phrase` = 'logout';
UPDATE `language` SET `portugues` = 'Bem-vindo!' WHERE `phrase` = 'welcome_back';
UPDATE `language` SET `portugues` = 'E-mail ou Senha Incorretos' WHERE `phrase` = 'incorrect_email_or_password';
UPDATE `language` SET `portugues` = 'Por favor, contate o administrador' WHERE `phrase` = 'please_contact_with_admin';
UPDATE `language` SET `portugues` = 'Administrador' WHERE `phrase` = 'admin';
UPDATE `language` SET `portugues` = 'Perfil' WHERE `phrase` = 'profile';
UPDATE `language` SET `portugues` = 'Configuração de Perfil' WHERE `phrase` = 'profile_setting';
UPDATE `language` SET `portugues` = 'Primeiro Nome' WHERE `phrase` = 'firstname';
UPDATE `language` SET `portugues` = 'Sobrenome' WHERE `phrase` = 'lastname';
UPDATE `language` SET `portugues` = 'Sobre' WHERE `phrase` = 'about';
UPDATE `language` SET `portugues` = 'Sobre Mim' WHERE `phrase` = 'about_me';
UPDATE `language` SET `portugues` = 'Último Login' WHERE `phrase` = 'last_login';
UPDATE `language` SET `portugues` = 'Último Logout' WHERE `phrase` = 'last_logout';
UPDATE `language` SET `portugues` = 'Endereço IP' WHERE `phrase` = 'ip_address';

-- ============================================
-- Idiomas
-- ============================================

UPDATE `language` SET `portugues` = 'Lista de Idiomas' WHERE `phrase` = 'language_list';
UPDATE `language` SET `portugues` = 'Editar Frase' WHERE `phrase` = 'edit_phrase';
UPDATE `language` SET `portugues` = 'Adicionar Nova Frase' WHERE `phrase` = 'add_new_phrase';
UPDATE `language` SET `portugues` = 'Adicionar Frase' WHERE `phrase` = 'add_phrase';
UPDATE `language` SET `portugues` = 'Nome do Idioma' WHERE `phrase` = 'add_language_name';
UPDATE `language` SET `portugues` = 'Adicionar Idioma' WHERE `phrase` = 'add_language';
UPDATE `language` SET `portugues` = 'Frase' WHERE `phrase` = 'phrase';
UPDATE `language` SET `portugues` = 'Nome da Frase' WHERE `phrase` = 'phrase_name';

-- ============================================
-- Mensagens e Alertas
-- ============================================

UPDATE `language` SET `portugues` = 'Tem certeza?' WHERE `phrase` = 'are_you_sure';
UPDATE `language` SET `portugues` = 'Sucesso' WHERE `phrase` = 'success';
UPDATE `language` SET `portugues` = 'Erro' WHERE `phrase` = 'error';
UPDATE `language` SET `portugues` = 'Aviso' WHERE `phrase` = 'warning';
UPDATE `language` SET `portugues` = 'Informação' WHERE `phrase` = 'info';
UPDATE `language` SET `portugues` = 'Salvo com sucesso!' WHERE `phrase` = 'save_successfully';
UPDATE `language` SET `portugues` = 'Atualizado com sucesso!' WHERE `phrase` = 'update_successfully';
UPDATE `language` SET `portugues` = 'Excluído com sucesso!' WHERE `phrase` = 'delete_successfully';
UPDATE `language` SET `portugues` = 'Importado com sucesso!' WHERE `phrase` = 'imported_successfully';
UPDATE `language` SET `portugues` = 'Enviado com sucesso!' WHERE `phrase` = 'upload_successfully';
UPDATE `language` SET `portugues` = 'Usuário adicionado com sucesso!' WHERE `phrase` = 'user_added_successfully';
UPDATE `language` SET `portugues` = 'Por favor, tente novamente' WHERE `phrase` = 'please_try_again';
UPDATE `language` SET `portugues` = 'Registro não encontrado' WHERE `phrase` = 'record_not_found';

-- ============================================
-- Devoluções
-- ============================================

UPDATE `language` SET `portugues` = 'Número da Devolução' WHERE `phrase` = 'return_number';
UPDATE `language` SET `portugues` = 'Data da Devolução' WHERE `phrase` = 'return_date';
UPDATE `language` SET `portugues` = 'Motivo da Devolução' WHERE `phrase` = 'return_reason';
UPDATE `language` SET `portugues` = 'Total da Devolução' WHERE `phrase` = 'return_total';
UPDATE `language` SET `portugues` = 'Qtd Vendida' WHERE `phrase` = 'sold_qty';
UPDATE `language` SET `portugues` = 'Qtd Devolvida' WHERE `phrase` = 'return_qty';
UPDATE `language` SET `portugues` = 'Motivo' WHERE `phrase` = 'reason';
UPDATE `language` SET `portugues` = 'Dedução' WHERE `phrase` = 'deduction';
UPDATE `language` SET `portugues` = 'Qtd de Compra' WHERE `phrase` = 'purchase_qty';
UPDATE `language` SET `portugues` = 'Ajuste' WHERE `phrase` = 'adjustment';
UPDATE `language` SET `portugues` = 'Último Saldo de Fechamento' WHERE `phrase` = 'last_closing_balance';

-- ============================================
-- Outros
-- ============================================

UPDATE `language` SET `portugues` = 'Imagem' WHERE `phrase` = 'image';
UPDATE `language` SET `portugues` = 'Foto' WHERE `phrase` = 'photo';
UPDATE `language` SET `portugues` = 'Ações' WHERE `phrase` = 'actions';
UPDATE `language` SET `portugues` = 'Detalhes' WHERE `phrase` = 'details';
UPDATE `language` SET `portugues` = 'Exportar' WHERE `phrase` = 'export';
UPDATE `language` SET `portugues` = 'Copiar' WHERE `phrase` = 'copy';
UPDATE `language` SET `portugues` = 'Anterior' WHERE `phrase` = 'previous';
UPDATE `language` SET `portugues` = 'Próximo' WHERE `phrase` = 'next';
UPDATE `language` SET `portugues` = 'Primeiro' WHERE `phrase` = 'first';
UPDATE `language` SET `portugues` = 'Último' WHERE `phrase` = 'last';
UPDATE `language` SET `portugues` = 'Carregando...' WHERE `phrase` = 'loading';
UPDATE `language` SET `portugues` = 'Lista' WHERE `phrase` = 'list';
UPDATE `language` SET `portugues` = 'Nº' WHERE `phrase` = 'sl';
UPDATE `language` SET `portugues` = 'Novo' WHERE `phrase` = 'new';
UPDATE `language` SET `portugues` = 'Adicionar Novo' WHERE `phrase` = 'add_new';
UPDATE `language` SET `portugues` = 'Início' WHERE `phrase` = 'home';
UPDATE `language` SET `portugues` = 'Painel' WHERE `phrase` = 'dashboard';
UPDATE `language` SET `portugues` = 'Escolher Arquivo' WHERE `phrase` = 'choose_file';
UPDATE `language` SET `portugues` = 'Selecione uma Opção' WHERE `phrase` = 'select_option';
UPDATE `language` SET `portugues` = 'Selecionar Nome' WHERE `phrase` = 'select_name';
UPDATE `language` SET `portugues` = 'Recibo' WHERE `phrase` = 'receipt';
UPDATE `language` SET `portugues` = 'Valor do Recibo' WHERE `phrase` = 'receipt_amount';
UPDATE `language` SET `portugues` = 'Nº do Chalã' WHERE `phrase` = 'chalan_no';
UPDATE `language` SET `portugues` = 'Taxa' WHERE `phrase` = 'rate';
UPDATE `language` SET `portugues` = 'Qtd na Caixa' WHERE `phrase` = 'box_qty';
UPDATE `language` SET `portugues` = 'Qtd Unitária' WHERE `phrase` = 'unit_qty';
UPDATE `language` SET `portugues` = 'Qtd na Cartela' WHERE `phrase` = 'cartoon_qty';
UPDATE `language` SET `portugues` = 'Categoria Pai' WHERE `phrase` = 'parent_category';
UPDATE `language` SET `portugues` = 'Módulo' WHERE `phrase` = 'module';
UPDATE `language` SET `portugues` = 'Nome do Módulo' WHERE `phrase` = 'module_name';
UPDATE `language` SET `portugues` = 'Inventário' WHERE `phrase` = 'inventory';
UPDATE `language` SET `portugues` = 'Pagamento Agora' WHERE `phrase` = 'payment_now';
UPDATE `language` SET `portugues` = 'Pagar Agora' WHERE `phrase` = 'pay_now';
UPDATE `language` SET `portugues` = 'ID da Transação' WHERE `phrase` = 'transactionid';
UPDATE `language` SET `portugues` = 'Crédito Total' WHERE `phrase` = 'total_credit';
UPDATE `language` SET `portugues` = 'Débito Total' WHERE `phrase` = 'total_debit';
UPDATE `language` SET `portugues` = 'Vendas' WHERE `phrase` = 'sales';
UPDATE `language` SET `portugues` = 'Total de Vendas' WHERE `phrase` = 'total_sales';
UPDATE `language` SET `portugues` = 'Nome do Produto' WHERE `phrase` = 'product_name';
UPDATE `language` SET `portugues` = 'Modelo do Produto' WHERE `phrase` = 'product_model';
UPDATE `language` SET `portugues` = 'Selecionar Produto' WHERE `phrase` = 'select_product';
UPDATE `language` SET `portugues` = 'Selecionar Fornecedor' WHERE `phrase` = 'select_supplier';
UPDATE `language` SET `portugues` = 'Por' WHERE `phrase` = 'by';
UPDATE `language` SET `portugues` = 'Lista de Categorias' WHERE `phrase` = 'category_list';
UPDATE `language` SET `portugues` = 'Lista de Unidades' WHERE `phrase` = 'unit_list';
UPDATE `language` SET `portugues` = 'Lista de Menus' WHERE `phrase` = 'menu_list';
UPDATE `language` SET `portugues` = 'Adicionar Menu' WHERE `phrase` = 'add_menu';
UPDATE `language` SET `portugues` = 'Título do Menu' WHERE `phrase` = 'menu_title';
UPDATE `language` SET `portugues` = 'URL da Página' WHERE `phrase` = 'page_url';
UPDATE `language` SET `portugues` = 'Menu Pai' WHERE `phrase` = 'parent_menu';
UPDATE `language` SET `portugues` = 'Nome de Usuário' WHERE `phrase` = 'user_name';
UPDATE `language` SET `portugues` = 'Esquerda' WHERE `phrase` = 'left';
UPDATE `language` SET `portugues` = 'Direita' WHERE `phrase` = 'right';
UPDATE `language` SET `portugues` = 'Créer' WHERE `phrase` = 'create';
UPDATE `language` SET `portugues` = 'Ler' WHERE `phrase` = 'read';
UPDATE `language` SET `portugues` = 'Depósito' WHERE `phrase` = 'deposit';
UPDATE `language` SET `portugues` = 'Saque' WHERE `phrase` = 'withdraw';
UPDATE `language` SET `portugues` = 'Entrada de Estoque' WHERE `phrase` = 'stock_in';
UPDATE `language` SET `portugues` = 'Saída de Estoque' WHERE `phrase` = 'stock_out';
UPDATE `language` SET `portugues` = 'Preço de Vendas' WHERE `phrase` = 'sales_price';
UPDATE `language` SET `portugues` = 'Qtd Disponível' WHERE `phrase` = 'available_qty';
UPDATE `language` SET `portugues` = 'Disponível' WHERE `phrase` = 'available';
UPDATE `language` SET `portugues` = 'Nome da Moeda' WHERE `phrase` = 'currency_name';
UPDATE `language` SET `portugues` = 'Despesa' WHERE `phrase` = 'expense';
UPDATE `language` SET `portugues` = 'Conta' WHERE `phrase` = 'account';
UPDATE `language` SET `portugues` = 'Nome do Host' WHERE `phrase` = 'hostname';

SELECT '✅ Traduções PT-BR aplicadas!' AS 'Status';
