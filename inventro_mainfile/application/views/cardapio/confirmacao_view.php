<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - <?php echo html_escape($loja->title ?? 'Cardápio Digital'); ?></title>
    
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-card) 100%);
            min-height: 100vh;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .confirmation-card {
            background: rgba(22, 33, 62, 0.95);
            border-radius: 24px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--success), var(--primary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }

        .success-icon i { font-size: 3rem; color: white; }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--success), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .order-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 20px 0;
        }

        .info-box {
            background: rgba(255,255,255,0.05);
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--text-secondary); display: flex; align-items: center; gap: 8px; }
        .info-value { font-weight: 600; }

        .total-row {
            background: rgba(37, 211, 102, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
        }

        .total-row .info-value { font-size: 1.4rem; color: var(--primary); }

        .message-box {
            background: rgba(37, 211, 102, 0.1);
            border: 1px solid rgba(37, 211, 102, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .message-box i { font-size: 2rem; color: var(--primary); margin-bottom: 10px; }
        .message-box p { color: var(--text-secondary); line-height: 1.6; }

        .buttons { display: flex; flex-direction: column; gap: 12px; margin-top: 25px; }

        .btn {
            padding: 16px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .btn-whatsapp:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4); }

        .btn-print {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(52, 152, 219, 0.4); }

        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
        }

        .btn-secondary:hover { background: rgba(255,255,255,0.2); }

        .items-list { text-align: left; margin: 15px 0; }
        .item-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.9rem; }
        .item-name { color: var(--text-secondary); }
        .item-qty { color: var(--primary); font-weight: 600; }

        @media (max-width: 480px) {
            .confirmation-card { padding: 25px; }
            .order-number { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Pedido Confirmado!</h1>
        <p class="order-number">#<?php echo $order->order_number; ?></p>

        <div class="message-box">
            <i class="fas fa-bell"></i>
            <p>Seu pedido foi recebido! Em breve entraremos em contato via WhatsApp para confirmar.</p>
        </div>

        <!-- Itens do Pedido -->
        <div class="info-box">
            <h3 style="margin-bottom: 15px;"><i class="fas fa-shopping-bag"></i> Seus Itens</h3>
            <div class="items-list">
                <?php foreach ($order->items as $item): ?>
                    <div class="item-row">
                        <span class="item-name"><?php echo html_escape($item->product_name); ?></span>
                        <span class="item-qty"><?php echo $item->quantity; ?>x R$ <?php echo number_format($item->unit_price, 2, ',', '.'); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Resumo -->
        <div class="info-box">
            <div class="info-row">
                <span class="info-label"><i class="fas fa-user"></i> Cliente</span>
                <span class="info-value"><?php echo html_escape($order->cliente_nome); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-credit-card"></i> Pagamento</span>
                <span class="info-value">
                    <?php 
                    $pagamentos = ['dinheiro' => 'Dinheiro', 'cartao' => 'Cartão', 'pix' => 'Pix'];
                    echo $pagamentos[$order->forma_pagamento] ?? $order->forma_pagamento;
                    ?>
                </span>
            </div>
            <?php if ($order->forma_pagamento == 'dinheiro' && $order->troco_para > 0): ?>
                <div class="info-row">
                    <span class="info-label"><i class="fas fa-money-bill-wave"></i> Troco para</span>
                    <span class="info-value">R$ <?php echo number_format($order->troco_para, 2, ',', '.'); ?></span>
                </div>
            <?php endif; ?>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-truck"></i> Taxa de Entrega</span>
                <span class="info-value">
                    <?php echo $order->taxa_entrega == 0 ? '<span style="color:var(--success);">GRÁTIS</span>' : 'R$ ' . number_format($order->taxa_entrega, 2, ',', '.'); ?>
                </span>
            </div>
            <div class="total-row">
                <div class="info-row" style="border: none; padding: 0;">
                    <span class="info-label" style="font-size: 1.1rem; color: var(--text-primary);">
                        <i class="fas fa-receipt"></i> TOTAL
                    </span>
                    <span class="info-value">R$ <?php echo number_format($order->total, 2, ',', '.'); ?></span>
                </div>
            </div>
        </div>

        <div class="buttons">
            <?php if (!empty($whatsapp)): ?>
                <a href="https://wa.me/55<?php echo $whatsapp; ?>?text=Olá! Fiz o pedido %23<?php echo $order->order_number; ?> e gostaria de confirmar." 
                   target="_blank" class="btn btn-whatsapp">
                    <i class="fab fa-whatsapp"></i> Falar no WhatsApp
                </a>
            <?php endif; ?>
            
            <a href="<?php echo base_url('cardapio/cupom/' . $order->order_number); ?>" 
               target="_blank" class="btn btn-print">
                <i class="fas fa-print"></i> Imprimir Cupom
            </a>
            
            <a href="<?php echo base_url('cardapio'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Fazer Novo Pedido
            </a>
        </div>

        <p style="margin-top: 30px; color: var(--text-secondary); font-size: 0.85rem;">
            <i class="fas fa-clock"></i> Pedido realizado em <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?>
        </p>
    </div>

    <script>
        localStorage.removeItem('cart');
    </script>
</body>
</html>
