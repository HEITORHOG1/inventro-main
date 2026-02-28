<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| CONFIGURAÇÃO DO BANCO DE DADOS
| -------------------------------------------------------------------
| Lê credenciais de variáveis de ambiente (Docker/produção)
| ou usa valores padrão para desenvolvimento local.
| -------------------------------------------------------------------
*/
$active_group = 'default';
$query_builder = TRUE;
$active_record = TRUE; // ci version 2.x

$db['default'] = array(
    'dsn'      => '',
    'hostname' => getenv('DB_HOST') ?: 'db',
    'username' => getenv('DB_USERNAME') ?: 'inventro_user',
    'password' => getenv('DB_PASSWORD') ?: 'inventro_password',
    'database' => getenv('DB_DATABASE') ?: 'inventro_db',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on' => FALSE,
    'cachedir' => '',
    'char_set' => 'utf8mb4',
    'dbcollat' => 'utf8mb4_unicode_ci',
    'swap_pre' => '',
    'encrypt'  => FALSE,
    'compress' => FALSE,
    'autoinit' => TRUE, // ci version 2.x
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => (ENVIRONMENT !== 'production')
);
