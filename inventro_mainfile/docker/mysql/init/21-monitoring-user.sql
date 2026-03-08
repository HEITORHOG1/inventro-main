-- =============================================
-- Usuário MySQL para o Prometheus MySQL Exporter
-- =============================================
-- Permissões mínimas: apenas leitura de métricas do servidor.
-- NÃO tem acesso a dados das tabelas do sistema.

CREATE USER IF NOT EXISTS 'exporter'@'%' IDENTIFIED BY 'exporter_password' WITH MAX_USER_CONNECTIONS 3;

GRANT PROCESS, REPLICATION CLIENT ON *.* TO 'exporter'@'%';
GRANT SELECT ON performance_schema.* TO 'exporter'@'%';
GRANT SELECT ON information_schema.* TO 'exporter'@'%';

FLUSH PRIVILEGES;
