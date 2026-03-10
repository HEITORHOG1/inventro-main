<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Redis Configuration
|--------------------------------------------------------------------------
| Usado para sessões PHP (sess_driver = 'redis') e stock locks do PDV.
|
| Host e porta lidos das variáveis de ambiente (configuradas no Docker Compose).
| Fallback para localhost:6379 caso as env vars não existam.
*/

$config['redis_host'] = getenv('REDIS_HOST') ?: 'redis';
$config['redis_port'] = (int) (getenv('REDIS_PORT') ?: 6379);
$config['redis_password'] = getenv('REDIS_PASSWORD') ?: NULL;
$config['redis_database'] = (int) (getenv('REDIS_DATABASE') ?: 0);

/*
|--------------------------------------------------------------------------
| Session Save Path (formato Redis para CodeIgniter)
|--------------------------------------------------------------------------
| Formato: tcp://host:port?database=X
| Se tiver senha: tcp://host:port?auth=password&database=X
*/

$redis_save_path = 'tcp://' . $config['redis_host'] . ':' . $config['redis_port'];
$redis_params = [];

if (!empty($config['redis_password'])) {
    $redis_params[] = 'auth=' . $config['redis_password'];
}

$redis_params[] = 'database=' . $config['redis_database'];

if (!empty($redis_params)) {
    $redis_save_path .= '?' . implode('&', $redis_params);
}

$config['redis_session_save_path'] = $redis_save_path;
