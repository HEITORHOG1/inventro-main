-- =====================================================
-- 20 - Performance indexes for legacy tables
-- =====================================================
-- Legacy tables (01-schema.sql) were created with only PRIMARY KEYs.
-- Every WHERE, JOIN, and ORDER BY hits a full table scan.
-- This migration adds indexes based on actual query patterns found in models.
--
-- Principle: only index columns that appear in WHERE, JOIN ON, or ORDER BY
-- in existing model code. No speculative indexes.
-- =====================================================

USE inventro_db;

-- Relax sql_mode to handle legacy 0000-00-00 timestamps during ALTER
SET sql_mode = '';

SELECT '========================================' AS '';
SELECT 'Adding performance indexes...' AS 'Status';
SELECT '========================================' AS '';

-- --------------------------------------------------------
-- 1. CONVERT MyISAM TABLES TO InnoDB
--    MyISAM has no row-level locking, no transactions, no FK support.
--    InnoDB is superior for all OLTP workloads.
-- --------------------------------------------------------

ALTER TABLE `product_purchase` ENGINE=InnoDB;
ALTER TABLE `product_purchase_details` ENGINE=InnoDB;
ALTER TABLE `tbl_currency` ENGINE=InnoDB;

-- --------------------------------------------------------
-- 2. invoice_tbl — HIGHEST TRAFFIC TABLE, ZERO INDEXES
--    Used by: Invoice_model, Report_model, Dashboard_model
-- --------------------------------------------------------

CREATE INDEX idx_invoice_invoice_id ON invoice_tbl (invoice_id);
CREATE INDEX idx_invoice_customer_id ON invoice_tbl (customer_id);
CREATE INDEX idx_invoice_date ON invoice_tbl (`date`);
CREATE INDEX idx_invoice_status ON invoice_tbl (status);
CREATE INDEX idx_invoice_created_by ON invoice_tbl (created_by);

-- --------------------------------------------------------
-- 3. invoice_details — JOINed on every invoice view
--    Used by: Invoice_model (get_invoice_details, reports)
-- --------------------------------------------------------

CREATE INDEX idx_invdet_invoice_id ON invoice_details (invoice_id);
CREATE INDEX idx_invdet_product_id ON invoice_details (product_id);
CREATE INDEX idx_invdet_created_date ON invoice_details (created_date);

-- --------------------------------------------------------
-- 4. product_tbl — searched by category, supplier, name
--    Used by: Item_model, Invoice_model, Stock models, Cardapio
-- --------------------------------------------------------

CREATE INDEX idx_product_product_id ON product_tbl (product_id);
CREATE INDEX idx_product_supplier_id ON product_tbl (supplier_id);
CREATE INDEX idx_product_status ON product_tbl (status);
CREATE INDEX idx_product_product_code ON product_tbl (product_code);

-- NOTE: category_id is TEXT type (bad design), cannot index directly.
-- If refactored to INT or VARCHAR in the future, add index then.

-- --------------------------------------------------------
-- 5. product_purchase — filtered by supplier, date
--    Used by: Purchase_model, Report_model
-- --------------------------------------------------------

CREATE INDEX idx_pp_supplier_id ON product_purchase (supplier_id);
CREATE INDEX idx_pp_purchase_date ON product_purchase (purchase_date);
CREATE INDEX idx_pp_status ON product_purchase (status);

-- --------------------------------------------------------
-- 6. product_purchase_details — JOINed on every purchase view
--    Used by: Purchase_model
-- --------------------------------------------------------

CREATE INDEX idx_ppd_purchase_id ON product_purchase_details (purchase_id);
CREATE INDEX idx_ppd_product_id ON product_purchase_details (product_id);

-- --------------------------------------------------------
-- 7. ledger_tbl — core financial table, queried heavily
--    Used by: Account_model, Bank_model, Report_model
-- --------------------------------------------------------

CREATE INDEX idx_ledger_ledger_id ON ledger_tbl (ledger_id);
CREATE INDEX idx_ledger_transaction_id ON ledger_tbl (transaction_id);
CREATE INDEX idx_ledger_d_c ON ledger_tbl (d_c);
CREATE INDEX idx_ledger_date ON ledger_tbl (`date`);
CREATE INDEX idx_ledger_created_by ON ledger_tbl (created_by);
CREATE INDEX idx_ledger_is_transaction ON ledger_tbl (is_transaction);
CREATE INDEX idx_ledger_status ON ledger_tbl (status);
-- Composite: balance queries filter by entity + debit/credit
CREATE INDEX idx_ledger_ledger_dc ON ledger_tbl (ledger_id, d_c);

-- --------------------------------------------------------
-- 8. transaction — account transactions
--    Used by: Account_model
-- --------------------------------------------------------

CREATE INDEX idx_txn_transaction_id ON `transaction` (transaction_id);
CREATE INDEX idx_txn_date ON `transaction` (date_of_transaction);
CREATE INDEX idx_txn_relation_id ON `transaction` (relation_id);
CREATE INDEX idx_txn_status ON `transaction` (status);

-- --------------------------------------------------------
-- 9. customer_tbl — lookups by business ID, status
--    Used by: Customer_model, Invoice_model (JOINs)
-- --------------------------------------------------------

CREATE INDEX idx_customer_customerid ON customer_tbl (customerid);
CREATE INDEX idx_customer_status ON customer_tbl (status);
CREATE INDEX idx_customer_mobile ON customer_tbl (mobile);

-- --------------------------------------------------------
-- 10. supplier_tbl — lookups by business ID, status
--     Used by: Supplier_model, Purchase_model (JOINs)
-- --------------------------------------------------------

CREATE INDEX idx_supplier_supplier_id ON supplier_tbl (supplier_id);
CREATE INDEX idx_supplier_status ON supplier_tbl (status);

-- --------------------------------------------------------
-- 11. category_tbl — tree queries by parent, status filtering
--     Used by: Category_model, Item_model
-- --------------------------------------------------------

CREATE INDEX idx_category_parent_id ON category_tbl (parent_id);
CREATE INDEX idx_category_status ON category_tbl (status);

-- --------------------------------------------------------
-- 12. product_return / return_details
--     Used by: Return_model
-- --------------------------------------------------------

CREATE INDEX idx_return_purchase_id ON product_return (purchase_id);
CREATE INDEX idx_return_customer_id ON product_return (customer_id);
CREATE INDEX idx_return_supplier_id ON product_return (supplier_id);
CREATE INDEX idx_return_invoice_id ON product_return (invoice_id);
CREATE INDEX idx_return_date ON product_return (return_date);

CREATE INDEX idx_retdet_return_id ON return_details (return_id);
CREATE INDEX idx_retdet_product_id ON return_details (product_id);

-- --------------------------------------------------------
-- 13. picture_tbl — product image lookups
--     Used by: Picture_model, Invoice_model
-- --------------------------------------------------------

CREATE INDEX idx_picture_from_id ON picture_tbl (from_id);
CREATE INDEX idx_picture_type ON picture_tbl (picture_type);

-- --------------------------------------------------------
-- 14. user — login by email
--     Used by: Auth_model
-- --------------------------------------------------------

CREATE INDEX idx_user_email ON `user` (email);
CREATE INDEX idx_user_status ON `user` (status);

-- --------------------------------------------------------
-- 15. RBAC tables — hot path on every authenticated request
--     Used by: Auth_model::userPermission2, Permission library
-- --------------------------------------------------------

CREATE INDEX idx_role_perm_role_id ON sec_role_permission (role_id);
CREATE INDEX idx_role_perm_menu_id ON sec_role_permission (menu_id);

CREATE INDEX idx_user_access_user ON sec_user_access_tbl (fk_user_id);
CREATE INDEX idx_user_access_role ON sec_user_access_tbl (fk_role_id);

-- --------------------------------------------------------
-- 16. sec_menu_item — menu tree rendering
--     Used by: Menu_model
-- --------------------------------------------------------

CREATE INDEX idx_menu_parent ON sec_menu_item (parent_menu);
CREATE INDEX idx_menu_status ON sec_menu_item (status);
CREATE INDEX idx_menu_module ON sec_menu_item (module);

-- --------------------------------------------------------
-- 17. HRM tables — employee lookups
--     Used by: Attendance_model, Salary models
-- --------------------------------------------------------

CREATE INDEX idx_attendance_employee ON attendance_tbl (employee_id);
CREATE INDEX idx_attendance_date ON attendance_tbl (`date`);

CREATE INDEX idx_salgen_employee ON salary_generat_tbl (employee_id);
CREATE INDEX idx_salgen_status ON salary_generat_tbl (status);

CREATE INDEX idx_salary_employee ON salary_tbl (employee_id);

CREATE INDEX idx_salpay_employee ON salary_payment_history (employee_id);
CREATE INDEX idx_salpay_generate ON salary_payment_history (generate_id);

-- --------------------------------------------------------
-- 18. language — i18n lookups (called on every page)
--     Used by: makeString() / display() helper
-- --------------------------------------------------------

CREATE INDEX idx_language_phrase ON `language` (phrase);

-- --------------------------------------------------------
-- 19. location_tbl — tree queries
-- --------------------------------------------------------

CREATE INDEX idx_location_parent ON location_tbl (parent_id);
CREATE INDEX idx_location_status ON location_tbl (status);

-- --------------------------------------------------------
-- 20. bank_tbl — used in JOINs from ledger
-- --------------------------------------------------------

CREATE INDEX idx_bank_bank_id ON bank_tbl (bank_id);
CREATE INDEX idx_bank_status ON bank_tbl (status);

-- --------------------------------------------------------
-- 21. daily_closing — date lookups
-- --------------------------------------------------------

CREATE INDEX idx_closing_date ON daily_closing (`date`);
CREATE INDEX idx_closing_status ON daily_closing (status);

-- --------------------------------------------------------
-- 22. orders — customer_id (added by migration 18)
-- --------------------------------------------------------

-- Only add if not already exists (migration 18 may not have added it)
SET @idx_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = 'inventro_db' AND TABLE_NAME = 'orders' AND INDEX_NAME = 'idx_customer_id');
SET @sql = IF(@idx_exists = 0,
    'CREATE INDEX idx_customer_id ON orders (customer_id)',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- --------------------------------------------------------

SELECT '========================================' AS '';
SELECT 'Performance indexes created successfully!' AS 'Status';
SELECT '========================================' AS '';
