<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento PIX - Pedido #<?php echo html_escape($order->order_number); ?></title>
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
            --warning: #ff9800;
            --danger: #f44336;
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
        .payment-card {
            background: rgba(22, 33, 62, 0.95);
            border-radius: 24px;
            padding: 30px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .pix-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #00b894, #00cec9);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .pix-icon i { font-size: 2.5rem; color: white; }
        h1 { font-size: 1.5rem; margin-bottom: 5px; }
        .order-number { color: var(--primary); font-weight: 700; font-size: 1.2rem; margin-bottom: 15px; }
        .total-display {
            background: rgba(37, 211, 102, 0.15);
            border: 1px solid rgba(37, 211, 102, 0.3);
            border-radius: 16px;
            padding: 15px;
            margin: 15px 0;
        }
        .total-label { color: var(--text-secondary); font-size: 0.85rem; }
        .total-value { font-size: 2rem; font-weight: 700; color: var(--primary); }
        .qrcode-container {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin: 20px 0;
            display: none;
        }
        .qrcode-container img { max-width: 250px; width: 100%; height: auto; }
        .copy-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 12px;
            margin: 15px 0;
            display: none;
        }
        .copy-text {
            font-size: 0.75rem;
            color: var(--text-secondary);
            word-break: break-all;
            max-height: 60px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            transition: all 0.3s;
            text-decoration: none;
            color: white;
        }
        .btn-copy {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        .btn-copy:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(52, 152, 219, 0.4); }
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
            margin-top: 10px;
        }
        .countdown {
            background: rgba(255,152,0,0.15);
            border: 1px solid rgba(255,152,0,0.3);
            border-radius: 12px;
            padding: 12px;
            margin: 15px 0;
        }
        .countdown i { color: var(--warning); }
        .countdown-time { font-weight: 700; font-size: 1.3rem; color: var(--warning); }
        .status-checking {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-top: 10px;
            display: none;
        }
        .status-checking i { animation: spin 1s linear infinite; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .loading-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding: 30px;
        }
        .loading-spinner .spinner {
            width: 50px; height: 50px;
            border: 4px solid rgba(255,255,255,0.1);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        .paid-box {
            background: rgba(0, 200, 83, 0.15);
            border: 1px solid rgba(0, 200, 83, 0.3);
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
            display: none;
        }
        .paid-box i { font-size: 3rem; color: var(--success); margin-bottom: 10px; }
        .paid-box h2 { color: var(--success); font-size: 1.3rem; }
        .expired-box {
            background: rgba(244, 67, 54, 0.15);
            border: 1px solid rgba(244, 67, 54, 0.3);
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
            display: none;
        }
        .expired-box i { font-size: 3rem; color: var(--danger); margin-bottom: 10px; }
        .btn-new-pix {
            background: linear-gradient(135deg, #00b894, #00cec9);
            margin-top: 15px;
        }
        .toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.9);
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            font-size: 0.9rem;
            z-index: 999;
            display: none;
        }
        .sandbox-banner {
            background: rgba(255, 152, 0, 0.2);
            border: 2px dashed rgba(255, 152, 0, 0.6);
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 15px;
            text-align: center;
        }
        .sandbox-banner .badge {
            background: var(--warning);
            color: #000;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sandbox-banner p {
            color: var(--warning);
            font-size: 0.8rem;
            margin-top: 6px;
        }
        .btn-simulate {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            margin-top: 12px;
            display: none;
        }
        .btn-simulate:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(255, 152, 0, 0.4); }
        @media (max-width: 480px) {
            .payment-card { padding: 20px; }
            .total-value { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <?php if (!empty($is_sandbox)): ?>
        <div class="sandbox-banner">
            <span class="badge">SANDBOX / HOMOLOGACAO</span>
            <p>Ambiente de testes — pagamentos nao sao reais</p>
        </div>
        <?php endif; ?>

        <div class="pix-icon">
            <i class="fas fa-qrcode"></i>
        </div>

        <h1>Pagamento PIX</h1>
        <p class="order-number">Pedido #<?php echo html_escape($order->order_number); ?></p>

        <div class="total-display">
            <div class="total-label">Valor a pagar</div>
            <div class="total-value">R$ <?php echo number_format($order->total, 2, ',', '.'); ?></div>
        </div>

        <!-- Loading -->
        <div id="loadingArea" class="loading-spinner">
            <div class="spinner"></div>
            <p>Gerando QR Code PIX...</p>
        </div>

        <!-- QR Code Area -->
        <div id="qrcodeArea" class="qrcode-container">
            <img id="qrcodeImg" src="" alt="QR Code PIX">
        </div>

        <!-- Copia e Cola -->
        <div id="copyArea" class="copy-box">
            <div class="copy-text" id="pixCopiaColaText"></div>
            <button class="btn btn-copy" onclick="copiarPix()">
                <i class="fas fa-copy"></i> Copiar codigo PIX
            </button>
        </div>

        <!-- Countdown -->
        <div id="countdownArea" class="countdown" style="display:none;">
            <i class="fas fa-clock"></i> Expira em <span id="countdownTime" class="countdown-time">--:--</span>
        </div>

        <!-- Verificando pagamento -->
        <div id="statusArea" class="status-checking">
            <i class="fas fa-spinner"></i> Verificando pagamento...
        </div>

        <?php if (!empty($is_sandbox)): ?>
        <!-- Simular pagamento (sandbox only) -->
        <button id="btnSimulate" class="btn btn-simulate" onclick="simularPagamento()">
            <i class="fas fa-flask"></i> Simular Pagamento (Teste)
        </button>
        <?php endif; ?>

        <!-- Pagamento confirmado -->
        <div id="paidArea" class="paid-box">
            <i class="fas fa-check-circle"></i>
            <h2>Pagamento Confirmado!</h2>
            <p style="color: var(--text-secondary); margin-top: 10px;">Redirecionando...</p>
        </div>

        <!-- PIX Expirado -->
        <div id="expiredArea" class="expired-box">
            <i class="fas fa-exclamation-circle"></i>
            <h2>PIX Expirado</h2>
            <p style="color: var(--text-secondary); margin-top: 10px;">O tempo para pagamento acabou.</p>
            <button class="btn btn-new-pix" onclick="gerarNovoPix()">
                <i class="fas fa-redo"></i> Gerar Novo PIX
            </button>
        </div>

        <a href="<?php echo base_url('cardapio'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Cardapio
        </a>
    </div>

    <div class="toast" id="toast"></div>

    <script>
    var baseUrl = <?php echo json_encode(base_url()); ?>;
    var orderNumber = <?php echo json_encode($order->order_number); ?>;
    var isSandbox = <?php echo json_encode(!empty($is_sandbox)); ?>;
    var pollInterval = null;
    var countdownInterval = null;
    var expiresAt = null;

    <?php if (!empty($pix_charge)): ?>
    // Charge ja existe — exibir diretamente
    (function() {
        var charge = {
            qrcode_base64: <?php echo json_encode($pix_charge->qrcode_base64); ?>,
            pix_copia_cola: <?php echo json_encode($pix_charge->pix_copia_cola); ?>,
            expiracao: <?php echo (int)$pix_charge->expiracao; ?>,
            created_at: <?php echo json_encode($pix_charge->created_at); ?>
        };
        document.getElementById('loadingArea').style.display = 'none';
        showPixData(charge);
    })();
    <?php else: ?>
    // Gerar novo PIX
    gerarPix();
    <?php endif; ?>

    function gerarPix() {
        document.getElementById('loadingArea').style.display = 'flex';
        document.getElementById('qrcodeArea').style.display = 'none';
        document.getElementById('copyArea').style.display = 'none';
        document.getElementById('countdownArea').style.display = 'none';
        document.getElementById('expiredArea').style.display = 'none';
        document.getElementById('paidArea').style.display = 'none';

        fetch(baseUrl + 'cardapio/api_criar_pix_pedido', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({order_number: orderNumber})
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            document.getElementById('loadingArea').style.display = 'none';
            if (res.success) {
                showPixData(res.data);
            } else {
                showToast(res.message || 'Erro ao gerar PIX');
            }
        })
        .catch(function(err) {
            document.getElementById('loadingArea').style.display = 'none';
            showToast('Erro de conexao');
        });
    }

    function gerarNovoPix() {
        if (pollInterval) clearInterval(pollInterval);
        if (countdownInterval) clearInterval(countdownInterval);
        gerarPix();
    }

    function showPixData(data) {
        // QR Code
        if (data.qrcode_base64) {
            var img = document.getElementById('qrcodeImg');
            if (data.qrcode_base64.indexOf('data:image') === 0) {
                img.src = data.qrcode_base64;
            } else {
                img.src = 'data:image/png;base64,' + data.qrcode_base64;
            }
            document.getElementById('qrcodeArea').style.display = 'block';
        }

        // Copia e cola
        if (data.pix_copia_cola) {
            document.getElementById('pixCopiaColaText').textContent = data.pix_copia_cola;
            document.getElementById('copyArea').style.display = 'block';
        }

        // Countdown — created_at vem no timezone do servidor (America/Sao_Paulo)
        // NAO adicionar 'Z' (UTC) — interpretar como horario local
        var createdTs = new Date(data.created_at.replace(' ', 'T')).getTime();
        if (isNaN(createdTs)) {
            createdTs = new Date(data.created_at).getTime();
        }
        expiresAt = createdTs + (data.expiracao * 1000);
        document.getElementById('countdownArea').style.display = 'block';
        startCountdown();

        // Polling de status
        document.getElementById('statusArea').style.display = 'block';
        startPolling();

        // Mostrar botao simular em sandbox
        if (isSandbox) {
            var btnSim = document.getElementById('btnSimulate');
            if (btnSim) btnSim.style.display = 'flex';
        }
    }

    function startCountdown() {
        if (countdownInterval) clearInterval(countdownInterval);
        countdownInterval = setInterval(function() {
            var now = Date.now();
            var diff = expiresAt - now;
            if (diff <= 0) {
                clearInterval(countdownInterval);
                clearInterval(pollInterval);
                document.getElementById('countdownTime').textContent = '00:00';
                document.getElementById('qrcodeArea').style.display = 'none';
                document.getElementById('copyArea').style.display = 'none';
                document.getElementById('countdownArea').style.display = 'none';
                document.getElementById('statusArea').style.display = 'none';
                document.getElementById('expiredArea').style.display = 'block';
                return;
            }
            var mins = Math.floor(diff / 60000);
            var secs = Math.floor((diff % 60000) / 1000);
            document.getElementById('countdownTime').textContent =
                (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
        }, 1000);
    }

    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(function() {
            fetch(baseUrl + 'cardapio/api_check_pagamento/' + orderNumber)
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success && res.paid) {
                    clearInterval(pollInterval);
                    clearInterval(countdownInterval);
                    showPaid(res.redirect_url);
                }
            })
            .catch(function() {});
        }, 5000);
    }

    function showPaid(redirectUrl) {
        document.getElementById('qrcodeArea').style.display = 'none';
        document.getElementById('copyArea').style.display = 'none';
        document.getElementById('countdownArea').style.display = 'none';
        document.getElementById('statusArea').style.display = 'none';
        document.getElementById('paidArea').style.display = 'block';

        setTimeout(function() {
            window.location.href = redirectUrl || baseUrl + 'cardapio/confirmacao/' + orderNumber;
        }, 2000);
    }

    function copiarPix() {
        var texto = document.getElementById('pixCopiaColaText').textContent;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(texto).then(function() {
                showToast('Codigo PIX copiado!');
            });
        } else {
            var el = document.createElement('textarea');
            el.value = texto;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            showToast('Codigo PIX copiado!');
        }
    }

    function simularPagamento() {
        var btn = document.getElementById('btnSimulate');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Simulando...';
        }

        fetch(baseUrl + 'cardapio/api_simular_pagamento', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({order_number: orderNumber})
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.success) {
                clearInterval(pollInterval);
                clearInterval(countdownInterval);
                showPaid(res.redirect_url);
            } else {
                showToast(res.message || 'Erro ao simular pagamento');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-flask"></i> Simular Pagamento (Teste)';
                }
            }
        })
        .catch(function() {
            showToast('Erro de conexao');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-flask"></i> Simular Pagamento (Teste)';
            }
        });
    }

    function showToast(msg) {
        var t = document.getElementById('toast');
        t.textContent = msg;
        t.style.display = 'block';
        setTimeout(function() { t.style.display = 'none'; }, 3000);
    }
    </script>
</body>
</html>
