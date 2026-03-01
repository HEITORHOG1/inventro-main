<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rate Limiter Library
 *
 * Controla a quantidade de requests por IP/token em janelas de tempo.
 * Usa tabela api_rate_limits no banco de dados.
 *
 * Limites padrão:
 * - Endpoints públicos: 60 req/min por IP
 * - Endpoints autenticados: 120 req/min por token
 * - Login: 5 tentativas a cada 15 minutos
 */
class Rate_limiter {

    private $CI;

    private $limits = [
        'public'  => ['max' => 60,  'window' => 60],    // 60 req/min
        'auth'    => ['max' => 120, 'window' => 60],    // 120 req/min
        'login'   => ['max' => 5,   'window' => 900],   // 5 req/15min
    ];

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    /**
     * Verificar se request está dentro do limite
     *
     * @param string $type   Tipo de limite: 'public', 'auth', 'login'
     * @param string $key    Identificador (IP ou user_id)
     * @return array {allowed: bool, remaining: int, reset_at: int}
     */
    public function check($type = 'public', $key = null) {
        if ($key === null) {
            $key = $this->CI->input->ip_address();
        }

        $limit  = $this->limits[$type] ?? $this->limits['public'];
        $max    = $limit['max'];
        $window = $limit['window'];
        $now    = time();
        $window_start = date('Y-m-d H:i:s', $now - $window);

        // Contar requests na janela
        $count = (int) $this->CI->db
            ->where('identifier', $key)
            ->where('endpoint_type', $type)
            ->where('created_at >', $window_start)
            ->count_all_results('api_rate_limits');

        if ($count >= $max) {
            // Buscar quando a janela reseta (primeiro registro + window)
            $first = $this->CI->db
                ->select('created_at')
                ->where('identifier', $key)
                ->where('endpoint_type', $type)
                ->where('created_at >', $window_start)
                ->order_by('created_at', 'ASC')
                ->limit(1)
                ->get('api_rate_limits')
                ->row();

            $reset_at = $first ? strtotime($first->created_at) + $window : $now + $window;

            return [
                'allowed'   => false,
                'remaining' => 0,
                'reset_at'  => $reset_at,
                'limit'     => $max
            ];
        }

        // Registrar request
        $this->CI->db->insert('api_rate_limits', [
            'identifier'    => $key,
            'endpoint_type' => $type,
            'created_at'    => date('Y-m-d H:i:s', $now)
        ]);

        return [
            'allowed'   => true,
            'remaining' => $max - $count - 1,
            'reset_at'  => $now + $window,
            'limit'     => $max
        ];
    }

    /**
     * Adicionar headers de rate limit na resposta HTTP
     *
     * @param array $result Resultado do check()
     */
    public function set_headers($result) {
        header('X-RateLimit-Limit: ' . $result['limit']);
        header('X-RateLimit-Remaining: ' . $result['remaining']);
        header('X-RateLimit-Reset: ' . $result['reset_at']);
    }

    /**
     * Limpar registros expirados (rodar periodicamente via cron)
     *
     * @return int Registros removidos
     */
    public function cleanup() {
        // Remove registros com mais de 1 hora
        $cutoff = date('Y-m-d H:i:s', time() - 3600);
        $this->CI->db->where('created_at <', $cutoff)->delete('api_rate_limits');
        return $this->CI->db->affected_rows();
    }
}
