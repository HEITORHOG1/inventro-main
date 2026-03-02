<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo html_escape($loja->title ?? 'Cardápio Digital'); ?></title>
    <meta name="description" content="Cardápio digital - Faça seu pedido">
    <link rel="manifest" href="<?php echo base_url('cardapio/manifest.json'); ?>">
    <meta name="theme-color" content="#25D366">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #25D366;
            --primary-dark: #128C7E;
            --accent: #FF6B35;
            --bg-dark: #1a1a2e;
            --bg-card: #16213e;
            --bg-light: #0f3460;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --success: #00c853;
            --danger: #ff5252;
            --border-radius: 16px;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-card) 50%, var(--bg-light) 100%);
            min-height: 100vh;
            color: var(--text-primary);
        }

        /* Header */
        .header {
            background: rgba(22, 33, 62, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo-section { display: flex; align-items: center; gap: 15px; }
        .logo-section img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); }
        .logo-section h1 { font-size: 1.5rem; font-weight: 600; background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .logo-section p { font-size: 0.85rem; color: var(--text-secondary); }

        .search-box { position: relative; flex: 1; max-width: 400px; min-width: 250px; }
        .search-box input { width: 100%; padding: 12px 20px 12px 50px; border: none; border-radius: 50px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 1rem; transition: all 0.3s; }
        .search-box input:focus { outline: none; background: rgba(255,255,255,0.15); box-shadow: 0 0 20px rgba(37, 211, 102, 0.3); }
        .search-box input::placeholder { color: var(--text-secondary); }
        .search-box i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }

        .minha-conta-btn { display: flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 50px; background: rgba(255,255,255,0.1); color: var(--text-primary); text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s; white-space: nowrap; flex-shrink: 0; }
        .minha-conta-btn:hover { background: rgba(37, 211, 102, 0.2); color: var(--primary); }
        .minha-conta-btn i { font-size: 1.3rem; }
        .minha-conta-nome { max-width: 120px; overflow: hidden; text-overflow: ellipsis; }

        /* Categories */
        .categories { padding: 20px; max-width: 1200px; margin: 0 auto; }
        .categories-scroll { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 15px; scroll-behavior: smooth; }
        .categories-scroll::-webkit-scrollbar { height: 6px; }
        .categories-scroll::-webkit-scrollbar-track { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .categories-scroll::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        .category-btn { padding: 10px 24px; border: none; border-radius: 50px; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 0.9rem; font-weight: 500; cursor: pointer; white-space: nowrap; transition: all 0.3s; flex-shrink: 0; }
        .category-btn:hover, .category-btn.active { background: var(--primary); transform: translateY(-2px); box-shadow: 0 5px 20px rgba(37, 211, 102, 0.4); }

        /* Products Grid */
        .products-section { padding: 0 20px 120px; max-width: 1200px; margin: 0 auto; }
        .category-title { font-size: 1.3rem; font-weight: 600; margin: 30px 0 20px; padding-left: 10px; border-left: 4px solid var(--primary); display: flex; align-items: center; gap: 10px; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

        /* Product Card */
        .product-card { background: rgba(22, 33, 62, 0.8); border-radius: var(--border-radius); overflow: hidden; transition: all 0.3s; border: 1px solid rgba(255,255,255,0.05); }
        .product-card:hover { transform: translateY(-5px); box-shadow: var(--shadow); border-color: rgba(37, 211, 102, 0.3); }
        .product-image { width: 100%; height: 180px; background: linear-gradient(135deg, var(--bg-light), var(--bg-card)); display: flex; align-items: center; justify-content: center; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }
        .product-image .no-image { font-size: 4rem; color: rgba(255,255,255,0.2); }
        .product-info { padding: 20px; }
        .product-name { font-size: 1.1rem; font-weight: 600; margin-bottom: 5px; }
        .product-description { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 15px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-footer { display: flex; justify-content: space-between; align-items: center; }
        .product-price { font-size: 1.4rem; font-weight: 700; color: var(--primary); }
        .product-unit { font-size: 0.8rem; color: var(--text-secondary); font-weight: 400; }
        .add-btn { width: 45px; height: 45px; border: none; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; font-size: 1.2rem; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; }
        .add-btn:hover { transform: scale(1.1); box-shadow: 0 5px 20px rgba(37, 211, 102, 0.5); }

        /* Cart Float Button */
        .cart-float { position: fixed; bottom: 20px; right: 20px; z-index: 1000; }
        .cart-btn { width: 65px; height: 65px; border: none; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; font-size: 1.5rem; cursor: pointer; box-shadow: 0 5px 30px rgba(37, 211, 102, 0.5); transition: all 0.3s; position: relative; }
        .cart-btn:hover { transform: scale(1.1); }
        .cart-count { position: absolute; top: -5px; right: -5px; background: var(--accent); color: white; width: 28px; height: 28px; border-radius: 50%; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; justify-content: center; }

        /* Cart Modal */
        .cart-modal { position: fixed; bottom: 0; left: 0; right: 0; background: var(--bg-card); border-radius: 24px 24px 0 0; padding: 20px; z-index: 1001; transform: translateY(100%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); max-height: 90vh; overflow-y: auto; box-shadow: 0 -10px 40px rgba(0,0,0,0.5); }
        .cart-modal.open { transform: translateY(0); }
        .cart-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s; }
        .cart-overlay.open { opacity: 1; visibility: visible; }

        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .cart-header h2 { font-size: 1.3rem; display: flex; align-items: center; gap: 10px; }
        .close-cart { width: 40px; height: 40px; border: none; border-radius: 50%; background: rgba(255,255,255,0.1); color: var(--text-primary); font-size: 1.2rem; cursor: pointer; }

        .cart-items { margin-bottom: 15px; max-height: 200px; overflow-y: auto; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .cart-item-info { flex: 1; }
        .cart-item-name { font-weight: 500; margin-bottom: 5px; font-size: 0.9rem; }
        .cart-item-price { color: var(--primary); font-weight: 600; font-size: 0.85rem; }
        .quantity-controls { display: flex; align-items: center; gap: 8px; background: rgba(37, 211, 102, 0.2); border-radius: 50px; padding: 4px; }
        .qty-btn { width: 30px; height: 30px; border: none; border-radius: 50%; background: var(--primary); color: white; font-size: 0.9rem; cursor: pointer; }
        .qty-btn:hover { background: var(--primary-dark); }
        .qty-value { font-size: 1rem; font-weight: 600; min-width: 25px; text-align: center; }
        .cart-item-remove { background: none; border: none; color: var(--danger); cursor: pointer; font-size: 1rem; padding: 8px; margin-left: 8px; }
        .cart-empty { text-align: center; padding: 30px 20px; color: var(--text-secondary); }
        .cart-empty i { font-size: 3rem; margin-bottom: 15px; opacity: 0.3; }

        /* Formulário de Entrega Simplificado */
        .delivery-form { background: rgba(255,255,255,0.05); border-radius: 16px; padding: 20px; margin: 15px 0; }
        .form-title { font-size: 1rem; font-weight: 600; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; color: var(--primary); }
        .form-group { margin-bottom: 12px; }
        .form-group label { display: block; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 5px; }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; background: rgba(255,255,255,0.05); color: var(--text-primary); font-size: 1rem; transition: all 0.3s; }
        .form-control:focus { outline: none; border-color: var(--primary); background: rgba(255,255,255,0.1); }
        .form-control::placeholder { color: var(--text-secondary); }
        select.form-control { background-color: var(--bg-card); color: var(--text-primary); -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23a0a0a0' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 15px center; padding-right: 40px; }
        select.form-control option { background-color: #1a1a2e; color: #ffffff; padding: 10px; }
        .form-row { display: flex; gap: 10px; }
        .form-row .form-group { flex: 1; }

        /* CEP / Endereço */
        .cep-row { display: flex; gap: 8px; align-items: center; }
        .btn-buscar-cep { padding: 12px 16px; border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; background: rgba(255,255,255,0.05); color: var(--text-secondary); font-size: 0.8rem; text-decoration: none; white-space: nowrap; transition: all 0.3s; }
        .btn-buscar-cep:hover { border-color: var(--primary); color: var(--primary); }
        #enderecoDetalhes .form-control[readonly] { cursor: default; }
        #enderecoDetalhes .form-control[readonly]:focus { border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); }

        /* Payment Options */
        .payment-options { display: flex; gap: 10px; flex-wrap: wrap; }
        .payment-option { flex: 1; min-width: 90px; padding: 12px; border: 2px solid rgba(255,255,255,0.1); border-radius: 12px; background: transparent; color: var(--text-primary); cursor: pointer; text-align: center; transition: all 0.3s; }
        .payment-option:hover { border-color: var(--primary); }
        .payment-option.selected { border-color: var(--primary); background: rgba(37, 211, 102, 0.2); }
        .payment-option i { font-size: 1.5rem; margin-bottom: 5px; display: block; }
        .payment-option span { font-size: 0.85rem; }
        .troco-field { margin-top: 10px; display: none; }
        .troco-field.show { display: block; }

        /* Totais */
        .cart-totals { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; margin-top: 10px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.95rem; }
        .total-row.final { border-top: 2px solid rgba(255,255,255,0.2); margin-top: 8px; padding-top: 12px; font-size: 1.2rem; font-weight: 700; }
        .total-row.final .value { color: var(--primary); }

        /* Buttons */
        .checkout-buttons { display: flex; flex-direction: column; gap: 10px; margin-top: 15px; }
        .btn-checkout { width: 100%; padding: 16px; border: none; border-radius: 50px; font-size: 1rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; }
        .btn-checkout:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-whatsapp { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; }
        .btn-whatsapp:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4); }
        .btn-site { background: linear-gradient(135deg, #3498db, #2980b9); color: white; }
        .btn-site:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(52, 152, 219, 0.4); }
        .divider { text-align: center; color: var(--text-secondary); font-size: 0.85rem; margin: 5px 0; }

        /* Taxa Info */
        .taxa-info { background: rgba(37, 211, 102, 0.1); border: 1px solid rgba(37, 211, 102, 0.3); border-radius: 12px; padding: 12px 15px; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .taxa-info i { font-size: 1.2rem; color: var(--primary); }
        .taxa-info .valor { font-weight: 700; color: var(--primary); }

        /* Toast */
        .toast { position: fixed; bottom: 100px; left: 50%; transform: translateX(-50%) translateY(100px); background: var(--bg-card); color: var(--text-primary); padding: 15px 30px; border-radius: 50px; box-shadow: var(--shadow); z-index: 2000; opacity: 0; transition: all 0.3s; display: flex; align-items: center; gap: 10px; border: 1px solid var(--primary); }
        .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }

        /* Responsive */
        @media (max-width: 600px) {
            .header-content { flex-direction: column; text-align: center; }
            .search-box { width: 100%; max-width: none; }
            .logo-section { flex-direction: column; }
            .minha-conta-nome { display: none; }
            .products-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
            .product-image { height: 140px; }
            .product-info { padding: 12px; }
            .product-name { font-size: 0.95rem; }
            .product-price { font-size: 1.1rem; }
            .add-btn { width: 38px; height: 38px; font-size: 1rem; }
            .payment-options { flex-direction: column; }
            .payment-option { min-width: auto; }
        }

        /* Cliente Encontrado */
        .cliente-encontrado {
            background: rgba(37, 211, 102, 0.15);
            border: 1px solid rgba(37, 211, 102, 0.4);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .cliente-info { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .cliente-info i { font-size: 2rem; color: var(--primary); }
        .cliente-info strong { display: block; font-size: 1rem; margin-bottom: 3px; }
        .cliente-info p { font-size: 0.85rem; color: var(--text-secondary); margin: 0; }
        .cliente-actions { display: flex; gap: 10px; }
        .btn-usar-dados, .btn-editar-dados {
            flex: 1; padding: 10px; border: none; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;
        }
        .btn-usar-dados { background: var(--primary); color: white; }
        .btn-usar-dados:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-editar-dados { background: rgba(255,255,255,0.1); color: var(--text-primary); }
        .btn-editar-dados:hover { background: rgba(255,255,255,0.2); }

        /* CPF Toggle */
        .cpf-nota-group { margin-top: 10px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.1); }
        .cpf-toggle { margin-bottom: 10px; }
        .toggle-label { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .toggle-label input[type="checkbox"] { width: 20px; height: 20px; accent-color: var(--primary); cursor: pointer; }
        .toggle-text { color: var(--text-secondary); font-size: 0.9rem; }
        .cpf-field { animation: slideIn 0.3s ease; }

        /* Pedidos Pendentes Banner */
        .pedidos-pendentes-banner { background: rgba(255, 107, 53, 0.15); border: 1px solid rgba(255, 107, 53, 0.4); border-radius: 12px; padding: 15px 20px; max-width: 1200px; margin: 10px auto; animation: slideIn 0.3s ease; }
        .pedidos-pendentes-banner .banner-header { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: var(--accent); font-weight: 600; font-size: 0.95rem; }
        .pedidos-pendentes-banner .banner-header i { font-size: 1.2rem; }
        .pedido-pendente-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: rgba(255,255,255,0.05); border-radius: 8px; margin-bottom: 6px; font-size: 0.85rem; }
        .pedido-pendente-item:last-child { margin-bottom: 0; }
        .pedido-pendente-info { display: flex; align-items: center; gap: 10px; }
        .pedido-pendente-status { padding: 3px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .status-pendente { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
        .status-confirmado { background: rgba(33, 150, 243, 0.2); color: #2196f3; }
        .status-preparando { background: rgba(156, 39, 176, 0.2); color: #ce93d8; }
        .status-saiu_entrega { background: rgba(37, 211, 102, 0.2); color: var(--primary); }
        .pedido-pendente-link { color: var(--primary); text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 5px; }
        .pedido-pendente-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo-section">
                <?php if (!empty($loja->logo)): ?>
                    <img src="<?php echo base_url($loja->logo); ?>" alt="Logo">
                <?php else: ?>
                    <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                        <i class="fas fa-store"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h1><?php echo html_escape($loja->title ?? 'Cardápio Digital'); ?></h1>
                    <?php if (isset($loja_aberta) && $loja_aberta): ?>
                        <p><i class="fas fa-clock"></i> Aberto agora</p>
                    <?php else: ?>
                        <p style="color:var(--danger);"><i class="fas fa-times-circle"></i> Fechado
                            <?php if (isset($horario_abertura)): ?>
                                — Abrimos as <?php echo html_escape($horario_abertura); ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar produtos...">
            </div>
            <a href="<?php echo base_url('cliente'); ?>" class="minha-conta-btn" title="Minha Conta">
                <i class="fas fa-user-circle"></i>
                <?php if ($this->session->userdata('cliente_logado')): ?>
                    <span class="minha-conta-nome"><?php echo html_escape($this->session->userdata('cliente_nome')); ?></span>
                <?php endif; ?>
            </a>
        </div>
    </header>

    <!-- Loja Fechada Banner -->
    <?php if (isset($loja_aberta) && !$loja_aberta): ?>
    <div style="background:rgba(255,82,82,0.15);border:1px solid rgba(255,82,82,0.4);padding:15px 20px;text-align:center;color:#ff5252;font-weight:500;max-width:1200px;margin:10px auto;border-radius:12px;">
        <i class="fas fa-store-slash" style="font-size:1.2rem;margin-right:8px;"></i>
        Estamos fechados no momento.
        <?php if (isset($horario_abertura) && isset($horario_fechamento)): ?>
            Funcionamos das <?php echo html_escape($horario_abertura); ?> as <?php echo html_escape($horario_fechamento); ?>.
        <?php endif; ?>
        Navegue pelo cardapio e volte no horario de funcionamento!
    </div>
    <?php endif; ?>

    <!-- Pedido Minimo Banner -->
    <?php if (isset($pedido_minimo) && $pedido_minimo > 0): ?>
    <div id="pedidoMinimoBanner" style="background:rgba(37,211,102,0.1);border:1px solid rgba(37,211,102,0.3);padding:10px 20px;text-align:center;color:var(--primary);font-size:0.9rem;max-width:1200px;margin:5px auto;border-radius:12px;">
        <i class="fas fa-info-circle"></i>
        Pedido minimo: <strong>R$ <?php echo number_format($pedido_minimo, 2, ',', '.'); ?></strong>
    </div>
    <?php endif; ?>

    <!-- Pedidos Pendentes Banner (preenchido via JS) -->
    <div id="pedidosPendentesBanner" class="pedidos-pendentes-banner" style="display:none;"></div>

    <!-- Categories -->
    <section class="categories">
        <div class="categories-scroll">
            <button class="category-btn active" data-category="all">
                <i class="fas fa-th-large"></i> Todos
            </button>
            <?php foreach ($categorias as $cat): ?>
                <button class="category-btn" data-category="<?php echo html_escape($cat->name); ?>">
                    <?php echo html_escape($cat->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Products -->
    <main class="products-section">
        <?php if (empty($produtos_por_categoria)): ?>
            <div style="text-align:center;padding:50px;color:var(--text-secondary);">
                <i class="fas fa-box-open" style="font-size:3rem;opacity:0.3;"></i>
                <p>Nenhum produto disponível no momento</p>
            </div>
        <?php else: ?>
            <?php foreach ($produtos_por_categoria as $categoria => $produtos): ?>
                <div class="category-group" data-category="<?php echo html_escape($categoria); ?>">
                    <h2 class="category-title">
                        <i class="fas fa-tag"></i>
                        <?php echo html_escape($categoria); ?>
                        <span style="font-size:0.8rem;color:var(--text-secondary);font-weight:400;">
                            (<?php echo count($produtos); ?> itens)
                        </span>
                    </h2>
                    <div class="products-grid">
                        <?php foreach ($produtos as $produto): ?>
                            <div class="product-card" 
                                 data-id="<?php echo $produto->id; ?>"
                                 data-name="<?php echo html_escape($produto->name); ?>"
                                 data-price="<?php echo $produto->price; ?>"
                                 data-unit="<?php echo html_escape($produto->unit_name ?? 'un'); ?>">
                                <div class="product-image">
                                    <?php if (!empty($produto->picture)): ?>
                                        <img src="<?php echo base_url('application/modules/item/assets/images/' . html_escape($produto->picture)); ?>" alt="<?php echo html_escape($produto->name); ?>">
                                    <?php else: ?>
                                        <i class="fas fa-box no-image"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name"><?php echo html_escape($produto->name); ?></h3>
                                    <p class="product-description">
                                        <?php echo html_escape($produto->description ?? 'Produto de qualidade'); ?>
                                    </p>
                                    <div class="product-footer">
                                        <div class="product-price">
                                            R$ <?php echo number_format($produto->price, 2, ',', '.'); ?>
                                            <span class="product-unit">/<?php echo html_escape($produto->unit_name ?? 'un'); ?></span>
                                        </div>
                                        <button class="add-btn" onclick="addToCart(this)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <!-- Cart Float Button -->
    <div class="cart-float">
        <button class="cart-btn" onclick="toggleCart()">
            <i class="fas fa-shopping-cart"></i>
            <span class="cart-count" id="cartCount">0</span>
        </button>
    </div>

    <!-- Cart Overlay -->
    <div class="cart-overlay" id="cartOverlay" onclick="toggleCart()"></div>

    <!-- Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-header">
            <h2><i class="fas fa-shopping-cart"></i> Seu Pedido</h2>
            <button class="close-cart" onclick="toggleCart()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="cart-items" id="cartItems">
            <div class="cart-empty">
                <i class="fas fa-shopping-basket"></i>
                <p>Seu carrinho está vazio</p>
                <p style="font-size:0.85rem;">Adicione produtos para fazer seu pedido</p>
            </div>
        </div>

        <!-- Formulário de Entrega Simplificado -->
        <div class="delivery-form" id="deliveryForm" style="display: none;">
            <h3 class="form-title"><i class="fas fa-truck"></i> Dados para Entrega</h3>
            
            <!-- Tipo de Entrega -->
            <div class="form-group">
                <label>Tipo de Pedido *</label>
                <div class="payment-options" style="margin-bottom:10px;">
                    <button type="button" class="payment-option selected" data-tipo="entrega" onclick="setTipoEntrega('entrega')">
                        <i class="fas fa-motorcycle"></i>
                        <span>Entrega</span>
                    </button>
                    <button type="button" class="payment-option" data-tipo="retirada" onclick="setTipoEntrega('retirada')">
                        <i class="fas fa-store"></i>
                        <span>Retirar</span>
                    </button>
                </div>
            </div>

            <!-- Taxa de Entrega Info (detectada automaticamente pelo CEP) -->
            <div class="taxa-info" id="taxaInfo" style="display:none;">
                <i class="fas fa-motorcycle"></i>
                <span id="taxaInfoText"></span>
            </div>
            <input type="hidden" id="zonaDetectadaId" value="">
            <input type="hidden" id="zonaDetectadaNome" value="">
            
            <div class="form-group">
                <label>Telefone/WhatsApp *</label>
                <div style="position:relative;">
                    <input type="tel" class="form-control" id="clienteTelefone" placeholder="(11) 99999-9999">
                    <div id="loadingIndicator" style="display:none;position:absolute;right:15px;top:50%;transform:translateY(-50%);">
                        <i class="fas fa-spinner fa-spin" style="color:var(--primary);"></i>
                    </div>
                </div>
                <small style="color:var(--text-secondary);font-size:0.75rem;">Digite seu telefone para buscar cadastro</small>
            </div>
            
            <!-- Alerta de Cliente Encontrado -->
            <div id="clienteEncontrado" class="cliente-encontrado" style="display:none;">
                <div class="cliente-info">
                    <i class="fas fa-user-check"></i>
                    <div>
                        <strong id="clienteEncontradoNome"></strong>
                        <p id="clienteEncontradoEndereco"></p>
                        <small style="color:var(--success);font-size:0.7rem;">Dados preenchidos automaticamente</small>
                    </div>
                </div>
                <div class="cliente-actions">
                    <button type="button" class="btn-editar-dados" onclick="editarDados()">
                        <i class="fas fa-edit"></i> Editar dados
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label>Nome completo *</label>
                <input type="text" class="form-control" id="clienteNome" placeholder="Seu nome">
            </div>
            
            <div class="form-group endereco-group" id="enderecoGroup">
                <label>Endereço de entrega *</label>
                <div class="cep-row">
                    <div style="flex:1;position:relative;">
                        <input type="text" class="form-control" id="clienteCep" placeholder="00000-000" maxlength="9" inputmode="numeric">
                        <div id="cepLoading" style="display:none;position:absolute;right:15px;top:50%;transform:translateY(-50%);">
                            <i class="fas fa-spinner fa-spin" style="color:var(--primary);"></i>
                        </div>
                    </div>
                    <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" target="_blank" class="btn-buscar-cep" title="Não sei meu CEP">
                        <i class="fas fa-search"></i> Buscar CEP
                    </a>
                </div>
                <div id="cepMsg" style="font-size:0.8rem;margin-top:4px;display:none;"></div>

                <div id="enderecoDetalhes" style="display:none;margin-top:10px;">
                    <input type="text" class="form-control" id="clienteRua" placeholder="Rua / Logradouro" readonly style="margin-bottom:8px;opacity:0.8;">
                    <input type="text" class="form-control" id="clienteBairro" placeholder="Bairro" readonly style="margin-bottom:8px;opacity:0.8;">
                    <div style="display:flex;gap:8px;margin-bottom:8px;">
                        <input type="text" class="form-control" id="clienteCidade" placeholder="Cidade" readonly style="flex:1;opacity:0.8;">
                        <input type="text" class="form-control" id="clienteEstado" placeholder="UF" readonly style="width:70px;text-align:center;opacity:0.8;">
                    </div>
                    <div style="display:flex;gap:8px;">
                        <input type="text" class="form-control" id="clienteNumero" placeholder="Número *" style="width:120px;" inputmode="numeric">
                        <input type="text" class="form-control" id="clienteComplemento" placeholder="Complemento (apto, bloco...)" style="flex:1;">
                    </div>
                </div>
                <!-- Campo hidden para manter compatibilidade -->
                <input type="hidden" id="clienteEndereco" value="">
            </div>
            
            <div class="form-group">
                <label>Forma de Pagamento *</label>
                <div class="payment-options">
                    <?php if (($config['aceita_dinheiro'] ?? '1') === '1'): ?>
                    <button type="button" class="payment-option" data-payment="dinheiro">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Dinheiro</span>
                    </button>
                    <?php endif; ?>
                    <?php if (($config['aceita_cartao'] ?? '1') === '1'): ?>
                    <button type="button" class="payment-option" data-payment="cartao">
                        <i class="fas fa-credit-card"></i>
                        <span>Cartão</span>
                    </button>
                    <?php endif; ?>
                    <?php if (($config['aceita_pix'] ?? '1') === '1'): ?>
                    <button type="button" class="payment-option" data-payment="pix">
                        <i class="fas fa-qrcode"></i>
                        <span>Pix</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group troco-field" id="trocoField">
                <label>Troco para quanto?</label>
                <input type="text" class="form-control" id="trocoPara" placeholder="Ex: R$ 50,00">
            </div>
            
            <div class="form-group">
                <label>Observações (opcional)</label>
                <input type="text" class="form-control" id="observacao" placeholder="Alguma observação?">
            </div>
            
            <!-- CPF na Nota -->
            <div class="form-group cpf-nota-group">
                <div class="cpf-toggle">
                    <label class="toggle-label">
                        <input type="checkbox" id="querCpf" onchange="toggleCpfField()">
                        <span class="toggle-text">Deseja CPF na nota fiscal?</span>
                    </label>
                </div>
                <div id="cpfField" class="cpf-field" style="display:none;">
                    <input type="text" class="form-control" id="cpfNota" placeholder="000.000.000-00" maxlength="14">
                </div>
            </div>
        </div>
        
        <!-- Cupom de Desconto -->
        <div class="form-group" id="cupomGroup" style="display:none;">
            <label><i class="fas fa-ticket-alt"></i> Cupom de Desconto</label>
            <div style="display:flex;gap:8px;">
                <input type="text" class="form-control" id="cupomCodigo" placeholder="Digite o cupom" style="flex:1;text-transform:uppercase;">
                <button type="button" onclick="aplicarCupom()" style="padding:12px 20px;border:none;border-radius:12px;background:var(--primary);color:#fff;font-weight:600;cursor:pointer;">Aplicar</button>
            </div>
            <div id="cupomMsg" style="font-size:0.85rem;margin-top:5px;display:none;"></div>
        </div>

        <!-- Totais -->
        <div class="cart-totals" id="cartTotals">
            <div class="total-row">
                <span>Subtotal</span>
                <span id="subtotalValue">R$ 0,00</span>
            </div>
            <div class="total-row">
                <span>Taxa de entrega</span>
                <span id="taxaValue">--</span>
            </div>
            <div class="total-row" id="descontoRow" style="display:none;color:var(--success);">
                <span>Desconto</span>
                <span id="descontoValue">- R$ 0,00</span>
            </div>
            <div class="total-row final">
                <span>TOTAL</span>
                <span class="value" id="totalValue">R$ 0,00</span>
            </div>
        </div>
        
        <!-- Buttons -->
        <div class="checkout-buttons" id="checkoutButtons">
            <button class="btn-checkout btn-whatsapp" id="btnWhatsapp" onclick="finalizarWhatsapp()" disabled>
                <i class="fab fa-whatsapp"></i> Finalizar via WhatsApp
            </button>
            <span class="divider">ou</span>
            <button class="btn-checkout btn-site" id="btnSite" onclick="finalizarSite()" disabled>
                <i class="fas fa-shopping-bag"></i> Finalizar pelo Site
            </button>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast">
        <i class="fas fa-check-circle" style="color:var(--primary)"></i>
        <span id="toastMessage">Produto adicionado!</span>
    </div>

    <!-- Cupom de Desconto Field (before totals) -->

    <script>
        let cart = [];
        let selectedPayment = null;
        let clienteEncontradoData = null;
        let tipoEntrega = 'entrega';
        let taxaEntrega = 0;
        let selectedZonaId = null;
        let cupomAplicado = null;
        const whatsappNumber = '<?php echo $whatsapp; ?>';
        const storeName = <?php echo json_encode($loja->title ?? 'Cardapio'); ?>;
        const baseUrl = '<?php echo base_url(); ?>';
        const lojaAberta = <?php echo (isset($loja_aberta) && $loja_aberta) ? 'true' : 'false'; ?>;
        const pedidoMinimo = <?php echo (float)($pedido_minimo ?? 0); ?>;
        const taxaFixa = <?php echo (float)($taxa_entrega ?? 0); ?>;

        // Buscar cliente por telefone
        async function buscarCliente(telefone) {
            if (telefone.length < 10) return;

            const loading = document.getElementById('loadingIndicator');
            loading.style.display = 'block';

            try {
                const response = await fetch(baseUrl + 'cardapio/api_buscar_cliente?telefone=' + encodeURIComponent(telefone));
                const data = await response.json();

                if (data.found && data.cliente) {
                    clienteEncontradoData = data.cliente;

                    // Preencher nome automaticamente
                    document.getElementById('clienteNome').value = data.cliente.nome;

                    // Preencher CEP e endereco se disponivel
                    if (data.cliente.cep) {
                        document.getElementById('clienteCep').value = formatCEP(data.cliente.cep);
                        buscarCEP(data.cliente.cep);
                    } else if (data.cliente.endereco) {
                        document.getElementById('clienteEndereco').value = data.cliente.endereco;
                    }

                    // Preencher CPF se disponivel
                    if (data.cliente.cpf) {
                        document.getElementById('querCpf').checked = true;
                        document.getElementById('cpfField').style.display = 'block';
                        document.getElementById('cpfNota').value = data.cliente.cpf;
                    }

                    // Mostrar indicador visual no campo telefone
                    var telInput = document.getElementById('clienteTelefone');
                    telInput.style.borderColor = 'var(--success)';
                    telInput.style.boxShadow = '0 0 0 2px rgba(40,167,69,0.15)';

                    // Mostrar banner com opcao de editar
                    document.getElementById('clienteEncontradoNome').textContent = data.cliente.nome;
                    document.getElementById('clienteEncontradoEndereco').textContent = data.cliente.endereco || 'Endereco nao cadastrado';
                    document.getElementById('clienteEncontrado').style.display = 'block';

                    showToast('Cliente encontrado! Dados preenchidos.');
                    validateForm();
                } else {
                    document.getElementById('clienteEncontrado').style.display = 'none';
                    clienteEncontradoData = null;

                    // Resetar estilo do campo telefone
                    var telInput = document.getElementById('clienteTelefone');
                    telInput.style.borderColor = '';
                    telInput.style.boxShadow = '';
                }
            } catch (error) {
                console.error('Erro ao buscar cliente:', error);
            } finally {
                loading.style.display = 'none';
            }
        }

        function editarDados() {
            document.getElementById('clienteEncontrado').style.display = 'none';
            document.getElementById('clienteNome').focus();
            document.getElementById('clienteNome').select();
        }

        function toggleCpfField() {
            const checkbox = document.getElementById('querCpf');
            const cpfField = document.getElementById('cpfField');
            cpfField.style.display = checkbox.checked ? 'block' : 'none';
            if (checkbox.checked) {
                document.getElementById('cpfNota').focus();
            }
        }

        function formatCPF(value) {
            let v = value.replace(/\D/g, '').slice(0, 11);
            if (v.length > 9) v = v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6,9) + '-' + v.slice(9);
            else if (v.length > 6) v = v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6);
            else if (v.length > 3) v = v.slice(0,3) + '.' + v.slice(3);
            return v;
        }

        function formatCEP(value) {
            let v = value.replace(/\D/g, '').slice(0, 8);
            if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
            return v;
        }

        async function buscarCEP(cep) {
            var cepLimpo = cep.replace(/\D/g, '');
            var cepMsg = document.getElementById('cepMsg');
            var cepLoading = document.getElementById('cepLoading');
            var detalhes = document.getElementById('enderecoDetalhes');

            if (cepLimpo.length !== 8) {
                return;
            }

            cepLoading.style.display = 'block';
            cepMsg.style.display = 'none';

            try {
                var response = await fetch('https://viacep.com.br/ws/' + cepLimpo + '/json/');
                var data = await response.json();

                if (data.erro) {
                    cepMsg.style.display = 'block';
                    cepMsg.style.color = 'var(--danger)';
                    cepMsg.innerHTML = '<i class="fas fa-times-circle"></i> CEP não encontrado';
                    detalhes.style.display = 'none';
                    return;
                }

                // Preencher campos
                document.getElementById('clienteRua').value = data.logradouro || '';
                document.getElementById('clienteBairro').value = data.bairro || '';
                document.getElementById('clienteCidade').value = data.localidade || '';
                document.getElementById('clienteEstado').value = data.uf || '';

                // Se logradouro veio vazio (CEP genérico), permitir edição
                if (!data.logradouro) {
                    document.getElementById('clienteRua').removeAttribute('readonly');
                    document.getElementById('clienteRua').style.opacity = '1';
                } else {
                    document.getElementById('clienteRua').setAttribute('readonly', true);
                    document.getElementById('clienteRua').style.opacity = '0.8';
                }
                if (!data.bairro) {
                    document.getElementById('clienteBairro').removeAttribute('readonly');
                    document.getElementById('clienteBairro').style.opacity = '1';
                } else {
                    document.getElementById('clienteBairro').setAttribute('readonly', true);
                    document.getElementById('clienteBairro').style.opacity = '0.8';
                }

                detalhes.style.display = 'block';
                cepMsg.style.display = 'block';
                cepMsg.style.color = 'var(--success)';
                cepMsg.innerHTML = '<i class="fas fa-check-circle"></i> ' + (data.logradouro ? data.logradouro + ', ' : '') + data.localidade + '/' + data.uf;

                // Focar no campo número
                document.getElementById('clienteNumero').focus();

                composerEndereco();

                // Detectar zona de entrega automaticamente pelo bairro
                if (data.bairro && tipoEntrega === 'entrega') {
                    detectarZona(data.bairro);
                }
            } catch (error) {
                cepMsg.style.display = 'block';
                cepMsg.style.color = 'var(--danger)';
                cepMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Erro ao buscar CEP. Tente novamente.';
            } finally {
                cepLoading.style.display = 'none';
            }
        }

        function composerEndereco() {
            var rua = document.getElementById('clienteRua').value.trim();
            var numero = document.getElementById('clienteNumero').value.trim();
            var complemento = document.getElementById('clienteComplemento').value.trim();
            var bairro = document.getElementById('clienteBairro').value.trim();
            var cidade = document.getElementById('clienteCidade').value.trim();
            var estado = document.getElementById('clienteEstado').value.trim();

            var partes = [];
            if (rua) partes.push(rua);
            if (numero) partes.push(numero);
            if (complemento) partes.push(complemento);
            if (bairro) partes.push(bairro);

            var endereco = partes.join(', ');
            if (cidade) endereco += ' - ' + cidade;
            if (estado) endereco += '/' + estado;

            document.getElementById('clienteEndereco').value = endereco;
            validateForm();
        }

        function setTipoEntrega(tipo) {
            tipoEntrega = tipo;
            document.querySelectorAll('[data-tipo]').forEach(function(btn) {
                btn.classList.toggle('selected', btn.dataset.tipo === tipo);
            });
            var enderecoGroup = document.getElementById('enderecoGroup');
            var taxaInfoEl = document.getElementById('taxaInfo');
            if (tipo === 'retirada') {
                if (enderecoGroup) enderecoGroup.style.display = 'none';
                if (taxaInfoEl) taxaInfoEl.style.display = 'none';
                taxaEntrega = 0;
                selectedZonaId = null;
            } else {
                if (enderecoGroup) enderecoGroup.style.display = 'block';
                // Mostrar taxa se ja foi detectada
                if (selectedZonaId && taxaInfoEl) taxaInfoEl.style.display = 'flex';
            }
            updateCartUI();
        }

        // Detectar zona automaticamente pelo bairro (chamado apos buscar CEP)
        async function detectarZona(bairro) {
            if (!bairro) return;

            try {
                var response = await fetch(baseUrl + 'cardapio/api_detectar_zona?bairro=' + encodeURIComponent(bairro));
                var data = await response.json();
                var taxaInfo = document.getElementById('taxaInfo');
                var taxaInfoText = document.getElementById('taxaInfoText');

                if (data.found && data.zona) {
                    selectedZonaId = data.zona.id;
                    taxaEntrega = data.zona.taxa;
                    document.getElementById('zonaDetectadaId').value = data.zona.id;
                    document.getElementById('zonaDetectadaNome').value = data.zona.nome;

                    var tempoText = data.zona.tempo_min + '-' + data.zona.tempo_max + ' min';
                    if (taxaEntrega == 0) {
                        taxaInfoText.innerHTML = '<strong>' + data.zona.nome + '</strong> &bull; Entrega em ' + tempoText + ' &bull; <strong style="color:var(--success)">GRATIS</strong>';
                    } else {
                        taxaInfoText.innerHTML = '<strong>' + data.zona.nome + '</strong> &bull; Entrega em ' + tempoText + ' &bull; Taxa: <strong>R$ ' + taxaEntrega.toFixed(2).replace('.', ',') + '</strong>';
                    }
                    taxaInfo.style.display = 'flex';
                    taxaInfo.style.borderColor = 'var(--success)';
                } else {
                    // Bairro nao encontrado nas zonas — usar taxa fixa padrao
                    selectedZonaId = null;
                    taxaEntrega = taxaFixa;
                    document.getElementById('zonaDetectadaId').value = '';
                    document.getElementById('zonaDetectadaNome').value = '';

                    if (taxaEntrega > 0) {
                        taxaInfoText.innerHTML = 'Taxa de entrega: <strong>R$ ' + taxaEntrega.toFixed(2).replace('.', ',') + '</strong>';
                    } else {
                        taxaInfoText.innerHTML = '<strong style="color:var(--success)">Entrega GRATIS</strong>';
                    }
                    taxaInfo.style.display = 'flex';
                    taxaInfo.style.borderColor = '';
                }
            } catch (error) {
                console.error('Erro ao detectar zona:', error);
                // Fallback para taxa fixa
                taxaEntrega = taxaFixa;
                selectedZonaId = null;
            }
            updateCartUI();
            validateForm();
        }

        function addToCart(btn) {
            const card = btn.closest('.product-card');
            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);
            const unit = card.dataset.unit;

            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.qty++;
            } else {
                cart.push({ id, name, price, unit, qty: 1 });
            }

            updateCartUI();
            showToast('Produto adicionado!');
            
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => btn.innerHTML = '<i class="fas fa-plus"></i>', 500);
        }

        function updateQty(id, delta) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.qty += delta;
                if (item.qty <= 0) cart = cart.filter(i => i.id !== id);
            }
            updateCartUI();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCartUI();
        }

        function updateCartUI() {
            const cartCount = document.getElementById('cartCount');
            const cartItems = document.getElementById('cartItems');
            const deliveryForm = document.getElementById('deliveryForm');

            var totalItems = cart.reduce(function(sum, item) { return sum + item.qty; }, 0);
            var subtotal = cart.reduce(function(sum, item) { return sum + (item.price * item.qty); }, 0);
            var desconto = cupomAplicado ? cupomAplicado.desconto : 0;
            var total = subtotal + taxaEntrega - desconto;
            if (total < 0) total = 0;

            cartCount.textContent = totalItems;
            document.getElementById('subtotalValue').textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
            document.getElementById('taxaValue').textContent = taxaEntrega == 0 ? 'GRATIS' : 'R$ ' + taxaEntrega.toFixed(2).replace('.', ',');
            document.getElementById('totalValue').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');

            // Desconto
            var descontoRow = document.getElementById('descontoRow');
            if (desconto > 0 && descontoRow) {
                descontoRow.style.display = 'flex';
                document.getElementById('descontoValue').textContent = '- R$ ' + desconto.toFixed(2).replace('.', ',');
            } else if (descontoRow) {
                descontoRow.style.display = 'none';
            }

            // Show cupom field when cart has items
            var cupomGroup = document.getElementById('cupomGroup');
            if (cupomGroup) cupomGroup.style.display = cart.length > 0 ? 'block' : 'none';

            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="cart-empty">
                        <i class="fas fa-shopping-basket"></i>
                        <p>Seu carrinho está vazio</p>
                        <p style="font-size:0.85rem;">Adicione produtos para fazer seu pedido</p>
                    </div>
                `;
                deliveryForm.style.display = 'none';
            } else {
                cartItems.innerHTML = cart.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-price">
                                R$ ${(item.price * item.qty).toFixed(2).replace('.', ',')}
                                <span style="color:var(--text-secondary);font-size:0.75rem;">
                                    (${item.qty}x R$ ${item.price.toFixed(2).replace('.', ',')})
                                </span>
                            </div>
                        </div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQty('${item.id}', -1)"><i class="fas fa-minus"></i></button>
                            <span class="qty-value">${item.qty}</span>
                            <button class="qty-btn" onclick="updateQty('${item.id}', 1)"><i class="fas fa-plus"></i></button>
                        </div>
                        <button class="cart-item-remove" onclick="removeFromCart('${item.id}')"><i class="fas fa-trash"></i></button>
                    </div>
                `).join('');
                deliveryForm.style.display = 'block';
            }

            validateForm();
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        function validateForm() {
            var nome = document.getElementById('clienteNome').value.trim();
            var telefone = document.getElementById('clienteTelefone').value.trim();
            var subtotal = cart.reduce(function(sum, item) { return sum + (item.price * item.qty); }, 0);

            var isValid = cart.length > 0 && nome && telefone && selectedPayment;

            // Endereco obrigatorio apenas para entrega
            if (tipoEntrega === 'entrega') {
                var cep = document.getElementById('clienteCep').value.replace(/\D/g, '');
                var rua = document.getElementById('clienteRua').value.trim();
                var numero = document.getElementById('clienteNumero').value.trim();
                isValid = isValid && cep.length === 8 && rua && numero;
            }

            // Loja fechada
            if (!lojaAberta) {
                isValid = false;
            }

            // Pedido minimo
            var minimoMsg = document.getElementById('pedidoMinimoMsg');
            if (pedidoMinimo > 0 && subtotal < pedidoMinimo) {
                isValid = false;
                var falta = (pedidoMinimo - subtotal).toFixed(2).replace('.', ',');
                if (!minimoMsg) {
                    minimoMsg = document.createElement('div');
                    minimoMsg.id = 'pedidoMinimoMsg';
                    minimoMsg.style.cssText = 'color:var(--danger);font-size:0.85rem;text-align:center;padding:8px;margin:5px 0;border:1px solid rgba(255,82,82,0.3);border-radius:8px;background:rgba(255,82,82,0.1);';
                    var totals = document.getElementById('cartTotals');
                    if (totals) totals.parentNode.insertBefore(minimoMsg, totals);
                }
                minimoMsg.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Pedido minimo R$ ' + pedidoMinimo.toFixed(2).replace('.', ',') + ' — falta R$ ' + falta;
                minimoMsg.style.display = 'block';
            } else if (minimoMsg) {
                minimoMsg.style.display = 'none';
            }

            document.getElementById('btnWhatsapp').disabled = !isValid;
            document.getElementById('btnSite').disabled = !isValid;
        }

        function toggleCart() {
            document.getElementById('cartModal').classList.toggle('open');
            document.getElementById('cartOverlay').classList.toggle('open');
        }

        function getFormData() {
            const cpfNota = document.getElementById('querCpf').checked ? document.getElementById('cpfNota').value.trim() : '';
            // Compor endereço antes de capturar
            composerEndereco();
            return {
                cliente_nome: document.getElementById('clienteNome').value.trim(),
                cliente_telefone: document.getElementById('clienteTelefone').value.trim(),
                cliente_endereco: document.getElementById('clienteEndereco').value.trim(),
                cliente_cep: document.getElementById('clienteCep').value.trim(),
                cliente_complemento: document.getElementById('clienteComplemento').value.trim(),
                cliente_cidade: document.getElementById('clienteCidade').value.trim(),
                cliente_estado: document.getElementById('clienteEstado').value.trim(),
                forma_pagamento: selectedPayment,
                troco_para: document.getElementById('trocoPara').value.trim(),
                observacao: document.getElementById('observacao').value.trim(),
                cpf_nota: cpfNota
            };
        }

        function finalizarWhatsapp() {
            var data = getFormData();
            if (!data.cliente_nome || !data.cliente_telefone || !selectedPayment) {
                showToast('Preencha todos os campos obrigatorios');
                return;
            }
            if (tipoEntrega === 'entrega' && !data.cliente_endereco) {
                showToast('Preencha o endereco de entrega');
                return;
            }

            var subtotal = cart.reduce(function(sum, item) { return sum + (item.price * item.qty); }, 0);
            var desconto = cupomAplicado ? cupomAplicado.desconto : 0;
            var total = subtotal + taxaEntrega - desconto;
            if (total < 0) total = 0;

            var message = '* PEDIDO - ' + storeName + '*\n\n';
            message += '*Cliente:* ' + data.cliente_nome + '\n';
            message += '*Telefone:* ' + data.cliente_telefone + '\n';
            if (tipoEntrega === 'retirada') {
                message += '*Tipo:* Retirada na loja\n\n';
            } else {
                message += '*Endereco:* ' + data.cliente_endereco + '\n\n';
            }
            message += '*ITENS:*\n';

            cart.forEach(function(item) {
                message += '- ' + item.qty + 'x ' + item.name + ' - R$ ' + (item.price * item.qty).toFixed(2).replace('.', ',') + '\n';
            });

            message += '\n*Subtotal:* R$ ' + subtotal.toFixed(2).replace('.', ',') + '\n';
            if (tipoEntrega !== 'retirada') {
                message += '*Taxa de Entrega:* ' + (taxaEntrega == 0 ? 'GRATIS' : 'R$ ' + taxaEntrega.toFixed(2).replace('.', ',')) + '\n';
            }
            if (desconto > 0) {
                message += '*Desconto:* - R$ ' + desconto.toFixed(2).replace('.', ',') + '\n';
            }
            message += '*TOTAL:* R$ ' + total.toFixed(2).replace('.', ',') + '\n\n';
            message += '*Pagamento:* ' + data.forma_pagamento.charAt(0).toUpperCase() + data.forma_pagamento.slice(1) + '\n';

            if (data.forma_pagamento == 'dinheiro' && data.troco_para) {
                message += '*Troco para:* R$ ' + data.troco_para + '\n';
            }
            if (data.observacao) {
                message += '\n*Obs:* ' + data.observacao + '\n';
            }

            saveOrder('whatsapp').then(function(response) {
                if (response.success) {
                    // Salvar telefone para identificar pedidos pendentes
                    localStorage.setItem('cliente_telefone', data.cliente_telefone);
                    // Limpar carrinho
                    cart = [];
                    updateCartUI();
                    toggleCart();
                    showToast('Pedido #' + response.order_number + ' registrado!');
                    // Verificar pedidos pendentes
                    verificarPedidosPendentes();
                }
                window.open('https://wa.me/55' + whatsappNumber + '?text=' + encodeURIComponent(message), '_blank');
            });
        }

        function finalizarSite() {
            var data = getFormData();
            if (!data.cliente_nome || !data.cliente_telefone || !selectedPayment) {
                showToast('Preencha todos os campos obrigatorios');
                return;
            }
            if (tipoEntrega === 'entrega' && !data.cliente_endereco) {
                showToast('Preencha o endereco de entrega');
                return;
            }

            saveOrder('site').then(function(response) {
                if (response.success) {
                    // Salvar telefone para identificar pedidos pendentes
                    localStorage.setItem('cliente_telefone', data.cliente_telefone);
                    // Se tem redirect_url (PIX/Cartao), ir para pagina de pagamento
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                    } else {
                        // Dinheiro — ir direto para confirmacao
                        window.location.href = baseUrl + 'cardapio/confirmacao/' + response.order_number;
                    }
                } else {
                    showToast('Erro: ' + (response.message || 'Tente novamente'));
                }
            });
        }

        async function saveOrder(tipo) {
            var data = getFormData();
            data.tipo_checkout = tipo;
            data.tipo_entrega = tipoEntrega;
            data.items = cart;
            data.taxa_entrega = taxaEntrega;
            var zonaId = document.getElementById('zonaDetectadaId').value;
            if (zonaId) data.zona_id = parseInt(zonaId);
            else if (selectedZonaId) data.zona_id = selectedZonaId;
            if (cupomAplicado) {
                data.cupom_codigo = cupomAplicado.codigo;
                data.desconto_cupom = cupomAplicado.desconto;
            }

            try {
                var response = await fetch(baseUrl + 'cardapio/processar_pedido', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                return await response.json();
            } catch (error) {
                console.error('Erro:', error);
                return { success: false, message: 'Erro de conexao' };
            }
        }

        async function aplicarCupom() {
            var codigo = document.getElementById('cupomCodigo').value.trim().toUpperCase();
            var cupomMsg = document.getElementById('cupomMsg');
            if (!codigo) { return; }
            var subtotal = cart.reduce(function(sum, item) { return sum + (item.price * item.qty); }, 0);
            try {
                var response = await fetch(baseUrl + 'cardapio/api/validar_cupom?codigo=' + encodeURIComponent(codigo) + '&subtotal=' + subtotal);
                var data = await response.json();
                cupomMsg.style.display = 'block';
                if (data.valido) {
                    cupomAplicado = {codigo: codigo, tipo: data.tipo, desconto: data.desconto_calculado};
                    if (data.tipo === 'frete_gratis') {
                        taxaEntrega = 0;
                        cupomAplicado.desconto = 0;
                    }
                    cupomMsg.style.color = 'var(--success)';
                    cupomMsg.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                    showToast('Cupom aplicado!');
                } else {
                    cupomAplicado = null;
                    cupomMsg.style.color = 'var(--danger)';
                    cupomMsg.innerHTML = '<i class="fas fa-times-circle"></i> ' + data.message;
                }
                updateCartUI();
            } catch (e) {
                cupomMsg.style.display = 'block';
                cupomMsg.style.color = 'var(--danger)';
                cupomMsg.textContent = 'Erro ao validar cupom';
            }
        }

        async function verificarPedidosPendentes() {
            const telefone = localStorage.getItem('cliente_telefone');
            if (!telefone) return;

            try {
                const telLimpo = telefone.replace(/\D/g, '');
                const response = await fetch(baseUrl + 'cardapio/api/pedidos_pendentes?tel=' + encodeURIComponent(telLimpo));
                const data = await response.json();

                const banner = document.getElementById('pedidosPendentesBanner');
                if (data.success && data.pedidos.length > 0) {
                    let html = '<div class="banner-header"><i class="fas fa-bell"></i> Voce tem ' + data.pedidos.length + ' pedido(s) em andamento</div>';
                    data.pedidos.forEach(function(pedido) {
                        const total = parseFloat(pedido.total).toFixed(2).replace('.', ',');
                        html += '<div class="pedido-pendente-item">' +
                            '<div class="pedido-pendente-info">' +
                                '<strong>#' + pedido.order_number + '</strong>' +
                                '<span class="pedido-pendente-status status-' + pedido.status + '">' + pedido.status_label + '</span>' +
                                '<span>R$ ' + total + '</span>' +
                            '</div>' +
                            '<a href="' + baseUrl + 'cardapio/acompanhar/' + pedido.order_number + '" class="pedido-pendente-link">' +
                                'Acompanhar <i class="fas fa-chevron-right"></i>' +
                            '</a>' +
                        '</div>';
                    });
                    banner.innerHTML = html;
                    banner.style.display = 'block';
                } else {
                    banner.style.display = 'none';
                }
            } catch (e) {
                console.error('Erro ao verificar pedidos:', e);
            }
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                updateCartUI();
            }

            // Verificar pedidos pendentes do cliente (imediato + a cada 30s)
            verificarPedidosPendentes();
            setInterval(verificarPedidosPendentes, 30000);

            // Preencher telefone salvo no campo, se existir
            const savedTel = localStorage.getItem('cliente_telefone');
            if (savedTel && document.getElementById('clienteTelefone').value === '') {
                document.getElementById('clienteTelefone').value = savedTel;
            }

            document.querySelectorAll('.payment-option[data-payment]').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.payment-option[data-payment]').forEach(o => o.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedPayment = this.dataset.payment;
                    
                    const trocoField = document.getElementById('trocoField');
                    trocoField.classList.toggle('show', selectedPayment == 'dinheiro');
                    
                    validateForm();
                });
            });

            ['clienteNome', 'clienteTelefone', 'clienteNumero', 'clienteComplemento'].forEach(id => {
                document.getElementById(id).addEventListener('input', validateForm);
            });

            // Atualizar endereço composto ao editar número/complemento
            ['clienteNumero', 'clienteComplemento', 'clienteRua', 'clienteBairro'].forEach(id => {
                document.getElementById(id).addEventListener('input', composerEndereco);
            });

            document.getElementById('clienteTelefone').addEventListener('input', function(e) {
                let val = e.target.value.replace(/\D/g, '');
                if (val.length > 11) val = val.slice(0, 11);
                if (val.length > 6) val = '(' + val.slice(0,2) + ') ' + val.slice(2,7) + '-' + val.slice(7);
                else if (val.length > 2) val = '(' + val.slice(0,2) + ') ' + val.slice(2);
                else if (val.length > 0) val = '(' + val;
                e.target.value = val;

                // Buscar cliente automaticamente quando completar 10-11 digitos
                if (val.replace(/\D/g, '').length >= 10) {
                    clearTimeout(window._buscaClienteTimer);
                    window._buscaClienteTimer = setTimeout(function() {
                        buscarCliente(val.replace(/\D/g, ''));
                    }, 500);
                }
            });

            // Formatação e busca do CEP
            document.getElementById('clienteCep').addEventListener('input', function(e) {
                e.target.value = formatCEP(e.target.value);
                var cepLimpo = e.target.value.replace(/\D/g, '');
                if (cepLimpo.length === 8) {
                    buscarCEP(cepLimpo);
                }
            });

            // Formatação automática do CPF
            document.getElementById('cpfNota').addEventListener('input', function(e) {
                e.target.value = formatCPF(e.target.value);
            });

            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const category = this.dataset.category;
                    document.querySelectorAll('.category-group').forEach(group => {
                        group.style.display = (category === 'all' || group.dataset.category === category) ? 'block' : 'none';
                    });
                });
            });

            document.getElementById('searchInput').addEventListener('input', function() {
                const term = this.value.toLowerCase();
                document.querySelectorAll('.product-card').forEach(card => {
                    card.style.display = card.dataset.name.toLowerCase().includes(term) || term === '' ? 'block' : 'none';
                });
                if (term) document.querySelectorAll('.category-group').forEach(g => g.style.display = 'block');
            });
        });
    </script>
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('<?php echo base_url('cardapio/sw.js'); ?>')
        .then(function(reg) { console.log('SW registered'); })
        .catch(function(err) { console.log('SW error:', err); });
}
</script>
</body>
</html>
