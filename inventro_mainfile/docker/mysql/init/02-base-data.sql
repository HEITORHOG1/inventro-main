-- ============================================
-- INVENTRO - DADOS BASE OBRIGATÓRIOS
-- Arquivo: 02-base-data.sql
-- Descrição: Dados essenciais para o sistema funcionar
-- Execução: Automática via docker-entrypoint-initdb.d
-- ============================================

SET sql_mode = '';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

USE inventro_db;

SELECT '========================================' AS '';
SELECT '📦 Inserindo dados base...' AS 'Status';
SELECT '========================================' AS '';

-- --------------------------------------------------------
-- PAÍSES
-- --------------------------------------------------------

INSERT INTO `country_tbl` (`country_id`, `sortname`, `country_name`, `phonecode`) VALUES
(1, 'AF', 'Afghanistan', 93),
(2, 'AL', 'Albania', 355),
(3, 'DZ', 'Algeria', 213),
(10, 'AR', 'Argentina', 54),
(13, 'AU', 'Australia', 61),
(14, 'AT', 'Austria', 43),
(21, 'BE', 'Belgium', 32),
(30, 'BR', 'Brazil', 55),
(38, 'CA', 'Canada', 1),
(43, 'CL', 'Chile', 56),
(44, 'CN', 'China', 86),
(47, 'CO', 'Colombia', 57),
(52, 'CR', 'Costa Rica', 506),
(57, 'CZ', 'Czech Republic', 420),
(58, 'DK', 'Denmark', 45),
(64, 'EG', 'Egypt', 20),
(74, 'FI', 'Finland', 358),
(75, 'FR', 'France', 33),
(82, 'DE', 'Germany', 49),
(85, 'GR', 'Greece', 30),
(99, 'HU', 'Hungary', 36),
(101, 'IN', 'India', 91),
(102, 'ID', 'Indonesia', 62),
(105, 'IE', 'Ireland', 353),
(106, 'IL', 'Israel', 972),
(107, 'IT', 'Italy', 39),
(109, 'JP', 'Japan', 81),
(116, 'KR', 'Korea South', 82),
(142, 'MX', 'Mexico', 52),
(155, 'NL', 'Netherlands', 31),
(157, 'NZ', 'New Zealand', 64),
(164, 'NO', 'Norway', 47),
(173, 'PH', 'Philippines', 63),
(175, 'PL', 'Poland', 48),
(176, 'PT', 'Portugal', 351),
(181, 'RU', 'Russia', 7),
(202, 'ZA', 'South Africa', 27),
(205, 'ES', 'Spain', 34),
(211, 'SE', 'Sweden', 46),
(212, 'CH', 'Switzerland', 41),
(230, 'GB', 'United Kingdom', 44),
(231, 'US', 'United States', 1)
ON DUPLICATE KEY UPDATE country_name = VALUES(country_name);

-- --------------------------------------------------------
-- MOEDAS
-- --------------------------------------------------------

INSERT INTO `tbl_currency` (`currencyid`, `currencyname`, `curr_icon`, `curr_rate`, `position`) VALUES
(1, 'Dollar', '$', '1.00', 0),
(2, 'Euro', '€', '1.00', 0),
(3, 'Pound', '£', '1.00', 0),
(4, 'Rupee', '₹', '1.00', 1),
(5, 'Real', 'R$', '1.00', 0)
ON DUPLICATE KEY UPDATE currencyname = VALUES(currencyname);

-- --------------------------------------------------------
-- CONFIGURAÇÕES DO SISTEMA
-- --------------------------------------------------------

INSERT INTO `setting` (`id`, `title`, `address`, `email`, `phone`, `logo`, `favicon`, `language`, `site_align`, `currency`, `footer_text`, `timezone`) VALUES
(2, 'Mercadinho do Bairro', 'Rua Principal, 100 - Centro - São Paulo/SP', 'contato@mercadinhodobairro.com.br', '11999998888', NULL, NULL, 'portugues', 'ltr', '5', '©2024 Inventro - Sistema de Gestão', 'America/Sao_Paulo')
ON DUPLICATE KEY UPDATE 
    title = VALUES(title),
    address = VALUES(address),
    email = VALUES(email),
    phone = VALUES(phone),
    language = VALUES(language),
    currency = VALUES(currency),
    timezone = VALUES(timezone);

-- --------------------------------------------------------
-- USUÁRIO ADMIN PADRÃO
-- Senha: 12345678 (MD5: 25d55ad283aa400af464c76d713c07ad)
-- --------------------------------------------------------

INSERT INTO `user` (`id`, `firstname`, `lastname`, `about`, `email`, `password`, `status`, `is_admin`) VALUES
(1, 'Admin', 'Sistema', 'Administrador do Sistema', 'admin@admin.com', '25d55ad283aa400af464c76d713c07ad', 1, 1)
ON DUPLICATE KEY UPDATE 
    firstname = VALUES(firstname),
    lastname = VALUES(lastname);

-- --------------------------------------------------------
-- MENUS DO SISTEMA
-- --------------------------------------------------------

INSERT INTO `sec_menu_item` (`menu_id`, `menu_title`, `page_url`, `module`, `parent_menu`, `status`, `createby`, `createdate`) VALUES
(132, 'Add Menu', 'menu/menu_setting/index', 'Menu', 0, 1, 1, '2020-01-18 00:00:00'),
(133, 'Menu List', 'menu/menu_setting/menu_list', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(134, 'Add Role', 'menu/crole/add_role', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(135, 'Role List', 'menu/crole/role_list', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(136, 'User Assign Role', 'menu/crole/role_assign', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(139, 'Assigned List', 'menu/crole/assigned_role_list', 'Menu', 132, 1, 1, '2020-01-18 00:00:00'),
(140, 'department', 'hrm/department/index', 'hrm', 0, 1, 1, NULL),
(141, 'designation', 'hrm/designation/index', 'hrm', 0, 1, 1, NULL),
(142, 'salary', 'hrm/salary', 'hrm', 0, 1, 1, NULL),
(143, 'salary_setup', 'hrm/salary/salary_setup', 'hrm', 142, 1, 1, NULL),
(144, 'salary_generat_list', 'hrm/salary/salary_generat_list', 'hrm', 142, 1, 1, NULL),
(145, 'attendance', 'hrm/attendance', 'hrm', 0, 1, 1, NULL),
(146, 'attendance_report', 'hrm/attendance/report', 'hrm', 145, 1, 1, NULL),
(147, 'employee', 'hrm/employee/index', 'hrm', 0, 1, 1, NULL),
(148, 'add_employee', 'hrm/employee/add_employee', 'hrm', 147, 1, 1, NULL),
(149, 'manage_employee', 'hrm/employee/manage_employee', 'hrm', 147, 1, 1, NULL),
(150, 'bank', 'bank/Bank/bank_list', 'bank', 0, 1, 1, NULL),
(151, 'bank', 'bank/Bank/bank_list', 'bank', 150, 1, 1, NULL),
(152, 'add_bank', 'bank/Bank/bank_list', 'bank', 150, 1, 1, NULL),
(153, 'bank_ledger', 'bank/Bank/bank_ledger', 'bank', 150, 1, 1, NULL),
(154, 'bank_adjustment', 'bank/Bank/bank_adjustment', 'bank', 150, 1, 1, NULL),
(155, 'item', 'item/item', 'item', 0, 1, 1, NULL),
(156, 'unit', 'item/Unit/unit_form', 'item', 155, 1, 1, NULL),
(157, 'add_unit', 'item/Unit/unit_form', 'item', 155, 1, 1, NULL),
(158, 'category', 'item/Category/category_form', 'item', 155, 1, 1, NULL),
(159, 'add_category', 'item/Category/category_form', 'item', 155, 1, 1, NULL),
(160, 'add_item', 'item/Item/item_form', 'item', 155, 1, 1, NULL),
(161, 'item_list', 'item/Item/item_list', 'item', 155, 1, 1, NULL),
(162, 'purchase', 'purchase/Purchase/', 'purchase', 0, 1, 1, NULL),
(163, 'new_purchase', 'purchase/Purchase/create_purchase', 'purchase', 162, 1, 1, NULL),
(164, 'purchase_list', 'purchase/Purchase/purchase_list', 'purchase', 162, 1, 1, NULL),
(165, 'accounts', 'accounts/index', 'accounts', 0, 1, 1, NULL),
(166, 'payment_or_receive', 'accounts/Account/payment_receive_form', 'accounts', 165, 1, 1, NULL),
(167, 'manage_transaction', 'accounts/Account/manage_transaction', 'accounts', 165, 1, 1, NULL),
(168, 'account_adjustment', 'accounts/Account/account_adjustment', 'accounts', 165, 1, 1, NULL),
(169, 'customer', 'customer/index', 'customer', 0, 1, 1, NULL),
(170, 'customer_list', 'customer/customer_info/index', 'customer', 169, 1, 1, NULL),
(171, 'customer_ledger', 'customer/customer_info/customerledger', 'customer', 169, 1, 1, NULL),
(172, 'report', 'report/index', 'report', 0, 1, 1, NULL),
(173, 'purchase_report', 'report/report/purchase_report', 'report', 172, 1, 1, NULL),
(174, 'sales_report', 'report/report/sales_report', 'report', 172, 1, 1, NULL),
(175, 'cash_book', 'report/report/cash_book', 'report', 172, 1, 1, NULL),
(176, 'stock', 'stock', 'stock', 0, 1, 1, NULL),
(177, 'stock_report', 'stock/stock/index', 'stock', 176, 1, 1, NULL),
(178, 'stock_report_supplier_wise', 'stock/stock/stock_report_supplier_wise', 'stock', 176, 1, 1, NULL),
(179, 'stock_report_product_wise', 'stock/stock/stock_report_product_wise', 'stock', 176, 1, 1, NULL),
(180, 'supplier', 'supplier', 'supplier', 0, 1, 1, NULL),
(181, 'supplier_list', 'supplier/supplierlist/index', 'supplier', 180, 1, 1, NULL),
(182, 'supplier_ledger', 'supplier/supplierlist/supplierledger', 'supplier', 180, 1, 1, NULL),
(183, 'invoice', 'invoice', 'invoice', 0, 1, 1, NULL),
(184, 'add_invoice', 'invoice/CInvoice/index', 'invoice', 183, 1, 1, NULL),
(185, 'invoice_list', 'invoice/CInvoice/invoice_list', 'invoice', 183, 1, 1, NULL),
(186, 'Role Permission', 'menu/crole/add_role', 'menu', 0, 1, 1, NULL),
(187, 'add_pos_invoice', 'add_pos', 'invoice', 183, 1, 1, NULL),
(188, 'cash_closing', 'closing_form', 'accounts', 165, 1, 1, NULL),
(189, 'closing_list', 'closing_list', 'accounts', 165, 1, 1, NULL)
ON DUPLICATE KEY UPDATE menu_title = VALUES(menu_title);

-- --------------------------------------------------------
-- ROLE ADMIN
-- --------------------------------------------------------

INSERT INTO `sec_role_tbl` (`role_id`, `role_name`, `role_description`, `create_by`, `role_status`) VALUES
(1, 'Administrador', 'Acesso total ao sistema', 1, 1)
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- --------------------------------------------------------
-- ATRIBUIR ROLE ADMIN AO USUÁRIO ADMIN
-- --------------------------------------------------------

INSERT INTO `sec_user_access_tbl` (`role_acc_id`, `fk_role_id`, `fk_user_id`) VALUES
(1, 1, 1)
ON DUPLICATE KEY UPDATE fk_role_id = VALUES(fk_role_id);

SELECT '✅ Dados base inseridos com sucesso!' AS 'Status';
