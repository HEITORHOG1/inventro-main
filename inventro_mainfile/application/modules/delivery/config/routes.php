<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Delivery module routes
// NOTA: No HMVC/MX, rotas module/controller/method/param são resolvidas automaticamente.
// NÃO usar rotas auto-referenciais como:
//   $route['delivery/orders/view/(:num)'] = 'delivery/orders/view/$1';
// Isso causa 404 porque MX parse_routes() prepende o módulo, resultando em:
//   delivery/delivery/orders/view/1 (duplicado)
// MX resolve module/controller/method/param nativamente sem rotas explícitas.
// Este arquivo precisa existir com $route array para MX load_file() não dar erro.

// Inicializar $route apenas se não existir (MX load_file precisa do array,
// mas o require() do routes.php principal compartilha o escopo — não pode sobrescrever)
if (!isset($route)) {
    $route = array();
}
