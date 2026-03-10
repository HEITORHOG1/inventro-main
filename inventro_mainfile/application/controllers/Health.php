<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Health Check — endpoint leve para Docker healthcheck
 * GET /health → 200 OK (verifica DB + Redis)
 */
class Health extends CI_Controller {

    public function index()
    {
        // Verificar conexão com o banco
        $db_ok = false;
        try {
            $query = $this->db->query('SELECT 1');
            $db_ok = ($query !== false);
        } catch (Exception $e) {
            $db_ok = false;
        }

        // Verificar conexão com Redis (sessão)
        $redis_ok = false;
        try {
            $redis = new Redis();
            $redis_host = getenv('REDIS_HOST') ?: 'redis';
            $redis_port = (int)(getenv('REDIS_PORT') ?: 6379);
            $redis_ok = $redis->connect($redis_host, $redis_port, 2);
            $redis->close();
        } catch (Exception $e) {
            $redis_ok = false;
        }

        $status = ($db_ok && $redis_ok) ? 'ok' : 'degraded';
        $http_code = ($db_ok && $redis_ok) ? 200 : 503;

        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => $status,
                'db'     => $db_ok ? 'ok' : 'fail',
                'redis'  => $redis_ok ? 'ok' : 'fail',
                'time'   => date('c'),
            ]));
    }
}
