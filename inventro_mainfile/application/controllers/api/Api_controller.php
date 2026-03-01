<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Controller para todos os endpoints da API v1
 *
 * Funcionalidades:
 * - Respostas JSON padronizadas
 * - CORS configurável
 * - Autenticação JWT (para rotas protegidas)
 * - Rate limiting automático
 * - Tratamento de erros
 *
 * Controllers API devem estender esta classe.
 */
class Api_controller extends CI_Controller {

    /** @var object|null Payload JWT do usuário autenticado */
    protected $auth_user = null;

    /** @var string Tipo de rate limit: 'public', 'auth', 'login' */
    protected $rate_limit_type = 'public';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('jwt_auth');
        $this->load->library('rate_limiter');

        // CORS headers em todas as respostas
        $this->_set_cors_headers();

        // Tratar preflight OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }

    // =========================================
    // Respostas JSON
    // =========================================

    /**
     * Enviar resposta JSON com status HTTP
     *
     * @param array $data   Dados para serializar
     * @param int   $status Código HTTP (200, 400, 401, 404, 429, 500)
     */
    protected function _json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function _success($data, $status = 200) {
        $this->_json(array_merge(['success' => true], $data), $status);
    }

    protected function _error($message, $status = 400, $extra = []) {
        $this->_json(array_merge([
            'success' => false,
            'error'   => $message
        ], $extra), $status);
    }

    // =========================================
    // Autenticação JWT
    // =========================================

    /**
     * Exigir autenticação JWT. Encerra com 401 se inválido.
     *
     * @param string|null $required_type Tipo de usuário exigido ('motoboy', 'admin') ou null para qualquer
     * @return object Payload JWT
     */
    protected function _require_auth($required_type = null) {
        // Rate limit para rotas autenticadas
        $this->rate_limit_type = 'auth';

        $token = $this->jwt_auth->get_token_from_header();

        if (!$token) {
            $this->_error('Token de autenticação não fornecido. Envie Authorization: Bearer {token}', 401);
        }

        $payload = $this->jwt_auth->validate_token($token);

        if (!$payload) {
            $this->_error('Token inválido ou expirado', 401);
        }

        if ($required_type && isset($payload->type) && $payload->type !== $required_type) {
            $this->_error('Acesso não autorizado para este tipo de usuário', 403);
        }

        $this->auth_user = $payload;
        return $payload;
    }

    // =========================================
    // Rate Limiting
    // =========================================

    /**
     * Aplicar rate limiting baseado no tipo de endpoint
     *
     * @param string      $type Tipo: 'public', 'auth', 'login'
     * @param string|null $key  Identificador (null = IP automático)
     */
    protected function _check_rate_limit($type = null, $key = null) {
        $type = $type ?: $this->rate_limit_type;

        if ($key === null && $this->auth_user) {
            $key = 'user_' . $this->auth_user->sub;
        }

        $result = $this->rate_limiter->check($type, $key);
        $this->rate_limiter->set_headers($result);

        if (!$result['allowed']) {
            $retry_after = $result['reset_at'] - time();
            header('Retry-After: ' . max(1, $retry_after));
            $this->_error(
                'Limite de requisições excedido. Tente novamente em ' . max(1, $retry_after) . ' segundos.',
                429
            );
        }
    }

    // =========================================
    // CORS
    // =========================================

    /**
     * Configurar headers CORS
     */
    private function _set_cors_headers() {
        $allowed_origins = $this->config->item('api_allowed_origins');

        if (empty($allowed_origins)) {
            // Fallback: permitir qualquer origem (dev mode)
            header('Access-Control-Allow-Origin: *');
        } else {
            $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
            if (in_array($origin, $allowed_origins)) {
                header('Access-Control-Allow-Origin: ' . $origin);
                header('Vary: Origin');
            }
        }

        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, X-Requested-With');
        header('Access-Control-Max-Age: 86400');
    }

    // =========================================
    // Helpers
    // =========================================

    /**
     * Obter body JSON do request (POST/PUT)
     *
     * @return array Dados decodificados ou array vazio
     */
    protected function _get_json_body() {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Validar que o request usa o método HTTP esperado
     *
     * @param string $method 'GET', 'POST', 'PUT', 'DELETE'
     */
    protected function _require_method($method) {
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== strtoupper($method)) {
            $this->_error('Método não permitido. Use ' . strtoupper($method), 405);
        }
    }
}
