<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model do Portal do Motoboy
 *
 * Gerencia autenticação, pool de entregas, aceitação (race-condition safe),
 * coleta, entrega e histórico de ganhos dos entregadores.
 */
class Motoboy_model extends CI_Model {

    /**
     * Autenticar entregador por telefone + senha
     *
     * @param string $telefone
     * @param string $senha
     * @return object|false Entregador se autenticado, false caso contrário
     */
    public function autenticar($telefone, $senha) {
        $telefone = preg_replace('/\D/', '', $telefone);
        if (empty($telefone) || empty($senha)) {
            return false;
        }

        // Busca por telefone (limpo, sem formatação) em entregadores ativos
        $entregadores = $this->db
            ->where('ativo', 1)
            ->where('senha IS NOT NULL')
            ->get('entregadores')
            ->result();

        foreach ($entregadores as $e) {
            $tel_limpo = preg_replace('/\D/', '', $e->telefone);
            if ($tel_limpo === $telefone && password_verify($senha, $e->senha)) {
                // Atualiza último login
                $this->db->where('id', $e->id)->update('entregadores', [
                    'ultimo_login' => date('Y-m-d H:i:s')
                ]);
                return $e;
            }
        }

        return false;
    }

    /**
     * Buscar entregas disponíveis no pool
     * Status = pronto_coleta E sem entregador atribuído
     *
     * @return array
     */
    public function get_pool() {
        return $this->db
            ->select('orders.id, orders.order_number, orders.cliente_nome, orders.cliente_endereco,
                      orders.zona_nome, orders.total, orders.taxa_entrega, orders.forma_pagamento,
                      orders.troco_para, orders.tipo_entrega, orders.created_at, orders.hora_pronto_coleta')
            ->where('orders.status', 'pronto_coleta')
            ->where('orders.entregador_id IS NULL')
            ->order_by('orders.hora_pronto_coleta', 'ASC')
            ->get('orders')
            ->result();
    }

    /**
     * Buscar entrega ativa do motoboy (com itens)
     *
     * @param int $motoboy_id
     * @return object|null
     */
    public function get_entrega_ativa($motoboy_id) {
        $order = $this->db
            ->where('entregador_id', (int) $motoboy_id)
            ->where_in('status', ['saiu_entrega'])
            ->get('orders')
            ->row();

        if ($order) {
            // Carregar itens
            $order->items = $this->db
                ->where('order_id', $order->id)
                ->get('order_items')
                ->result();

            // Carregar dados da entrega do motoboy
            $order->entrega_info = $this->db
                ->where('order_id', $order->id)
                ->where('entregador_id', (int) $motoboy_id)
                ->get('entregador_entregas')
                ->row();
        }

        return $order;
    }

    /**
     * Verificar se motoboy tem entrega ativa (não pode pegar outra)
     *
     * @param int $motoboy_id
     * @return bool
     */
    public function tem_entrega_ativa($motoboy_id) {
        $count = $this->db
            ->where('entregador_id', (int) $motoboy_id)
            ->where_in('status', ['saiu_entrega'])
            ->count_all_results('orders');

        return $count > 0;
    }

    /**
     * Aceitar entrega do pool — RACE CONDITION SAFE
     *
     * Usa transação + WHERE entregador_id IS NULL para garantir
     * que apenas o primeiro motoboy a clicar consiga aceitar.
     *
     * @param int    $order_id
     * @param int    $motoboy_id
     * @param string $motoboy_nome
     * @param float  $taxa_fixa
     * @return array ['success' => bool, 'message' => string]
     */
    public function aceitar_entrega($order_id, $motoboy_id, $motoboy_nome, $taxa_fixa) {
        // Verificar se já tem entrega ativa
        if ($this->tem_entrega_ativa($motoboy_id)) {
            return ['success' => false, 'message' => 'Você já tem uma entrega ativa. Finalize antes de aceitar outra.'];
        }

        $this->db->trans_start();

        // Tentar atribuir — só funciona se ninguém pegou antes
        $this->db->where('id', (int) $order_id)
                  ->where('status', 'pronto_coleta')
                  ->where('entregador_id IS NULL')
                  ->update('orders', [
                      'entregador_id'    => (int) $motoboy_id,
                      'entregador_nome'  => $motoboy_nome,
                      'status'           => 'saiu_entrega',
                      'hora_saiu_entrega' => date('Y-m-d H:i:s')
                  ]);

        $affected = $this->db->affected_rows();

        if ($affected > 0) {
            // Registrar na tabela de entregas do motoboy
            $this->db->insert('entregador_entregas', [
                'entregador_id' => (int) $motoboy_id,
                'order_id'      => (int) $order_id,
                'valor_ganho'   => (float) $taxa_fixa,
                'aceito_em'     => date('Y-m-d H:i:s'),
                'status'        => 'aceito'
            ]);

            // Atualizar status do motoboy para em_entrega
            $this->db->where('id', (int) $motoboy_id)
                      ->update('entregadores', ['status' => 'em_entrega']);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false || $affected === 0) {
            return ['success' => false, 'message' => 'Entrega já foi aceita por outro entregador.'];
        }

        return ['success' => true, 'message' => 'Entrega aceita com sucesso!'];
    }

    /**
     * Registrar coleta (motoboy pegou a mercadoria)
     *
     * @param int $order_id
     * @param int $motoboy_id
     * @return array
     */
    public function registrar_coleta($order_id, $motoboy_id) {
        // Validar ownership
        $order = $this->db
            ->where('id', (int) $order_id)
            ->where('entregador_id', (int) $motoboy_id)
            ->where('status', 'saiu_entrega')
            ->get('orders')
            ->row();

        if (!$order) {
            return ['success' => false, 'message' => 'Pedido não encontrado ou não pertence a você.'];
        }

        // Atualizar registro de entrega
        $this->db->where('order_id', (int) $order_id)
                  ->where('entregador_id', (int) $motoboy_id)
                  ->update('entregador_entregas', [
                      'coletado_em' => date('Y-m-d H:i:s'),
                      'status'      => 'coletado'
                  ]);

        return ['success' => true, 'message' => 'Coleta registrada!'];
    }

    /**
     * Registrar entrega (motoboy entregou ao cliente)
     *
     * Atualiza: orders.status → entregue, entregador volta a disponivel,
     * soma ganhos no total do entregador.
     *
     * @param int $order_id
     * @param int $motoboy_id
     * @return array
     */
    public function registrar_entrega($order_id, $motoboy_id) {
        // Validar ownership
        $order = $this->db
            ->where('id', (int) $order_id)
            ->where('entregador_id', (int) $motoboy_id)
            ->where('status', 'saiu_entrega')
            ->get('orders')
            ->row();

        if (!$order) {
            return ['success' => false, 'message' => 'Pedido não encontrado ou não pertence a você.'];
        }

        $now = date('Y-m-d H:i:s');

        $this->db->trans_start();

        // 1. Atualizar pedido para entregue
        $this->db->where('id', (int) $order_id)->update('orders', [
            'status'        => 'entregue',
            'hora_entregue' => $now
        ]);

        // 2. Atualizar registro de entrega
        $this->db->where('order_id', (int) $order_id)
                  ->where('entregador_id', (int) $motoboy_id)
                  ->update('entregador_entregas', [
                      'entregue_em' => $now,
                      'status'      => 'entregue'
                  ]);

        // 3. Buscar valor ganho
        $entrega = $this->db
            ->where('order_id', (int) $order_id)
            ->where('entregador_id', (int) $motoboy_id)
            ->get('entregador_entregas')
            ->row();

        $valor_ganho = $entrega ? (float) $entrega->valor_ganho : 0;

        // 4. Atualizar totais e status do motoboy → disponivel
        $this->db->where('id', (int) $motoboy_id)->update('entregadores', [
            'status'          => 'disponivel',
            'total_entregas'  => $this->db->select('total_entregas')->where('id', (int) $motoboy_id)->get('entregadores')->row()->total_entregas + 1,
            'total_ganhos'    => $this->db->select('total_ganhos')->where('id', (int) $motoboy_id)->get('entregadores')->row()->total_ganhos + $valor_ganho
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['success' => false, 'message' => 'Erro ao registrar entrega. Tente novamente.'];
        }

        return ['success' => true, 'message' => 'Entrega concluída! R$ ' . number_format($valor_ganho, 2, ',', '.') . ' adicionado aos seus ganhos.'];
    }

    /**
     * Buscar histórico de entregas do motoboy
     *
     * @param int    $motoboy_id
     * @param string $periodo (hoje, semana, mes, todos)
     * @param int    $limit
     * @return array
     */
    public function get_historico($motoboy_id, $periodo = 'todos', $limit = 50) {
        $this->db->select('ee.*, o.order_number, o.cliente_nome, o.zona_nome, o.total as order_total')
                  ->from('entregador_entregas ee')
                  ->join('orders o', 'o.id = ee.order_id', 'left')
                  ->where('ee.entregador_id', (int) $motoboy_id)
                  ->where('ee.status', 'entregue')
                  ->order_by('ee.entregue_em', 'DESC');

        switch ($periodo) {
            case 'hoje':
                $this->db->where('DATE(ee.entregue_em)', date('Y-m-d'));
                break;
            case 'semana':
                $this->db->where('ee.entregue_em >=', date('Y-m-d', strtotime('monday this week')));
                break;
            case 'mes':
                $this->db->where('MONTH(ee.entregue_em)', date('m'));
                $this->db->where('YEAR(ee.entregue_em)', date('Y'));
                break;
        }

        return $this->db->limit($limit)->get()->result();
    }

    /**
     * Resumo de ganhos do motoboy
     *
     * @param int $motoboy_id
     * @return object {hoje, semana, mes, total_entregas, total_ganhos}
     */
    public function get_resumo_ganhos($motoboy_id) {
        $hoje = $this->db
            ->select_sum('valor_ganho', 'total')
            ->select('COUNT(*) as qtd', false)
            ->where('entregador_id', (int) $motoboy_id)
            ->where('status', 'entregue')
            ->where('DATE(entregue_em)', date('Y-m-d'))
            ->get('entregador_entregas')
            ->row();

        $semana = $this->db
            ->select_sum('valor_ganho', 'total')
            ->select('COUNT(*) as qtd', false)
            ->where('entregador_id', (int) $motoboy_id)
            ->where('status', 'entregue')
            ->where('entregue_em >=', date('Y-m-d', strtotime('monday this week')))
            ->get('entregador_entregas')
            ->row();

        $mes = $this->db
            ->select_sum('valor_ganho', 'total')
            ->select('COUNT(*) as qtd', false)
            ->where('entregador_id', (int) $motoboy_id)
            ->where('status', 'entregue')
            ->where('MONTH(entregue_em)', date('m'))
            ->where('YEAR(entregue_em)', date('Y'))
            ->get('entregador_entregas')
            ->row();

        return (object) [
            'hoje_valor'     => (float) ($hoje->total ?? 0),
            'hoje_qtd'       => (int) ($hoje->qtd ?? 0),
            'semana_valor'   => (float) ($semana->total ?? 0),
            'semana_qtd'     => (int) ($semana->qtd ?? 0),
            'mes_valor'      => (float) ($mes->total ?? 0),
            'mes_qtd'        => (int) ($mes->qtd ?? 0),
        ];
    }
}
