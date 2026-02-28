# Inventro - Sistema de Gestao para Mercadinhos

Sistema de gestao de estoque + cardapio digital (estilo iFood) para mercadinhos brasileiros.
Construido com CodeIgniter 3.x (HMVC) + AdminLTE 3 + jQuery.

## Requisitos

- Docker + Docker Compose
- (Ou: PHP 7.4, MySQL 8.0, Apache 2.4)

## Instalacao com Docker

```bash
cd inventro_mainfile
cp .env.example .env          # Edite as senhas se quiser
docker-compose --profile dev up -d --build
```

Aguarde o MySQL inicializar (~30s). Os seeds rodam automaticamente na primeira vez.

## URLs de Acesso

| Pagina | URL | Descricao |
|--------|-----|-----------|
| **Admin (Login)** | http://localhost:8080/login | Painel administrativo |
| **Cardapio Digital** | http://localhost:8080/cardapio | Cardapio publico (cliente) |
| **Kanban Pedidos** | http://localhost:8080/delivery/orders/kanban | Painel de pedidos em tempo real |
| **Lista de Pedidos** | http://localhost:8080/delivery/orders | Lista de pedidos online |
| **Entregadores** | http://localhost:8080/delivery/entregadores | CRUD de entregadores |
| **Cupons** | http://localhost:8080/delivery/cupons | CRUD de cupons de desconto |
| **Config Delivery** | http://localhost:8080/delivery/config | Config do cardapio/delivery |
| **Zonas de Entrega** | http://localhost:8080/delivery/zones | Zonas e taxas de entrega |
| **PDV (Ponto de Venda)** | http://localhost:8080/invoice/pos | Caixa / PDV |
| **phpMyAdmin** | http://localhost:8081 | Gerenciador do banco de dados |

## Logins de Teste

Todos os usuarios usam a senha: **`12345678`**

| Usuario | Email | Perfil | Acesso |
|---------|-------|--------|--------|
| Admin Sistema | `admin@admin.com` | Administrador | Acesso total a todos os modulos |
| Carlos Gerente | `gerente@mercadinho.com` | Gerente | (Sem role atribuida - apenas admin funciona) |
| Ana Caixa | `caixa@mercadinho.com` | Caixa | (Sem role atribuida - apenas admin funciona) |
| Pedro Estoquista | `estoque@mercadinho.com` | Estoquista | (Sem role atribuida - apenas admin funciona) |
| Maria Vendedora | `vendedor@mercadinho.com` | Vendedora | (Sem role atribuida - apenas admin funciona) |

> **Nota:** Apenas o usuario `admin@admin.com` tem role atribuida (Administrador). Os demais foram criados nas seeds mas nao tiveram roles vinculadas. Para usar, atribua roles via Admin > Usuarios.

## Dados de Teste (Seeds)

### Loja
- **Nome:** Mercadinho do Bairro
- **Telefone:** 11999998888
- **Endereco:** Rua Principal, 100 - Centro - Sao Paulo/SP
- **Email:** contato@mercadinhodobairro.com.br

### Produtos (exemplos)
| Produto | Preco |
|---------|-------|
| Coca-Cola 2L | R$ 10,99 |
| Arroz Tipo 1 5kg | R$ 24,90 |
| Feijao Carioca 1kg | R$ 8,99 |
| Agua Mineral 500ml | R$ 2,50 |

Total: ~50 produtos cadastrados em 10 categorias (Bebidas, Graos, Laticinios, Carnes, Higiene, etc.)

### Clientes (10 cadastrados)
| Cliente | Telefone |
|---------|----------|
| Maria das Gracas Silva | (11) 98765-1111 |
| Joao Carlos Santos | (11) 98765-2222 |
| Restaurante Sabor Caseiro | (11) 98765-4444 |
| Bar do Ze | (11) 98765-5555 |

### Fornecedores (8 cadastrados)
- Distribuidora Bebidas Brasil
- Atacadao Alimentos Ltda
- Hortifruti Sao Paulo
- Laticinios Vale Verde
- Frigorifico Boi Gordo

### Zonas de Entrega (10 bairros)
| Zona | Taxa | Tempo |
|------|------|-------|
| Centro | Gratis | 15-25 min |
| Vila Maria | R$ 5,00 | 20-35 min |
| Pinheiros | R$ 6,00 | 25-40 min |
| Mooca | R$ 5,00 | 20-35 min |
| Santana | R$ 7,00 | 30-45 min |
| Bela Vista | R$ 4,00 | 15-30 min |

### Configuracoes do Cardapio
| Config | Valor |
|--------|-------|
| Horario | 08:00 - 22:00 |
| Dias | Seg a Sab |
| Taxa fixa padrao | R$ 8,00 |
| Pedido minimo | R$ 0,00 |
| Tempo medio | 45 min |

### Pedidos de Teste
Existem 6 pedidos de teste ja cadastrados (status: pendente).

## Banco de Dados

- **Host:** db (interno Docker)
- **Database:** inventro_db
- **Usuario:** inventro_user
- **Senha:** inventro_password (ou o valor em .env)
- **Root password:** inventro_root_password (ou o valor em .env)

### Acesso via terminal
```bash
docker exec -it inventro_db mysql -u inventro_user -pinventro_password inventro_db
```

### Seeds (ordem de execucao)
```
docker/mysql/init/
  01-schema.sql          # Estrutura das tabelas
  02-base-data.sql       # Dados base (admin, config, moeda)
  03-language-base.sql   # Traducoes base (ingles)
  04-traducoes-portugues.sql  # Traducoes PT-BR
  05-adaptacoes-brasil.sql    # Adaptacoes fiscais Brasil
  06-seed-mercadinho.sql      # Dados de teste (produtos, clientes, fornecedores)
  07-delivery-orders.sql      # Modulo delivery + zonas + pedidos
  08-modulo-financeiro.sql    # Modulo financeiro
  09-ifood-local.sql          # Features iFood (entregadores, cupons, kanban, etc.)
```

> **Para resetar tudo:** `docker-compose down -v && docker-compose --profile dev up -d --build`

## Comandos Uteis

```bash
# Iniciar
docker-compose --profile dev up -d

# Parar
docker-compose down

# Logs
docker-compose logs -f

# Shell do PHP
docker exec -it inventro_app bash

# Rodar migration manualmente (se ja tinha dados)
docker exec -i inventro_db mysql -u inventro_user -pinventro_password inventro_db < docker/mysql/init/09-ifood-local.sql
```

## Modulos Principais

| Modulo | Descricao |
|--------|-----------|
| `invoice` | PDV, faturas, vendas |
| `item` | Produtos e categorias |
| `stock` | Controle de estoque |
| `purchase` | Compras |
| `customer` | Clientes |
| `supplier` | Fornecedores |
| `delivery` | Pedidos online, kanban, entregadores, cupons, zonas |
| `financeiro` | Contas a pagar/receber |
| `accounts` | Plano de contas |
| `hrm` | RH, funcionarios, folha |
| `report` | Relatorios |
| `dashboard` | Auth, usuarios, config |

## Licenca

Todos os direitos reservados.
