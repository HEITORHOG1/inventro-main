<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * JWT Authentication Library
 *
 * Implementação leve de JWT (JSON Web Tokens) para autenticação de APIs.
 * Usa HMAC-SHA256 sem dependências externas (sem Composer).
 *
 * Tokens:
 * - access_token: curta duração (1h), usado em Authorization header
 * - refresh_token: longa duração (30d), armazenado no banco, usado para renovar access_token
 */
class Jwt_auth {

    private $CI;
    private $secret_key;
    private $access_ttl  = 3600;     // 1 hora
    private $refresh_ttl = 2592000;  // 30 dias

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();

        // Chave secreta: usar env var ou fallback para encryption_key do CI
        $this->secret_key = getenv('JWT_SECRET') ?: $this->CI->config->item('encryption_key');

        if (empty($this->secret_key) || strlen($this->secret_key) < 16) {
            log_message('error', 'JWT_SECRET não configurado ou muito curto. Configure a variável de ambiente JWT_SECRET.');
        }
    }

    /**
     * Gerar par de tokens (access + refresh) para um usuário
     *
     * @param int    $user_id   ID do entregador/usuário
     * @param string $user_type Tipo: 'motoboy' ou 'admin'
     * @param array  $extra     Dados extras para incluir no payload
     * @return array {access_token, refresh_token, expires_in, token_type}
     */
    public function generate_tokens($user_id, $user_type = 'motoboy', $extra = []) {
        $now = time();

        // Access token (JWT)
        $payload = array_merge([
            'sub'  => (int) $user_id,
            'type' => $user_type,
            'iat'  => $now,
            'exp'  => $now + $this->access_ttl
        ], $extra);

        $access_token = $this->_encode($payload);

        // Refresh token (random string, armazenado no banco)
        $refresh_token = bin2hex(random_bytes(32));
        $refresh_hash  = hash('sha256', $refresh_token);

        // Revogar refresh tokens anteriores do mesmo usuário
        $this->CI->db->where('user_id', (int) $user_id)
                      ->where('user_type', $user_type)
                      ->delete('api_tokens');

        // Salvar novo refresh token
        $this->CI->db->insert('api_tokens', [
            'user_id'       => (int) $user_id,
            'user_type'     => $user_type,
            'refresh_token' => $refresh_hash,
            'expires_at'    => date('Y-m-d H:i:s', $now + $this->refresh_ttl),
            'created_at'    => date('Y-m-d H:i:s', $now)
        ]);

        return [
            'access_token'  => $access_token,
            'refresh_token' => $refresh_token,
            'expires_in'    => $this->access_ttl,
            'token_type'    => 'Bearer'
        ];
    }

    /**
     * Validar access token JWT
     *
     * @param string $token JWT access token
     * @return object|false Payload decodificado ou false se inválido/expirado
     */
    public function validate_token($token) {
        $payload = $this->_decode($token);

        if (!$payload) {
            return false;
        }

        // Verificar expiração
        if (!isset($payload->exp) || $payload->exp < time()) {
            return false;
        }

        return $payload;
    }

    /**
     * Renovar access token usando refresh token
     *
     * @param string $refresh_token Refresh token em texto plano
     * @return array|false Novos tokens ou false se inválido
     */
    public function refresh($refresh_token) {
        $refresh_hash = hash('sha256', $refresh_token);

        $record = $this->CI->db
            ->where('refresh_token', $refresh_hash)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get('api_tokens')
            ->row();

        if (!$record) {
            return false;
        }

        // Gerar novos tokens (revoga o antigo automaticamente)
        return $this->generate_tokens(
            $record->user_id,
            $record->user_type
        );
    }

    /**
     * Revogar todos os tokens de um usuário (logout)
     *
     * @param int    $user_id
     * @param string $user_type
     * @return bool
     */
    public function revoke($user_id, $user_type = 'motoboy') {
        $this->CI->db->where('user_id', (int) $user_id)
                      ->where('user_type', $user_type)
                      ->delete('api_tokens');

        return $this->CI->db->affected_rows() > 0;
    }

    /**
     * Extrair token do header Authorization
     *
     * @return string|null Token JWT ou null se não encontrado
     */
    public function get_token_from_header() {
        // Tentar Authorization header
        $header = $this->CI->input->get_request_header('Authorization');

        if (empty($header)) {
            // Fallback: alguns servidores removem Authorization
            $header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        }

        if (empty($header)) {
            // Fallback: Apache com mod_rewrite
            if (function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
                $header = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
            }
        }

        if (!empty($header) && preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }

    // =========================================
    // Métodos privados JWT (HS256)
    // =========================================

    /**
     * Codificar payload em JWT
     */
    private function _encode($payload) {
        $header = $this->_base64url_encode(json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]));

        $payload_encoded = $this->_base64url_encode(json_encode($payload));

        $signature = $this->_base64url_encode(
            hash_hmac('sha256', "{$header}.{$payload_encoded}", $this->secret_key, true)
        );

        return "{$header}.{$payload_encoded}.{$signature}";
    }

    /**
     * Decodificar e validar JWT
     */
    private function _decode($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        list($header_b64, $payload_b64, $signature_b64) = $parts;

        // Verificar assinatura
        $expected_sig = $this->_base64url_encode(
            hash_hmac('sha256', "{$header_b64}.{$payload_b64}", $this->secret_key, true)
        );

        if (!hash_equals($expected_sig, $signature_b64)) {
            return false;
        }

        $payload = json_decode($this->_base64url_decode($payload_b64));
        if (!$payload) {
            return false;
        }

        return $payload;
    }

    private function _base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function _base64url_decode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
