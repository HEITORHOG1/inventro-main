<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Delivery module routes
// NOTA: No HMVC/MX, rotas module/controller/method são resolvidas automaticamente.
// Só precisamos de rotas explícitas quando:
//   1. Queremos mapear /module/controller para /module/controller/index (atalho)
//   2. Queremos rotas com parâmetros regex (:num), (:any)
// Rotas auto-referenciais (que mapeiam para si mesmas) QUEBRAM o MX e causam 404.

// Atalhos para index
$route['delivery/orders'] = 'delivery/orders/index';
$route['delivery/entregadores'] = 'delivery/entregadores/index';
$route['delivery/cupons'] = 'delivery/cupons/index';
$route['delivery/config'] = 'delivery/config/index';
$route['delivery/zones'] = 'delivery/zones/index';

// Rotas com parâmetros (precisam de regex)
$route['delivery/orders/view/(:num)'] = 'delivery/orders/view/$1';
$route['delivery/orders/update_status/(:num)'] = 'delivery/orders/update_status/$1';
$route['delivery/orders/print_order/(:num)'] = 'delivery/orders/print_order/$1';
$route['delivery/entregadores/form/(:num)'] = 'delivery/entregadores/form/$1';
$route['delivery/entregadores/delete/(:num)'] = 'delivery/entregadores/delete/$1';
$route['delivery/entregadores/toggle_status/(:num)'] = 'delivery/entregadores/toggle_status/$1';
$route['delivery/cupons/form/(:num)'] = 'delivery/cupons/form/$1';
$route['delivery/cupons/delete/(:num)'] = 'delivery/cupons/delete/$1';
$route['delivery/cupons/toggle/(:num)'] = 'delivery/cupons/toggle/$1';
