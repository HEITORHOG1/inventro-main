<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Swagger UI — Documentação interativa da API v1
 *
 * Disponível apenas em ambiente de desenvolvimento.
 * Acesso: /api/docs
 */
class V1_docs extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Bloquear em produção
        if (ENVIRONMENT === 'production') {
            show_404();
        }
    }

    /**
     * GET /api/docs
     *
     * Renderiza o Swagger UI carregando a spec OpenAPI.
     */
    public function index() {
        $spec_url = base_url('api/docs/openapi.json');

        echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventro API v1 — Documentação</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css" >
    <style>
        html { box-sizing: border-box; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
        .topbar { display: none !important; }
        .swagger-ui .info .title { font-size: 2em; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
    window.onload = function() {
        SwaggerUIBundle({
            url: "' . $spec_url . '",
            dom_id: "#swagger-ui",
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "StandaloneLayout",
            tryItOutEnabled: true,
            persistAuthorization: true
        });
    };
    </script>
</body>
</html>';
    }

    /**
     * GET /api/docs/openapi.json
     *
     * Retorna a spec OpenAPI 3.0 dinamicamente com base_url correto.
     */
    public function spec() {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');

        $base = rtrim(base_url(), '/');

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title'       => 'Inventro API v1',
                'description' => "API REST para os apps nativos Android/iOS do Inventro.\n\n**Dois apps:**\n- **App Cardápio** — cliente final faz pedidos (endpoints públicos)\n- **App Entregador** — motoboy gerencia entregas (requer JWT)\n\n**Autenticação:** Endpoints do motoboy exigem `Authorization: Bearer {access_token}`. Obtenha tokens via `/api/v1/motoboy/auth/login`.",
                'version'     => '1.0.0',
                'contact'     => ['name' => 'Inventro Dev Team']
            ],
            'servers' => [
                ['url' => $base, 'description' => 'Servidor atual']
            ],
            'tags' => [
                ['name' => 'Cardápio', 'description' => 'Endpoints públicos do cardápio digital'],
                ['name' => 'Motoboy — Auth', 'description' => 'Login, refresh e logout do entregador'],
                ['name' => 'Motoboy — Entregas', 'description' => 'Pool, aceitar, coletar, entregar'],
                ['name' => 'Motoboy — Perfil', 'description' => 'Perfil, histórico e ganhos']
            ],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type'         => 'http',
                        'scheme'       => 'bearer',
                        'bearerFormat' => 'JWT',
                        'description'  => 'Access token obtido via POST /api/v1/motoboy/auth/login'
                    ]
                ],
                'schemas' => $this->_get_schemas()
            ],
            'paths' => $this->_get_paths()
        ];

        echo json_encode($spec, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    // =========================================
    // Schemas (objetos de dados)
    // =========================================

    private function _get_schemas() {
        return [
            'Produto' => [
                'type' => 'object',
                'properties' => [
                    'id'            => ['type' => 'integer', 'example' => 1],
                    'product_id'    => ['type' => 'string', 'example' => 'PRD001'],
                    'name'          => ['type' => 'string', 'example' => 'Arroz 5kg'],
                    'price'         => ['type' => 'number', 'format' => 'float', 'example' => 25.90],
                    'description'   => ['type' => 'string', 'example' => 'Arroz tipo 1'],
                    'category_id'   => ['type' => 'integer', 'example' => 1],
                    'category_name' => ['type' => 'string', 'example' => 'Grãos'],
                    'unit_name'     => ['type' => 'string', 'example' => 'un'],
                    'picture'       => ['type' => 'string', 'nullable' => true, 'example' => 'http://localhost:8080/img/product/2026-03-08/arroz.jpg']
                ]
            ],
            'Zona' => [
                'type' => 'object',
                'properties' => [
                    'id'        => ['type' => 'integer', 'example' => 1],
                    'nome'      => ['type' => 'string', 'example' => 'Centro'],
                    'taxa'      => ['type' => 'number', 'format' => 'float', 'example' => 5.00],
                    'tempo_min' => ['type' => 'integer', 'example' => 20],
                    'tempo_max' => ['type' => 'integer', 'example' => 40]
                ]
            ],
            'PedidoInput' => [
                'type' => 'object',
                'required' => ['cliente_nome', 'cliente_telefone', 'forma_pagamento', 'items'],
                'properties' => [
                    'cliente_nome'        => ['type' => 'string', 'example' => 'João Silva'],
                    'cliente_telefone'    => ['type' => 'string', 'example' => '11999999999'],
                    'cliente_endereco'    => ['type' => 'string', 'example' => 'Rua das Flores, 123'],
                    'cliente_complemento' => ['type' => 'string', 'example' => 'Apto 4B'],
                    'cliente_cep'         => ['type' => 'string', 'example' => '01001-000'],
                    'cliente_cidade'      => ['type' => 'string', 'example' => 'São Paulo'],
                    'cliente_estado'      => ['type' => 'string', 'example' => 'SP'],
                    'cpf_nota'            => ['type' => 'string', 'example' => '123.456.789-00'],
                    'zona_id'             => ['type' => 'integer', 'example' => 1],
                    'forma_pagamento'     => ['type' => 'string', 'enum' => ['dinheiro', 'cartao', 'pix']],
                    'tipo_entrega'        => ['type' => 'string', 'enum' => ['entrega', 'retirada'], 'default' => 'entrega'],
                    'troco_para'          => ['type' => 'number', 'format' => 'float', 'example' => 50.00],
                    'observacao'          => ['type' => 'string', 'example' => 'Sem cebola'],
                    'cupom_codigo'        => ['type' => 'string', 'example' => 'DESC10'],
                    'items' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'required' => ['id', 'name', 'price', 'qty'],
                            'properties' => [
                                'id'    => ['type' => 'integer', 'example' => 1],
                                'name'  => ['type' => 'string', 'example' => 'Arroz 5kg'],
                                'price' => ['type' => 'number', 'format' => 'float', 'example' => 25.90],
                                'qty'   => ['type' => 'integer', 'example' => 2]
                            ]
                        ]
                    ]
                ]
            ],
            'StatusPedido' => [
                'type' => 'object',
                'properties' => [
                    'order_number'       => ['type' => 'string', 'example' => '0001'],
                    'status'             => ['type' => 'string', 'enum' => ['pendente', 'confirmado', 'preparando', 'pronto_coleta', 'saiu_entrega', 'entregue', 'cancelado']],
                    'tipo_entrega'       => ['type' => 'string', 'enum' => ['entrega', 'retirada']],
                    'zona_nome'          => ['type' => 'string', 'nullable' => true],
                    'entregador_nome'    => ['type' => 'string', 'nullable' => true],
                    'hora_confirmado'    => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                    'hora_preparando'    => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                    'hora_pronto_coleta' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                    'hora_saiu_entrega'  => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                    'hora_entregue'      => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                    'updated_at'         => ['type' => 'string', 'format' => 'date-time']
                ]
            ],
            'LoginInput' => [
                'type' => 'object',
                'required' => ['telefone', 'senha'],
                'properties' => [
                    'telefone' => ['type' => 'string', 'example' => '11999999999'],
                    'senha'    => ['type' => 'string', 'example' => 'minhasenha123']
                ]
            ],
            'TokenResponse' => [
                'type' => 'object',
                'properties' => [
                    'success'       => ['type' => 'boolean', 'example' => true],
                    'access_token'  => ['type' => 'string', 'example' => 'eyJhbGciOiJIUzI1NiJ9...'],
                    'refresh_token' => ['type' => 'string', 'example' => 'a1b2c3d4e5f6...'],
                    'expires_in'    => ['type' => 'integer', 'example' => 3600],
                    'token_type'    => ['type' => 'string', 'example' => 'Bearer']
                ]
            ],
            'Motoboy' => [
                'type' => 'object',
                'properties' => [
                    'id'               => ['type' => 'integer', 'example' => 1],
                    'nome'             => ['type' => 'string', 'example' => 'Carlos'],
                    'telefone'         => ['type' => 'string', 'example' => '11999999999'],
                    'status'           => ['type' => 'string', 'enum' => ['disponivel', 'em_entrega']],
                    'taxa_por_entrega' => ['type' => 'number', 'format' => 'float', 'example' => 5.00],
                    'total_entregas'   => ['type' => 'integer', 'example' => 42],
                    'total_ganhos'     => ['type' => 'number', 'format' => 'float', 'example' => 210.00]
                ]
            ],
            'PoolOrder' => [
                'type' => 'object',
                'properties' => [
                    'id'               => ['type' => 'integer'],
                    'order_number'     => ['type' => 'string', 'example' => '0015'],
                    'cliente_nome'     => ['type' => 'string'],
                    'cliente_endereco' => ['type' => 'string'],
                    'zona_nome'        => ['type' => 'string'],
                    'total'            => ['type' => 'number', 'format' => 'float'],
                    'forma_pagamento'  => ['type' => 'string'],
                    'troco_para'       => ['type' => 'number', 'nullable' => true],
                    'hora_pronto'      => ['type' => 'string', 'format' => 'date-time'],
                    'created_at'       => ['type' => 'string', 'format' => 'date-time']
                ]
            ],
            'Error' => [
                'type' => 'object',
                'properties' => [
                    'success' => ['type' => 'boolean', 'example' => false],
                    'error'   => ['type' => 'string', 'example' => 'Descrição do erro']
                ]
            ]
        ];
    }

    // =========================================
    // Paths (endpoints)
    // =========================================

    private function _get_paths() {
        return [
            // ========= CARDÁPIO =========
            '/api/v1/cardapio/produtos' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Listar produtos do cardápio',
                    'description' => 'Retorna todos produtos disponíveis com categoria, preço e imagem.',
                    'operationId' => 'getProdutos',
                    'responses'   => [
                        '200' => ['description' => 'Lista de produtos', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['success' => ['type' => 'boolean'], 'produtos' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Produto']], 'count' => ['type' => 'integer']]]]]]
                    ]
                ]
            ],
            '/api/v1/cardapio/categorias' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Listar categorias',
                    'operationId' => 'getCategorias',
                    'responses'   => [
                        '200' => ['description' => 'Lista de categorias']
                    ]
                ]
            ],
            '/api/v1/cardapio/zonas' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Listar zonas de entrega',
                    'description' => 'Retorna zonas ativas com taxa e tempo estimado.',
                    'operationId' => 'getZonas',
                    'responses'   => [
                        '200' => ['description' => 'Lista de zonas', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['success' => ['type' => 'boolean'], 'zonas' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Zona']]]]]]]
                    ]
                ]
            ],
            '/api/v1/cardapio/zona/detectar' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Detectar zona por bairro',
                    'operationId' => 'detectarZona',
                    'parameters'  => [
                        ['name' => 'bairro', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string'], 'example' => 'Centro']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Zona encontrada ou não']
                    ]
                ]
            ],
            '/api/v1/cardapio/config' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Configurações da loja',
                    'description' => 'Horários, pedido mínimo, formas de pagamento, status (aberta/fechada).',
                    'operationId' => 'getConfig',
                    'responses'   => [
                        '200' => ['description' => 'Configurações públicas da loja']
                    ]
                ]
            ],
            '/api/v1/cardapio/pedido' => [
                'post' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Criar pedido',
                    'description' => 'Envia um novo pedido com itens, endereço e pagamento.',
                    'operationId' => 'criarPedido',
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/PedidoInput']]]
                    ],
                    'responses' => [
                        '201' => ['description' => 'Pedido criado com sucesso'],
                        '400' => ['description' => 'Validação falhou', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]]
                    ]
                ]
            ],
            '/api/v1/cardapio/pedido/{order_number}/status' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Status do pedido',
                    'description' => 'Polling: verificar status em tempo real.',
                    'operationId' => 'getStatusPedido',
                    'parameters'  => [
                        ['name' => 'order_number', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'example' => '0001']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Status do pedido', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/StatusPedido']]]],
                        '404' => ['description' => 'Pedido não encontrado']
                    ]
                ]
            ],
            '/api/v1/cardapio/pedido/{order_number}/avaliar' => [
                'post' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Avaliar pedido',
                    'operationId' => 'avaliarPedido',
                    'parameters'  => [
                        ['name' => 'order_number', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]
                    ],
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['nota'], 'properties' => ['nota' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 5], 'comentario' => ['type' => 'string']]]]]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Avaliação registrada'],
                        '400' => ['description' => 'Já avaliado ou nota inválida']
                    ]
                ]
            ],
            '/api/v1/cardapio/cupom/validar' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Validar cupom de desconto',
                    'operationId' => 'validarCupom',
                    'parameters'  => [
                        ['name' => 'codigo', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string'], 'example' => 'DESC10'],
                        ['name' => 'subtotal', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'number'], 'example' => 50.00]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Resultado da validação']
                    ]
                ]
            ],
            '/api/v1/cardapio/cliente/{telefone}' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Buscar cliente por telefone',
                    'operationId' => 'buscarCliente',
                    'parameters'  => [
                        ['name' => 'telefone', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string'], 'example' => '11999999999']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Cliente encontrado ou não']
                    ]
                ]
            ],
            '/api/v1/cardapio/cliente/{telefone}/pedidos' => [
                'get' => [
                    'tags'        => ['Cardápio'],
                    'summary'     => 'Pedidos pendentes do cliente',
                    'operationId' => 'pedidosCliente',
                    'parameters'  => [
                        ['name' => 'telefone', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Lista de pedidos pendentes']
                    ]
                ]
            ],

            // ========= MOTOBOY AUTH =========
            '/api/v1/motoboy/auth/login' => [
                'post' => [
                    'tags'        => ['Motoboy — Auth'],
                    'summary'     => 'Login do entregador',
                    'description' => 'Autentica com telefone + senha e retorna JWT tokens.',
                    'operationId' => 'motoboyLogin',
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/LoginInput']]]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Login bem-sucedido', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/TokenResponse']]]],
                        '401' => ['description' => 'Credenciais inválidas'],
                        '429' => ['description' => 'Muitas tentativas de login']
                    ]
                ]
            ],
            '/api/v1/motoboy/auth/refresh' => [
                'post' => [
                    'tags'        => ['Motoboy — Auth'],
                    'summary'     => 'Renovar access token',
                    'operationId' => 'motoboyRefresh',
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['refresh_token'], 'properties' => ['refresh_token' => ['type' => 'string']]]]]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Novos tokens', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/TokenResponse']]]],
                        '401' => ['description' => 'Refresh token inválido']
                    ]
                ]
            ],
            '/api/v1/motoboy/auth/logout' => [
                'post' => [
                    'tags'        => ['Motoboy — Auth'],
                    'summary'     => 'Logout (revogar tokens)',
                    'operationId' => 'motoboyLogout',
                    'security'    => [['bearerAuth' => []]],
                    'responses'   => [
                        '200' => ['description' => 'Logout realizado'],
                        '401' => ['description' => 'Token inválido']
                    ]
                ]
            ],

            // ========= MOTOBOY PERFIL =========
            '/api/v1/motoboy/perfil' => [
                'get' => [
                    'tags'        => ['Motoboy — Perfil'],
                    'summary'     => 'Dados do entregador logado',
                    'operationId' => 'motoboyPerfil',
                    'security'    => [['bearerAuth' => []]],
                    'responses'   => [
                        '200' => ['description' => 'Dados do motoboy', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['success' => ['type' => 'boolean'], 'motoboy' => ['$ref' => '#/components/schemas/Motoboy']]]]]],
                        '401' => ['description' => 'Não autenticado']
                    ]
                ]
            ],
            '/api/v1/motoboy/ganhos' => [
                'get' => [
                    'tags'        => ['Motoboy — Perfil'],
                    'summary'     => 'Resumo de ganhos (hoje, semana, mês)',
                    'operationId' => 'motoboyGanhos',
                    'security'    => [['bearerAuth' => []]],
                    'responses'   => [
                        '200' => ['description' => 'Resumo de ganhos'],
                        '401' => ['description' => 'Não autenticado']
                    ]
                ]
            ],
            '/api/v1/motoboy/historico' => [
                'get' => [
                    'tags'        => ['Motoboy — Perfil'],
                    'summary'     => 'Histórico de entregas',
                    'operationId' => 'motoboyHistorico',
                    'security'    => [['bearerAuth' => []]],
                    'parameters'  => [
                        ['name' => 'periodo', 'in' => 'query', 'schema' => ['type' => 'string', 'enum' => ['hoje', 'semana', 'mes', 'todos'], 'default' => 'todos']],
                        ['name' => 'limit', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 50, 'maximum' => 100]]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Lista de entregas realizadas'],
                        '401' => ['description' => 'Não autenticado']
                    ]
                ]
            ],

            // ========= MOTOBOY ENTREGAS =========
            '/api/v1/motoboy/pool' => [
                'get' => [
                    'tags'        => ['Motoboy — Entregas'],
                    'summary'     => 'Pool de entregas disponíveis',
                    'description' => 'Pedidos prontos para coleta sem entregador atribuído.',
                    'operationId' => 'motoboyPool',
                    'security'    => [['bearerAuth' => []]],
                    'responses'   => [
                        '200' => ['description' => 'Lista de entregas disponíveis', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'properties' => ['success' => ['type' => 'boolean'], 'pool' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/PoolOrder']], 'count' => ['type' => 'integer']]]]]],
                        '401' => ['description' => 'Não autenticado']
                    ]
                ]
            ],
            '/api/v1/motoboy/entrega/ativa' => [
                'get' => [
                    'tags'        => ['Motoboy — Entregas'],
                    'summary'     => 'Entrega ativa do motoboy',
                    'description' => 'Retorna a entrega em andamento (se houver) com itens e detalhes.',
                    'operationId' => 'motoboyEntregaAtiva',
                    'security'    => [['bearerAuth' => []]],
                    'responses'   => [
                        '200' => ['description' => 'Entrega ativa ou null'],
                        '401' => ['description' => 'Não autenticado']
                    ]
                ]
            ],
            '/api/v1/motoboy/entrega/{id}/aceitar' => [
                'post' => [
                    'tags'        => ['Motoboy — Entregas'],
                    'summary'     => 'Aceitar entrega',
                    'description' => 'Aceita uma entrega do pool (race-condition safe — só o primeiro motoboy a clicar consegue).',
                    'operationId' => 'motoboyAceitar',
                    'security'    => [['bearerAuth' => []]],
                    'parameters'  => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer'], 'description' => 'ID do pedido']
                    ],
                    'responses' => [
                        '200' => ['description' => 'Entrega aceita'],
                        '401' => ['description' => 'Não autenticado'],
                        '409' => ['description' => 'Já aceita por outro entregador ou já tem entrega ativa']
                    ]
                ]
            ],
            '/api/v1/motoboy/entrega/{id}/coletar' => [
                'post' => [
                    'tags'        => ['Motoboy — Entregas'],
                    'summary'     => 'Registrar coleta',
                    'description' => 'Indica que o motoboy pegou a mercadoria na loja.',
                    'operationId' => 'motoboyColetar',
                    'security'    => [['bearerAuth' => []]],
                    'parameters'  => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Coleta registrada'],
                        '400' => ['description' => 'Pedido não encontrado ou não pertence ao motoboy'],
                        '401' => ['description' => 'Não autenticado']
                    ]
                ]
            ],
            '/api/v1/motoboy/entrega/{id}/entregar' => [
                'post' => [
                    'tags'        => ['Motoboy — Entregas'],
                    'summary'     => 'Registrar entrega',
                    'description' => 'Indica que o motoboy entregou ao cliente. Atualiza status, soma ganhos.',
                    'operationId' => 'motoboyEntregar',
                    'security'    => [['bearerAuth' => []]],
                    'parameters'  => [
                        ['name' => 'id', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'integer']]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Entrega concluída + ganho adicionado'],
                        '400' => ['description' => 'Pedido não encontrado ou não pertence ao motoboy'],
                        '401' => ['description' => 'Não autenticado']
                    ]
                ]
            ],
            '/api/v1/motoboy/device' => [
                'post' => [
                    'tags'        => ['Motoboy — Perfil'],
                    'summary'     => 'Registrar device para push notifications',
                    'operationId' => 'motoboyDevice',
                    'security'    => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['fcm_token'], 'properties' => ['fcm_token' => ['type' => 'string', 'description' => 'Firebase Cloud Messaging token'], 'platform' => ['type' => 'string', 'enum' => ['android', 'ios', 'web'], 'default' => 'android']]]]]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Device registrado'],
                        '401' => ['description' => 'Não autenticado']
                    ]
                ]
            ]
        ];
    }
}
