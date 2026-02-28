<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cardápio Digital - Controller Público
 */
class Cardapio extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url', 'html']);
        $this->load->model('delivery/delivery_model');
    }

    public function index() {
        $data['loja'] = $this->db->get('setting')->row();
        $data['categorias'] = $this->db->get('category_tbl')->result();

        // Produtos com imagem (join picture_tbl) e filtro disponibilidade
        $this->db->select('p.*, c.name as category_name, pu.unit_name, pic.picture as picture');
        $this->db->from('product_tbl p');
        $this->db->join('category_tbl c', 'p.category_id = c.id', 'left');
        $this->db->join('product_unit pu', 'p.unit = pu.id', 'left');
        $this->db->join('picture_tbl pic', "pic.from_id = p.product_id AND pic.picture_type = 'product'", 'left');
        $this->db->where('p.status', 1);
        $this->db->where('p.disponivel_cardapio', 1);
        $this->db->order_by('p.ordem_exibicao', 'ASC');
        $this->db->order_by('c.name', 'ASC');
        $this->db->order_by('p.name', 'ASC');
        $data['produtos'] = $this->db->get()->result();

        $data['produtos_por_categoria'] = [];
        foreach ($data['produtos'] as $produto) {
            $cat = $produto->category_name ?? 'Outros';
            if (!isset($data['produtos_por_categoria'][$cat])) {
                $data['produtos_por_categoria'][$cat] = [];
            }
            $data['produtos_por_categoria'][$cat][] = $produto;
        }

        // Configurações do delivery
        $config = $this->delivery_model->get_config();
        $data['config'] = $config;
        $data['taxa_entrega'] = (float)($config['taxa_entrega'] ?? 0);
        $data['pedido_minimo'] = (float)($config['pedido_minimo'] ?? 0);
        $data['horario_abertura'] = $config['horario_abertura'] ?? '08:00';
        $data['horario_fechamento'] = $config['horario_fechamento'] ?? '22:00';

        // Zonas de entrega ativas
        $data['zonas'] = $this->delivery_model->get_zones(true);

        // Verificar se loja está aberta
        $data['loja_aberta'] = $this->_verificar_loja_aberta($config);

        // Usar whatsapp_numero da config, fallback para phone da loja
        $whatsapp_raw = !empty($config['whatsapp_numero']) ? $config['whatsapp_numero'] : ($data['loja']->phone ?? '');
        $data['whatsapp'] = preg_replace('/[^0-9]/', '', $whatsapp_raw);

        $this->load->view('cardapio/cardapio_view', $data);
    }

    /**
     * API para buscar cliente por telefone
     * Retorna dados se encontrar, ou vazio se não encontrar
     */
    public function api_buscar_cliente() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        $telefone = $this->input->get('telefone', true);
        
        if (empty($telefone)) {
            echo json_encode(['found' => false]);
            return;
        }
        
        // Limpar telefone para busca (remover formatação)
        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        
        // Buscar cliente pelo telefone - remover formatação de ambos os lados
        // Usamos REPLACE para remover caracteres especiais do campo mobile no MySQL
        $this->db->select('id, name, mobile, address, cpf, cep, cidade, estado');
        $this->db->from('customer_tbl');
        $this->db->where('status', 1);
        
        // Comparar telefone limpo — escape_like_str previne SQL Injection
        $telefone_escaped = $this->db->escape_like_str($telefone_limpo);
        $this->db->where("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(mobile, '(', ''), ')', ''), '-', ''), ' ', ''), '+', '') LIKE '%" . $telefone_escaped . "%'", NULL, FALSE);
        
        $cliente = $this->db->get()->row();
        
        if ($cliente) {
            // Montar endereço completo
            $endereco_completo = $cliente->address;
            if (!empty($cliente->cidade)) {
                $endereco_completo .= ' - ' . $cliente->cidade;
            }
            if (!empty($cliente->estado)) {
                $endereco_completo .= '/' . $cliente->estado;
            }
            
            echo json_encode([
                'found' => true,
                'cliente' => [
                    'id' => $cliente->id,
                    'nome' => $cliente->name,
                    'telefone' => $cliente->mobile,
                    'endereco' => $endereco_completo,
                    'cpf' => $cliente->cpf ?? '',
                    'cep' => $cliente->cep ?? ''
                ]
            ]);
        } else {
            echo json_encode(['found' => false]);
        }
    }

    public function processar_pedido() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método não permitido']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        // Sanitizar inputs de texto (XSS)
        $input['cliente_nome'] = strip_tags(trim($input['cliente_nome'] ?? ''));
        $input['cliente_telefone'] = preg_replace('/[^0-9()\-\s+]/', '', $input['cliente_telefone'] ?? '');
        $input['cliente_endereco'] = strip_tags(trim($input['cliente_endereco'] ?? ''));
        $input['cliente_complemento'] = strip_tags(trim($input['cliente_complemento'] ?? ''));
        $input['observacao'] = strip_tags(trim($input['observacao'] ?? ''));

        $required = ['cliente_nome', 'cliente_telefone', 'forma_pagamento', 'items'];
        // Endereço obrigatório apenas para entrega (não para retirada)
        $tipo_entrega_input = ($input['tipo_entrega'] ?? 'entrega') === 'retirada' ? 'retirada' : 'entrega';
        if ($tipo_entrega_input === 'entrega') {
            $required[] = 'cliente_endereco';
        }
        foreach ($required as $field) {
            if (empty($input[$field])) {
                echo json_encode(['success' => false, 'message' => "Campo obrigatório: " . html_escape($field)]);
                return;
            }
        }

        if (!is_array($input['items']) || count($input['items']) == 0) {
            echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
            return;
        }

        // Validar forma de pagamento
        $formas_validas = ['dinheiro', 'cartao', 'pix'];
        if (!in_array($input['forma_pagamento'], $formas_validas)) {
            echo json_encode(['success' => false, 'message' => 'Forma de pagamento inválida']);
            return;
        }

        // Buscar config e validar
        $config = $this->delivery_model->get_config();

        // Verificar se a loja está aberta
        if (!$this->_verificar_loja_aberta($config)) {
            echo json_encode(['success' => false, 'message' => 'A loja está fechada no momento']);
            return;
        }

        // Calcular taxa pela zona (se informada) ou usar taxa fixa
        $zona_id = isset($input['zona_id']) ? (int)$input['zona_id'] : null;
        $zona = null;
        $taxa_entrega = 0;

        if ($zona_id) {
            $zona = $this->delivery_model->get_zone($zona_id);
            if ($zona && $zona->ativo) {
                $taxa_entrega = (float)$zona->taxa;
            } else {
                echo json_encode(['success' => false, 'message' => 'Zona de entrega inválida']);
                return;
            }
        } else {
            $taxa_entrega = (float)($config['taxa_entrega'] ?? 0);
        }

        $subtotal = 0;
        $items_to_save = [];
        foreach ($input['items'] as $item) {
            $qty = max(1, (int)$item['qty']);
            $price = (float)$item['price'];
            $item_total = $price * $qty;
            $subtotal += $item_total;

            $items_to_save[] = [
                'product_id' => (int)$item['id'],
                'product_name' => strip_tags($item['name'] ?? ''),
                'quantity' => $qty,
                'unit_price' => $price,
                'total_price' => $item_total
            ];
        }

        // Validar pedido mínimo
        $pedido_minimo = (float)($config['pedido_minimo'] ?? 0);
        if ($pedido_minimo > 0 && $subtotal < $pedido_minimo) {
            $falta = number_format($pedido_minimo - $subtotal, 2, ',', '.');
            echo json_encode([
                'success' => false,
                'message' => "Pedido mínimo R$ " . number_format($pedido_minimo, 2, ',', '.') . " — falta R$ {$falta}"
            ]);
            return;
        }

        // Tipo de entrega (entrega ou retirada)
        $tipo_entrega = ($input['tipo_entrega'] ?? 'entrega') === 'retirada' ? 'retirada' : 'entrega';
        if ($tipo_entrega === 'retirada') {
            $taxa_entrega = 0;
        }

        $total = $subtotal + $taxa_entrega;

        // Processar CPF na nota (limpar formatação)
        $cpf_nota = null;
        if (!empty($input['cpf_nota'])) {
            $cpf_nota = preg_replace('/[^0-9]/', '', $input['cpf_nota']);
            // Formatar CPF: 000.000.000-00
            if (strlen($cpf_nota) == 11) {
                $cpf_nota = substr($cpf_nota, 0, 3) . '.' . 
                           substr($cpf_nota, 3, 3) . '.' . 
                           substr($cpf_nota, 6, 3) . '-' . 
                           substr($cpf_nota, 9, 2);
            }
        }

        $order_data = [
            'cliente_nome' => $input['cliente_nome'],
            'cliente_telefone' => $input['cliente_telefone'],
            'cliente_endereco' => $input['cliente_endereco'],
            'cliente_complemento' => $input['cliente_complemento'] ?: null,
            'zona_id' => $zona ? (int)$zona->id : null,
            'zona_nome' => $zona ? $zona->nome : null,
            'subtotal' => $subtotal,
            'taxa_entrega' => $taxa_entrega,
            'desconto' => 0,
            'total' => $total,
            'forma_pagamento' => $input['forma_pagamento'],
            'troco_para' => ($input['forma_pagamento'] == 'dinheiro' && !empty($input['troco_para']))
                            ? (float)str_replace([',', 'R$', ' '], ['.', '', ''], $input['troco_para'])
                            : null,
            'tipo_checkout' => $input['tipo_checkout'] ?? 'site',
            'tipo_entrega' => $tipo_entrega,
            'status' => 'pendente',
            'observacao' => $input['observacao'] ?: null,
            'cpf_nota' => $cpf_nota
        ];

        $result = $this->delivery_model->create_order($order_data, $items_to_save);

        if ($result) {
            echo json_encode([
                'success' => true,
                'order_id' => $result['order_id'],
                'order_number' => $result['order_number'],
                'total' => $total,
                'message' => 'Pedido realizado com sucesso!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao processar pedido']);
        }
    }

    public function confirmacao($order_number = null) {
        if (!$order_number) {
            redirect('cardapio');
            return;
        }

        $data['order'] = $this->delivery_model->get_order_by_number($order_number);
        
        if (!$data['order']) {
            redirect('cardapio');
            return;
        }

        $data['loja'] = $this->db->get('setting')->row();
        $data['whatsapp'] = preg_replace('/[^0-9]/', '', $data['loja']->phone ?? '');
        
        $this->load->view('cardapio/confirmacao_view', $data);
    }

    /**
     * Cupom fiscal para impressão térmica
     */
    public function cupom($order_number = null) {
        if (!$order_number) {
            redirect('cardapio');
            return;
        }

        $data['order'] = $this->delivery_model->get_order_by_number($order_number);
        
        if (!$data['order']) {
            redirect('cardapio');
            return;
        }

        $data['loja'] = $this->db->get('setting')->row();
        
        $this->load->view('cardapio/cupom_view', $data);
    }

    public function api_produtos() {
        header('Content-Type: application/json');

        $this->db->select('p.id, p.product_id, p.name, p.price, p.description, c.name as category_name, pu.unit_name');
        $this->db->from('product_tbl p');
        $this->db->join('category_tbl c', 'p.category_id = c.id', 'left');
        $this->db->join('product_unit pu', 'p.unit = pu.id', 'left');
        $this->db->where('p.status', 1);
        $this->db->where('p.disponivel_cardapio', 1);
        $produtos = $this->db->get()->result();

        echo json_encode(['success' => true, 'produtos' => $produtos]);
    }

    /**
     * Página de acompanhamento de pedido em tempo real
     */
    public function acompanhar($order_number = null) {
        if (!$order_number) {
            redirect('cardapio');
            return;
        }

        $data['order'] = $this->delivery_model->get_order_by_number($order_number);

        if (!$data['order']) {
            $data['erro'] = 'Pedido não encontrado';
            $data['loja'] = $this->db->get('setting')->row();
            $this->load->view('cardapio/acompanhar_view', $data);
            return;
        }

        $data['loja'] = $this->db->get('setting')->row();
        $this->load->view('cardapio/acompanhar_view', $data);
    }

    /**
     * API JSON para polling do status do pedido (usado pelo acompanhamento)
     */
    public function api_status($order_number = null) {
        header('Content-Type: application/json');

        if (!$order_number) {
            echo json_encode(['success' => false, 'message' => 'Número do pedido obrigatório']);
            return;
        }

        $order = $this->delivery_model->get_order_by_number($order_number);

        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Pedido não encontrado']);
            return;
        }

        echo json_encode([
            'success' => true,
            'status' => $order->status,
            'hora_confirmado' => $order->hora_confirmado ?? null,
            'hora_preparando' => $order->hora_preparando ?? null,
            'hora_saiu_entrega' => $order->hora_saiu_entrega ?? null,
            'hora_entregue' => $order->hora_entregue ?? null,
            'tipo_entrega' => $order->tipo_entrega ?? 'entrega',
            'zona_nome' => $order->zona_nome,
            'updated_at' => $order->updated_at
        ]);
    }

    /**
     * Página de avaliação pós-entrega
     */
    public function avaliar($order_number = null) {
        if (!$order_number) {
            redirect('cardapio');
            return;
        }

        $order = $this->delivery_model->get_order_by_number($order_number);

        if (!$order) {
            redirect('cardapio');
            return;
        }

        // Se já foi avaliado, mostrar a avaliação existente
        if (!empty($order->avaliacao_nota)) {
            $data['ja_avaliou'] = true;
        }

        // Processar envio de avaliação
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nota = (int)$this->input->post('nota', TRUE);
            $comentario = $this->input->post('comentario', TRUE);

            if ($nota >= 1 && $nota <= 5) {
                $this->db->where('id', $order->id)
                    ->update('orders', [
                        'avaliacao_nota' => $nota,
                        'avaliacao_comentario' => $comentario
                    ]);
                $data['avaliacao_salva'] = true;
            }
        }

        $data['order'] = $order;
        $data['loja'] = $this->db->get('setting')->row();
        $this->load->view('cardapio/avaliar_view', $data);
    }

    /**
     * API para buscar último pedido pelo telefone (repetir pedido)
     */
    public function api_ultimo_pedido() {
        header('Content-Type: application/json');

        $telefone = $this->input->get('tel', TRUE);
        if (empty($telefone)) {
            echo json_encode(['success' => false]);
            return;
        }

        $telefone_limpo = preg_replace('/[^0-9]/', '', $telefone);
        $telefone_escaped = $this->db->escape_like_str($telefone_limpo);

        $this->db->select('o.id, o.order_number, o.created_at');
        $this->db->from('orders o');
        $this->db->where("REPLACE(REPLACE(REPLACE(o.cliente_telefone, '(', ''), ')', ''), '-', '') LIKE '%" . $telefone_escaped . "%'", NULL, FALSE);
        $this->db->where('o.status !=', 'cancelado');
        $this->db->order_by('o.created_at', 'DESC');
        $this->db->limit(1);
        $order = $this->db->get()->row();

        if (!$order) {
            echo json_encode(['success' => false]);
            return;
        }

        $items = $this->db->where('order_id', $order->id)
            ->get('order_items')->result();

        $items_data = [];
        foreach ($items as $item) {
            $items_data[] = [
                'id' => (int)$item->product_id,
                'name' => $item->product_name,
                'price' => (float)$item->unit_price,
                'qty' => (int)$item->quantity
            ];
        }

        echo json_encode([
            'success' => true,
            'order_number' => $order->order_number,
            'items' => $items_data
        ]);
    }

    /**
     * API para validar cupom de desconto
     */
    public function api_validar_cupom() {
        header('Content-Type: application/json');

        $codigo = strtoupper(trim($this->input->get('codigo', TRUE)));
        $subtotal = (float)$this->input->get('subtotal', TRUE);

        if (empty($codigo)) {
            echo json_encode(['valido' => false, 'message' => 'Informe o código do cupom']);
            return;
        }

        $cupom = $this->db->where('codigo', $codigo)
            ->where('ativo', 1)
            ->get('cupons_desconto')->row();

        if (!$cupom) {
            echo json_encode(['valido' => false, 'message' => 'Cupom não encontrado']);
            return;
        }

        $hoje = date('Y-m-d');
        if ($cupom->validade_inicio && $hoje < $cupom->validade_inicio) {
            echo json_encode(['valido' => false, 'message' => 'Cupom ainda não está ativo']);
            return;
        }
        if ($cupom->validade_fim && $hoje > $cupom->validade_fim) {
            echo json_encode(['valido' => false, 'message' => 'Cupom expirado']);
            return;
        }
        if ($cupom->uso_maximo !== null && $cupom->uso_atual >= $cupom->uso_maximo) {
            echo json_encode(['valido' => false, 'message' => 'Cupom esgotado']);
            return;
        }
        if ($subtotal < (float)$cupom->valor_minimo_pedido) {
            $minimo = number_format($cupom->valor_minimo_pedido, 2, ',', '.');
            echo json_encode(['valido' => false, 'message' => "Pedido mínimo R$ {$minimo} para este cupom"]);
            return;
        }

        // Calcular desconto
        $desconto = 0;
        switch ($cupom->tipo) {
            case 'percentual':
                $desconto = $subtotal * ((float)$cupom->valor / 100);
                break;
            case 'valor_fixo':
                $desconto = (float)$cupom->valor;
                break;
            case 'frete_gratis':
                $desconto = 0; // desconto na taxa, não no subtotal
                break;
        }

        echo json_encode([
            'valido' => true,
            'tipo' => $cupom->tipo,
            'valor' => (float)$cupom->valor,
            'desconto_calculado' => round($desconto, 2),
            'message' => 'Cupom aplicado!'
        ]);
    }

    /**
     * PWA Manifest (JSON dinamico com dados da loja)
     */
    public function manifest() {
        $loja = $this->db->get('setting')->row();
        header('Content-Type: application/manifest+json');
        echo json_encode([
            'name' => $loja->title ?? 'Cardapio Digital',
            'short_name' => mb_substr($loja->title ?? 'Cardapio', 0, 12),
            'start_url' => base_url('cardapio'),
            'display' => 'standalone',
            'background_color' => '#1a1a2e',
            'theme_color' => '#25D366',
            'orientation' => 'portrait-primary',
            'icons' => [
                ['src' => base_url('admin_assets/dist/img/AdminLTELogo.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                ['src' => base_url('admin_assets/dist/img/AdminLTELogo.png'), 'sizes' => '512x512', 'type' => 'image/png']
            ]
        ]);
    }

    /**
     * Service Worker JS
     */
    public function service_worker() {
        header('Content-Type: application/javascript');
        header('Service-Worker-Allowed: /');
        $base_url = base_url();
        echo "const CACHE_NAME = 'cardapio-v1';
const BASE_URL = '{$base_url}';
const urlsToCache = [
    BASE_URL + 'cardapio',
    BASE_URL + 'cardapio/offline',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
];

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.addAll(urlsToCache);
        })
    );
});

self.addEventListener('fetch', function(event) {
    if (event.request.method !== 'GET') return;
    event.respondWith(
        fetch(event.request).then(function(response) {
            if (response.ok) {
                var responseClone = response.clone();
                caches.open(CACHE_NAME).then(function(cache) {
                    cache.put(event.request, responseClone);
                });
            }
            return response;
        }).catch(function() {
            return caches.match(event.request).then(function(response) {
                return response || caches.match(BASE_URL + 'cardapio/offline');
            });
        })
    );
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(name) { return name !== CACHE_NAME; })
                    .map(function(name) { return caches.delete(name); })
            );
        })
    );
});";
    }

    /**
     * Pagina offline para PWA
     */
    public function offline() {
        $data['loja'] = $this->db->get('setting')->row();
        $this->load->view('cardapio/offline_view', $data);
    }

    // =========================================
    // Métodos privados
    // =========================================

    /**
     * Verifica se a loja está aberta (horário + dia + pausa)
     */
    private function _verificar_loja_aberta($config) {
        // Loja pausada manualmente?
        if (($config['loja_pausada'] ?? '0') === '1') {
            return false;
        }

        // Verificar dia da semana (0=dom, 1=seg, ..., 6=sab)
        $dia_atual = (int)date('w');
        $dias_funcionamento = $config['dias_funcionamento'] ?? '1,2,3,4,5,6';
        $dias_array = array_map('intval', explode(',', $dias_funcionamento));

        if (!in_array($dia_atual, $dias_array)) {
            return false;
        }

        // Verificar horário
        $hora_atual = date('H:i');
        $abertura = $config['horario_abertura'] ?? '08:00';
        $fechamento = $config['horario_fechamento'] ?? '22:00';

        if ($hora_atual < $abertura || $hora_atual >= $fechamento) {
            return false;
        }

        return true;
    }
}
