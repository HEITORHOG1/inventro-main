# =============================================
# Inventro — Makefile
# =============================================
# Comandos de conveniência para gerenciar o Docker.
#
# Uso:
#   make dev      — Sobe ambiente de desenvolvimento
#   make prod     — Sobe ambiente de produção
#   make down     — Para todos os containers
#   make reset    — Reset total (destrói dados e recria)
#   make logs     — Acompanhar logs em tempo real
#   make shell    — Abrir shell no container PHP
#   make db       — Abrir shell no MySQL
#   make status   — Ver status dos containers

.PHONY: dev dev-n8n prod prod-n8n down reset logs shell db status build n8n-up n8n-down n8n-logs monitor monitor-down monitor-logs loja loja-online backup-agora backup-listar

# ---- Desenvolvimento (padrão) ----
# Carrega docker-compose.yml + docker-compose.override.yml automaticamente
dev:
	@echo "🚀 Subindo ambiente de DESENVOLVIMENTO..."
	docker compose up -d --build
	@echo ""
	@echo "✅ Ambiente dev pronto!"
	@echo "   App:        http://localhost:$${APP_PORT:-8080}"
	@echo "   phpMyAdmin: http://localhost:$${PMA_PORT:-8081}"
	@echo "   MySQL:      localhost:$${DB_EXTERNAL_PORT:-3307}"
	@echo "   Swagger:    http://localhost:$${APP_PORT:-8080}/api/docs"

# ---- Desenvolvimento com n8n (WhatsApp automação) ----
# Carrega docker-compose.yml + override + profile online
dev-n8n:
	@echo "🚀 Subindo ambiente de DEV + n8n..."
	docker compose --profile online up -d --build
	@echo ""
	@echo "✅ Ambiente dev + n8n pronto!"
	@echo "   App:        http://localhost:$${APP_PORT:-8080}"
	@echo "   phpMyAdmin: http://localhost:$${PMA_PORT:-8081}"
	@echo "   MySQL:      localhost:$${DB_EXTERNAL_PORT:-3307}"
	@echo "   n8n:        http://localhost:$${N8N_PORT:-5678}"
	@echo "   Swagger:    http://localhost:$${APP_PORT:-8080}/api/docs"

# ---- n8n: subir apenas n8n ----
n8n-up:
	@echo "🚀 Subindo n8n..."
	docker compose --profile online up -d n8n
	@echo "✅ n8n pronto em http://localhost:$${N8N_PORT:-5678}"

# ---- n8n: parar apenas n8n ----
n8n-down:
	@echo "⏹️  Parando n8n..."
	docker compose stop n8n

# ---- n8n: logs ----
n8n-logs:
	docker compose logs -f n8n

# ---- Produção (Hostiger) ----
# Carrega APENAS docker-compose.yml + docker-compose.prod.yml (sem override)
prod:
	@echo "🚀 Subindo ambiente de PRODUÇÃO..."
	docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
	@echo ""
	@echo "✅ Ambiente prod pronto!"
	@echo "   App: http://localhost:$${APP_PORT:-80}"

# ---- Produção com n8n ----
prod-n8n:
	@echo "🚀 Subindo ambiente de PRODUÇÃO + n8n..."
	docker compose -f docker-compose.yml -f docker-compose.prod.yml --profile online up -d --build
	@echo ""
	@echo "✅ Ambiente prod + n8n pronto!"
	@echo "   App: http://localhost:$${APP_PORT:-80}"
	@echo "   n8n: acesso via rede interna (sem porta exposta)"

# ---- Loja Offline (PDV no mercadinho, sem n8n) ----
# Carrega docker-compose.yml + docker-compose.loja.yml
loja:
	@echo "🏪 Subindo LOJA OFFLINE (PDV + DB + Redis + Backup)..."
	docker compose -f docker-compose.yml -f docker-compose.loja.yml up -d --build
	@echo ""
	@echo "✅ Loja offline pronta!"
	@echo "   PDV:   http://localhost:$${APP_PORT:-80}/pdv/terminal/001"
	@echo "   Admin: http://localhost:$${APP_PORT:-80}"
	@echo "   MySQL: localhost:$${DB_EXTERNAL_PORT:-3307}"

# ---- Loja Online (PDV + cardápio + WhatsApp) ----
loja-online:
	@echo "🏪 Subindo LOJA ONLINE (PDV + n8n + WhatsApp)..."
	docker compose -f docker-compose.yml -f docker-compose.loja.yml --profile online up -d --build
	@echo ""
	@echo "✅ Loja online pronta!"
	@echo "   PDV:   http://localhost:$${APP_PORT:-80}/pdv/terminal/001"
	@echo "   Admin: http://localhost:$${APP_PORT:-80}"

# ---- Backup: executar agora ----
backup-agora:
	@echo "💾 Executando backup agora..."
	docker exec inventro_backup /backup.sh
	@echo "✅ Backup concluído!"

# ---- Backup: listar backups existentes ----
backup-listar:
	@echo "📋 Backups disponíveis:"
	docker exec inventro_backup ls -lh /db/

# ---- Parar containers ----
down:
	@echo "⏹️  Parando containers..."
	docker compose down

# ---- Reset total (CUIDADO: destrói todos os dados!) ----
reset:
	@echo "⚠️  Reset total — destruindo volumes e recriando..."
	docker compose down -v
	docker compose up -d --build
	@echo "✅ Reset completo!"

# ---- Logs em tempo real ----
logs:
	docker compose logs -f

# ---- Logs de um serviço específico ----
logs-app:
	docker compose logs -f app

logs-db:
	docker compose logs -f db

# ---- Shell no container PHP ----
shell:
	docker exec -it inventro_app bash

# ---- Shell no MySQL ----
db:
	docker exec -it inventro_db mysql -u inventro_user -p inventro_db

# ---- Status dos containers ----
status:
	docker compose ps

# ---- Monitoramento: subir Grafana + Loki + Prometheus ----
# Uso: make monitor (dev) ou COMPOSE_ENV=prod make monitor
MONITORING_COMPOSE = -f docker-compose.yml -f docker-compose.override.yml -f docker-compose.monitoring.yml

monitor:
	@echo "📊 Subindo stack de monitoramento..."
	docker compose $(MONITORING_COMPOSE) up -d grafana loki promtail prometheus cadvisor mysql-exporter apache-exporter
	@echo ""
	@echo "✅ Monitoramento pronto!"
	@echo "   Grafana:    http://localhost:$${GRAFANA_PORT:-3000}"
	@echo "   Prometheus: http://localhost:$${PROMETHEUS_PORT:-9090}"
	@echo "   Login:      admin / inventro2026"

# ---- Monitoramento: parar ----
monitor-down:
	@echo "⏹️  Parando stack de monitoramento..."
	docker compose $(MONITORING_COMPOSE) stop grafana loki promtail prometheus cadvisor mysql-exporter apache-exporter

# ---- Monitoramento: logs ----
monitor-logs:
	docker compose $(MONITORING_COMPOSE) logs -f grafana loki promtail prometheus cadvisor mysql-exporter apache-exporter

# ---- Dev + Monitoramento (tudo junto) ----
dev-monitor:
	@echo "🚀 Subindo DEV + Monitoramento..."
	docker compose $(MONITORING_COMPOSE) up -d --build
	@echo ""
	@echo "✅ Dev + Monitoramento pronto!"
	@echo "   App:        http://localhost:$${APP_PORT:-8080}"
	@echo "   phpMyAdmin: http://localhost:$${PMA_PORT:-8081}"
	@echo "   Grafana:    http://localhost:$${GRAFANA_PORT:-3000}"
	@echo "   Prometheus: http://localhost:$${PROMETHEUS_PORT:-9090}"
	@echo "   Login Grafana: admin / inventro2026"

# ---- Build sem subir ----
build:
	docker compose build
