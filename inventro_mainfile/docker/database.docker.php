<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| CONFIGURAÇÃO DO BANCO DE DADOS PARA DOCKER
| -------------------------------------------------------------------
| Copie este arquivo para application/config/database.php
| ou substitua as credenciais no arquivo original
| -------------------------------------------------------------------
*/
$active_group = 'default';
$query_builder = TRUE;
$active_record = TRUE;//ci version 2.x

$db['default'] = array(
    'dsn'   => '',
    'hostname' => 'db', // Nome do serviço MySQL no Docker Compose
    'username' => 'inventro_user',
    'password' => 'inventro_password',
    'database' => 'inventro_db',
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
    'autoinit' => TRUE,//ci version 2.x
    'stricton' => FALSE,
    'failover' => array(),
    'save_queries' => TRUE
);
