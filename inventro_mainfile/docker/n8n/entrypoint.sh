#!/bin/sh
# =============================================================
# Inventro n8n — Custom entrypoint
# Executa provisionamento em background e inicia n8n normalmente.
# =============================================================

# Provisioning em background (espera n8n ficar pronto)
(sleep 10 && node /home/node/provision.js) &

# Entrypoint original do n8n (com tini ja gerenciando sinais)
exec /docker-entrypoint.sh "$@"
