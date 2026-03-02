<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pedidos - <?php echo html_escape($loja->title ?? 'Inventro'); ?></title>
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

        /* Page Title */
        .page-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 24px 0 16px;
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title h1 i {
            color: var(--primary);
        }

        .count-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            min-width: 24px;
            height: 24px;
            padding: 0 8px;
            border-radius: 12px;
        }

        /* Filter */
        .filter-section {
            margin-bottom: 20px;
        }

        .filter-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            background: var(--bg-card);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23a0a0a0' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* Order Card */
        .order-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 16px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .order-number {
            font-size: 1.05rem;
            font-weight: 600;
        }

        .order-date {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .order-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .order-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .order-total {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary);
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .payment-method i {
            font-size: 1rem;
        }

        .btn-details {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            border: none;
            border-radius: 10px;
            background: rgba(37, 211, 102, 0.15);
            color: var(--primary);
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-details:hover {
            background: rgba(37, 211, 102, 0.25);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-secondary);
            margin-bottom: 16px;
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            padding: 24px 0;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.3s;
        }

        .pagination a {
            background: var(--bg-card);
            color: var(--primary);
            border: 1px solid rgba(37, 211, 102, 0.3);
        }

        .pagination a:hover {
            background: rgba(37, 211, 102, 0.15);
        }

        .pagination .page-info {
            background: transparent;
            color: var(--text-secondary);
            border: none;
            padding: 10px 8px;
        }

        .pagination .disabled {
            opacity: 0.4;
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .page-title h1 {
                font-size: 1.2rem;
            }

            .order-body {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-details {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<?php $this->load->view('cliente/_header'); ?>

<div class="container">

    <!-- Page Title -->
    <div class="page-title">
        <h1>
            <i class="fas fa-receipt"></i>
            Meus Pedidos
            <span class="count-badge"><?php echo (int)$total; ?></span>
        </h1>
    </div>

    <!-- Status Filter -->
    <div class="filter-section">
        <select class="filter-select" id="statusFilter" onchange="window.location.href = '<?php echo base_url('cliente/pedidos'); ?>?status=' + this.value">
            <option value="" <?php echo empty($status_filter) ? 'selected' : ''; ?>>Todos os pedidos</option>
            <option value="pendente_pagamento" <?php echo ($status_filter === 'pendente_pagamento') ? 'selected' : ''; ?>>Aguardando pagamento</option>
            <option value="pendente" <?php echo ($status_filter === 'pendente') ? 'selected' : ''; ?>>Aguardando confirmacao</option>
            <option value="confirmado" <?php echo ($status_filter === 'confirmado') ? 'selected' : ''; ?>>Confirmado</option>
            <option value="preparando" <?php echo ($status_filter === 'preparando') ? 'selected' : ''; ?>>Em preparo</option>
            <option value="pronto_coleta" <?php echo ($status_filter === 'pronto_coleta') ? 'selected' : ''; ?>>Pronto para coleta</option>
            <option value="saiu_entrega" <?php echo ($status_filter === 'saiu_entrega') ? 'selected' : ''; ?>>Saiu para entrega</option>
            <option value="entregue" <?php echo ($status_filter === 'entregue') ? 'selected' : ''; ?>>Entregue</option>
            <option value="cancelado" <?php echo ($status_filter === 'cancelado') ? 'selected' : ''; ?>>Cancelado</option>
        </select>
    </div>

    <!-- Order List -->
    <?php if (!empty($orders)): ?>
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
        ?>
        <?php foreach ($orders as $order): ?>
            <?php
            $status_key   = $order->status ?? 'pendente';
            $status_info  = $status_labels[$status_key] ?? ['label' => html_escape($status_key), 'color' => '#999'];
            $status_color = $status_info['color'];
            $status_label = $status_info['label'];

            // Payment method icon
            $payment_method = $order->payment_method ?? '';
            $payment_icon   = 'fas fa-wallet';
            $payment_text   = html_escape($payment_method);
            if (stripos($payment_method, 'dinheiro') !== false) {
                $payment_icon = 'fas fa-money-bill';
                $payment_text = 'Dinheiro';
            } elseif (stripos($payment_method, 'pix') !== false) {
                $payment_icon = 'fas fa-qrcode';
                $payment_text = 'PIX';
            } elseif (stripos($payment_method, 'cartao') !== false || stripos($payment_method, 'credito') !== false || stripos($payment_method, 'debito') !== false) {
                $payment_icon = 'fas fa-credit-card';
                $payment_text = 'Cartao';
            }
            ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <div class="order-number">Pedido #<?php echo html_escape($order->order_number); ?></div>
                        <div class="order-date">
                            <i class="far fa-calendar-alt"></i>
                            <?php echo html_escape(date('d/m/Y H:i', strtotime($order->created_at))); ?>
                        </div>
                    </div>
                    <span class="status-badge" style="background: <?php echo htmlspecialchars($status_color, ENT_QUOTES, 'UTF-8'); ?>20; color: <?php echo htmlspecialchars($status_color, ENT_QUOTES, 'UTF-8'); ?>;">
                        <?php echo html_escape($status_label); ?>
                    </span>
                </div>
                <div class="order-body">
                    <div class="order-info">
                        <span class="order-total">R$ <?php echo html_escape(number_format($order->total, 2, ',', '.')); ?></span>
                        <span class="payment-method">
                            <i class="<?php echo htmlspecialchars($payment_icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
                            <?php echo html_escape($payment_text); ?>
                        </span>
                    </div>
                    <a href="<?php echo base_url('cliente/pedido/' . html_escape($order->order_number)); ?>" class="btn-details">
                        <i class="fas fa-eye"></i>
                        Ver Detalhes
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="<?php echo base_url('cliente/pedidos?page=' . ($page - 1) . (!empty($status_filter) ? '&status=' . urlencode($status_filter) : '')); ?>">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                <?php else: ?>
                    <span class="disabled">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </span>
                <?php endif; ?>

                <span class="page-info">Pagina <?php echo (int)$page; ?> de <?php echo (int)$total_pages; ?></span>

                <?php if ($page < $total_pages): ?>
                    <a href="<?php echo base_url('cliente/pedidos?page=' . ($page + 1) . (!empty($status_filter) ? '&status=' . urlencode($status_filter) : '')); ?>">
                        Proximo <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="disabled">
                        Proximo <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <p>Nenhum pedido encontrado</p>
        </div>
    <?php endif; ?>

</div>

<?php $this->load->view('cliente/_footer'); ?>

</body>
</html>
