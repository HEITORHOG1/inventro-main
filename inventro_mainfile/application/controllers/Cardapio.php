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
        
        $this->db->select('product_tbl.*, category_tbl.name as category_name, product_unit.unit_name');
        $this->db->from('product_tbl');
        $this->db->join('category_tbl', 'product_tbl.category_id = category_tbl.id', 'left');
        $this->db->join('product_unit', 'product_tbl.unit = product_unit.id', 'left');
        $this->db->where('product_tbl.status', 1);
        $this->db->order_by('category_tbl.name', 'ASC');
        $this->db->order_by('product_tbl.name', 'ASC');
        $data['produtos'] = $this->db->get()->result();
        
        $data['produtos_por_categoria'] = [];
        foreach ($data['produtos'] as $produto) {
            $cat = $produto->category_name ?? 'Outros';
            if (!isset($data['produtos_por_categoria'][$cat])) {
                $data['produtos_por_categoria'][$cat] = [];
            }
            $data['produtos_por_categoria'][$cat][] = $produto;
        }
        
        // Taxa de entrega fixa do banco
        $config = $this->delivery_model->get_config();
        $data['taxa_entrega'] = floatval($config['taxa_entrega'] ?? 0);
        
        $data['whatsapp'] = preg_replace('/[^0-9]/', '', $data['loja']->phone ?? '');
        
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
        
        // Comparar telefone limpo (sem formatação)
        $this->db->where("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(mobile, '(', ''), ')', ''), '-', ''), ' ', ''), '+', '') LIKE '%{$telefone_limpo}%'", NULL, FALSE);
        
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
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Allow-Headers: Content-Type');

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        $required = ['cliente_nome', 'cliente_telefone', 'cliente_endereco', 'forma_pagamento', 'items'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                echo json_encode(['success' => false, 'message' => "Campo obrigatório: $field"]);
                return;
            }
        }

        if (!is_array($input['items']) || count($input['items']) == 0) {
            echo json_encode(['success' => false, 'message' => 'Carrinho vazio']);
            return;
        }

        // Buscar taxa de entrega fixa
        $config = $this->delivery_model->get_config();
        $taxa_entrega = floatval($input['taxa_entrega'] ?? $config['taxa_entrega'] ?? 0);

        $subtotal = 0;
        $items_to_save = [];
        foreach ($input['items'] as $item) {
            $item_total = floatval($item['price']) * intval($item['qty']);
            $subtotal += $item_total;
            
            $items_to_save[] = [
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'quantity' => $item['qty'],
                'unit_price' => $item['price'],
                'total_price' => $item_total
            ];
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
            'cliente_complemento' => $input['cliente_complemento'] ?? null,
            'zona_id' => null,
            'zona_nome' => null,
            'subtotal' => $subtotal,
            'taxa_entrega' => $taxa_entrega,
            'desconto' => 0,
            'total' => $total,
            'forma_pagamento' => $input['forma_pagamento'],
            'troco_para' => ($input['forma_pagamento'] == 'dinheiro' && !empty($input['troco_para'])) 
                            ? floatval(str_replace([',', 'R$', ' '], ['.', '', ''], $input['troco_para'])) 
                            : null,
            'tipo_checkout' => $input['tipo_checkout'] ?? 'site',
            'status' => 'pendente',
            'observacao' => $input['observacao'] ?? null,
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
        header('Access-Control-Allow-Origin: *');
        
        $this->db->select('product_tbl.id, product_tbl.product_id, product_tbl.name, product_tbl.price, product_tbl.description, category_tbl.name as category_name, product_unit.unit_name');
        $this->db->from('product_tbl');
        $this->db->join('category_tbl', 'product_tbl.category_id = category_tbl.id', 'left');
        $this->db->join('product_unit', 'product_tbl.unit = product_unit.id', 'left');
        $this->db->where('product_tbl.status', 1);
        $produtos = $this->db->get()->result();
        
        echo json_encode(['success' => true, 'produtos' => $produtos]);
    }
}
