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

.PHONY: dev prod down reset logs shell db status build

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

# ---- Produção (Hostiger) ----
# Carrega APENAS docker-compose.yml + docker-compose.prod.yml (sem override)
prod:
	@echo "🚀 Subindo ambiente de PRODUÇÃO..."
	docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
	@echo ""
	@echo "✅ Ambiente prod pronto!"
	@echo "   App: http://localhost:$${APP_PORT:-80}"

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

# ---- Build sem subir ----
build:
	docker compose build
