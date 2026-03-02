# Workflows n8n - Inventro

Workflows pre-configurados para integracao Inventro + WhatsApp Business API.

## Importacao

1. Acesse o n8n: `http://localhost:5678`
2. Clique em **Settings** > **Community nodes** e instale se necessario
3. Para cada arquivo JSON:
   - Clique em **Workflows** > **Import from file**
   - Selecione o arquivo `.json`
   - Ative o workflow

## Configuracao de Credenciais

Antes de ativar os workflows, configure as credenciais no n8n:

### MySQL (nome: "Inventro MySQL")
- Host: `db`
- Port: `3306`
- Database: `inventro_db`
- User/Password: conforme `.env`

### WhatsApp Business (nome: "WhatsApp Business")
- Access Token: System User Token do Meta Business
- Phone Number ID: conforme painel Meta Developers

### Variaveis de Ambiente n8n
Adicione ao `.env` ou configure direto no n8n:
- `WHATSAPP_PHONE_ID` — Phone Number ID
- `N8N_WEBHOOK_SECRET` — mesma chave configurada no admin Inventro

## Workflows

| # | Arquivo | Trigger | Descricao |
|---|---------|---------|-----------|
| 01 | `01-novo-pedido-motoboys.json` | Webhook | Novo pedido → notifica todos os motoboys ativos |
| 02 | `02-status-cliente.json` | Webhook | Mudanca de status → notifica cliente via template |
| 03 | `03-cupom-fiscal-pdf.json` | Webhook | Gera PDF do cupom e envia via WhatsApp |
| 04 | `04-motoboy-atribuido.json` | Webhook | Entrega atribuida → notifica motoboy especifico |
| 05 | `05-resumo-diario.json` | Schedule (23h) | Resumo diario de vendas para o admin |
| 06 | `06-teste-conexao.json` | Webhook | Testa conexao Inventro → n8n → WhatsApp |

## Ordem de ativacao recomendada

1. `06-teste-conexao.json` (para validar que tudo funciona)
2. `02-status-cliente.json` (mais importante)
3. `01-novo-pedido-motoboys.json`
4. `04-motoboy-atribuido.json`
5. `03-cupom-fiscal-pdf.json`
6. `05-resumo-diario.json`

## Webhook unico

Todos os workflows usam o mesmo path `/webhook/inventro`. O n8n roteia
internamente usando o campo `event` do payload JSON. Se preferir webhooks
separados, altere o `path` de cada workflow e atualize as URLs no admin
Inventro (secao WhatsApp & Automacao).
