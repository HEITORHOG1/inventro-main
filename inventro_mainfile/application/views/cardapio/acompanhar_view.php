<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhar Pedido - <?php echo html_escape($loja->title ?? 'Cardápio Digital'); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #25D366;
            --primary-dark: #128C7E;
            --bg-dark: #1a1a2e;
            --bg-card: #16213e;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --success: #00c853;
            --danger: #ff5252;
            --warning: #ffab00;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-card) 100%);
            min-height: 100vh;
            color: var(--text-primary);
            padding: 20px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .page-header .store-name {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 5px;
        }

        .page-header .client-name {
            font-size: 1.1rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }

        .order-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1.2;
        }

        .order-date {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 5px;
        }

        /* Card base */
        .card {
            background: rgba(22, 33, 62, 0.95);
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--primary);
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .timeline-step {
            position: relative;
            padding-bottom: 32px;
        }

        .timeline-step:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -40px;
            top: 0;
            width: 33px;
            height: 33px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            z-index: 1;
            transition: all 0.3s;
        }

        .timeline-step.completed .timeline-dot {
            background: var(--primary);
            color: white;
            box-shadow: 0 0 15px rgba(37, 211, 102, 0.4);
        }

        .timeline-step.current .timeline-dot {
            background: var(--primary);
            color: white;
            box-shadow: 0 0 20px rgba(37, 211, 102, 0.6);
            animation: pulse-dot 2s infinite;
        }

        .timeline-step.pending .timeline-dot {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            border: 2px solid rgba(255, 255, 255, 0.15);
        }

        /* Pulse animation for current step */
        @keyframes pulse-dot {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 20px rgba(37, 211, 102, 0.6);
            }
            50% {
                transform: scale(1.15);
                box-shadow: 0 0 30px rgba(37, 211, 102, 0.8);
            }
        }

        /* Line fill for completed steps */
        .timeline-step.completed::after {
            content: '';
            position: absolute;
            left: -25px;
            top: 33px;
            width: 3px;
            height: calc(100% - 33px);
            background: var(--primary);
            border-radius: 3px;
        }

        .timeline-step:last-child::after {
            display: none;
        }

        .timeline-step.current::after {
            content: '';
            position: absolute;
            left: -25px;
            top: 33px;
            width: 3px;
            height: calc(100% - 33px);
            background: linear-gradient(to bottom, var(--primary), rgba(255, 255, 255, 0.1));
            border-radius: 3px;
        }

        .timeline-label {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2px;
            transition: color 0.3s;
        }

        .timeline-step.completed .timeline-label,
        .timeline-step.current .timeline-label {
            color: var(--text-primary);
        }

        .timeline-step.pending .timeline-label {
            color: var(--text-secondary);
        }

        .timeline-time {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .timeline-step.current .timeline-time {
            color: var(--primary);
            font-weight: 500;
        }

        /* Cancelled banner */
        .cancelled-banner {
            background: rgba(255, 82, 82, 0.15);
            border: 1px solid rgba(255, 82, 82, 0.4);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
        }

        .cancelled-banner .icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 82, 82, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .cancelled-banner .icon i {
            font-size: 2rem;
            color: var(--danger);
        }

        .cancelled-banner h2 {
            color: var(--danger);
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .cancelled-banner p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Order summary */
        .items-list {
            margin-bottom: 15px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            color: var(--text-secondary);
            font-size: 0.9rem;
            flex: 1;
        }

        .item-qty {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin: 0 12px;
            white-space: nowrap;
        }

        .item-price {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        /* Totals */
        .totals-section {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
            margin-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 0.9rem;
        }

        .total-row .label {
            color: var(--text-secondary);
        }

        .total-row .value {
            font-weight: 500;
        }

        .total-row.final {
            border-top: 2px solid rgba(37, 211, 102, 0.3);
            margin-top: 8px;
            padding-top: 12px;
            font-size: 1.15rem;
            font-weight: 700;
        }

        .total-row.final .value {
            color: var(--primary);
        }

        /* Info row */
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.9rem;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .label {
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-row .value {
            font-weight: 500;
        }

        /* Delivery type badge */
        .delivery-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(37, 211, 102, 0.15);
            border: 1px solid rgba(37, 211, 102, 0.3);
            border-radius: 50px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--primary);
            margin-top: 10px;
        }

        /* Auto-refresh indicator */
        .refresh-indicator {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .refresh-indicator .dot {
            width: 6px;
            height: 6px;
            background: var(--primary);
            border-radius: 50%;
            animation: blink 2s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Buttons */
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4);
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }

        /* Error page */
        .error-card {
            text-align: center;
        }

        .error-icon {
            width: 90px;
            height: 90px;
            background: rgba(255, 82, 82, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .error-icon i {
            font-size: 2.5rem;
            color: var(--danger);
        }

        .error-card h1 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .error-card p {
            color: var(--text-secondary);
            margin-bottom: 25px;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .card {
                padding: 20px;
                border-radius: 16px;
            }

            .order-number {
                font-size: 2rem;
            }

            .timeline {
                padding-left: 35px;
            }

            .timeline-dot {
                width: 28px;
                height: 28px;
                left: -35px;
                font-size: 0.7rem;
            }

            .timeline::before {
                left: 12px;
            }

            .timeline-step.completed::after,
            .timeline-step.current::after {
                left: -23px;
            }
        }
    </style>
</head>
<body>
    <div class="container">

        <?php if (isset($erro)): ?>
            <!-- Error state -->
            <div class="card error-card">
                <div class="error-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h1>Pedido não encontrado</h1>
                <p><?php echo html_escape($erro); ?></p>
                <a href="<?php echo base_url('cardapio'); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Cardápio
                </a>
            </div>

        <?php else: ?>
            <!-- Header -->
            <div class="page-header">
                <div class="store-name">
                    <i class="fas fa-store"></i>
                    <?php echo html_escape($loja->title ?? 'Cardápio Digital'); ?>
                </div>
                <div class="client-name">
                    Pedido de <?php echo html_escape($order->cliente_nome); ?>
                </div>
                <div class="order-number">#<?php echo html_escape($order->order_number); ?></div>
                <div class="order-date">
                    <i class="fas fa-clock"></i>
                    <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?>
                </div>
                <?php
                    $is_retirada = (isset($order->tipo_entrega) && $order->tipo_entrega === 'retirada');
                ?>
                <div class="delivery-badge">
                    <?php if ($is_retirada): ?>
                        <i class="fas fa-bag-shopping"></i> Retirada no local
                    <?php else: ?>
                        <i class="fas fa-motorcycle"></i> Entrega
                        <?php if (!empty($order->zona_nome)): ?>
                            - <?php echo html_escape($order->zona_nome); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($order->status) && $order->status === 'cancelado'): ?>
                <!-- Cancelled banner -->
                <div class="cancelled-banner">
                    <div class="icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <h2>Pedido Cancelado</h2>
                    <p>Infelizmente este pedido foi cancelado. Entre em contato com a loja para mais informações.</p>
                    <?php if (!empty($loja->phone)): ?>
                        <div style="margin-top: 15px;">
                            <a href="https://wa.me/55<?php echo html_escape($loja->phone); ?>" target="_blank" class="btn btn-whatsapp" style="display:inline-flex; width:auto;">
                                <i class="fab fa-whatsapp"></i> Falar com a loja
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else: ?>
                <!-- Timeline card -->
                <div class="card" id="timelineCard">
                    <div class="card-title">
                        <i class="fas fa-route"></i> Acompanhamento
                    </div>

                    <?php
                        // Define status order for comparison
                        $status_order = ['recebido' => 0, 'confirmado' => 1, 'preparando' => 2, 'pronto_coleta' => 3, 'saiu_entrega' => 4, 'entregue' => 5];
                        $current_status = isset($order->status) ? $order->status : 'recebido';
                        $current_index = isset($status_order[$current_status]) ? $status_order[$current_status] : 0;

                        // Timeline steps configuration
                        $steps = [
                            [
                                'key'   => 'recebido',
                                'icon'  => 'fa-clipboard-check',
                                'label' => 'Recebido',
                                'time'  => isset($order->created_at) ? $order->created_at : null,
                            ],
                            [
                                'key'   => 'confirmado',
                                'icon'  => 'fa-thumbs-up',
                                'label' => 'Confirmado',
                                'time'  => isset($order->hora_confirmado) ? $order->hora_confirmado : null,
                            ],
                            [
                                'key'   => 'preparando',
                                'icon'  => 'fa-fire-burner',
                                'label' => 'Preparando',
                                'time'  => isset($order->hora_preparando) ? $order->hora_preparando : null,
                            ],
                            [
                                'key'   => 'pronto_coleta',
                                'icon'  => 'fa-box-open',
                                'label' => 'Pedido Pronto!',
                                'time'  => isset($order->hora_pronto_coleta) ? $order->hora_pronto_coleta : null,
                            ],
                            [
                                'key'   => 'saiu_entrega',
                                'icon'  => $is_retirada ? 'fa-bag-shopping' : 'fa-motorcycle',
                                'label' => $is_retirada ? 'Pronto para Retirada' : 'A caminho',
                                'time'  => isset($order->hora_saiu_entrega) ? $order->hora_saiu_entrega : null,
                            ],
                            [
                                'key'   => 'entregue',
                                'icon'  => 'fa-circle-check',
                                'label' => $is_retirada ? 'Retirado' : 'Entregue',
                                'time'  => isset($order->hora_entregue) ? $order->hora_entregue : null,
                            ],
                        ];
                    ?>

                    <div class="timeline" id="timeline">
                        <?php foreach ($steps as $i => $step): ?>
                            <?php
                                $step_index = $status_order[$step['key']];
                                if ($step_index < $current_index) {
                                    $state = 'completed';
                                } elseif ($step_index === $current_index) {
                                    $state = 'current';
                                } else {
                                    $state = 'pending';
                                }
                            ?>
                            <div class="timeline-step <?php echo $state; ?>" data-step="<?php echo html_escape($step['key']); ?>">
                                <div class="timeline-dot">
                                    <?php if ($state === 'completed'): ?>
                                        <i class="fas fa-check"></i>
                                    <?php else: ?>
                                        <i class="fas <?php echo html_escape($step['icon']); ?>"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="timeline-label"><?php echo html_escape($step['label']); ?></div>
                                <div class="timeline-time">
                                    <?php if ($state === 'completed' && !empty($step['time'])): ?>
                                        <i class="fas fa-check" style="font-size: 0.7rem; color: var(--primary);"></i>
                                        <?php echo date('H:i', strtotime($step['time'])); ?>
                                    <?php elseif ($state === 'current'): ?>
                                        <?php if (!empty($step['time'])): ?>
                                            <?php echo date('H:i', strtotime($step['time'])); ?> -
                                        <?php endif; ?>
                                        Em andamento...
                                    <?php else: ?>
                                        Aguardando
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="refresh-indicator" id="refreshIndicator">
                        <span class="dot"></span>
                        Atualizando automaticamente
                    </div>
                </div>
            <?php endif; ?>

            <!-- Order summary card -->
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-shopping-bag"></i> Resumo do Pedido
                </div>

                <div class="items-list">
                    <?php if (!empty($order->items)): ?>
                        <?php foreach ($order->items as $item): ?>
                            <div class="item-row">
                                <span class="item-name"><?php echo html_escape($item->product_name); ?></span>
                                <span class="item-qty"><?php echo (int)$item->quantity; ?>x</span>
                                <span class="item-price">R$ <?php echo number_format($item->unit_price * $item->quantity, 2, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="totals-section">
                    <?php if (isset($order->subtotal)): ?>
                        <div class="total-row">
                            <span class="label">Subtotal</span>
                            <span class="value">R$ <?php echo number_format($order->subtotal, 2, ',', '.'); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="total-row">
                        <span class="label">Taxa de entrega</span>
                        <span class="value">
                            <?php echo (isset($order->taxa_entrega) && $order->taxa_entrega > 0) ? 'R$ ' . number_format($order->taxa_entrega, 2, ',', '.') : '<span style="color:var(--success);">GRÁTIS</span>'; ?>
                        </span>
                    </div>
                    <div class="total-row final">
                        <span class="label">TOTAL</span>
                        <span class="value">R$ <?php echo number_format($order->total, 2, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment & delivery info card -->
            <div class="card">
                <div class="card-title">
                    <i class="fas fa-info-circle"></i> Informações
                </div>

                <div class="info-row">
                    <span class="label"><i class="fas fa-credit-card"></i> Pagamento</span>
                    <span class="value">
                        <?php
                            $pagamentos = ['dinheiro' => 'Dinheiro', 'cartao' => 'Cartão', 'pix' => 'Pix'];
                            echo html_escape($pagamentos[$order->forma_pagamento] ?? $order->forma_pagamento);
                        ?>
                    </span>
                </div>

                <div class="info-row">
                    <span class="label"><i class="fas fa-user"></i> Cliente</span>
                    <span class="value"><?php echo html_escape($order->cliente_nome); ?></span>
                </div>

                <?php if (!$is_retirada && !empty($order->zona_nome)): ?>
                    <div class="info-row">
                        <span class="label"><i class="fas fa-map-marker-alt"></i> Zona</span>
                        <span class="value"><?php echo html_escape($order->zona_nome); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Action buttons -->
            <div class="buttons">
                <?php if (!empty($loja->phone)): ?>
                    <a href="https://wa.me/55<?php echo html_escape($loja->phone); ?>?text=<?php echo rawurlencode('Olá! Gostaria de informações sobre o pedido #' . $order->order_number); ?>"
                       target="_blank" class="btn btn-whatsapp">
                        <i class="fab fa-whatsapp"></i> Falar com a loja
                    </a>
                <?php endif; ?>
                <a href="<?php echo base_url('cardapio'); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Cardápio
                </a>
            </div>

            <p style="text-align: center; margin-top: 20px; color: var(--text-secondary); font-size: 0.8rem;">
                <?php echo html_escape($loja->title ?? 'Cardápio Digital'); ?>
            </p>

            <?php if (!isset($order->status) || $order->status !== 'cancelado'): ?>
            <!-- AJAX polling for live updates -->
            <script>
                (function() {
                    var baseUrl = <?php echo json_encode(base_url()); ?>;
                    var orderNumber = <?php echo json_encode($order->order_number); ?>;
                    var isRetirada = <?php echo json_encode($is_retirada); ?>;
                    var currentStatus = <?php echo json_encode($current_status); ?>;
                    var pollInterval = 30000; // 30 seconds
                    var pollTimer = null;

                    // Status order mapping
                    var statusOrder = {
                        'recebido': 0,
                        'confirmado': 1,
                        'preparando': 2,
                        'pronto_coleta': 3,
                        'saiu_entrega': 4,
                        'entregue': 5
                    };

                    // Labels for each step
                    var stepLabels = {
                        'recebido': 'Recebido',
                        'confirmado': 'Confirmado',
                        'preparando': 'Preparando',
                        'pronto_coleta': 'Pedido Pronto!',
                        'saiu_entrega': isRetirada ? 'Pronto para Retirada' : 'A caminho',
                        'entregue': isRetirada ? 'Retirado' : 'Entregue'
                    };

                    // Icons for each step
                    var stepIcons = {
                        'recebido': 'fa-clipboard-check',
                        'confirmado': 'fa-thumbs-up',
                        'preparando': 'fa-fire-burner',
                        'pronto_coleta': 'fa-box-open',
                        'saiu_entrega': isRetirada ? 'fa-bag-shopping' : 'fa-motorcycle',
                        'entregue': 'fa-circle-check'
                    };

                    function formatTime(dateStr) {
                        if (!dateStr) return '';
                        var d = new Date(dateStr);
                        if (isNaN(d.getTime())) return '';
                        var h = ('0' + d.getHours()).slice(-2);
                        var m = ('0' + d.getMinutes()).slice(-2);
                        return h + ':' + m;
                    }

                    function escapeHtml(str) {
                        if (!str) return '';
                        var div = document.createElement('div');
                        div.appendChild(document.createTextNode(str));
                        return div.innerHTML;
                    }

                    function updateTimeline(data) {
                        var newStatus = data.status;

                        // If cancelled, reload the page to show the cancelled banner
                        if (newStatus === 'cancelado') {
                            window.location.reload();
                            return;
                        }

                        // If status hasn't changed, do nothing
                        if (newStatus === currentStatus) return;

                        currentStatus = newStatus;
                        var newIndex = statusOrder[newStatus] !== undefined ? statusOrder[newStatus] : 0;

                        // Timestamps from API response
                        var timestamps = {
                            'recebido': data.created_at || null,
                            'confirmado': data.hora_confirmado || null,
                            'preparando': data.hora_preparando || null,
                            'pronto_coleta': data.hora_pronto_coleta || null,
                            'saiu_entrega': data.hora_saiu_entrega || null,
                            'entregue': data.hora_entregue || null
                        };

                        var steps = document.querySelectorAll('.timeline-step');
                        var keys = ['recebido', 'confirmado', 'preparando', 'pronto_coleta', 'saiu_entrega', 'entregue'];

                        steps.forEach(function(stepEl, i) {
                            var stepKey = keys[i];
                            var stepIndex = statusOrder[stepKey];
                            var state;

                            if (stepIndex < newIndex) {
                                state = 'completed';
                            } else if (stepIndex === newIndex) {
                                state = 'current';
                            } else {
                                state = 'pending';
                            }

                            // Update classes
                            stepEl.className = 'timeline-step ' + state;

                            // Update dot icon
                            var dot = stepEl.querySelector('.timeline-dot');
                            if (state === 'completed') {
                                dot.innerHTML = '<i class="fas fa-check"></i>';
                            } else {
                                dot.innerHTML = '<i class="fas ' + escapeHtml(stepIcons[stepKey]) + '"></i>';
                            }

                            // Update time text
                            var timeEl = stepEl.querySelector('.timeline-time');
                            if (state === 'completed' && timestamps[stepKey]) {
                                timeEl.innerHTML = '<i class="fas fa-check" style="font-size: 0.7rem; color: var(--primary);"></i> ' + escapeHtml(formatTime(timestamps[stepKey]));
                            } else if (state === 'current') {
                                var timeStr = '';
                                if (timestamps[stepKey]) {
                                    timeStr = escapeHtml(formatTime(timestamps[stepKey])) + ' - ';
                                }
                                timeEl.innerHTML = timeStr + 'Em andamento...';
                            } else {
                                timeEl.textContent = 'Aguardando';
                            }
                        });

                        // If order is delivered, stop polling
                        if (newStatus === 'entregue') {
                            stopPolling();
                            var indicator = document.getElementById('refreshIndicator');
                            if (indicator) {
                                indicator.innerHTML = '<i class="fas fa-check-circle" style="color: var(--primary);"></i> Pedido finalizado';
                            }
                        }
                    }

                    function pollStatus() {
                        var xhr = new XMLHttpRequest();
                        xhr.open('GET', baseUrl + 'cardapio/api/status/' + encodeURIComponent(orderNumber), true);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4 && xhr.status === 200) {
                                try {
                                    var data = JSON.parse(xhr.responseText);
                                    if (data && data.status) {
                                        updateTimeline(data);
                                    }
                                } catch (e) {
                                    // Silently ignore parse errors
                                }
                            }
                        };

                        xhr.onerror = function() {
                            // Silently ignore network errors, will retry on next poll
                        };

                        xhr.send();
                    }

                    function startPolling() {
                        // Don't poll if already delivered
                        if (currentStatus === 'entregue') return;

                        pollTimer = setInterval(pollStatus, pollInterval);
                    }

                    function stopPolling() {
                        if (pollTimer) {
                            clearInterval(pollTimer);
                            pollTimer = null;
                        }
                    }

                    // Start polling when page loads
                    startPolling();

                    // Pause polling when tab is hidden, resume when visible
                    document.addEventListener('visibilitychange', function() {
                        if (document.hidden) {
                            stopPolling();
                        } else {
                            // Poll immediately on tab focus, then resume interval
                            pollStatus();
                            startPolling();
                        }
                    });
                })();
            </script>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</body>
</html>
