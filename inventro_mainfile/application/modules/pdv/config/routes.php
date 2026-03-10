<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| PDV Module Routes
|--------------------------------------------------------------------------
| Rotas customizadas para o módulo PDV (Frente de Caixa).
|
| HMVC resolve automaticamente: pdv/{method}/{param}
| Estas rotas criam aliases mais limpos para URLs do terminal.
*/

if (!isset($route)) {
    $route = array();
}

// /pdv/terminal/001 → Pdv::index('001')
$route['pdv/terminal/(:any)'] = 'pdv/pdv/index/$1';

// /pdv/display/001 → Pdv::display('001') — tela do consumidor (pública)
$route['pdv/display/(:any)'] = 'pdv/pdv/display/$1';

// /pdv/auditoria → Auditoria::index
$route['pdv/auditoria'] = 'pdv/auditoria/index';

// /pdv/auditoria/listar → Auditoria::listar (DataTables AJAX)
$route['pdv/auditoria/listar'] = 'pdv/auditoria/listar';

// /pdv/cupom_impressao/{venda_id} e /pdv/cupom_impressao/{venda_id}/{segunda_via}
$route['pdv/cupom_impressao/(:num)']        = 'pdv/pdv/cupom_impressao/$1';
$route['pdv/cupom_impressao/(:num)/(:num)'] = 'pdv/pdv/cupom_impressao/$1/$2';
