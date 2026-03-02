<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pagamentos - <?php echo html_escape($loja->title ?? 'Inventro'); ?></title>
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
            gap: 10px;
            padding: 24px 0 16px;
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .page-title h1 i {
            color: var(--primary);
            margin-right: 8px;
        }

        /* Tab Navigation */
        .tab-nav {
            display: flex;
            gap: 0;
            margin-bottom: 20px;
            background: var(--bg-card);
            border-radius: 14px;
            padding: 4px;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .tab-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: 10px;
            background: transparent;
            color: var(--text-secondary);
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .tab-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 211, 102, 0.3);
        }

        .tab-btn:not(.active):hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-primary);
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Payment Card */
        .payment-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 12px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: transform 0.2s;
        }

        .payment-card:hover {
            transform: translateY(-1px);
        }

        .payment-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .payment-order {
            font-size: 0.95rem;
            font-weight: 600;
        }

        .payment-order i {
            color: var(--primary);
            margin-right: 4px;
        }

        .payment-date {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .payment-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .payment-card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.03);
        }

        .payment-amount {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary);
        }

        .payment-detail {
            font-size: 0.8rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .payment-detail i {
            font-size: 0.85rem;
        }

        .payment-paid-at {
            font-size: 0.78rem;
            color: var(--success);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .payment-paid-at i {
            font-size: 0.8rem;
        }

        /* Card brand badge */
        .card-brand {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .card-brand i {
            font-size: 1rem;
        }

        .installments {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: var(--text-secondary);
            margin-bottom: 14px;
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        /* Status colors */
        .status-pending {
            background: rgba(255, 152, 0, 0.15);
            color: var(--warning);
        }

        .status-confirmed,
        .status-approved {
            background: rgba(0, 200, 83, 0.15);
            color: var(--success);
        }

        .status-expired,
        .status-error {
            background: rgba(244, 67, 54, 0.15);
            color: var(--danger);
        }

        .status-refunded {
            background: rgba(255, 152, 0, 0.15);
            color: #ff9800;
        }

        .status-waiting {
            background: rgba(255, 152, 0, 0.15);
            color: var(--warning);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .page-title h1 {
                font-size: 1.2rem;
            }

            .payment-card-body {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<?php $this->load->view('cliente/_header'); ?>

<div class="container">

    <!-- Page Title -->
    <div class="page-title">
        <h1><i class="fas fa-wallet"></i> Meus Pagamentos</h1>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-nav">
        <button class="tab-btn active" data-tab="pix" onclick="switchTab('pix')">
            <i class="fas fa-qrcode"></i> PIX
        </button>
        <button class="tab-btn" data-tab="cartao" onclick="switchTab('cartao')">
            <i class="fas fa-credit-card"></i> Cartao
        </button>
    </div>

    <!-- PIX Tab -->
    <div class="tab-content active" id="tab-pix">
        <?php if (!empty($pix_charges)): ?>
            <?php
            $pix_status_map = [
                'pending'   => ['label' => 'Pendente',    'class' => 'status-pending'],
                'confirmed' => ['label' => 'Confirmado',  'class' => 'status-confirmed'],
                'expired'   => ['label' => 'Expirado',    'class' => 'status-expired'],
                'refunded'  => ['label' => 'Estornado',   'class' => 'status-refunded'],
            ];
            ?>
            <?php foreach ($pix_charges as $charge): ?>
                <?php
                $pix_status_key  = $charge->status ?? 'pending';
                $pix_status_info = $pix_status_map[$pix_status_key] ?? ['label' => html_escape($pix_status_key), 'class' => 'status-pending'];
                ?>
                <div class="payment-card">
                    <div class="payment-card-header">
                        <div>
                            <div class="payment-order">
                                <i class="fas fa-receipt"></i>
                                Pedido #<?php echo html_escape($charge->order_number); ?>
                            </div>
                            <div class="payment-date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo html_escape(date('d/m/Y H:i', strtotime($charge->created_at))); ?>
                            </div>
                        </div>
                        <span class="payment-status <?php echo htmlspecialchars($pix_status_info['class'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo html_escape($pix_status_info['label']); ?>
                        </span>
                    </div>
                    <div class="payment-card-body">
                        <div>
                            <div class="payment-amount">R$ <?php echo html_escape(number_format($charge->valor, 2, ',', '.')); ?></div>
                            <?php if (!empty($charge->paid_at)): ?>
                                <div class="payment-paid-at">
                                    <i class="fas fa-check-circle"></i>
                                    Pago em <?php echo html_escape(date('d/m/Y H:i', strtotime($charge->paid_at))); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="payment-detail">
                            <i class="fas fa-qrcode"></i> PIX
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-qrcode"></i>
                <p>Nenhum pagamento PIX encontrado</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Cartao Tab -->
    <div class="tab-content" id="tab-cartao">
        <?php if (!empty($card_charges)): ?>
            <?php
            $card_status_map = [
                'approved' => ['label' => 'Aprovado',    'class' => 'status-approved'],
                'waiting'  => ['label' => 'Aguardando',  'class' => 'status-waiting'],
                'error'    => ['label' => 'Erro',        'class' => 'status-error'],
                'refunded' => ['label' => 'Estornado',   'class' => 'status-refunded'],
            ];
            ?>
            <?php foreach ($card_charges as $charge): ?>
                <?php
                $card_status_key  = $charge->status ?? 'waiting';
                $card_status_info = $card_status_map[$card_status_key] ?? ['label' => html_escape($card_status_key), 'class' => 'status-waiting'];

                // Card brand icon
                $brand = strtolower($charge->bandeira ?? '');
                $brand_icon = 'fas fa-credit-card';
                if (strpos($brand, 'visa') !== false) {
                    $brand_icon = 'fab fa-cc-visa';
                } elseif (strpos($brand, 'master') !== false) {
                    $brand_icon = 'fab fa-cc-mastercard';
                } elseif (strpos($brand, 'amex') !== false) {
                    $brand_icon = 'fab fa-cc-amex';
                } elseif (strpos($brand, 'elo') !== false) {
                    $brand_icon = 'fas fa-credit-card';
                }
                ?>
                <div class="payment-card">
                    <div class="payment-card-header">
                        <div>
                            <div class="payment-order">
                                <i class="fas fa-receipt"></i>
                                Pedido #<?php echo html_escape($charge->order_number); ?>
                            </div>
                            <div class="payment-date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo html_escape(date('d/m/Y H:i', strtotime($charge->created_at))); ?>
                            </div>
                        </div>
                        <span class="payment-status <?php echo htmlspecialchars($card_status_info['class'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo html_escape($card_status_info['label']); ?>
                        </span>
                    </div>
                    <div class="payment-card-body">
                        <div>
                            <div class="payment-amount">R$ <?php echo html_escape(number_format($charge->valor, 2, ',', '.')); ?></div>
                            <?php if (isset($charge->parcelas) && $charge->parcelas > 1): ?>
                                <div class="installments">
                                    <?php echo (int)$charge->parcelas; ?>x de R$ <?php echo html_escape(number_format($charge->valor / $charge->parcelas, 2, ',', '.')); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if (!empty($charge->bandeira) || !empty($charge->ultimos_digitos)): ?>
                                <span class="card-brand">
                                    <i class="<?php echo htmlspecialchars($brand_icon, ENT_QUOTES, 'UTF-8'); ?>"></i>
                                    <?php if (!empty($charge->bandeira)): ?>
                                        <?php echo html_escape(ucfirst($charge->bandeira)); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($charge->ultimos_digitos)): ?>
                                        **** <?php echo html_escape($charge->ultimos_digitos); ?>
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <div class="payment-detail">
                                    <i class="fas fa-credit-card"></i> Cartao
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-credit-card"></i>
                <p>Nenhum pagamento por cartao encontrado</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php $this->load->view('cliente/_footer'); ?>

<script>
(function() {
    'use strict';

    window.switchTab = function(tab) {
        // Update buttons
        var buttons = document.querySelectorAll('.tab-btn');
        for (var i = 0; i < buttons.length; i++) {
            buttons[i].classList.remove('active');
            if (buttons[i].getAttribute('data-tab') === tab) {
                buttons[i].classList.add('active');
            }
        }

        // Update content
        var contents = document.querySelectorAll('.tab-content');
        for (var j = 0; j < contents.length; j++) {
            contents[j].classList.remove('active');
        }

        var targetTab = document.getElementById('tab-' + tab);
        if (targetTab) {
            targetTab.classList.add('active');
        }
    };
})();
</script>

</body>
</html>
