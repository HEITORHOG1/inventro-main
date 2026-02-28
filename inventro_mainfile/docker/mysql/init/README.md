# 📦 Scripts de Inicialização do Banco de Dados

Este diretório contém todos os scripts SQL que são executados **automaticamente** quando o container MySQL é iniciado pela primeira vez.

## 🗂️ Ordem de Execução

Os arquivos são executados em **ordem alfabética/numérica**, por isso usamos prefixos numéricos:

| Arquivo | Descrição | Obrigatório |
|---------|-----------|-------------|
| `01-schema.sql` | Criação de todas as tabelas do sistema | ✅ Sim |
| `02-base-data.sql` | Dados essenciais: países, moedas, admin, menus | ✅ Sim |
| `03-language-base.sql` | Frases do sistema em inglês | ✅ Sim |
| `04-traducoes-portugues.sql` | Traduções para PT-BR | ✅ Sim |
| `05-adaptacoes-brasil.sql` | Campos CPF/CNPJ, moeda Real, timezone BR | ✅ Sim |
| `06-seed-mercadinho.sql` | Dados de teste (categorias, produtos, clientes) | ⚠️ Recomendado |

## 🚀 Como Funciona

1. **Docker Compose** monta este diretório em `/docker-entrypoint-initdb.d/` no container MySQL
2. O MySQL executa automaticamente todos os arquivos `.sql` em ordem alfabética
3. Os scripts só são executados na **primeira inicialização** (quando o volume está vazio)

## ⚙️ Configuração

O `docker-compose.yml` mapeia este diretório:

```yaml
volumes:
  - ./docker/mysql/init:/docker-entrypoint-initdb.d
```

## 🔄 Resetando o Banco

Para executar os scripts novamente, você precisa **apagar o volume**:

```bash
# Para e remove containers + volumes
docker-compose down -v

# Reinicia tudo do zero
docker-compose up -d --build
```

## 📊 Dados de Teste Incluídos

O arquivo `06-seed-mercadinho.sql` inclui:

- **10 Categorias**: Bebidas, Alimentos, Frutas, Laticínios, Carnes, etc.
- **8 Fornecedores**: Com CNPJ/CPF, endereço, cidade, estado
- **10 Clientes**: Mistura de PF e PJ
- **34 Produtos**: Refrigerantes, arroz, feijão, leite, carne, etc.
- **4 Funções**: Gerente, Caixa, Estoquista, Vendedor
- **5 Usuários**: Admin + 4 funcionários

## 🔐 Credenciais Padrão

| Usuário | E-mail | Senha |
|---------|--------|-------|
| **Admin** | admin@admin.com | 12345678 |
| Gerente | gerente@mercadinho.com | 12345678 |
| Caixa | caixa@mercadinho.com | 12345678 |
| Estoquista | estoque@mercadinho.com | 12345678 |
| Vendedor | vendedor@mercadinho.com | 12345678 |

## 🛠️ Personalizando

### Adicionar Novos Scripts

1. Crie um novo arquivo `.sql` com prefixo numérico (ex: `07-meus-dados.sql`)
2. Certifique-se de incluir no início:
   ```sql
   SET sql_mode = '';
   SET NAMES utf8mb4;
   USE inventro_db;
   ```
3. Delete o volume e reinicie:
   ```bash
   docker-compose down -v && docker-compose up -d --build
   ```

### Desabilitar Seed de Teste

Se não quiser os dados de teste, renomeie o arquivo:

```bash
mv 06-seed-mercadinho.sql 06-seed-mercadinho.sql.disabled
```

## 📝 Notas Importantes

1. **Encoding**: Todos os arquivos usam UTF-8 com suporte a caracteres especiais (acentos)
2. **Idempotência**: Os scripts usam `ON DUPLICATE KEY UPDATE` e `IF NOT EXISTS` quando possível
3. **Ordem Importa**: O schema (01) deve vir antes dos dados (02+)
4. **Volume Persistente**: Os dados são mantidos mesmo após parar os containers

## 🐛 Troubleshooting

### Scripts não executam
- Verifique se o volume existe: `docker volume ls | grep inventro`
- Se existir, delete: `docker-compose down -v`

### Erro de encoding
- Certifique-se de salvar os arquivos como UTF-8
- Verifique se `SET NAMES utf8mb4` está no início

### Erro de sintaxe
- Conecte no MySQL e teste o script manualmente:
  ```bash
  docker exec -i inventro_db mysql -u inventro_user -pinventro_password inventro_db < arquivo.sql
  ```
