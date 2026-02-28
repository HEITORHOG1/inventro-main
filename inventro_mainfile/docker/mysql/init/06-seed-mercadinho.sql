-- ===========================================
-- SEED DE DADOS - MERCADINHO DO BAIRRO
-- Arquivo: 06-seed-mercadinho.sql
-- Descrição: Dados de teste para desenvolvimento
-- Execução: Automática via docker-entrypoint-initdb.d
-- ===========================================

SET sql_mode = '';
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- Desabilitar verificação de chave estrangeira durante o seed
SET FOREIGN_KEY_CHECKS = 0;

USE inventro_db;

SELECT '========================================' AS '';
SELECT '🌱 Inserindo dados de teste (seed)...' AS 'Status';
SELECT '========================================' AS '';

-- ===========================================
-- 1. UNIDADES DE MEDIDA
-- ===========================================
INSERT INTO product_unit (unit_name) VALUES 
('un'),
('kg'),
('g'),
('L'),
('ml'),
('pct'),
('cx'),
('dz'),
('fardo'),
('lata')
ON DUPLICATE KEY UPDATE unit_name = unit_name;

-- ===========================================
-- 2. CATEGORIAS DE PRODUTOS
-- ===========================================
DELETE FROM category_tbl WHERE category_id LIKE 'CAT%';

INSERT INTO category_tbl (category_id, name, parent_id, status, created_by, updated_by, description) VALUES 
('CAT001', 'Bebidas', 0, 1, 1, 1, 'Refrigerantes, sucos, águas e bebidas em geral'),
('CAT002', 'Alimentos Básicos', 0, 1, 1, 1, 'Arroz, feijão, açúcar, sal, óleo'),
('CAT003', 'Frutas e Verduras', 0, 1, 1, 1, 'Hortifruti frescos'),
('CAT004', 'Laticínios', 0, 1, 1, 1, 'Leite, queijo, iogurte, manteiga'),
('CAT005', 'Carnes e Frios', 0, 1, 1, 1, 'Carnes, frios, embutidos'),
('CAT006', 'Padaria', 0, 1, 1, 1, 'Pães, bolos, biscoitos'),
('CAT007', 'Higiene e Limpeza', 0, 1, 1, 1, 'Produtos de higiene pessoal e limpeza'),
('CAT008', 'Mercearia', 0, 1, 1, 1, 'Enlatados, conservas, temperos'),
('CAT009', 'Congelados', 0, 1, 1, 1, 'Sorvetes, polpas, congelados'),
('CAT010', 'Pet Shop', 0, 1, 1, 1, 'Ração e produtos para pets');

-- ===========================================
-- 3. FORNECEDORES
-- ===========================================
DELETE FROM supplier_tbl WHERE supplier_id LIKE 'FORN%';

INSERT INTO supplier_tbl (supplier_id, name, mobile, email, address, cpf, cnpj, cep, cidade, estado, tipo_pessoa, status, created_by) VALUES 
('FORN001', 'Distribuidora Bebidas Brasil', '(11) 99999-1111', 'contato@bebidasbrasil.com.br', 'Av. Paulista, 1000 - São Paulo', NULL, '12.345.678/0001-99', '01310-100', 'São Paulo', 'SP', 'J', 1, 1),
('FORN002', 'Atacadão Alimentos Ltda', '(11) 99999-2222', 'vendas@atacadaoalimentos.com.br', 'Rua do Comércio, 500 - Guarulhos', NULL, '23.456.789/0001-88', '07041-000', 'Guarulhos', 'SP', 'J', 1, 1),
('FORN003', 'Hortifruti São Paulo', '(11) 99999-3333', 'compras@hortifruti.com.br', 'CEAGESP - Box 123 - São Paulo', NULL, '34.567.890/0001-77', '05316-900', 'São Paulo', 'SP', 'J', 1, 1),
('FORN004', 'Laticínios Vale Verde', '(19) 99999-4444', 'vendas@valeverde.com.br', 'Fazenda Vale Verde - Campinas', NULL, '45.678.901/0001-66', '13076-520', 'Campinas', 'SP', 'J', 1, 1),
('FORN005', 'Frigorífico Boi Gordo', '(11) 99999-5555', 'frigoboigordo@gmail.com', 'Rua dos Frigoríficos, 200 - Osasco', NULL, '56.789.012/0001-55', '06020-010', 'Osasco', 'SP', 'J', 1, 1),
('FORN006', 'Higiene Total Distribuidor', '(11) 99999-6666', 'vendas@higienetotal.com.br', 'Av. Industrial, 800 - Santo André', NULL, '67.890.123/0001-44', '09080-500', 'Santo André', 'SP', 'J', 1, 1),
('FORN007', 'PetFood Distribuidora', '(11) 99999-7777', 'atacado@petfood.com.br', 'Rua dos Animais, 100 - São Bernardo', NULL, '78.901.234/0001-33', '09750-000', 'São Bernardo do Campo', 'SP', 'J', 1, 1),
('FORN008', 'José da Silva - Produtor Rural', '(11) 98888-1234', 'joseprodutorrural@gmail.com', 'Sítio Boa Vista - Mogi das Cruzes', '123.456.789-00', NULL, '08773-000', 'Mogi das Cruzes', 'SP', 'F', 1, 1);

-- ===========================================
-- 4. CLIENTES
-- ===========================================
DELETE FROM customer_tbl WHERE customerid LIKE 'CLI%';

INSERT INTO customer_tbl (customerid, name, mobile, email, address, cpf, cnpj, cep, cidade, estado, tipo_pessoa, status, created_by) VALUES 
('CLI001', 'Maria das Graças Silva', '(11) 98765-1111', 'maria.gracas@gmail.com', 'Rua das Flores, 123 - Vila Maria', '111.222.333-44', NULL, '02112-000', 'São Paulo', 'SP', 'F', 1, 1),
('CLI002', 'João Carlos Santos', '(11) 98765-2222', 'joaocarlos@hotmail.com', 'Av. Brasil, 456 - Centro', '222.333.444-55', NULL, '01430-000', 'São Paulo', 'SP', 'F', 1, 1),
('CLI003', 'Ana Paula Oliveira', '(11) 98765-3333', 'anapaulao@gmail.com', 'Rua São Paulo, 789 - Pinheiros', '333.444.555-66', NULL, '05401-000', 'São Paulo', 'SP', 'F', 1, 1),
('CLI004', 'Restaurante Sabor Caseiro', '(11) 98765-4444', 'saborcaseiro@restaurante.com.br', 'Rua da Gastronomia, 100', NULL, '11.222.333/0001-44', '04551-000', 'São Paulo', 'SP', 'J', 1, 1),
('CLI005', 'Bar do Zé', '(11) 98765-5555', 'bardoze@gmail.com', 'Rua dos Bares, 50', NULL, '22.333.444/0001-55', '03310-000', 'São Paulo', 'SP', 'J', 1, 1),
('CLI006', 'Pedro Henrique Costa', '(11) 98765-6666', 'pedrohc@yahoo.com.br', 'Rua Nova, 321 - Mooca', '444.555.666-77', NULL, '03103-000', 'São Paulo', 'SP', 'F', 1, 1),
('CLI007', 'Fernanda Lima Souza', '(11) 98765-7777', 'fernandalima@gmail.com', 'Av. Paulista, 1500 - Apto 42', '555.666.777-88', NULL, '01310-100', 'São Paulo', 'SP', 'F', 1, 1),
('CLI008', 'Carlos Eduardo Mendes', '(11) 98765-8888', 'carloseduardo@outlook.com', 'Rua Augusta, 2000', '666.777.888-99', NULL, '01412-000', 'São Paulo', 'SP', 'F', 1, 1),
('CLI009', 'Lanchonete Bom Sabor', '(11) 98765-9999', 'bomsaborlanche@gmail.com', 'Rua XV de Novembro, 80', NULL, '33.444.555/0001-66', '01010-000', 'São Paulo', 'SP', 'J', 1, 1),
('CLI010', 'Dona Antônia Ferreira', '(11) 98765-0000', 'antoniaferreira@gmail.com', 'Rua das Acácias, 45 - Jardim das Flores', '777.888.999-00', NULL, '04301-000', 'São Paulo', 'SP', 'F', 1, 1);

-- ===========================================
-- 5. PRODUTOS
-- ===========================================
DELETE FROM product_tbl WHERE product_id LIKE 'PROD%';

-- IDs das categorias
SET @cat_bebidas = (SELECT id FROM category_tbl WHERE category_id = 'CAT001' LIMIT 1);
SET @cat_alimentos = (SELECT id FROM category_tbl WHERE category_id = 'CAT002' LIMIT 1);
SET @cat_frutas = (SELECT id FROM category_tbl WHERE category_id = 'CAT003' LIMIT 1);
SET @cat_laticinios = (SELECT id FROM category_tbl WHERE category_id = 'CAT004' LIMIT 1);
SET @cat_carnes = (SELECT id FROM category_tbl WHERE category_id = 'CAT005' LIMIT 1);
SET @cat_higiene = (SELECT id FROM category_tbl WHERE category_id = 'CAT007' LIMIT 1);
SET @cat_pet = (SELECT id FROM category_tbl WHERE category_id = 'CAT010' LIMIT 1);

-- IDs das unidades
SET @un_unidade = (SELECT id FROM product_unit WHERE unit_name = 'un' LIMIT 1);
SET @un_kg = (SELECT id FROM product_unit WHERE unit_name = 'kg' LIMIT 1);
SET @un_pct = (SELECT id FROM product_unit WHERE unit_name = 'pct' LIMIT 1);
SET @un_lata = (SELECT id FROM product_unit WHERE unit_name = 'lata' LIMIT 1);
SET @un_dz = (SELECT id FROM product_unit WHERE unit_name = 'dz' LIMIT 1);

-- BEBIDAS
INSERT INTO product_tbl (product_id, product_code, model, name, category_id, description, price, purchase_price, unit, supplier_id, status, created_by, updated_by) VALUES 
('PROD001', '7891000100103', '', 'Coca-Cola 2L', COALESCE(@cat_bebidas, 1), 'Refrigerante Coca-Cola 2 Litros', 10.99, 7.50, COALESCE(@un_unidade, 1), 1, 1, 1, 1),
('PROD002', '7891000100110', '', 'Coca-Cola Lata 350ml', COALESCE(@cat_bebidas, 1), 'Refrigerante Coca-Cola Lata', 4.99, 3.20, COALESCE(@un_lata, 1), 1, 1, 1, 1),
('PROD003', '7891000200101', '', 'Guaraná Antarctica 2L', COALESCE(@cat_bebidas, 1), 'Refrigerante Guaraná Antarctica 2 Litros', 8.99, 6.00, COALESCE(@un_unidade, 1), 1, 1, 1, 1),
('PROD004', '7891000300102', '', 'Fanta Laranja 2L', COALESCE(@cat_bebidas, 1), 'Refrigerante Fanta Laranja 2 Litros', 8.99, 6.00, COALESCE(@un_unidade, 1), 1, 1, 1, 1),
('PROD005', '7891000500104', '', 'Água Mineral 500ml', COALESCE(@cat_bebidas, 1), 'Água Mineral Sem Gás 500ml', 2.50, 1.20, COALESCE(@un_unidade, 1), 1, 1, 1, 1);

-- ALIMENTOS BÁSICOS
INSERT INTO product_tbl (product_id, product_code, model, name, category_id, description, price, purchase_price, unit, supplier_id, status, created_by, updated_by) VALUES 
('PROD011', '7891100100201', '', 'Arroz Tipo 1 - 5kg', COALESCE(@cat_alimentos, 2), 'Arroz Branco Tipo 1 Pacote 5kg', 24.90, 18.00, COALESCE(@un_pct, 1), 2, 1, 1, 1),
('PROD012', '7891100200202', '', 'Arroz Tipo 1 - 1kg', COALESCE(@cat_alimentos, 2), 'Arroz Branco Tipo 1 Pacote 1kg', 6.99, 4.50, COALESCE(@un_pct, 1), 2, 1, 1, 1),
('PROD013', '7891100300203', '', 'Feijão Carioca 1kg', COALESCE(@cat_alimentos, 2), 'Feijão Carioca Pacote 1kg', 8.99, 6.50, COALESCE(@un_pct, 1), 2, 1, 1, 1),
('PROD014', '7891100400204', '', 'Feijão Preto 1kg', COALESCE(@cat_alimentos, 2), 'Feijão Preto Pacote 1kg', 9.99, 7.00, COALESCE(@un_pct, 1), 2, 1, 1, 1),
('PROD015', '7891100500205', '', 'Açúcar Cristal 5kg', COALESCE(@cat_alimentos, 2), 'Açúcar Cristal Pacote 5kg', 19.90, 14.00, COALESCE(@un_pct, 1), 2, 1, 1, 1),
('PROD016', '7891100600206', '', 'Açúcar Cristal 1kg', COALESCE(@cat_alimentos, 2), 'Açúcar Cristal Pacote 1kg', 4.99, 3.20, COALESCE(@un_pct, 1), 2, 1, 1, 1),
('PROD017', '7891100700207', '', 'Óleo de Soja 900ml', COALESCE(@cat_alimentos, 2), 'Óleo de Soja Soya 900ml', 7.99, 5.50, COALESCE(@un_unidade, 1), 2, 1, 1, 1),
('PROD018', '7891100800208', '', 'Sal Refinado 1kg', COALESCE(@cat_alimentos, 2), 'Sal Refinado Iodado 1kg', 2.99, 1.80, COALESCE(@un_pct, 1), 2, 1, 1, 1);

-- FRUTAS E VERDURAS
INSERT INTO product_tbl (product_id, product_code, model, name, category_id, description, price, purchase_price, unit, supplier_id, status, created_by, updated_by) VALUES 
('PROD021', '0000000000301', '', 'Banana Prata', COALESCE(@cat_frutas, 3), 'Banana Prata - Preço por kg', 5.99, 3.00, COALESCE(@un_kg, 1), 3, 1, 1, 1),
('PROD022', '0000000000302', '', 'Maçã Fuji', COALESCE(@cat_frutas, 3), 'Maçã Fuji Nacional - Preço por kg', 9.99, 6.00, COALESCE(@un_kg, 1), 3, 1, 1, 1),
('PROD023', '0000000000303', '', 'Laranja Pera', COALESCE(@cat_frutas, 3), 'Laranja Pera - Preço por kg', 4.99, 2.50, COALESCE(@un_kg, 1), 3, 1, 1, 1),
('PROD024', '0000000000304', '', 'Tomate', COALESCE(@cat_frutas, 3), 'Tomate Salada - Preço por kg', 6.99, 3.50, COALESCE(@un_kg, 1), 3, 1, 1, 1),
('PROD025', '0000000000305', '', 'Cebola', COALESCE(@cat_frutas, 3), 'Cebola Nacional - Preço por kg', 5.99, 3.00, COALESCE(@un_kg, 1), 3, 1, 1, 1);

-- LATICÍNIOS
INSERT INTO product_tbl (product_id, product_code, model, name, category_id, description, price, purchase_price, unit, supplier_id, status, created_by, updated_by) VALUES 
('PROD031', '7891200100401', '', 'Leite Integral 1L', COALESCE(@cat_laticinios, 4), 'Leite UHT Integral 1 Litro', 5.49, 3.80, COALESCE(@un_unidade, 1), 4, 1, 1, 1),
('PROD032', '7891200200402', '', 'Leite Desnatado 1L', COALESCE(@cat_laticinios, 4), 'Leite UHT Desnatado 1 Litro', 5.49, 3.80, COALESCE(@un_unidade, 1), 4, 1, 1, 1),
('PROD033', '7891200300403', '', 'Queijo Mussarela', COALESCE(@cat_laticinios, 4), 'Queijo Mussarela Fatiado - Preço por kg', 45.90, 35.00, COALESCE(@un_kg, 1), 4, 1, 1, 1),
('PROD034', '7891200400404', '', 'Manteiga 200g', COALESCE(@cat_laticinios, 4), 'Manteiga com Sal 200g', 12.99, 9.00, COALESCE(@un_unidade, 1), 4, 1, 1, 1),
('PROD035', '7891200500405', '', 'Ovos 12 unidades', COALESCE(@cat_laticinios, 4), 'Ovos Brancos Dúzia', 12.99, 9.00, COALESCE(@un_dz, 1), 4, 1, 1, 1);

-- CARNES E FRIOS
INSERT INTO product_tbl (product_id, product_code, model, name, category_id, description, price, purchase_price, unit, supplier_id, status, created_by, updated_by) VALUES 
('PROD041', '0000000000501', '', 'Carne Moída', COALESCE(@cat_carnes, 5), 'Carne Moída Patinho - Preço por kg', 32.90, 25.00, COALESCE(@un_kg, 1), 5, 1, 1, 1),
('PROD042', '0000000000502', '', 'Frango Inteiro', COALESCE(@cat_carnes, 5), 'Frango Inteiro Congelado - Preço por kg', 14.90, 10.00, COALESCE(@un_kg, 1), 5, 1, 1, 1),
('PROD043', '0000000000503', '', 'Presunto', COALESCE(@cat_carnes, 5), 'Presunto Fatiado - Preço por kg', 29.90, 22.00, COALESCE(@un_kg, 1), 5, 1, 1, 1),
('PROD044', '0000000000504', '', 'Mortadela', COALESCE(@cat_carnes, 5), 'Mortadela Fatiada - Preço por kg', 19.90, 14.00, COALESCE(@un_kg, 1), 5, 1, 1, 1);

-- HIGIENE E LIMPEZA
INSERT INTO product_tbl (product_id, product_code, model, name, category_id, description, price, purchase_price, unit, supplier_id, status, created_by, updated_by) VALUES 
('PROD051', '7891400100701', '', 'Detergente 500ml', COALESCE(@cat_higiene, 7), 'Detergente Ypê Neutro 500ml', 2.99, 1.80, COALESCE(@un_unidade, 1), 6, 1, 1, 1),
('PROD052', '7891400200702', '', 'Água Sanitária 2L', COALESCE(@cat_higiene, 7), 'Água Sanitária Qboa 2L', 6.99, 4.50, COALESCE(@un_unidade, 1), 6, 1, 1, 1),
('PROD053', '7891400300703', '', 'Sabão em Pó 1kg', COALESCE(@cat_higiene, 7), 'Sabão em Pó Omo 1kg', 14.99, 10.50, COALESCE(@un_pct, 1), 6, 1, 1, 1),
('PROD054', '7891400400704', '', 'Papel Higiênico 12 rolos', COALESCE(@cat_higiene, 7), 'Papel Higiênico Folha Dupla 12 rolos', 19.99, 14.00, COALESCE(@un_pct, 1), 6, 1, 1, 1);

-- PET SHOP
INSERT INTO product_tbl (product_id, product_code, model, name, category_id, description, price, purchase_price, unit, supplier_id, status, created_by, updated_by) VALUES 
('PROD061', '7891500100101', '', 'Ração Cães Adultos 3kg', COALESCE(@cat_pet, 10), 'Ração Pedigree Adulto 3kg', 39.90, 28.00, COALESCE(@un_pct, 1), 7, 1, 1, 1),
('PROD062', '7891500200102', '', 'Ração Gatos Adultos 3kg', COALESCE(@cat_pet, 10), 'Ração Whiskas Adulto 3kg', 42.90, 30.00, COALESCE(@un_pct, 1), 7, 1, 1, 1);

-- ===========================================
-- 6. FUNÇÕES (ROLES)
-- ===========================================

INSERT INTO sec_role_tbl (role_name, role_description, role_status) VALUES 
('Gerente', 'Gerente da loja - acesso quase total', 1),
('Caixa', 'Operador de caixa - vendas e clientes', 1),
('Estoquista', 'Controle de estoque e compras', 1),
('Vendedor', 'Vendas e atendimento ao cliente', 1)
ON DUPLICATE KEY UPDATE role_name = VALUES(role_name);

-- ===========================================
-- 7. USUÁRIOS (FUNCIONÁRIOS)
-- Senha padrão: 12345678 (MD5: 25d55ad283aa400af464c76d713c07ad)
-- ===========================================

INSERT INTO user (firstname, lastname, email, password, status, is_admin) VALUES 
('Carlos', 'Gerente', 'gerente@mercadinho.com', '25d55ad283aa400af464c76d713c07ad', 1, 0),
('Ana', 'Caixa', 'caixa@mercadinho.com', '25d55ad283aa400af464c76d713c07ad', 1, 0),
('Pedro', 'Estoquista', 'estoque@mercadinho.com', '25d55ad283aa400af464c76d713c07ad', 1, 0),
('Maria', 'Vendedora', 'vendedor@mercadinho.com', '25d55ad283aa400af464c76d713c07ad', 1, 0)
ON DUPLICATE KEY UPDATE firstname = VALUES(firstname);

-- ===========================================
-- 8. DEPARTAMENTOS
-- ===========================================

INSERT INTO department_tbl (department_name, department_description) VALUES
('Administração', 'Departamento Administrativo'),
('Vendas', 'Departamento de Vendas e Atendimento'),
('Estoque', 'Controle de Estoque e Almoxarifado'),
('Caixa', 'Operações de Caixa')
ON DUPLICATE KEY UPDATE department_name = VALUES(department_name);

-- ===========================================
-- 9. CARGOS
-- ===========================================

INSERT INTO designation_tbl (designation_name, designation_description) VALUES
('Gerente Geral', 'Responsável pela loja'),
('Operador de Caixa', 'Atendimento no caixa'),
('Repositor', 'Reposição de produtos'),
('Atendente', 'Atendimento ao cliente')
ON DUPLICATE KEY UPDATE designation_name = VALUES(designation_name);

-- Reabilitar verificação de chave estrangeira
SET FOREIGN_KEY_CHECKS = 1;

-- ===========================================
-- RESUMO DOS DADOS INSERIDOS
-- ===========================================

SELECT '========================================' AS '';
SELECT '📊 RESUMO DOS DADOS INSERIDOS:' AS 'Status';
SELECT '========================================' AS '';

SELECT 'Categorias:' as item, COUNT(*) as total FROM category_tbl
UNION ALL
SELECT 'Fornecedores:', COUNT(*) FROM supplier_tbl
UNION ALL
SELECT 'Clientes:', COUNT(*) FROM customer_tbl
UNION ALL
SELECT 'Produtos:', COUNT(*) FROM product_tbl
UNION ALL
SELECT 'Roles:', COUNT(*) FROM sec_role_tbl
UNION ALL
SELECT 'Usuários:', COUNT(*) FROM user;

SELECT '' AS '';
SELECT '✅ SEED EXECUTADA COM SUCESSO!' AS 'Status';
SELECT '========================================' AS '';
SELECT '🔐 Credenciais de Acesso:' AS '';
SELECT '   E-mail: admin@admin.com' AS '';
SELECT '   Senha:  12345678' AS '';
SELECT '========================================' AS '';
