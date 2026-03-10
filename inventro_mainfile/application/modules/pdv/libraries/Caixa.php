<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Caixa Library — Lógica de negócio para abertura/fechamento/sangria/suprimento
 *
 * Encapsula as regras de negócio do caixa PDV, separando do controller.
 * Usa Pdv_model para acesso a dados.
 */
class Caixa {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('pdv/Pdv_model', 'pdv_model');
    }

    // =========================================================================
    // ABERTURA
    // =========================================================================

    /**
     * Verifica se o terminal já tem caixa aberto
     *
     * @param int $terminal_id
     * @return object|null  Dados do caixa aberto ou null
     */
    public function get_caixa_aberto($terminal_id)
    {
        return $this->CI->pdv_model->get_caixa_aberto($terminal_id);
    }

    /**
     * Abre um novo caixa no terminal
     *
     * @param int    $terminal_id
     * @param int    $operador_id
     * @param float  $valor_abertura  Fundo de troco
     * @param string $observacao      Observação opcional
     * @return array ['success' => bool, 'caixa_id' => int, 'message' => string]
     */
    public function abrir($terminal_id, $operador_id, $valor_abertura, $observacao = '')
    {
        // Verifica se já existe caixa aberto neste terminal
        $caixa_aberto = $this->get_caixa_aberto($terminal_id);
        if ($caixa_aberto) {
            return [
                'success' => false,
                'message' => 'Caixa já aberto por ' . html_escape($caixa_aberto->operador_nome)
                    . ' desde ' . date('H:i', strtotime($caixa_aberto->aberto_em)),
            ];
        }

        // Abre o caixa
        $caixa_id = $this->CI->pdv_model->abrir_caixa([
            'terminal_id'    => $terminal_id,
            'operador_id'    => $operador_id,
            'valor_abertura' => $valor_abertura,
            'observacao'     => $observacao,
        ]);

        if (!$caixa_id) {
            return [
                'success' => false,
                'message' => 'Erro ao abrir caixa. Tente novamente.',
            ];
        }

        // Registra movimento de suprimento (fundo de troco)
        if ($valor_abertura > 0) {
            $this->CI->pdv_model->registrar_movimento([
                'caixa_id'    => $caixa_id,
                'tipo'        => 'suprimento',
                'valor'       => $valor_abertura,
                'descricao'   => 'Fundo de troco — abertura de caixa',
                'operador_id' => $operador_id,
            ]);
        }

        // Audit log
        $terminal = $this->CI->pdv_model->get_terminal($terminal_id);
        $this->CI->pdv_model->registrar_audit([
            'terminal_id' => $terminal_id,
            'caixa_id'    => $caixa_id,
            'operador_id' => $operador_id,
            'acao'        => 'abertura_caixa',
            'entidade'    => 'pdv_caixa',
            'entidade_id' => $caixa_id,
            'detalhes'    => [
                'valor_abertura' => $valor_abertura,
                'terminal'       => $terminal ? $terminal->numero : $terminal_id,
            ],
            'ip' => $this->CI->input->ip_address(),
        ]);

        return [
            'success'  => true,
            'caixa_id' => $caixa_id,
            'message'  => 'Caixa aberto com sucesso!',
        ];
    }

    /**
     * Verifica se o operador atual é o dono do caixa aberto
     *
     * @param int $caixa_id
     * @param int $operador_id
     * @return bool
     */
    public function is_operador_do_caixa($caixa_id, $operador_id)
    {
        $caixa = $this->CI->pdv_model->get_caixa($caixa_id);
        if (!$caixa) {
            return false;
        }
        return (int) $caixa->operador_id === (int) $operador_id;
    }
}
