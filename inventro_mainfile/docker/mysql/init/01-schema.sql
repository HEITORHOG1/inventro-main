-- ============================================
-- INVENTRO - SCHEMA DO BANCO DE DADOS
-- Arquivo: 01-schema.sql
-- Descrição: Cria todas as tabelas do sistema
-- Execução: Automática via docker-entrypoint-initdb.d
-- ============================================

-- Configurações iniciais
SET sql_mode = '';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- Garantir uso do banco correto
USE inventro_db;

-- Mensagem de início
SELECT '========================================' AS '';
SELECT '🚀 Iniciando criação do schema...' AS 'Status';
SELECT '========================================' AS '';

-- --------------------------------------------------------
-- TABELAS DO SISTEMA
-- --------------------------------------------------------

--
-- Table structure for table `attendance_tbl`
--

CREATE TABLE IF NOT EXISTS `attendance_tbl` (
  `attandence_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `in_time` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `out_time` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `staytime` time NOT NULL,
  `employee_id` int(11) NOT NULL,
  PRIMARY KEY (`attandence_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bank_tbl`
--

CREATE TABLE IF NOT EXISTS `bank_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bank_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `bank_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `account_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `branch_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `area` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_tbl`
--

CREATE TABLE IF NOT EXISTS `category_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `is_menu` int(2) DEFAULT NULL,
  `is_front` int(2) DEFAULT NULL,
  `ordering` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `country_tbl`
--

CREATE TABLE IF NOT EXISTS `country_tbl` (
  `country_id` int(11) NOT NULL AUTO_INCREMENT,
  `sortname` varchar(150) NOT NULL,
  `country_name` varchar(150) NOT NULL,
  `phonecode` int(11) NOT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `customer_tbl`
--

CREATE TABLE IF NOT EXISTS `customer_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customerid` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_closing`
--

CREATE TABLE IF NOT EXISTS `daily_closing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `last_day_closing` float NOT NULL,
  `cash_in` float NOT NULL,
  `cash_out` float NOT NULL,
  `date` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `amount` float NOT NULL,
  `adjustment` float DEFAULT NULL,
  `status` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `department_tbl`
--

CREATE TABLE IF NOT EXISTS `department_tbl` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `department_description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designation_tbl`
--

CREATE TABLE IF NOT EXISTS `designation_tbl` (
  `designation_id` int(11) NOT NULL AUTO_INCREMENT,
  `designation_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `designation_description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`designation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_tbl`
--

CREATE TABLE IF NOT EXISTS `employee_tbl` (
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `em_first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_designation` int(11) NOT NULL,
  `em_department` int(11) NOT NULL,
  `em_phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `em_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_salary` float DEFAULT NULL,
  `em_country` int(11) NOT NULL,
  `em_city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_zip` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `em_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `em_image` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_details`
--

CREATE TABLE IF NOT EXISTS `invoice_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_details_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `product_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `discount` decimal(10,0) DEFAULT NULL,
  `total_price` decimal(10,0) DEFAULT NULL,
  `discount_amount` decimal(10,0) DEFAULT NULL,
  `tax` decimal(10,0) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_tbl`
--

CREATE TABLE IF NOT EXISTS `invoice_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `customer_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `invoice` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_different` int(11) DEFAULT NULL,
  `is_inhouse` int(2) NOT NULL COMMENT '1=In and 2 = out',
  `shipping_method` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `payment_method` varchar(55) COLLATE utf8_unicode_ci NOT NULL,
  `bank_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `invoice_discount` float NOT NULL,
  `total_discount` float NOT NULL,
  `total_amount` float NOT NULL,
  `paid_amount` float NOT NULL,
  `due_amount` float NOT NULL,
  `total_tax` float NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 = pending, 1 = Processing and 2 = Delivered',
  `created_by` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phrase` varchar(100) NOT NULL,
  `english` varchar(255) NOT NULL,
  `yoruba` text,
  `odia` text,
  `xx` text,
  `spanish` text,
  `arabic` text,
  `portugues` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_tbl`
--

CREATE TABLE IF NOT EXISTS `ledger_tbl` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_type` int(11) NOT NULL COMMENT '1=payment and 2= receive',
  `transaction_category` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ledger_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_no` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `receipt_no` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` float NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque_bank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source_bank` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `d_c` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_capital` int(2) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(2) NOT NULL DEFAULT '1',
  `relation_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_transaction` int(11) NOT NULL DEFAULT '0' COMMENT '0 = default and 1=from account transaction',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `location_tbl`
--

CREATE TABLE IF NOT EXISTS `location_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_by` int(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `picture_tbl`
--

CREATE TABLE IF NOT EXISTS `picture_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `picture` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `picture_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_purchase`
--

CREATE TABLE IF NOT EXISTS `product_purchase` (
  `purchase_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `chalan_no` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `grand_total_amount` float NOT NULL,
  `purchase_date` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `purchase_details` text COLLATE utf8_unicode_ci NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `discount` decimal(6,2) NOT NULL DEFAULT '0.00',
  `payment_type` tinyint(4) NOT NULL,
  `bank_id` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`purchase_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_purchase_details`
--

CREATE TABLE IF NOT EXISTS `product_purchase_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_detail_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `purchase_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `product_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `rate` float NOT NULL,
  `total_amount` float NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_return`
--

CREATE TABLE IF NOT EXISTS `product_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `purchase_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `supplier_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `return_date` date NOT NULL,
  `deduction` float NOT NULL,
  `invoice_discount` float NOT NULL,
  `total_amount` float NOT NULL,
  `reason` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `paymet_type` tinyint(4) NOT NULL,
  `bank_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(2) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product_tbl`
--

CREATE TABLE IF NOT EXISTS `product_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `old_price` decimal(10,0) DEFAULT NULL,
  `product_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `unit` int(11) DEFAULT NULL,
  `model` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `cartoon_qty` int(11) NOT NULL,
  `supplier_id` varchar(20) NOT NULL,
  `offer` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `top_offer` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '1=top dealz, 2 = top accessories and 3 = discount',
  `tag` text COLLATE utf8_unicode_ci,
  `is_specification` int(2) DEFAULT NULL COMMENT '1=yes and 2= no',
  `is_exclusive` int(2) DEFAULT NULL COMMENT '1 = Yes and 2 = No',
  `exclusive_date` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int(11) NOT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_unit`
--

CREATE TABLE IF NOT EXISTS `product_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_details`
--

CREATE TABLE IF NOT EXISTS `return_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `product_id` bigint(25) NOT NULL,
  `return_qty` float NOT NULL,
  `sold_pur_qty` float NOT NULL,
  `price` float NOT NULL,
  `amount` float NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_generat_tbl`
--

CREATE TABLE IF NOT EXISTS `salary_generat_tbl` (
  `generat_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `salary_amount` float NOT NULL,
  `salary_month` int(11) NOT NULL,
  `generate_date` date NOT NULL,
  `generate_by` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`generat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_payment_history`
--

CREATE TABLE IF NOT EXISTS `salary_payment_history` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `generate_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `paid_amount` float NOT NULL,
  `payment_note` text COLLATE utf8_unicode_ci NOT NULL,
  `payment_date` date NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_tbl`
--

CREATE TABLE IF NOT EXISTS `salary_tbl` (
  `salary_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `salary_amount` float NOT NULL,
  `salary_type` int(11) NOT NULL,
  PRIMARY KEY (`salary_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_menu_item`
--

CREATE TABLE IF NOT EXISTS `sec_menu_item` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_url` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_menu` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `createby` int(11) NOT NULL,
  `createdate` datetime DEFAULT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_role_permission`
--

CREATE TABLE IF NOT EXISTS `sec_role_permission` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `can_access` tinyint(1) NOT NULL,
  `can_create` tinyint(1) NOT NULL,
  `can_edit` tinyint(1) NOT NULL,
  `can_delete` tinyint(1) NOT NULL,
  `createby` int(11) NOT NULL,
  `createdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sec_role_tbl`
--

CREATE TABLE IF NOT EXISTS `sec_role_tbl` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` text NOT NULL,
  `role_description` text NOT NULL,
  `create_by` int(11) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `role_status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sec_user_access_tbl`
--

CREATE TABLE IF NOT EXISTS `sec_user_access_tbl` (
  `role_acc_id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_role_id` int(11) NOT NULL,
  `fk_user_id` int(11) NOT NULL,
  PRIMARY KEY (`role_acc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `address` text,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `logo` varchar(200) DEFAULT NULL,
  `favicon` varchar(200) DEFAULT NULL,
  `language` varchar(100) DEFAULT NULL,
  `site_align` varchar(50) DEFAULT NULL,
  `currency` varchar(5) NOT NULL,
  `footer_text` varchar(255) DEFAULT NULL,
  `timezone` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_tbl`
--

CREATE TABLE IF NOT EXISTS `supplier_tbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `status` int(11) NOT NULL,
  `created_by` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_by` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_currency`
--

CREATE TABLE IF NOT EXISTS `tbl_currency` (
  `currencyid` int(11) NOT NULL AUTO_INCREMENT,
  `currencyname` varchar(100) NOT NULL,
  `curr_icon` text CHARACTER SET utf8 NOT NULL,
  `curr_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `position` int(11) NOT NULL DEFAULT '0' COMMENT '0=left,1=right',
  PRIMARY KEY (`currencyid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE IF NOT EXISTS `transaction` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `date_of_transaction` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_type` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_category` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_mode` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `received_amount` float NOT NULL,
  `pay_amount` float DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `relation_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `from_invoice_data` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque_issue_date` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cheque_bank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `about` text,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(55) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `ip_address` varchar(14) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_admin` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1=super_admin',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

SELECT '✅ Schema criado com sucesso!' AS 'Status';
