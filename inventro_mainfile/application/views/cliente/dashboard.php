<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Minha Conta - <?php echo html_escape($loja->title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --primary: #25D366;
            --primary-dark: #128C7E;
            --bg-dark: #1a1a2e;
            --bg-card: #16213e;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --success: #00c853;
            --danger: #f44336;
            --warning: #ff9800;
            --blue: #3498db;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* ===== Welcome Section ===== */
        .welcome-section {
            padding: 24px 0 8px;
        }

        .welcome-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ===== Flash Message ===== */
        .alert-success {
            background: rgba(0, 200, 83, 0.15);
            border: 1px solid rgba(0, 200, 83, 0.3);
            color: var(--success);
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            margin: 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success i {
            font-size: 16px;
        }

        /* ===== Stats Cards ===== */
        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 20px 0;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: 14px;
            padding: 20px 16px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .stat-card-icon.pedidos {
            background: rgba(52, 152, 219, 0.15);
            color: var(--blue);
        }

        .stat-card-icon.gasto {
            background: rgba(37, 211, 102, 0.15);
            color: var(--primary);
        }

        .stat-card-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .stat-card-label {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 4px;
            font-weight: 500;
        }

        /* ===== Recent Orders Section ===== */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 28px 0 16px;
        }

        .section-title {
            font-size: 17px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .section-link {
            font-size: 13px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .section-link:hover {
            color: var(--primary-dark);
        }

        /* ===== Empty State ===== */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: var(--bg-card);
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.6;
        }

        .empty-state-text {
            font-size: 15px;
            color: var(--text-secondary);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .empty-state-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .empty-state-btn:hover {
            background: var(--primary-dark);
        }

        /* ===== Order Cards ===== */
        .order-card {
            background: var(--bg-card);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .order-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .order-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .order-number {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .order-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-pendente_pagamento {
            background: rgba(255, 152, 0, 0.15);
            color: var(--warning);
        }

        .badge-pendente {
            background: rgba(52, 152, 219, 0.15);
            color: var(--blue);
        }

        .badge-confirmado {
            background: rgba(52, 152, 219, 0.15);
            color: var(--blue);
        }

        .badge-preparando {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .badge-saiu_entrega {
            background: rgba(156, 39, 176, 0.15);
            color: #ab47bc;
        }

        .badge-entregue {
            background: rgba(0, 200, 83, 0.15);
            color: var(--success);
        }

        .badge-cancelado {
            background: rgba(244, 67, 54, 0.15);
            color: var(--danger);
        }

        .order-card-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .order-date {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .order-total {
            font-size: 15px;
            font-weight: 700;
            color: var(--primary);
        }

        .order-card-action {
            display: block;
            text-align: center;
            padding: 10px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .order-card-action:hover {
            background: rgba(37, 211, 102, 0.1);
            color: var(--primary);
        }

        /* ===== Quick Actions ===== */
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin: 28px 0;
        }

        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 20px 12px;
            background: var(--bg-card);
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .quick-action:hover {
            transform: translateY(-2px);
            border-color: rgba(37, 211, 102, 0.3);
        }

        .quick-action-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .quick-action-icon.qa-pedido {
            background: rgba(37, 211, 102, 0.15);
            color: var(--primary);
        }

        .quick-action-icon.qa-perfil {
            background: rgba(52, 152, 219, 0.15);
            color: var(--blue);
        }

        .quick-action-icon.qa-pagamentos {
            background: rgba(255, 152, 0, 0.15);
            color: var(--warning);
        }

        .quick-action-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
            text-align: center;
        }

        /* ===== Responsive ===== */
        @media (max-width: 400px) {
            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .stat-card {
                padding: 16px 12px;
            }

            .stat-card-value {
                font-size: 20px;
            }

            .quick-actions {
                gap: 8px;
            }

            .quick-action {
                padding: 16px 8px;
            }
        }
    </style>
</head>
<body>

<?php $this->load->view('cliente/_header'); ?>

<div class="container">

    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1 class="welcome-title">Bem-vindo, <?php echo html_escape($customer->name); ?>! &#128075;</h1>
    </div>

    <!-- Flash Message -->
    <?php if (!empty($sucesso)): ?>
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo html_escape($sucesso); ?>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-card-icon pedidos">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-card-value"><?php echo (int)$stats->total_pedidos; ?></div>
            <div class="stat-card-label">Total de Pedidos</div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon gasto">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-card-value">R$ <?php echo number_format($stats->total_gasto, 2, ',', '.'); ?></div>
            <div class="stat-card-label">Total Gasto</div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="section-header">
        <h2 class="section-title">Ultimos Pedidos</h2>
        <a href="<?php echo base_url('cliente/pedidos'); ?>" class="section-link">Ver Todos &rarr;</a>
    </div>

    <?php if (empty($recent_orders)): ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <p class="empty-state-text">
                Nenhum pedido ainda.<br>
                Faca seu primeiro pedido!
            </p>
            <a href="<?php echo base_url('cardapio'); ?>" class="empty-state-btn">
                <i class="fas fa-utensils"></i>
                Ver Cardapio
            </a>
        </div>
    <?php else: ?>
        <!-- Order Cards -->
        <?php
        $status_labels = array(
            'pendente_pagamento' => 'Aguardando pagamento',
            'pendente'           => 'Aguardando confirmacao',
            'confirmado'         => 'Confirmado',
            'preparando'         => 'Em preparo',
            'saiu_entrega'       => 'Saiu para entrega',
            'entregue'           => 'Entregue',
            'cancelado'          => 'Cancelado',
        );
        ?>
        <?php foreach ($recent_orders as $order): ?>
            <div class="order-card">
                <div class="order-card-top">
                    <span class="order-number">#<?php echo htmlspecialchars(str_pad($order->order_number, 4, '0', STR_PAD_LEFT), ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="order-badge badge-<?php echo htmlspecialchars($order->status, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php
                        $status_key = $order->status;
                        echo html_escape(isset($status_labels[$status_key]) ? $status_labels[$status_key] : ucfirst($status_key));
                        ?>
                    </span>
                </div>
                <div class="order-card-info">
                    <span class="order-date">
                        <i class="far fa-calendar-alt"></i>
                        <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?>
                    </span>
                    <span class="order-total">R$ <?php echo number_format($order->total, 2, ',', '.'); ?></span>
                </div>
                <a href="<?php echo base_url('cliente/pedido/' . htmlspecialchars(urlencode($order->order_number), ENT_QUOTES, 'UTF-8')); ?>"
                   class="order-card-action">
                    Ver Detalhes <i class="fas fa-chevron-right" style="margin-left: 4px;"></i>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Quick Actions -->
    <div class="section-header">
        <h2 class="section-title">Acoes Rapidas</h2>
    </div>

    <div class="quick-actions">
        <a href="<?php echo base_url('cardapio'); ?>" class="quick-action">
            <div class="quick-action-icon qa-pedido">
                <i class="fas fa-utensils"></i>
            </div>
            <span class="quick-action-label">Fazer Pedido</span>
        </a>
        <a href="<?php echo base_url('cliente/perfil'); ?>" class="quick-action">
            <div class="quick-action-icon qa-perfil">
                <i class="fas fa-user-circle"></i>
            </div>
            <span class="quick-action-label">Meu Perfil</span>
        </a>
        <a href="<?php echo base_url('cliente/pagamentos'); ?>" class="quick-action">
            <div class="quick-action-icon qa-pagamentos">
                <i class="fas fa-credit-card"></i>
            </div>
            <span class="quick-action-label">Pagamentos</span>
        </a>
    </div>

</div>

<?php $this->load->view('cliente/_footer'); ?>

</body>
</html>
