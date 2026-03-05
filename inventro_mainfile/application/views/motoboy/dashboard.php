<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Painel - <?php echo html_escape($motoboy->nome); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --green: #1a5c2e;
            --green-light: #2d8a4e;
            --orange: #e67e22;
            --blue: #3498db;
            --red: #e74c3c;
            --gray: #7f8c8d;
            --bg: #f0f2f5;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            padding-bottom: 70px;
        }

        /* Header */
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
        .header h1 {
            font-size: 18px;
            font-weight: 700;
        }
        .header .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-disponivel { background: rgba(255,255,255,0.3); }
        .status-em_entrega { background: var(--orange); }
        .btn-logout {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 6px;
            background: rgba(255,255,255,0.15);
        }

        /* Ganhos resumo */
        .ganhos-bar {
            display: flex;
            gap: 12px;
            padding: 12px 20px;
            background: #fff;
            border-bottom: 1px solid #e0e0e0;
            overflow-x: auto;
        }
        .ganho-item {
            text-align: center;
            min-width: 90px;
        }
        .ganho-item .valor {
            font-size: 18px;
            font-weight: 700;
            color: var(--green);
        }
        .ganho-item .label {
            font-size: 11px;
            color: var(--gray);
            text-transform: uppercase;
        }

        /* Content */
        .content { padding: 16px 20px; }

        /* Flash messages */
        .flash-msg {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 500;
        }
        .flash-success { background: #d4edda; color: #155724; }
        .flash-error { background: #f8d7da; color: #721c24; }

        /* Section title */
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title .count {
            background: var(--orange);
            color: #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        /* Entrega Ativa Card */
        .card-ativa {
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border-left: 5px solid var(--blue);
        }
        .card-ativa .order-number {
            font-size: 20px;
            font-weight: 800;
            color: var(--blue);
            margin-bottom: 4px;
        }
        .card-ativa .timer {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 12px;
        }
        .card-ativa .info-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .card-ativa .info-row:last-child { border-bottom: none; }
        .card-ativa .info-icon {
            font-size: 18px;
            min-width: 24px;
            text-align: center;
        }
        .card-ativa .info-label {
            font-weight: 600;
            color: #555;
            min-width: 70px;
        }
        .card-ativa .items-list {
            font-size: 13px;
            color: #666;
            padding: 8px 0 8px 34px;
        }
        .card-ativa .items-list li {
            padding: 2px 0;
            list-style: none;
        }
        .card-ativa .ganho-valor {
            text-align: center;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 8px;
            margin: 12px 0;
        }
        .card-ativa .ganho-valor .amount {
            font-size: 24px;
            font-weight: 800;
            color: var(--green);
        }
        .card-ativa .ganho-valor .label { font-size: 12px; color: var(--gray); }

        /* Action buttons */
        .btn-action {
            display: block;
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-bottom: 10px;
            transition: opacity 0.3s;
        }
        .btn-action:active { transform: scale(0.98); }
        .btn-coletar { background: linear-gradient(135deg, var(--orange), #d35400); }
        .btn-entregar { background: linear-gradient(135deg, var(--blue), #2980b9); }
        .btn-aceitar { background: linear-gradient(135deg, var(--green-light), var(--green)); }
        .btn-aceitar:disabled { opacity: 0.5; cursor: not-allowed; }

        .btn-link-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            text-decoration: none;
            color: #fff;
            font-weight: 600;
        }
        .btn-maps { background: #4285f4; }
        .btn-tel { background: #25D366; }

        /* Pool Cards */
        .pool-card {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid var(--orange);
        }
        .pool-card .pool-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .pool-card .pool-order {
            font-weight: 700;
            font-size: 15px;
            color: #333;
        }
        .pool-card .pool-ganho {
            background: #e8f5e9;
            color: var(--green);
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
        }
        .pool-card .pool-info {
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
        }
        .pool-card .pool-info span {
            margin-right: 12px;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }
        .empty-state .emoji { font-size: 48px; margin-bottom: 12px; }
        .empty-state p { font-size: 15px; }

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
            transition: color 0.2s;
        }
        .nav-item.active { color: var(--green); }
        .nav-item .nav-icon { font-size: 20px; display: block; margin-bottom: 2px; }

        /* Pulse animation for pool */
        .pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* Loading overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }
        .loading-overlay.active { display: flex; }
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<!-- Header -->
<div class="header">
    <div class="header-top">
        <div>
            <h1><?php echo html_escape($motoboy->nome); ?></h1>
            <span class="status-badge status-<?php echo html_escape($motoboy->status); ?>">
                <?php echo $motoboy->status === 'em_entrega' ? 'Em Entrega' : 'Disponivel'; ?>
            </span>
        </div>
        <a href="<?php echo base_url('motoboy/logout'); ?>" class="btn-logout">Sair</a>
    </div>
</div>

<!-- Ganhos Resumo -->
<div class="ganhos-bar">
    <div class="ganho-item">
        <div class="valor">R$ <?php echo number_format($resumo->hoje_valor, 2, ',', '.'); ?></div>
        <div class="label">Hoje (<?php echo (int)$resumo->hoje_qtd; ?>)</div>
    </div>
    <div class="ganho-item">
        <div class="valor">R$ <?php echo number_format($resumo->semana_valor, 2, ',', '.'); ?></div>
        <div class="label">Semana (<?php echo (int)$resumo->semana_qtd; ?>)</div>
    </div>
    <div class="ganho-item">
        <div class="valor">R$ <?php echo number_format($resumo->mes_valor, 2, ',', '.'); ?></div>
        <div class="label">Mes (<?php echo (int)$resumo->mes_qtd; ?>)</div>
    </div>
</div>

<div class="content">

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('message')): ?>
        <div class="flash-msg flash-success"><?php echo html_escape($this->session->flashdata('message')); ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('exception')): ?>
        <div class="flash-msg flash-error"><?php echo html_escape($this->session->flashdata('exception')); ?></div>
    <?php endif; ?>

    <?php if ($entrega_ativa): ?>
    <!-- ===== ENTREGA ATIVA ===== -->
    <div class="section-title">&#x1F6F5; Sua Entrega Atual</div>

    <div class="card-ativa" id="cardAtiva" data-order-id="<?php echo (int)$entrega_ativa->id; ?>">
        <div class="order-number">Pedido #<?php echo html_escape($entrega_ativa->order_number); ?></div>
        <div class="timer" id="timerEntrega">
            <?php if (!empty($entrega_ativa->entrega_info->aceito_em)): ?>
                Aceito em <?php echo date('H:i', strtotime($entrega_ativa->entrega_info->aceito_em)); ?>
            <?php endif; ?>
        </div>

        <!-- Endereço -->
        <div class="info-row">
            <span class="info-icon">&#x1F4CD;</span>
            <div>
                <div class="info-label">Endereco</div>
                <div><?php echo html_escape($entrega_ativa->cliente_endereco); ?></div>
                <?php if (!empty($entrega_ativa->cliente_complemento)): ?>
                    <div style="color:#888;"><?php echo html_escape($entrega_ativa->cliente_complemento); ?></div>
                <?php endif; ?>
                <?php if (!empty($entrega_ativa->zona_nome)): ?>
                    <div style="color:#888;">Bairro: <?php echo html_escape($entrega_ativa->zona_nome); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cliente -->
        <div class="info-row">
            <span class="info-icon">&#x1F464;</span>
            <div>
                <div class="info-label">Cliente</div>
                <div><?php echo html_escape($entrega_ativa->cliente_nome); ?></div>
            </div>
        </div>

        <!-- Links rápidos -->
        <div style="display:flex;gap:8px;margin:12px 0;">
            <a href="https://www.google.com/maps/search/<?php echo urlencode($entrega_ativa->cliente_endereco . ' ' . ($entrega_ativa->zona_nome ?? '')); ?>"
               target="_blank" class="btn-link-action btn-maps">
                &#x1F5FA; Maps
            </a>
            <a href="tel:<?php echo html_escape(preg_replace('/\D/', '', $entrega_ativa->cliente_telefone)); ?>"
               class="btn-link-action btn-tel">
                &#x1F4DE; Ligar
            </a>
        </div>

        <!-- Itens -->
        <div class="info-row">
            <span class="info-icon">&#x1F6D2;</span>
            <div class="info-label">Itens</div>
        </div>
        <ul class="items-list">
            <?php foreach ($entrega_ativa->items as $item): ?>
                <li><?php echo (int)$item->quantity; ?>x <?php echo html_escape($item->product_name); ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- Pagamento -->
        <div class="info-row">
            <span class="info-icon">&#x1F4B3;</span>
            <div>
                <div class="info-label">Pagamento</div>
                <div>
                    <?php
                    $pgto_nomes = ['dinheiro' => 'Dinheiro', 'cartao' => 'Cartao', 'pix' => 'PIX'];
                    echo $pgto_nomes[$entrega_ativa->forma_pagamento] ?? ucfirst(html_escape($entrega_ativa->forma_pagamento));
                    ?>
                    - <strong>R$ <?php echo number_format($entrega_ativa->total, 2, ',', '.'); ?></strong>
                </div>
                <?php if ($entrega_ativa->forma_pagamento === 'dinheiro' && !empty($entrega_ativa->troco_para) && $entrega_ativa->troco_para > 0): ?>
                    <div style="color:var(--orange);font-weight:600;">
                        Troco para R$ <?php echo number_format($entrega_ativa->troco_para, 2, ',', '.'); ?>
                        (Troco: R$ <?php echo number_format($entrega_ativa->troco_para - $entrega_ativa->total, 2, ',', '.'); ?>)
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ganho -->
        <div class="ganho-valor">
            <div class="label">Seu ganho nesta entrega</div>
            <div class="amount">R$ <?php echo number_format($entrega_ativa->entrega_info->valor_ganho ?? 0, 2, ',', '.'); ?></div>
        </div>

        <!-- Botões de ação -->
        <?php
        $entrega_status = $entrega_ativa->entrega_info->status ?? 'aceito';
        ?>
        <?php if ($entrega_status === 'aceito'): ?>
            <button type="button" class="btn-action btn-coletar" onclick="ajaxAction('coletar', <?php echo (int)$entrega_ativa->id; ?>)">
                &#x1F4E6; PEGUEI A MERCADORIA
            </button>
        <?php endif; ?>
        <?php if ($entrega_status === 'aceito' || $entrega_status === 'coletado'): ?>
            <button type="button" class="btn-action btn-entregar" onclick="ajaxAction('entregar', <?php echo (int)$entrega_ativa->id; ?>, true)">
                &#x2705; ENTREGUEI AO CLIENTE
            </button>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <!-- ===== POOL DE ENTREGAS ===== -->
    <div class="section-title">
        &#x1F4E6; Entregas Disponiveis
        <span class="count" id="poolCount"><?php echo count($pool); ?></span>
        <span class="pulse" style="font-size:12px;color:var(--gray);margin-left:auto;" id="pollingStatus">atualizando...</span>
    </div>

    <div id="poolContainer">
        <?php if (empty($pool)): ?>
            <div class="empty-state" id="emptyPool">
                <div class="emoji">&#x1F634;</div>
                <p>Nenhuma entrega disponivel no momento</p>
                <p style="font-size:13px;margin-top:8px;">Aguarde, novas entregas aparecem automaticamente</p>
            </div>
        <?php else: ?>
            <?php foreach ($pool as $order): ?>
                <div class="pool-card" data-order-id="<?php echo (int)$order->id; ?>">
                    <div class="pool-header">
                        <span class="pool-order">#<?php echo html_escape($order->order_number); ?></span>
                        <span class="pool-ganho">R$ <?php echo number_format($motoboy->taxa_entrega_fixa, 2, ',', '.'); ?></span>
                    </div>
                    <div class="pool-info">
                        <span>&#x1F4CD; <?php echo html_escape($order->zona_nome ?? 'Sem bairro'); ?></span>
                        <span>&#x1F4B0; R$ <?php echo number_format($order->total, 2, ',', '.'); ?></span>
                    </div>
                    <div class="pool-info">
                        <span>&#x1F4B3; <?php echo ucfirst(html_escape($order->forma_pagamento)); ?></span>
                        <?php if (!empty($order->hora_pronto_coleta)): ?>
                            <span>&#x23F0; Pronto <?php echo date('H:i', strtotime($order->hora_pronto_coleta)); ?></span>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn-action btn-aceitar" style="margin-top:10px;" onclick="ajaxAction('aceitar', <?php echo (int)$order->id; ?>)">
                        &#x1F680; ACEITAR ENTREGA
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <a href="<?php echo base_url('motoboy/dashboard'); ?>" class="nav-item active">
        <span class="nav-icon">&#x1F3E0;</span>
        Inicio
    </a>
    <a href="<?php echo base_url('motoboy/historico'); ?>" class="nav-item">
        <span class="nav-icon">&#x1F4CB;</span>
        Historico
    </a>
</div>

<script>
(function() {
    'use strict';

    var baseUrl = '<?php echo base_url(); ?>';
    var temAtiva = <?php echo $entrega_ativa ? 'true' : 'false'; ?>;
    var pollInterval = temAtiva ? 30000 : 15000; // 15s pool, 30s se tem ativa
    var pollTimer = null;
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

    // Loading overlay
    window.showLoading = function() {
        document.getElementById('loadingOverlay').classList.add('active');
    };

    // Fetch fresh CSRF token then execute POST action
    window.ajaxAction = function(action, orderId, needConfirm) {
        if (needConfirm && !confirm('Confirma que a mercadoria foi entregue ao cliente?')) {
            return;
        }

        showLoading();

        // Step 1: get fresh CSRF token via GET (won't be invalidated by other tabs)
        var csrfXhr = new XMLHttpRequest();
        csrfXhr.open('GET', baseUrl + 'motoboy/api_csrf', true);
        csrfXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        csrfXhr.onreadystatechange = function() {
            if (csrfXhr.readyState !== 4) return;
            if (csrfXhr.status === 200) {
                try {
                    var csrfData = JSON.parse(csrfXhr.responseText);
                    if (csrfData.csrf_token) csrfHash = csrfData.csrf_token;
                } catch(e) {}
            }

            // Step 2: execute the actual POST with fresh token
            var xhr = new XMLHttpRequest();
            xhr.open('POST', baseUrl + 'motoboy/' + action + '/' + orderId, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function() {
                if (xhr.readyState !== 4) return;
                document.getElementById('loadingOverlay').classList.remove('active');

                if (xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (data.csrf_token) csrfHash = data.csrf_token;
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert(data.message || 'Erro ao processar');
                        }
                    } catch(e) {
                        alert('Erro de comunicacao');
                    }
                } else if (xhr.status === 403) {
                    window.location.reload();
                } else {
                    alert('Erro de conexao (' + xhr.status + ')');
                }
            };
            xhr.send(csrfName + '=' + encodeURIComponent(csrfHash));
        };
        csrfXhr.send();
    };

    // Timer da entrega ativa
    <?php if ($entrega_ativa && !empty($entrega_ativa->entrega_info->aceito_em)): ?>
    (function() {
        var aceito = new Date('<?php echo date('Y-m-d\TH:i:s', strtotime($entrega_ativa->entrega_info->aceito_em)); ?>');
        var timerEl = document.getElementById('timerEntrega');

        function updateTimer() {
            var now = new Date();
            var diff = Math.floor((now - aceito) / 1000);
            var mins = Math.floor(diff / 60);
            var secs = diff % 60;
            timerEl.textContent = 'Ha ' + mins + 'min ' + (secs < 10 ? '0' : '') + secs + 's';
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    })();
    <?php endif; ?>

    // Polling para novas entregas (só quando NÃO tem ativa)
    <?php if (!$entrega_ativa): ?>
    function pollPool() {
        var statusEl = document.getElementById('pollingStatus');
        if (statusEl) statusEl.textContent = 'atualizando...';

        var xhr = new XMLHttpRequest();
        xhr.open('GET', baseUrl + 'motoboy/api/pool', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;
            if (statusEl) statusEl.textContent = '';

            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        updatePoolUI(data.pool, data.count);
                    }
                } catch(e) {}
            } else if (xhr.status === 401) {
                window.location.href = baseUrl + 'motoboy/login';
            }
        };
        xhr.send();
    }

    function updatePoolUI(pool, count) {
        var countEl = document.getElementById('poolCount');
        if (countEl) countEl.textContent = count;

        var container = document.getElementById('poolContainer');
        if (!container) return;

        if (count === 0) {
            container.innerHTML = '<div class="empty-state" id="emptyPool">' +
                '<div class="emoji">&#x1F634;</div>' +
                '<p>Nenhuma entrega disponivel no momento</p>' +
                '<p style="font-size:13px;margin-top:8px;">Aguarde, novas entregas aparecem automaticamente</p></div>';
            return;
        }

        // Verificar se os pedidos mudaram
        var currentIds = [];
        container.querySelectorAll('.pool-card').forEach(function(el) {
            currentIds.push(parseInt(el.dataset.orderId));
        });
        var newIds = pool.map(function(o) { return parseInt(o.id); });

        // Se mudou, recarregar a página (mais simples e confiável)
        if (JSON.stringify(currentIds.sort()) !== JSON.stringify(newIds.sort())) {
            window.location.reload();
        }
    }

    pollTimer = setInterval(pollPool, pollInterval);
    <?php endif; ?>

    // Polling para verificar se tem entrega ativa (quando no pool)
    <?php if (!$entrega_ativa): ?>
    // A cada 30s, verifica se outro dispositivo aceitou uma entrega
    setInterval(function() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', baseUrl + 'motoboy/api/minha', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4 || xhr.status !== 200) return;
            try {
                var data = JSON.parse(xhr.responseText);
                if (data.tem_ativa) {
                    window.location.reload();
                }
            } catch(e) {}
        };
        xhr.send();
    }, 30000);
    <?php endif; ?>

    // Polling para atualizar status da entrega ativa
    <?php if ($entrega_ativa): ?>
    setInterval(function() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', baseUrl + 'motoboy/api/minha', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4 || xhr.status !== 200) return;
            try {
                var data = JSON.parse(xhr.responseText);
                if (!data.tem_ativa) {
                    // Entrega foi concluída ou cancelada, voltar ao pool
                    window.location.reload();
                }
            } catch(e) {}
        };
        xhr.send();
    }, pollInterval);
    <?php endif; ?>

    // Pause polling when tab is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (pollTimer) clearInterval(pollTimer);
        } else {
            <?php if (!$entrega_ativa): ?>
            pollPool(); // Atualizar imediatamente ao voltar
            pollTimer = setInterval(pollPool, pollInterval);
            <?php endif; ?>
        }
    });

})();
</script>
</body>
</html>
