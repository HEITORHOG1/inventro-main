<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Planos de Negócio - Mapeamento de módulos por plano
|--------------------------------------------------------------------------
|
| Cada plano define quais módulos do sistema ficam disponíveis.
| Os nomes dos módulos correspondem aos diretórios em application/modules/
| e às chaves do $HmvcMenu2 no sidebar.php.
|
*/

$config['planos'] = array(

    'mercadinho' => array(
        'item',       // Produtos, categorias, unidades
        'customer',   // Clientes
        'delivery',   // Pedidos, config cardápio, zonas, entregadores, cupons
        'menu',       // Gerenciamento de roles/permissões
    ),

    'mercado_completo' => array(
        // Tudo do mercadinho
        'item',
        'customer',
        'delivery',
        'menu',
        // Módulos avançados
        'invoice',    // PDV / Vendas
        'purchase',   // Compras de fornecedores
        'supplier',   // Fornecedores
        'stock',      // Controle de estoque
        'report',     // Relatórios
        'bank',       // Contas bancárias
        'accounts',   // Lançamentos contábeis
        'financeiro', // Contas a pagar/receber
        'hrm',        // Funcionários, salários, ponto
        'return',     // Devoluções
    ),
);

// Módulos sempre permitidos independente do plano (infraestrutura)
$config['planos_always_allowed'] = array(
    'dashboard',
    'template',
);

// Nomes legíveis para exibição no dropdown de configurações
$config['planos_nomes'] = array(
    'mercadinho'       => 'Plano Mercadinho (Básico)',
    'mercado_completo' => 'Plano Mercado Completo',
);
