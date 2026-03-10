<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model para Contas a Pagar
 */
class Contas_pagar_model extends CI_Model {

    private $table = 'contas_pagar';

    /**
     * Contar total de registros
     */
    public function count_all() {
        return $this->db->count_all($this->table);
    }

    /**
     * Buscar por ID
     */
    public function find_by_id($id) {
        return $this->db->select('cp.*, s.name as fornecedor_nome, cf.nome as categoria_nome')
            ->from('contas_pagar cp')
            ->join('supplier_tbl s', 's.supplier_id = cp.fornecedor_id', 'left')
            ->join('categorias_financeiras cf', 'cf.id = cp.categoria_id', 'left')
            ->where('cp.id', $id)
            ->get()
            ->row();
    }

    /**
     * Criar nova conta
     */
    public function create($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Atualizar conta
     */
    public function update($data) {
        $id = $data['id'];
        unset($data['id']);
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    /**
     * Excluir conta
     */
    public function delete($id) {
        // Primeiro excluir baixas relacionadas
        $this->db->where('tipo', 'pagar')->where('conta_id', $id)->delete('baixas_financeiras');
        // Depois excluir a conta
        $this->db->where('id', $id)->delete($this->table);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Buscar lista para DataTable
     */
    public function get_lista_datatable($postData = null) {
        $response = array();
        
        // Filtros
        $status = isset($postData['status']) ? $postData['status'] : '';
        $fornecedor_id = isset($postData['fornecedor_id']) ? $postData['fornecedor_id'] : '';
        $data_inicio = isset($postData['data_inicio']) ? $postData['data_inicio'] : '';
        $data_fim = isset($postData['data_fim']) ? $postData['data_fim'] : '';
        
        // Paginação
        $draw = isset($postData['draw']) ? $postData['draw'] : 1;
        $start = isset($postData['start']) ? $postData['start'] : 0;
        $rowperpage = isset($postData['length']) ? $postData['length'] : 10;
        $columnIndex = isset($postData['order'][0]['column']) ? $postData['order'][0]['column'] : 0;
        $columnName = isset($postData['columns'][$columnIndex]['data']) ? $postData['columns'][$columnIndex]['data'] : 'vencimento';
        $columnSortOrder = isset($postData['order'][0]['dir']) ? $postData['order'][0]['dir'] : 'asc';
        $searchValue = isset($postData['search']['value']) ? $postData['search']['value'] : '';

        // Map DataTable column names to actual SQL columns
        $sortMap = array(
            'sl' => 'cp.id',
            'codigo' => 'cp.codigo',
            'descricao' => 'cp.descricao',
            'fornecedor' => 's.name',
            'categoria' => 'cf.nome',
            'valor_original' => 'cp.valor_original',
            'valor_pago' => 'cp.valor_pago',
            'valor_pendente' => 'cp.valor_original',
            'vencimento' => 'cp.data_vencimento',
            'status' => 'cp.status',
        );
        $columnName = isset($sortMap[$columnName]) ? $sortMap[$columnName] : 'cp.data_vencimento';

        // Query base
        $this->db->select('cp.*, s.name as fornecedor_nome, cf.nome as categoria_nome, cf.cor as categoria_cor');
        $this->db->from('contas_pagar cp');
        $this->db->join('supplier_tbl s', 's.supplier_id = cp.fornecedor_id', 'left');
        $this->db->join('categorias_financeiras cf', 'cf.id = cp.categoria_id', 'left');
        
        // Aplicar filtros
        if (!empty($status)) {
            if ($status == 'vencido') {
                $this->db->where_in('cp.status', array('aberto', 'parcial'));
                $this->db->where('cp.data_vencimento <', date('Y-m-d'));
            } else {
                $this->db->where('cp.status', $status);
            }
        }
        if (!empty($fornecedor_id)) {
            $this->db->where('cp.fornecedor_id', $fornecedor_id);
        }
        if (!empty($data_inicio) && !empty($data_fim)) {
            $this->db->where('cp.data_vencimento >=', $data_inicio);
            $this->db->where('cp.data_vencimento <=', $data_fim);
        }
        if (!empty($searchValue)) {
            $this->db->group_start();
            $this->db->like('cp.codigo', $searchValue);
            $this->db->or_like('cp.descricao', $searchValue);
            $this->db->or_like('s.name', $searchValue);
            $this->db->group_end();
        }
        
        // Total com filtros
        $totalRecordwithFilter = $this->db->count_all_results('', false);
        
        // Ordenação e paginação
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        
        $records = $this->db->get()->result();
        
        // Total sem filtros
        $totalRecords = $this->db->count_all($this->table);
        
        // Montar dados
        $data = array();
        $base_url = base_url();
        $sl = $start + 1;
        
        foreach ($records as $record) {
            $valor_pendente = $record->valor_original - $record->valor_pago;
            
            // Determinar situação visual
            $status_class = 'secondary';
            $status_text = $record->status;
            if ($record->status == 'pago') {
                $status_class = 'success';
            } elseif ($record->status == 'parcial') {
                $status_class = 'warning';
            } elseif ($record->status == 'cancelado') {
                $status_class = 'dark';
            } elseif ($record->data_vencimento < date('Y-m-d') && $record->status != 'pago') {
                $status_class = 'danger';
                $status_text = 'vencido';
            } elseif ($record->data_vencimento == date('Y-m-d')) {
                $status_class = 'info';
                $status_text = 'hoje';
            }
            
            // Botões de ação
            $button = '';
            if ($record->status != 'pago' && $record->status != 'cancelado') {
                $button .= '<a href="'.$base_url.'financeiro/contas_pagar/baixa/'.$record->id.'" class="btn btn-success btn-sm" title="'.makeString(['dar_baixa']).'"><i class="fas fa-dollar-sign"></i></a> ';
            }
            $button .= '<a href="'.$base_url.'financeiro/contas_pagar/form/'.$record->id.'" class="btn btn-info btn-sm" title="'.makeString(['edit']).'"><i class="fas fa-edit"></i></a> ';
            if ($record->status != 'pago') {
                $button .= '<a href="'.$base_url.'financeiro/contas_pagar/cancelar/'.$record->id.'" class="btn btn-warning btn-sm" title="'.makeString(['cancel']).'" onclick="event.preventDefault(); var u=this.href; showConfirm(\'Cancelar esta conta?\', function(){ window.location.href=u; })"><i class="fas fa-ban"></i></a> ';
            }
            $button .= '<a href="'.$base_url.'financeiro/contas_pagar/delete/'.$record->id.'" class="btn btn-danger btn-sm" title="'.makeString(['delete']).'" onclick="event.preventDefault(); var u=this.href; showConfirm(\'Excluir permanentemente?\', function(){ window.location.href=u; })"><i class="fas fa-trash"></i></a>';
            
            $data[] = array(
                'sl' => $sl,
                'codigo' => $record->codigo,
                'descricao' => $record->descricao,
                'fornecedor' => $record->fornecedor_nome ?: '-',
                'categoria' => $record->categoria_nome ? '<span class="badge" style="background-color:'.$record->categoria_cor.'">'.$record->categoria_nome.'</span>' : '-',
                'valor_original' => number_format($record->valor_original, 2, ',', '.'),
                'valor_pago' => number_format($record->valor_pago, 2, ',', '.'),
                'valor_pendente' => number_format($valor_pendente, 2, ',', '.'),
                'vencimento' => date('d/m/Y', strtotime($record->data_vencimento)),
                'status' => '<span class="badge badge-'.$status_class.'">'.ucfirst($status_text).'</span>',
                'button' => $button
            );
            $sl++;
        }
        
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );
        
        return $response;
    }

    /**
     * Buscar fornecedores
     */
    public function get_fornecedores() {
        return $this->db->select('supplier_id, name')
            ->from('supplier_tbl')
            ->where('status', 1)
            ->order_by('name', 'asc')
            ->get()
            ->result();
    }

    /**
     * Buscar categorias
     */
    public function get_categorias($tipo = null) {
        $this->db->select('*')->from('categorias_financeiras')->where('status', 1);
        if ($tipo) {
            $this->db->where_in('tipo', array($tipo, 'ambos'));
        }
        return $this->db->order_by('nome', 'asc')->get()->result();
    }

    /**
     * Buscar bancos
     */
    public function get_bancos() {
        return $this->db->select('bank_id, bank_name')
            ->from('bank_tbl')
            ->where('status', 1)
            ->order_by('bank_name', 'asc')
            ->get()
            ->result();
    }

    /**
     * Registrar baixa
     */
    public function registrar_baixa($data) {
        return $this->db->insert('baixas_financeiras', $data);
    }

    /**
     * Buscar histórico de baixas
     */
    public function get_historico_baixas($conta_id, $tipo = 'pagar') {
        return $this->db->select('*')
            ->from('baixas_financeiras')
            ->where('tipo', $tipo)
            ->where('conta_id', $conta_id)
            ->order_by('data_baixa', 'desc')
            ->get()
            ->result();
    }

    /**
     * Resumo para dashboard
     */
    public function get_resumo() {
        $hoje = date('Y-m-d');
        $semana = date('Y-m-d', strtotime('+7 days'));
        $mes_inicio = date('Y-m-01');
        $mes_fim = date('Y-m-t');
        
        $resumo = new stdClass();
        
        // Total pendente
        $query = $this->db->select('SUM(valor_original - valor_pago) as total')
            ->from($this->table)
            ->where_in('status', array('aberto', 'parcial'))
            ->get()->row();
        $resumo->total_pendente = $query->total ?: 0;
        
        // Vencidos
        $query = $this->db->select('SUM(valor_original - valor_pago) as total')
            ->from($this->table)
            ->where_in('status', array('aberto', 'parcial'))
            ->where('data_vencimento <', $hoje)
            ->get()->row();
        $resumo->total_vencido = $query->total ?: 0;
        
        // Vence hoje
        $query = $this->db->select('SUM(valor_original - valor_pago) as total')
            ->from($this->table)
            ->where_in('status', array('aberto', 'parcial'))
            ->where('data_vencimento', $hoje)
            ->get()->row();
        $resumo->total_hoje = $query->total ?: 0;
        
        // Vence esta semana
        $query = $this->db->select('SUM(valor_original - valor_pago) as total')
            ->from($this->table)
            ->where_in('status', array('aberto', 'parcial'))
            ->where('data_vencimento >=', $hoje)
            ->where('data_vencimento <=', $semana)
            ->get()->row();
        $resumo->total_semana = $query->total ?: 0;
        
        // Vence este mês
        $query = $this->db->select('SUM(valor_original - valor_pago) as total')
            ->from($this->table)
            ->where_in('status', array('aberto', 'parcial'))
            ->where('data_vencimento >=', $mes_inicio)
            ->where('data_vencimento <=', $mes_fim)
            ->get()->row();
        $resumo->total_mes = $query->total ?: 0;
        
        return $resumo;
    }

    /**
     * Próximos vencimentos
     */
    public function get_proximos_vencimentos($limite = 10) {
        return $this->db->select('cp.*, s.name as fornecedor_nome')
            ->from('contas_pagar cp')
            ->join('supplier_tbl s', 's.supplier_id = cp.fornecedor_id', 'left')
            ->where_in('cp.status', array('aberto', 'parcial'))
            ->order_by('cp.data_vencimento', 'asc')
            ->limit($limite)
            ->get()
            ->result();
    }
}
