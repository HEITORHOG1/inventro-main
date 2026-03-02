<?php
$currency = $get_appsetting->currencyname;
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-credit-card"></i> <?php echo makeString(['pagamento_cartao']); ?>
        </h3>
        <div class="card-tools">
            <a href="<?php echo base_url('financeiro/contas_receber/lista'); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> <?php echo makeString(['back']); ?>
            </a>
        </div>
    </div>

    <div class="card-body">
        <!-- Informacoes da Conta -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="callout callout-info">
                    <h5><strong><?php echo html_escape($conta->codigo); ?></strong> - <?php echo html_escape($conta->descricao); ?></h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong><?php echo makeString(['customer']); ?>:</strong><br>
                            <?php echo html_escape($conta->cliente_nome ?: 'Nao informado'); ?>
                        </div>
                        <div class="col-md-3">
                            <strong><?php echo makeString(['data_vencimento']); ?>:</strong><br>
                            <?php echo date('d/m/Y', strtotime($conta->data_vencimento)); ?>
                        </div>
                        <div class="col-md-3 text-right">
                            <strong><?php echo makeString(['valor_original']); ?>:</strong><br>
                            <span class="text-primary">R$ <?php echo number_format($conta->valor_original, 2, ',', '.'); ?></span>
                        </div>
                        <div class="col-md-3 text-right">
                            <strong><?php echo makeString(['valor_pendente']); ?>:</strong><br>
                            <span class="text-danger font-weight-bold" style="font-size:1.2em;">R$ <?php echo number_format($valor_pendente, 2, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Cartao -->
        <div id="cardFormArea">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3"><i class="fas fa-credit-card"></i> Dados do Cartao</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Valor <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                            <input type="number" step="0.01" min="0.01" max="<?php echo $valor_pendente; ?>"
                                                   class="form-control" id="cardValor" value="<?php echo $valor_pendente; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo makeString(['nome_cartao']); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="cardNome" placeholder="Nome impresso no cartao"
                                               value="<?php echo html_escape($conta->cliente_nome ?: ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo makeString(['numero_cartao']); ?> <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="cardNumero" placeholder="0000 0000 0000 0000" maxlength="19">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="cardBrandIcon"><i class="far fa-credit-card"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php echo makeString(['validade_cartao']); ?> <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="cardValidade" placeholder="MM/AA" maxlength="5">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>CVV <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="cardCvv" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <h5 class="mb-3"><i class="fas fa-user"></i> Dados do Pagador</h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>CPF <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="cardCpf" placeholder="000.000.000-00" maxlength="14">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>E-mail <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="cardEmail" placeholder="email@exemplo.com">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Telefone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="cardPhone" placeholder="(00) 00000-0000" maxlength="15">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php echo makeString(['parcelas']); ?></label>
                                        <select class="form-control" id="cardParcelas">
                                            <option value="1">1x (a vista)</option>
                                        </select>
                                        <small class="text-muted" id="installmentsHint">Informe o numero do cartao para ver parcelas.</small>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary btn-lg btn-block" id="btnProcessarCartao">
                                <i class="fas fa-lock"></i> Pagar com Cartao
                            </button>

                            <div id="cardResult" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sucesso -->
        <div id="cardSuccessArea" style="display:none;">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h2><i class="fas fa-check-circle"></i></h2>
                            <h3><?php echo makeString(['pagamento_aprovado']); ?></h3>
                            <p>Baixa registrada automaticamente.</p>
                            <a href="<?php echo base_url('financeiro/contas_receber/lista'); ?>" class="btn btn-light btn-lg mt-2">
                                <i class="fas fa-arrow-left"></i> Voltar para Lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="csrf_name" value="<?php echo $this->security->get_csrf_token_name(); ?>">
<input type="hidden" id="csrf_hash" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" id="conta_id" value="<?php echo (int) $conta->id; ?>">
<input type="hidden" id="efi_account_id" value="<?php echo html_escape($efi_account_id); ?>">
<input type="hidden" id="efi_sandbox" value="<?php echo $efi_sandbox ? '1' : '0'; ?>">

<!-- Efi Pay JS Token Library -->
<script src="https://cdn.jsdelivr.net/gh/efipay/js-payment-token-efi/dist/payment-token-efi-umd.min.js"></script>

<script>
$(document).ready(function() {
    var base_url = $('#base_url').val();
    var csrf_name = $('#csrf_name').val();
    var csrf_hash = $('#csrf_hash').val();
    var accountId = $('#efi_account_id').val();
    var isSandbox = $('#efi_sandbox').val() === '1';
    var detectedBrand = '';

    function updateCsrf(token) {
        if (token) {
            csrf_hash = token;
            $('#csrf_hash').val(token);
        }
    }

    // Mascara do numero do cartao
    $('#cardNumero').on('input', function() {
        var val = $(this).val().replace(/\D/g, '');
        var formatted = val.replace(/(\d{4})(?=\d)/g, '$1 ');
        $(this).val(formatted);

        // Detectar bandeira com 6+ digitos
        if (val.length >= 6) {
            try {
                EfiPay.CreditCard.setAccount(accountId);
                EfiPay.CreditCard.setEnvironment(isSandbox ? 'sandbox' : 'production');
                EfiPay.CreditCard.verifyCardBrand(val.substring(0, 6))
                    .then(function(result) {
                        detectedBrand = result.brand || '';
                        updateBrandIcon(detectedBrand);
                    })
                    .catch(function() {
                        detectedBrand = '';
                        updateBrandIcon('');
                    });
            } catch(e) {
                // SDK nao carregado
            }
        }
    });

    function updateBrandIcon(brand) {
        var icons = {
            'visa': 'fab fa-cc-visa',
            'mastercard': 'fab fa-cc-mastercard',
            'amex': 'fab fa-cc-amex',
            'elo': 'fas fa-credit-card'
        };
        var iconClass = icons[brand] || 'far fa-credit-card';
        $('#cardBrandIcon').html('<i class="' + iconClass + '"></i>');
    }

    // Mascara validade
    $('#cardValidade').on('input', function() {
        var val = $(this).val().replace(/\D/g, '');
        if (val.length >= 2) {
            val = val.substring(0, 2) + '/' + val.substring(2, 4);
        }
        $(this).val(val);
    });

    // Mascara CPF
    $('#cardCpf').on('input', function() {
        var val = $(this).val().replace(/\D/g, '');
        if (val.length > 3) val = val.substring(0,3) + '.' + val.substring(3);
        if (val.length > 7) val = val.substring(0,7) + '.' + val.substring(7);
        if (val.length > 11) val = val.substring(0,11) + '-' + val.substring(11,13);
        $(this).val(val);
    });

    // Mascara telefone
    $('#cardPhone').on('input', function() {
        var val = $(this).val().replace(/\D/g, '');
        if (val.length > 0) val = '(' + val;
        if (val.length > 3) val = val.substring(0,3) + ') ' + val.substring(3);
        if (val.length > 10) val = val.substring(0,10) + '-' + val.substring(10,14);
        $(this).val(val);
    });

    // Processar pagamento
    $('#btnProcessarCartao').click(function() {
        var btn = $(this);
        var numero = $('#cardNumero').val().replace(/\s/g, '');
        var validade = $('#cardValidade').val().split('/');
        var cvv = $('#cardCvv').val();
        var nome = $('#cardNome').val();
        var cpf = $('#cardCpf').val().replace(/\D/g, '');
        var email = $('#cardEmail').val();
        var phone = $('#cardPhone').val();

        if (!numero || validade.length < 2 || !cvv || !nome || !cpf || !email || !phone) {
            $('#cardResult').html('<div class="alert alert-danger">Preencha todos os campos.</div>');
            return;
        }

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        $('#cardResult').html('');

        // Gerar payment token via SDK Efi
        try {
            EfiPay.CreditCard.setAccount(accountId);
            EfiPay.CreditCard.setEnvironment(isSandbox ? 'sandbox' : 'production');
            EfiPay.CreditCard.getPaymentToken({
                brand: detectedBrand,
                number: numero,
                cvv: cvv,
                expirationMonth: validade[0],
                expirationYear: '20' + validade[1],
                reuse: false
            }).then(function(result) {
                var paymentToken = result.payment_token;
                var cardMask = result.card_mask || '';
                var last4 = numero.slice(-4);

                // Enviar ao servidor
                var data = {};
                data[csrf_name] = csrf_hash;
                data['conta_id'] = $('#conta_id').val();
                data['payment_token'] = paymentToken;
                data['parcelas'] = $('#cardParcelas').val();
                data['valor'] = $('#cardValor').val();
                data['cliente_nome'] = nome;
                data['cliente_cpf'] = cpf;
                data['cliente_email'] = email;
                data['cliente_phone'] = phone;
                data['card_brand'] = detectedBrand;
                data['card_last4'] = last4;

                $.ajax({
                    url: base_url + 'financeiro/contas_receber/processar_cartao',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(r) {
                        updateCsrf(r.csrf_token);
                        btn.prop('disabled', false).html('<i class="fas fa-lock"></i> Pagar com Cartao');

                        if (r.success) {
                            $('#cardFormArea').hide();
                            $('#cardSuccessArea').show();
                        } else {
                            $('#cardResult').html('<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ' + r.message + '</div>');
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-lock"></i> Pagar com Cartao');
                        $('#cardResult').html('<div class="alert alert-danger">Erro de conexao.</div>');
                    }
                });
            }).catch(function(err) {
                btn.prop('disabled', false).html('<i class="fas fa-lock"></i> Pagar com Cartao');
                $('#cardResult').html('<div class="alert alert-danger">Erro ao gerar token: ' + (err.error_description || err.message || 'Verifique os dados do cartao') + '</div>');
            });
        } catch(e) {
            btn.prop('disabled', false).html('<i class="fas fa-lock"></i> Pagar com Cartao');
            $('#cardResult').html('<div class="alert alert-danger">Erro no SDK Efi Pay. Verifique se o Account ID esta configurado.</div>');
        }
    });
});
</script>
