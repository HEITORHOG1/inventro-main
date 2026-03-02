<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #<?php echo html_escape($order->order_number); ?> - <?php echo html_escape($loja->title ?? 'Inventro'); ?></title>
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
            --danger: #f44336;
            --warning: #ff9800;
            --blue: #3498db;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-card) 50%, #0f3460 100%);
            min-height: 100vh;
            color: var(--text-primary);
            padding-bottom: 40px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* Page Header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 24px 0 8px;
        }

        .page-header h1 {
            font-size: 1.4rem;
            font-weight: 600;
        }

        .page-header h1 i {
            color: var(--primary);
            margin-right: 8px;
        }

        .order-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            padding-bottom: 20px;
        }

        .order-date-info {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
        }

        /* Section Card */
        .section-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        /* Status Timeline */
        .timeline {
            position: relative;
            padding-left: 32px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 8px;
            bottom: 8px;
            width: 2px;
            background: rgba(255,255,255,0.1);
        }

        .timeline-step {
            position: relative;
            padding-bottom: 24px;
        }

        .timeline-step:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -32px;
            top: 2px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            z-index: 1;
        }

        .timeline-dot.completed {
            background: var(--success);
            color: #fff;
        }

        .timeline-dot.current {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 0 12px rgba(37, 211, 102, 0.5);
        }

        .timeline-dot.pending {
            background: rgba(255,255,255,0.1);
            color: var(--text-secondary);
        }

        .timeline-dot.cancelled {
            background: var(--danger);
            color: #fff;
        }

        .timeline-label {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .timeline-label.inactive {
            color: var(--text-secondary);
        }

        .timeline-time {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead th {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 0 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .items-table thead th:last-child {
            text-align: right;
        }

        .items-table tbody td {
            padding: 12px 0;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }

        .items-table tbody td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .item-qty {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .item-unit-price {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }

        /* Totals */
        .totals-section {
            padding-top: 12px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 0.9rem;
        }

        .total-row.discount {
            color: var(--success);
        }

        .total-row.grand-total {
            padding-top: 12px;
            margin-top: 8px;
            border-top: 2px solid rgba(255,255,255,0.1);
            font-size: 1.2rem;
            font-weight: 700;
        }

        .total-row.grand-total .total-value {
            color: var(--primary);
            font-size: 1.4rem;
        }

        .total-label {
            color: var(--text-secondary);
        }

        /* Payment Info */
        .payment-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            font-size: 0.9rem;
        }

        .payment-badge i {
            color: var(--primary);
        }

        .payment-confirmed {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--success);
        }

        .payment-pending {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--warning);
        }

        /* Observation */
        .observacao-box {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.5;
            border-left: 3px solid var(--warning);
        }

        /* Action Buttons */
        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            padding: 24px 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s;
            flex: 1;
            min-width: 140px;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.15);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid rgba(255,255,255,0.15);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 1.2rem;
            }

            .items-table thead th:nth-child(3),
            .items-table tbody td:nth-child(3) {
                display: none;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<?php $this->load->view('cliente/_header'); ?>

<?php
$status_labels = [
    'pendente_pagamento' => ['label' => 'Aguardando pagamento', 'color' => '#ff9800'],
    'pendente'           => ['label' => 'Aguardando confirmacao', 'color' => '#3498db'],
    'confirmado'         => ['label' => 'Confirmado', 'color' => '#2196f3'],
    'preparando'         => ['label' => 'Em preparo', 'color' => '#ff9800'],
    'pronto_coleta'      => ['label' => 'Pronto para coleta', 'color' => '#9c27b0'],
    'saiu_entrega'       => ['label' => 'Saiu para entrega', 'color' => '#9c27b0'],
    'entregue'           => ['label' => 'Entregue', 'color' => '#00c853'],
    'cancelado'          => ['label' => 'Cancelado', 'color' => '#f44336'],
];

$current_status = $order->status ?? 'pendente';
$status_info    = $status_labels[$current_status] ?? ['label' => html_escape($current_status), 'color' => '#999'];

// Timeline steps (normal flow)
$timeline_steps = [
    'pendente'      => ['label' => 'Pedido recebido',      'icon' => 'fas fa-check',          'time_field' => 'created_at'],
    'confirmado'    => ['label' => 'Confirmado',            'icon' => 'fas fa-thumbs-up',      'time_field' => 'hora_confirmado'],
    'preparando'    => ['label' => 'Em preparo',            'icon' => 'fas fa-utensils',        'time_field' => 'hora_preparando'],
    'saiu_entrega'  => ['label' => 'Saiu para entrega',     'icon' => 'fas fa-motorcycle',      'time_field' => 'hora_saiu_entrega'],
    'entregue'      => ['label' => 'Entregue',              'icon' => 'fas fa-check-double',    'time_field' => 'hora_entregue'],
];

// Determine completed steps
$step_order = array_keys($timeline_steps);
$current_index = array_search($current_status, $step_order);
if ($current_index === false) {
    // Handle statuses not in the normal flow (pendente_pagamento, pronto_coleta, cancelado)
    if ($current_status === 'pendente_pagamento') {
        $current_index = -1; // Before first step
    } elseif ($current_status === 'pronto_coleta') {
        $current_index = array_search('preparando', $step_order);
    } elseif ($current_status === 'cancelado') {
        $current_index = -2; // Special case
    }
}

$is_cancelled = ($current_status === 'cancelado');
?>

<div class="container">

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-receipt"></i> Pedido #<?php echo html_escape($order->order_number); ?></h1>
        <span class="status-badge" style="background: <?php echo htmlspecialchars($status_info['color'], ENT_QUOTES, 'UTF-8'); ?>20; color: <?php echo htmlspecialchars($status_info['color'], ENT_QUOTES, 'UTF-8'); ?>;">
            <?php echo html_escape($status_info['label']); ?>
        </span>
    </div>

    <div class="order-meta">
        <span class="order-date-info">
            <i class="far fa-calendar-alt"></i>
            <?php echo html_escape(date('d/m/Y H:i', strtotime($order->created_at))); ?>
        </span>
    </div>

    <!-- Status Timeline -->
    <div class="section-card">
        <div class="section-title"><i class="fas fa-route"></i> Acompanhamento</div>
        <div class="timeline">
            <?php foreach ($timeline_steps as $step_key => $step): ?>
                <?php
                $step_index = array_search($step_key, $step_order);
                $time_field = $step['time_field'];
                $step_time  = isset($order->$time_field) ? $order->$time_field : null;

                if ($is_cancelled) {
                    $dot_class = 'pending';
                    $label_class = 'inactive';
                } elseif ($step_index < $current_index) {
                    $dot_class = 'completed';
                    $label_class = '';
                } elseif ($step_index == $current_index) {
                    $dot_class = 'current';
                    $label_class = '';
                } else {
                    $dot_class = 'pending';
                    $label_class = 'inactive';
                }
                ?>
                <div class="timeline-step">
                    <div class="timeline-dot <?php echo htmlspecialchars($dot_class, ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="<?php echo htmlspecialchars($step['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                    </div>
                    <div class="timeline-label <?php echo htmlspecialchars($label_class, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo html_escape($step['label']); ?>
                    </div>
                    <?php if ($step_time && ($step_index <= $current_index)): ?>
                        <div class="timeline-time"><?php echo html_escape(date('d/m/Y H:i', strtotime($step_time))); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if ($is_cancelled): ?>
                <div class="timeline-step">
                    <div class="timeline-dot cancelled">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="timeline-label">Cancelado</div>
                    <?php if (isset($order->hora_cancelado) && $order->hora_cancelado): ?>
                        <div class="timeline-time"><?php echo html_escape(date('d/m/Y H:i', strtotime($order->hora_cancelado))); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Items -->
    <div class="section-card">
        <div class="section-title"><i class="fas fa-shopping-basket"></i> Itens do Pedido</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Qtd</th>
                    <th>Preco Unit.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($order->items)): ?>
                    <?php foreach ($order->items as $item): ?>
                        <tr>
                            <td><?php echo html_escape($item->product_name); ?></td>
                            <td class="item-qty"><?php echo (int)$item->quantity; ?></td>
                            <td class="item-unit-price">R$ <?php echo html_escape(number_format($item->unit_price, 2, ',', '.')); ?></td>
                            <td>R$ <?php echo html_escape(number_format($item->total_price, 2, ',', '.')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <span class="total-label">Subtotal</span>
                <span>R$ <?php echo html_escape(number_format($order->subtotal ?? 0, 2, ',', '.')); ?></span>
            </div>
            <?php if (isset($order->taxa_entrega) && $order->taxa_entrega > 0): ?>
                <div class="total-row">
                    <span class="total-label">Taxa de entrega</span>
                    <span>R$ <?php echo html_escape(number_format($order->taxa_entrega, 2, ',', '.')); ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($order->desconto) && $order->desconto > 0): ?>
                <div class="total-row discount">
                    <span class="total-label">Desconto</span>
                    <span>- R$ <?php echo html_escape(number_format($order->desconto, 2, ',', '.')); ?></span>
                </div>
            <?php endif; ?>
            <div class="total-row grand-total">
                <span>Total</span>
                <span class="total-value">R$ <?php echo html_escape(number_format($order->total ?? 0, 2, ',', '.')); ?></span>
            </div>
        </div>
    </div>

    <!-- Payment Info -->
    <div class="section-card">
        <div class="section-title"><i class="fas fa-credit-card"></i> Pagamento</div>
        <div class="payment-info">
            <?php
            $payment_method = $order->payment_method ?? '';
            $pay_icon = 'fas fa-wallet';
            $pay_text = html_escape($payment_method);
            if (stripos($payment_method, 'dinheiro') !== false) {
                $pay_icon = 'fas fa-money-bill';
                $pay_text = 'Dinheiro';
            } elseif (stripos($payment_method, 'pix') !== false) {
                $pay_icon = 'fas fa-qrcode';
                $pay_text = 'PIX';
            } elseif (stripos($payment_method, 'cartao') !== false || stripos($payment_method, 'credito') !== false || stripos($payment_method, 'debito') !== false) {
                $pay_icon = 'fas fa-credit-card';
                $pay_text = 'Cartao';
            }
            ?>
            <span class="payment-badge">
                <i class="<?php echo htmlspecialchars($pay_icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
                <?php echo html_escape($pay_text); ?>
            </span>

            <?php if (!empty($order->pagamento_confirmado) && $order->pagamento_confirmado): ?>
                <span class="payment-confirmed">
                    <i class="fas fa-check-circle"></i> Pagamento confirmado
                </span>
            <?php else: ?>
                <span class="payment-pending">
                    <i class="fas fa-clock"></i> Aguardando pagamento
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Observation -->
    <?php if (!empty($order->observacao)): ?>
        <div class="section-card">
            <div class="section-title"><i class="fas fa-comment-alt"></i> Observacao</div>
            <div class="observacao-box">
                <?php echo html_escape($order->observacao); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="actions">
        <?php if (!in_array($current_status, ['entregue', 'cancelado'])): ?>
            <a href="<?php echo base_url('cardapio/acompanhar/' . html_escape($order->order_number)); ?>" class="btn btn-primary">
                <i class="fas fa-map-marker-alt"></i> Acompanhar Pedido
            </a>
        <?php endif; ?>
        <a href="<?php echo base_url('cardapio'); ?>" class="btn btn-secondary">
            <i class="fas fa-redo"></i> Repetir Pedido
        </a>
        <a href="<?php echo base_url('cliente/pedidos'); ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

</div>

<?php $this->load->view('cliente/_footer'); ?>

</body>
</html>
