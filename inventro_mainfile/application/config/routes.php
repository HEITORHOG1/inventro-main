<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'dashboard/auth';
$route['login']  = "dashboard/auth/index";
$route['logout'] = "dashboard/auth/logout";

// Rotas públicas - Cardápio Digital (sem login)
$route['cardapio'] = 'cardapio/index';
$route['cardapio/buscar'] = 'cardapio/buscar';
$route['cardapio/api/produtos'] = 'cardapio/api_produtos';
$route['cardapio/api/categorias'] = 'cardapio/api_categorias';
$route['cardapio/acompanhar/(:any)'] = 'cardapio/acompanhar/$1';
$route['cardapio/api/status/(:any)'] = 'cardapio/api_status/$1';
$route['cardapio/avaliar/(:any)'] = 'cardapio/avaliar/$1';
$route['cardapio/api/validar_cupom'] = 'cardapio/api_validar_cupom';
$route['cardapio/api_detectar_zona'] = 'cardapio/api_detectar_zona';
$route['cardapio/api/ultimo_pedido'] = 'cardapio/api_ultimo_pedido';
$route['cardapio/api/pedidos_pendentes'] = 'cardapio/api_pedidos_pendentes';
$route['cardapio/manifest.json'] = 'cardapio/manifest';
$route['cardapio/sw.js'] = 'cardapio/service_worker';
$route['cardapio/offline'] = 'cardapio/offline';

// Rotas públicas - Portal do Motoboy (auth própria, sem RBAC)
$route['motoboy'] = 'motoboy/index';
$route['motoboy/login'] = 'motoboy/login';
$route['motoboy/logout'] = 'motoboy/logout';
$route['motoboy/dashboard'] = 'motoboy/dashboard';
$route['motoboy/aceitar/(:num)'] = 'motoboy/aceitar/$1';
$route['motoboy/coletar/(:num)'] = 'motoboy/coletar/$1';
$route['motoboy/entregar/(:num)'] = 'motoboy/entregar/$1';
$route['motoboy/historico'] = 'motoboy/historico';
$route['motoboy/api/pool'] = 'motoboy/api_pool';
$route['motoboy/api/minha'] = 'motoboy/api_minha_entrega';
$route['motoboy/api/historico'] = 'motoboy/api_historico';

// =========================================
// API v1 — Endpoints para apps nativos Android/iOS
// =========================================

// Cardápio (público, sem auth)
$route['api/v1/cardapio/produtos']                = 'api/v1_cardapio/produtos';
$route['api/v1/cardapio/categorias']              = 'api/v1_cardapio/categorias';
$route['api/v1/cardapio/zonas']                   = 'api/v1_cardapio/zonas';
$route['api/v1/cardapio/zona/detectar']           = 'api/v1_cardapio/detectar_zona';
$route['api/v1/cardapio/config']                  = 'api/v1_cardapio/config';
$route['api/v1/cardapio/pedido']                  = 'api/v1_cardapio/criar_pedido';
$route['api/v1/cardapio/pedido/(:any)/status']    = 'api/v1_cardapio/status_pedido/$1';
$route['api/v1/cardapio/pedido/(:any)/avaliar']   = 'api/v1_cardapio/avaliar_pedido/$1';
$route['api/v1/cardapio/cupom/validar']           = 'api/v1_cardapio/validar_cupom';
$route['api/v1/cardapio/cliente/(:any)/pedidos']  = 'api/v1_cardapio/pedidos_cliente/$1';
$route['api/v1/cardapio/cliente/(:any)']          = 'api/v1_cardapio/buscar_cliente/$1';

// Motoboy — Auth
$route['api/v1/motoboy/auth/login']               = 'api/v1_motoboy/login';
$route['api/v1/motoboy/auth/refresh']             = 'api/v1_motoboy/refresh';
$route['api/v1/motoboy/auth/logout']              = 'api/v1_motoboy/logout';

// Motoboy — Endpoints protegidos (JWT)
$route['api/v1/motoboy/perfil']                   = 'api/v1_motoboy/perfil';
$route['api/v1/motoboy/pool']                     = 'api/v1_motoboy/pool';
$route['api/v1/motoboy/entrega/ativa']            = 'api/v1_motoboy/entrega_ativa';
$route['api/v1/motoboy/entrega/(:num)/aceitar']   = 'api/v1_motoboy/aceitar/$1';
$route['api/v1/motoboy/entrega/(:num)/coletar']   = 'api/v1_motoboy/coletar/$1';
$route['api/v1/motoboy/entrega/(:num)/entregar']  = 'api/v1_motoboy/entregar/$1';
$route['api/v1/motoboy/historico']                = 'api/v1_motoboy/historico';
$route['api/v1/motoboy/ganhos']                   = 'api/v1_motoboy/ganhos';
$route['api/v1/motoboy/device']                   = 'api/v1_motoboy/registrar_device';

// Rotas públicas - Portal do Cliente (auth própria, sem RBAC)
$route['cliente'] = 'customer_portal/index';
$route['cliente/login'] = 'customer_portal/login';
$route['cliente/registrar'] = 'customer_portal/registrar';
$route['cliente/logout'] = 'customer_portal/logout';
$route['cliente/esqueci-senha'] = 'customer_portal/esqueci_senha';
$route['cliente/redefinir-senha/(:any)'] = 'customer_portal/redefinir_senha/$1';
$route['cliente/dashboard'] = 'customer_portal/dashboard';
$route['cliente/pedidos'] = 'customer_portal/pedidos';
$route['cliente/pedido/(:any)'] = 'customer_portal/pedido_detalhe/$1';
$route['cliente/perfil'] = 'customer_portal/perfil';
$route['cliente/alterar-senha'] = 'customer_portal/alterar_senha';
$route['cliente/pagamentos'] = 'customer_portal/pagamentos';

// Efi Pay PIX webhook (publico, sem auth)
$route['efi_webhook/pix'] = 'efi_webhook/pix';

// Swagger UI (dev only)
$route['api/docs']                                = 'api/v1_docs/index';
$route['api/docs/openapi.json']                   = 'api/v1_docs/spec';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//set modules/config/routes.php
$modules_path = APPPATH.'modules/';     
$modules = scandir($modules_path);

foreach($modules as $module)
{
    if($module === '.' || $module === '..') continue;
    if(is_dir($modules_path) . '/' . $module)
    {
        $routes_path = $modules_path . $module . '/config/routes.php';
        if(file_exists($routes_path))
        {
            require($routes_path);
        }
        else
        {
            continue;
        }
    }
}
