<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auditoria Controller — Admin page for PDV audit log
 *
 * Extends MX_Controller (admin session required).
 * Uses admin template layout (not fullscreen PDV).
 */
class Auditoria extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->permission->module('pdv')->redirect();
        $this->load->model(['Pdv_model']);
    }

    /**
     * Audit log listing page (DataTable)
     */
    public function index()
    {
        $data['title']      = 'Auditoria PDV';
        $data['module']     = 'pdv';
        $data['page']       = 'auditoria';
        $data['terminais']  = $this->Pdv_model->listarTerminais();
        $data['operadores'] = $this->Pdv_model->listarOperadoresAudit();
        $data['acoes']      = $this->Pdv_model->listarAcoesAudit();

        echo Modules::run('template/layout', $data);
    }

    /**
     * Server-side DataTables AJAX endpoint
     * POST: /pdv/auditoria/listar
     */
    public function listar()
    {
        if ($this->input->method() !== 'post') {
            $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Method not allowed']));
            return;
        }

        $params = [
            'draw'        => (int) $this->input->post('draw'),
            'start'       => (int) $this->input->post('start'),
            'length'      => (int) $this->input->post('length'),
            'search'      => '',
            'order_col'   => 0,
            'order_dir'   => 'desc',
            'terminal_id' => (int) $this->input->post('terminal_id'),
            'operador_id' => (int) $this->input->post('operador_id'),
            'acao'        => $this->input->post('acao', true),
            'data_inicio' => $this->input->post('data_inicio', true),
            'data_fim'    => $this->input->post('data_fim', true),
        ];

        // DataTables search
        $search_arr = $this->input->post('search');
        if (is_array($search_arr) && isset($search_arr['value'])) {
            $params['search'] = trim($search_arr['value']);
        }

        // DataTables ordering
        $order_arr = $this->input->post('order');
        if (is_array($order_arr) && isset($order_arr[0])) {
            $params['order_col'] = (int) $order_arr[0]['column'];
            $params['order_dir'] = $order_arr[0]['dir'];
        }

        $result = $this->Pdv_model->listarAuditLog($params);

        // Ações que merecem destaque (vermelho)
        $acoes_alerta = [
            'cupom_cancelado', 'venda_cancelada', 'sangria', 'devolucao',
            'desconto_item', 'desconto_venda', 'login_falha',
        ];

        // Formatar aaData
        $rows = [];
        foreach ($result['aaData'] as $row) {
            $detalhes_raw = $row->detalhes;
            $detalhes_preview = '';
            if (!empty($detalhes_raw)) {
                $decoded = json_decode($detalhes_raw, true);
                if (is_array($decoded)) {
                    // Criar preview legível (chave: valor)
                    $parts = [];
                    foreach ($decoded as $k => $v) {
                        if (is_array($v)) {
                            $v = json_encode($v);
                        }
                        $parts[] = htmlspecialchars($k, ENT_QUOTES, 'UTF-8')
                            . ': ' . htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
                    }
                    $detalhes_preview = implode(' | ', array_slice($parts, 0, 3));
                    if (count($parts) > 3) {
                        $detalhes_preview .= ' ...';
                    }
                }
            }

            $is_alerta = in_array($row->acao, $acoes_alerta);

            $rows[] = [
                'id'              => (int) $row->id,
                'created_at'      => date('d/m/Y H:i:s', strtotime($row->created_at)),
                'terminal'        => htmlspecialchars($row->terminal_numero ?: '-', ENT_QUOTES, 'UTF-8'),
                'operador'        => htmlspecialchars($row->operador_nome ?: '-', ENT_QUOTES, 'UTF-8'),
                'acao'            => htmlspecialchars($row->acao, ENT_QUOTES, 'UTF-8'),
                'entidade'        => htmlspecialchars(($row->entidade ?: '-') . ($row->entidade_id ? ' #' . $row->entidade_id : ''), ENT_QUOTES, 'UTF-8'),
                'detalhes'        => $detalhes_preview,
                'detalhes_full'   => htmlspecialchars($detalhes_raw ?: '{}', ENT_QUOTES, 'UTF-8'),
                'ip'              => htmlspecialchars($row->ip ?: '-', ENT_QUOTES, 'UTF-8'),
                'is_alerta'       => $is_alerta,
            ];
        }

        $response = [
            'draw'                 => $result['draw'],
            'iTotalRecords'        => $result['iTotalRecords'],
            'iTotalDisplayRecords' => $result['iTotalDisplayRecords'],
            'aaData'               => $rows,
            'csrf_token'           => $this->security->get_csrf_hash(),
            'csrf_name'            => $this->security->get_csrf_token_name(),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
