<?php
header('X-Robots-Tag: noindex');
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>Display Cliente — Caixa <?php echo html_escape($terminal->numero); ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=swap">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #1a2332;
            color: #fff;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ---- Header ---- */
        .display-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 16px 24px;
            background: linear-gradient(135deg, #17a2b8, #2874A6);
            flex-shrink: 0;
        }

        .display-header img {
            max-height: 48px;
            border-radius: 6px;
        }

        .display-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        /* ---- Content Area ---- */
        .display-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ---- States ---- */
        .display-state {
            display: none;
            flex: 1;
            flex-direction: column;
        }

        .display-state.display-state-active {
            display: flex;
        }

        /* ---- Idle State ---- */
        .display-idle {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(180deg, #1a2332, #23314b);
        }

        .display-idle h2 {
            font-size: 56px;
            font-weight: 700;
            color: #17a2b8;
            margin-bottom: 16px;
        }

        .display-idle .display-loja-nome {
            font-size: 28px;
            color: rgba(255,255,255,0.6);
            margin-bottom: 40px;
        }

        .display-relogio {
            font-size: 72px;
            font-weight: 300;
            color: rgba(255,255,255,0.5);
            font-family: 'Courier New', monospace;
            letter-spacing: 4px;
        }

        /* ---- Venda State ---- */
        .display-venda-itens {
            flex: 1;
            overflow-y: auto;
            padding: 16px 24px;
            background: #23314b;
        }

        .display-venda-item {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .display-venda-item:last-child {
            border-bottom: none;
        }

        .display-venda-item-nome {
            font-size: 24px;
            font-weight: 500;
            color: #e2e8f0;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 20px;
        }

        .display-venda-item-qtd {
            font-size: 18px;
            color: #94a3b8;
            min-width: 80px;
            text-align: center;
        }

        .display-venda-item-valor {
            font-size: 24px;
            font-weight: 700;
            color: #4ade80;
            min-width: 140px;
            text-align: right;
        }

        .display-venda-ultimo {
            background: rgba(23, 162, 184, 0.15);
            border-left: 4px solid #17a2b8;
            padding-left: 12px;
            animation: display-slide-in 0.3s ease;
        }

        @keyframes display-slide-in {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ---- Total Bar (venda) ---- */
        .display-total-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 20px 32px;
            background: linear-gradient(135deg, #17a2b8, #2874A6);
            flex-shrink: 0;
        }

        .display-total-label {
            font-size: 24px;
            font-weight: 600;
            color: rgba(255,255,255,0.8);
            margin-right: 16px;
            text-transform: uppercase;
        }

        .display-total-valor {
            font-size: 48px;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .display-itens-count {
            margin-right: auto;
            font-size: 18px;
            color: rgba(255,255,255,0.6);
        }

        /* ---- Finalizado State ---- */
        .display-finalizado {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: linear-gradient(135deg, #059669, #10b981);
        }

        .display-finalizado h2 {
            font-size: 64px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 24px;
            animation: display-bounce 0.6s ease;
        }

        @keyframes display-bounce {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .display-finalizado-total {
            font-size: 36px;
            font-weight: 600;
            color: rgba(255,255,255,0.9);
            margin-bottom: 12px;
        }

        .display-finalizado-troco {
            font-size: 48px;
            font-weight: 700;
            color: #fff;
            background: rgba(0,0,0,0.15);
            padding: 12px 40px;
            border-radius: 12px;
        }

        /* ---- Footer ---- */
        .display-footer {
            padding: 8px 24px;
            background: #1a2332;
            border-top: 1px solid #2d3e50;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <div class="display-header">
        <?php if (!empty($setting->logo)): ?>
            <img src="<?php echo base_url('application/modules/dashboard/assets/images/' . html_escape($setting->logo)); ?>"
                 alt="<?php echo html_escape($setting->title ?? 'Loja'); ?>">
        <?php endif; ?>
        <h1 id="display-loja-nome"><?php echo html_escape($setting->title ?? 'Inventro'); ?></h1>
    </div>

    <div class="display-content">

        <!-- Estado: Idle -->
        <div class="display-state display-state-active" id="display-idle">
            <div class="display-idle">
                <h2>Bem-vindo!</h2>
                <div class="display-loja-nome" id="display-loja-sub"><?php echo html_escape($setting->title ?? 'Inventro'); ?></div>
                <div class="display-relogio" id="display-relogio">--:--:--</div>
            </div>
        </div>

        <!-- Estado: Venda -->
        <div class="display-state" id="display-venda">
            <div class="display-venda-itens" id="display-itens">
                <!-- Itens inseridos dinamicamente pelo JS -->
            </div>
            <div class="display-total-bar">
                <span class="display-itens-count" id="display-count">0 itens</span>
                <span class="display-total-label">TOTAL</span>
                <span class="display-total-valor" id="display-total">R$ 0,00</span>
            </div>
        </div>

        <!-- Estado: Finalizado -->
        <div class="display-state" id="display-finalizado">
            <div class="display-finalizado">
                <h2>Obrigado!</h2>
                <div class="display-finalizado-total" id="display-final-total">R$ 0,00</div>
                <div class="display-finalizado-troco pdv-hidden" id="display-final-troco">TROCO: R$ 0,00</div>
            </div>
        </div>

    </div>

    <div class="display-footer">
        Caixa <?php echo html_escape($terminal->numero); ?> &mdash; <?php echo html_escape($setting->title ?? 'Inventro'); ?>
    </div>

    <script>
    (function() {
        'use strict';

        var terminalNumero = <?php echo json_encode($terminal->numero); ?>;
        var channelName = 'pdv-display-' + terminalNumero;
        var bc = null;

        // ---- Clock ----
        function updateClock() {
            var now = new Date();
            var h = String(now.getHours()).padStart(2, '0');
            var m = String(now.getMinutes()).padStart(2, '0');
            var s = String(now.getSeconds()).padStart(2, '0');
            var el = document.getElementById('display-relogio');
            if (el) el.textContent = h + ':' + m + ':' + s;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ---- State Machine ----
        function showState(state) {
            var states = document.querySelectorAll('.display-state');
            for (var i = 0; i < states.length; i++) {
                states[i].classList.remove('display-state-active');
            }
            var target = document.getElementById('display-' + state);
            if (target) target.classList.add('display-state-active');
        }

        // ---- Format Currency ----
        function formatMoney(value) {
            var num = parseFloat(value) || 0;
            return 'R$ ' + num.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // ---- Render Items ----
        function renderItens(itens) {
            var container = document.getElementById('display-itens');
            if (!container) return;
            container.innerHTML = '';

            for (var i = 0; i < itens.length; i++) {
                var item = itens[i];
                var div = document.createElement('div');
                div.className = 'display-venda-item';
                if (i === itens.length - 1) div.className += ' display-venda-ultimo';

                var nome = document.createElement('span');
                nome.className = 'display-venda-item-nome';
                nome.textContent = item.nome || item.product_name || '';

                var qtd = document.createElement('span');
                qtd.className = 'display-venda-item-qtd';
                qtd.textContent = (item.qty || 1) + 'x';

                var valor = document.createElement('span');
                valor.className = 'display-venda-item-valor';
                valor.textContent = formatMoney(item.total || item.subtotal || 0);

                div.appendChild(nome);
                div.appendChild(qtd);
                div.appendChild(valor);
                container.appendChild(div);
            }

            // Auto-scroll to last item
            container.scrollTop = container.scrollHeight;
        }

        // ---- BroadcastChannel Listener ----
        if (typeof BroadcastChannel !== 'undefined') {
            bc = new BroadcastChannel(channelName);

            bc.onmessage = function(e) {
                var msg = e.data;
                if (!msg || !msg.type) return;

                switch (msg.type) {
                    case 'idle':
                        showState('idle');
                        break;

                    case 'venda':
                        showState('venda');
                        if (msg.itens) renderItens(msg.itens);
                        var totalEl = document.getElementById('display-total');
                        if (totalEl) totalEl.textContent = formatMoney(msg.total || 0);
                        var countEl = document.getElementById('display-count');
                        if (countEl) {
                            var n = msg.itens ? msg.itens.length : 0;
                            countEl.textContent = n + (n === 1 ? ' item' : ' itens');
                        }
                        break;

                    case 'item_add':
                        showState('venda');
                        if (msg.itens) renderItens(msg.itens);
                        var totalEl2 = document.getElementById('display-total');
                        if (totalEl2) totalEl2.textContent = formatMoney(msg.total || 0);
                        var countEl2 = document.getElementById('display-count');
                        if (countEl2) {
                            var n2 = msg.itens ? msg.itens.length : 0;
                            countEl2.textContent = n2 + (n2 === 1 ? ' item' : ' itens');
                        }
                        break;

                    case 'finalizado':
                        showState('finalizado');
                        var ftEl = document.getElementById('display-final-total');
                        if (ftEl) ftEl.textContent = formatMoney(msg.total || 0);
                        var trocoEl = document.getElementById('display-final-troco');
                        if (trocoEl) {
                            if (msg.troco && parseFloat(msg.troco) > 0) {
                                trocoEl.textContent = 'TROCO: ' + formatMoney(msg.troco);
                                trocoEl.classList.remove('pdv-hidden');
                            } else {
                                trocoEl.classList.add('pdv-hidden');
                            }
                        }
                        // Return to idle after delay
                        setTimeout(function() { showState('idle'); }, msg.timeout || 8000);
                        break;
                }
            };
        }
    })();
    </script>

</body>
</html>
