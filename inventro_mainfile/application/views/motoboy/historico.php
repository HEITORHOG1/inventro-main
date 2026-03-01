<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Historico - <?php echo html_escape($motoboy->nome); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green: #1a5c2e;
            --green-light: #2d8a4e;
            --gray: #7f8c8d;
            --bg: #f0f2f5;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            padding-bottom: 70px;
        }

        .header {
            background: linear-gradient(135deg, var(--green), var(--green-light));
            color: #fff;
            padding: 16px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header h1 { font-size: 18px; font-weight: 700; }
        .btn-logout {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 6px;
            background: rgba(255,255,255,0.15);
        }

        /* Resumo Cards */
        .resumo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            padding: 16px 20px;
        }
        .resumo-card {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .resumo-card .valor {
            font-size: 20px;
            font-weight: 800;
            color: var(--green);
        }
        .resumo-card .qtd {
            font-size: 12px;
            color: var(--gray);
        }
        .resumo-card .label {
            font-size: 11px;
            color: var(--gray);
            text-transform: uppercase;
            margin-top: 4px;
        }

        .content { padding: 0 20px 20px; }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin: 16px 0 12px;
        }

        /* Entrega card */
        .entrega-card {
            background: #fff;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .entrega-ganho {
            min-width: 70px;
            text-align: center;
        }
        .entrega-ganho .amount {
            font-size: 16px;
            font-weight: 800;
            color: var(--green);
        }
        .entrega-ganho .tag {
            font-size: 10px;
            color: #fff;
            background: var(--green);
            border-radius: 4px;
            padding: 1px 6px;
            display: inline-block;
            margin-top: 3px;
        }
        .entrega-info {
            flex: 1;
            min-width: 0;
        }
        .entrega-info .order-num {
            font-weight: 700;
            font-size: 14px;
            color: #333;
        }
        .entrega-info .details {
            font-size: 12px;
            color: var(--gray);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .entrega-info .date {
            font-size: 11px;
            color: #aaa;
            margin-top: 2px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }
        .empty-state .emoji { font-size: 48px; margin-bottom: 12px; }

        /* Bottom nav */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            display: flex;
            border-top: 1px solid #e0e0e0;
            z-index: 100;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        .nav-item {
            flex: 1;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            color: var(--gray);
            font-size: 11px;
            font-weight: 600;
        }
        .nav-item.active { color: var(--green); }
        .nav-item .nav-icon { font-size: 20px; display: block; margin-bottom: 2px; }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="header-top">
        <h1>&#x1F4CB; Historico de Entregas</h1>
        <a href="<?php echo base_url('motoboy/logout'); ?>" class="btn-logout">Sair</a>
    </div>
</div>

<!-- Resumo -->
<div class="resumo-grid">
    <div class="resumo-card">
        <div class="valor">R$ <?php echo number_format($resumo->hoje_valor, 2, ',', '.'); ?></div>
        <div class="qtd"><?php echo (int)$resumo->hoje_qtd; ?> entregas</div>
        <div class="label">Hoje</div>
    </div>
    <div class="resumo-card">
        <div class="valor">R$ <?php echo number_format($resumo->semana_valor, 2, ',', '.'); ?></div>
        <div class="qtd"><?php echo (int)$resumo->semana_qtd; ?> entregas</div>
        <div class="label">Semana</div>
    </div>
    <div class="resumo-card">
        <div class="valor">R$ <?php echo number_format($resumo->mes_valor, 2, ',', '.'); ?></div>
        <div class="qtd"><?php echo (int)$resumo->mes_qtd; ?> entregas</div>
        <div class="label">Mes</div>
    </div>
</div>

<div class="content">
    <div class="section-title">Ultimas Entregas</div>

    <?php if (empty($entregas)): ?>
        <div class="empty-state">
            <div class="emoji">&#x1F4ED;</div>
            <p>Nenhuma entrega realizada ainda</p>
        </div>
    <?php else: ?>
        <?php foreach ($entregas as $e): ?>
            <div class="entrega-card">
                <div class="entrega-ganho">
                    <div class="amount">R$ <?php echo number_format($e->valor_ganho, 2, ',', '.'); ?></div>
                    <div class="tag">ganho</div>
                </div>
                <div class="entrega-info">
                    <div class="order-num">#<?php echo html_escape($e->order_number); ?></div>
                    <div class="details">
                        <?php echo html_escape($e->cliente_nome ?? ''); ?>
                        <?php if (!empty($e->zona_nome)): ?>
                            &bull; <?php echo html_escape($e->zona_nome); ?>
                        <?php endif; ?>
                    </div>
                    <div class="details">
                        Pedido: R$ <?php echo number_format($e->order_total ?? 0, 2, ',', '.'); ?>
                    </div>
                    <div class="date">
                        <?php if (!empty($e->entregue_em)): ?>
                            <?php echo date('d/m/Y H:i', strtotime($e->entregue_em)); ?>
                        <?php endif; ?>
                        <?php if (!empty($e->aceito_em) && !empty($e->entregue_em)): ?>
                            <?php
                            $diff = strtotime($e->entregue_em) - strtotime($e->aceito_em);
                            $mins = floor($diff / 60);
                            ?>
                            &bull; <?php echo (int)$mins; ?> min
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <a href="<?php echo base_url('motoboy/dashboard'); ?>" class="nav-item">
        <span class="nav-icon">&#x1F3E0;</span>
        Inicio
    </a>
    <a href="<?php echo base_url('motoboy/historico'); ?>" class="nav-item active">
        <span class="nav-icon">&#x1F4CB;</span>
        Historico
    </a>
</div>

</body>
</html>
