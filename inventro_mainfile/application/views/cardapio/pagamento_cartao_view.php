<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Cartao - Pedido #<?php echo html_escape($order->order_number); ?></title>
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
            --blue: #3498db;
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
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .card-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, var(--blue), #2980b9);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }
        .card-icon i { font-size: 2.5rem; color: white; }
        h1 { font-size: 1.5rem; margin-bottom: 5px; text-align: center; }
        .order-number { color: var(--primary); font-weight: 700; font-size: 1.2rem; text-align: center; margin-bottom: 15px; }
        .total-display {
            background: rgba(37, 211, 102, 0.15);
            border: 1px solid rgba(37, 211, 102, 0.3);
            border-radius: 16px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }
        .total-label { color: var(--text-secondary); font-size: 0.85rem; }
        .total-value { font-size: 2rem; font-weight: 700; color: var(--primary); }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: var(--blue);
        }
        .form-group input::placeholder { color: rgba(255,255,255,0.3); }
        .form-row { display: flex; gap: 12px; }
        .form-row .form-group { flex: 1; }
        .brand-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
        }
        .form-group.with-icon { position: relative; }
        .form-group.with-icon input { padding-right: 45px; }
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
            width: 100%;
            transition: all 0.3s;
            text-decoration: none;
            color: white;
        }
        .btn-pay {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            margin-top: 10px;
        }
        .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(37, 211, 102, 0.4); }
        .btn-pay:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: var(--text-primary);
            margin-top: 10px;
        }
        .error-msg {
            background: rgba(244, 67, 54, 0.15);
            border: 1px solid rgba(244, 67, 54, 0.3);
            border-radius: 12px;
            padding: 12px;
            margin: 15px 0;
            color: var(--danger);
            text-align: center;
            display: none;
        }
        .success-msg {
            background: rgba(0, 200, 83, 0.15);
            border: 1px solid rgba(0, 200, 83, 0.3);
            border-radius: 16px;
            padding: 25px;
            margin: 20px 0;
            text-align: center;
            display: none;
        }
        .success-msg i { font-size: 3rem; color: var(--success); margin-bottom: 10px; }
        .success-msg h2 { color: var(--success); font-size: 1.3rem; }
        .loading-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
            flex-direction: column;
            gap: 15px;
        }
        .loading-overlay .spinner {
            width: 50px; height: 50px;
            border: 4px solid rgba(255,255,255,0.1);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { 100% { transform: rotate(360deg); } }
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
        .parcelas-info {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin-top: 4px;
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
            background: var(--warning, #ff9800);
            color: #000;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sandbox-banner p {
            color: #ff9800;
            font-size: 0.8rem;
            margin-top: 6px;
        }
        .btn-simulate {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            margin-top: 12px;
        }
        .btn-simulate:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(255, 152, 0, 0.4); }
        @media (max-width: 480px) {
            .payment-card { padding: 20px; }
            .form-row { flex-direction: column; gap: 0; }
            .total-value { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="payment-card" id="cardForm">
        <?php if (!empty($is_sandbox)): ?>
        <div class="sandbox-banner">
            <span class="badge">SANDBOX / HOMOLOGACAO</span>
            <p>Ambiente de testes — pagamentos nao sao reais</p>
        </div>
        <?php endif; ?>

        <div class="card-icon">
            <i class="fas fa-credit-card"></i>
        </div>

        <h1>Pagamento por Cartao</h1>
        <p class="order-number">Pedido #<?php echo html_escape($order->order_number); ?></p>

        <div class="total-display">
            <div class="total-label">Valor a pagar</div>
            <div class="total-value">R$ <?php echo number_format($order->total, 2, ',', '.'); ?></div>
        </div>

        <div id="errorMsg" class="error-msg"></div>

        <form id="paymentForm" onsubmit="return processarCartao(event)">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nome no cartao</label>
                <input type="text" id="cardName" placeholder="Nome como esta no cartao" required
                       value="<?php echo html_escape($order->cliente_nome); ?>">
            </div>

            <div class="form-group with-icon">
                <label><i class="fas fa-credit-card"></i> Numero do cartao</label>
                <input type="text" id="cardNumber" placeholder="0000 0000 0000 0000" required
                       maxlength="19" inputmode="numeric" autocomplete="cc-number">
                <span class="brand-icon" id="brandIcon"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Validade</label>
                    <input type="text" id="cardExpiry" placeholder="MM/AA" required
                           maxlength="5" inputmode="numeric" autocomplete="cc-exp">
                </div>
                <div class="form-group">
                    <label>CVV</label>
                    <input type="text" id="cardCvv" placeholder="000" required
                           maxlength="4" inputmode="numeric" autocomplete="cc-csc">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-id-card"></i> CPF do titular</label>
                <input type="text" id="cardCpf" placeholder="000.000.000-00" required
                       maxlength="14" inputmode="numeric">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> E-mail</label>
                    <input type="email" id="cardEmail" placeholder="email@exemplo.com" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Telefone</label>
                    <input type="text" id="cardPhone" placeholder="(00) 00000-0000"
                           maxlength="15" inputmode="tel"
                           value="<?php echo html_escape($order->cliente_telefone); ?>">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-list-ol"></i> Parcelas</label>
                <select id="cardParcelas">
                    <option value="1">1x de R$ <?php echo number_format($order->total, 2, ',', '.'); ?> (sem juros)</option>
                </select>
                <div class="parcelas-info" id="parcelasInfo"></div>
            </div>

            <button type="submit" class="btn btn-pay" id="btnPay">
                <i class="fas fa-lock"></i> Pagar R$ <?php echo number_format($order->total, 2, ',', '.'); ?>
            </button>
        </form>

        <?php if (!empty($is_sandbox)): ?>
        <button class="btn btn-simulate" onclick="simularPagamento()">
            <i class="fas fa-flask"></i> Simular Pagamento (Teste)
        </button>
        <?php endif; ?>

        <a href="<?php echo base_url('cardapio'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Cardapio
        </a>
    </div>

    <!-- Sucesso -->
    <div id="successMsg" class="success-msg" style="max-width:500px;width:100%;">
        <i class="fas fa-check-circle"></i>
        <h2>Pagamento Aprovado!</h2>
        <p style="color: var(--text-secondary); margin-top: 10px;">Redirecionando...</p>
    </div>

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <p style="color:white;">Processando pagamento...</p>
    </div>

    <div class="toast" id="toast"></div>

    <!-- Efi Pay tokenization JS -->
    <script type="text/javascript">
        var s = document.createElement('script');
        s.setAttribute('src', 'https://cdn.jsdelivr.net/gh/efipay/js-payment-token-efi/dist/payment-token-efi.min.js');
        s.setAttribute('data-account-id', <?php echo json_encode($account_id); ?>);
        <?php if ($is_sandbox): ?>
        s.setAttribute('data-environment', 'homologacao');
        <?php endif; ?>
        document.head.appendChild(s);
    </script>

    <script>
    var baseUrl = <?php echo json_encode(base_url()); ?>;
    var orderNumber = <?php echo json_encode($order->order_number); ?>;
    var orderTotal = <?php echo (float)$order->total; ?>;
    var detectedBrand = '';

    // Formatacao do numero do cartao
    document.getElementById('cardNumber').addEventListener('input', function(e) {
        var val = this.value.replace(/\D/g, '').substring(0, 16);
        var formatted = val.replace(/(\d{4})(?=\d)/g, '$1 ');
        this.value = formatted;
        detectBrand(val);
    });

    // Formatacao validade
    document.getElementById('cardExpiry').addEventListener('input', function() {
        var val = this.value.replace(/\D/g, '').substring(0, 4);
        if (val.length >= 3) {
            this.value = val.substring(0, 2) + '/' + val.substring(2);
        } else {
            this.value = val;
        }
    });

    // Formatacao CVV
    document.getElementById('cardCvv').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').substring(0, 4);
    });

    // Formatacao CPF
    document.getElementById('cardCpf').addEventListener('input', function() {
        var val = this.value.replace(/\D/g, '').substring(0, 11);
        if (val.length > 9) {
            this.value = val.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
        } else if (val.length > 6) {
            this.value = val.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
        } else if (val.length > 3) {
            this.value = val.replace(/(\d{3})(\d{1,3})/, '$1.$2');
        } else {
            this.value = val;
        }
    });

    // Formatacao telefone
    document.getElementById('cardPhone').addEventListener('input', function() {
        var val = this.value.replace(/\D/g, '').substring(0, 11);
        if (val.length > 6) {
            this.value = '(' + val.substring(0,2) + ') ' + val.substring(2, val.length > 10 ? 7 : 6) + '-' + val.substring(val.length > 10 ? 7 : 6);
        } else if (val.length > 2) {
            this.value = '(' + val.substring(0,2) + ') ' + val.substring(2);
        } else {
            this.value = val;
        }
    });

    function detectBrand(num) {
        var icon = document.getElementById('brandIcon');
        var brands = {
            'visa': /^4/,
            'mastercard': /^(5[1-5]|2[2-7])/,
            'amex': /^3[47]/,
            'elo': /^(636368|438935|504175|451416|636297|5067|4576|4011)/,
            'hipercard': /^(606282|3841)/
        };
        detectedBrand = '';
        for (var b in brands) {
            if (brands[b].test(num)) {
                detectedBrand = b;
                break;
            }
        }
        var icons = {
            'visa': '<i class="fab fa-cc-visa" style="color:#1A1F71"></i>',
            'mastercard': '<i class="fab fa-cc-mastercard" style="color:#EB001B"></i>',
            'amex': '<i class="fab fa-cc-amex" style="color:#2E77BC"></i>',
            'elo': '<span style="color:#00A4E0;font-weight:700;font-size:0.9rem;">elo</span>',
            'hipercard': '<span style="color:#822124;font-weight:700;font-size:0.9rem;">Hiper</span>'
        };
        icon.innerHTML = icons[detectedBrand] || '';
    }

    function processarCartao(e) {
        e.preventDefault();

        var errorEl = document.getElementById('errorMsg');
        errorEl.style.display = 'none';

        var cardNum = document.getElementById('cardNumber').value.replace(/\s/g, '');
        var expiry = document.getElementById('cardExpiry').value;
        var cvv = document.getElementById('cardCvv').value;
        var cpf = document.getElementById('cardCpf').value.replace(/\D/g, '');

        if (cardNum.length < 13 || !expiry || cvv.length < 3 || cpf.length < 11) {
            errorEl.textContent = 'Preencha todos os campos corretamente';
            errorEl.style.display = 'block';
            return false;
        }

        if (!detectedBrand) {
            errorEl.textContent = 'Bandeira do cartao nao identificada';
            errorEl.style.display = 'block';
            return false;
        }

        var parts = expiry.split('/');
        var expMonth = parts[0];
        var expYear = parts.length > 1 ? parts[1] : '';
        if (expYear.length === 2) expYear = '20' + expYear;

        // Desabilitar botao
        document.getElementById('btnPay').disabled = true;
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Tokenizar via SDK Efi
        if (typeof EfiJs === 'undefined' && typeof $gn === 'undefined') {
            errorEl.textContent = 'SDK de pagamento nao carregado. Recarregue a pagina.';
            errorEl.style.display = 'block';
            document.getElementById('btnPay').disabled = false;
            document.getElementById('loadingOverlay').style.display = 'none';
            return false;
        }

        var tokenFunc = (typeof EfiJs !== 'undefined') ? EfiJs : $gn;

        tokenFunc.checkout.getPaymentToken({
            brand: detectedBrand,
            number: cardNum,
            cvv: cvv,
            expirationMonth: expMonth,
            expirationYear: expYear,
            reuse: false
        }, function(err, response) {
            if (err) {
                document.getElementById('loadingOverlay').style.display = 'none';
                document.getElementById('btnPay').disabled = false;
                var errMsg = 'Erro na tokenizacao do cartao';
                if (err.error_description) errMsg = err.error_description;
                else if (err.message) errMsg = err.message;
                errorEl.textContent = errMsg;
                errorEl.style.display = 'block';
                return;
            }

            var paymentToken = response.data.payment_token;
            enviarPagamento(paymentToken);
        });

        return false;
    }

    function enviarPagamento(paymentToken) {
        var payload = {
            order_number: orderNumber,
            payment_token: paymentToken,
            parcelas: parseInt(document.getElementById('cardParcelas').value),
            cliente_nome: document.getElementById('cardName').value,
            cliente_cpf: document.getElementById('cardCpf').value.replace(/\D/g, ''),
            cliente_email: document.getElementById('cardEmail').value,
            cliente_telefone: document.getElementById('cardPhone').value.replace(/\D/g, '')
        };

        fetch(baseUrl + 'cardapio/api_processar_cartao_pedido', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            document.getElementById('loadingOverlay').style.display = 'none';
            if (res.success) {
                document.getElementById('cardForm').style.display = 'none';
                document.getElementById('successMsg').style.display = 'block';
                setTimeout(function() {
                    window.location.href = res.redirect_url || baseUrl + 'cardapio/confirmacao/' + orderNumber;
                }, 2000);
            } else {
                document.getElementById('btnPay').disabled = false;
                var errorEl = document.getElementById('errorMsg');
                errorEl.textContent = res.message || 'Pagamento recusado';
                errorEl.style.display = 'block';
            }
        })
        .catch(function() {
            document.getElementById('loadingOverlay').style.display = 'none';
            document.getElementById('btnPay').disabled = false;
            document.getElementById('errorMsg').textContent = 'Erro de conexao. Tente novamente.';
            document.getElementById('errorMsg').style.display = 'block';
        });
    }

    function simularPagamento() {
        document.getElementById('loadingOverlay').style.display = 'flex';

        fetch(baseUrl + 'cardapio/api_simular_pagamento', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({order_number: orderNumber})
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            document.getElementById('loadingOverlay').style.display = 'none';
            if (res.success) {
                document.getElementById('cardForm').style.display = 'none';
                document.getElementById('successMsg').style.display = 'block';
                setTimeout(function() {
                    window.location.href = res.redirect_url || baseUrl + 'cardapio/confirmacao/' + orderNumber;
                }, 2000);
            } else {
                showToast(res.message || 'Erro ao simular pagamento');
            }
        })
        .catch(function() {
            document.getElementById('loadingOverlay').style.display = 'none';
            showToast('Erro de conexao');
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
