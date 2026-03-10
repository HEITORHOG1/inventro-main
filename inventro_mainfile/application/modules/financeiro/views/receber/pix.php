<?php
$currency = $get_appsetting->currencyname;
?>

<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-qrcode"></i> <?php echo makeString(['cobranca_pix']); ?>
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

        <?php if (!$efi_active): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Efi Pay nao esta configurado. <a href="<?php echo base_url('financeiro/efi_config'); ?>">Configurar agora</a>.
            </div>
        <?php else: ?>

        <!-- Area do PIX -->
        <div id="pixGenerateArea" <?php echo $pix_ativo ? 'style="display:none;"' : ''; ?>>
            <div class="row">
                <div class="col-md-4 offset-md-4">
                    <div class="form-group">
                        <label for="pixValor"><?php echo makeString(['valor_cobranca_pix']); ?></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">R$</span>
                            </div>
                            <input type="number" step="0.01" min="0.01" max="<?php echo $valor_pendente; ?>"
                                   class="form-control form-control-lg" id="pixValor"
                                   value="<?php echo $valor_pendente; ?>">
                        </div>
                    </div>
                    <button type="button" class="btn btn-success btn-lg btn-block" id="btnGerarPix">
                        <i class="fas fa-qrcode"></i> <?php echo makeString(['gerar_pix']); ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- QR Code Display -->
        <div id="pixDisplayArea" <?php echo $pix_ativo ? '' : 'style="display:none;"'; ?>>
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-success mb-3">
                                <i class="fas fa-qrcode"></i> <?php echo makeString(['qrcode_pix']); ?>
                            </h4>

                            <div id="pixStatusBadge" class="mb-3">
                                <span class="badge badge-warning p-2" style="font-size:1.1em;">
                                    <i class="fas fa-clock"></i> <?php echo makeString(['aguardando_pagamento']); ?>
                                </span>
                            </div>

                            <div id="pixQrCodeImg" class="mb-3">
                                <?php if ($pix_ativo && !empty($pix_ativo->qrcode_base64)): ?>
                                    <img src="<?php echo $pix_ativo->qrcode_base64; ?>" alt="QR Code PIX" class="img-fluid" style="max-width:300px;">
                                <?php endif; ?>
                            </div>

                            <div id="pixValorDisplay" class="mb-3" style="font-size:1.5em;">
                                <?php if ($pix_ativo): ?>
                                    <strong>R$ <?php echo number_format($pix_ativo->valor, 2, ',', '.'); ?></strong>
                                <?php endif; ?>
                            </div>

                            <!-- Copia e Cola -->
                            <div class="form-group" id="pixCopiaColaGroup">
                                <label><?php echo makeString(['pix_copia_cola']); ?>:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="pixCopiaCola" readonly
                                           value="<?php echo $pix_ativo ? html_escape($pix_ativo->pix_copia_cola) : ''; ?>">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" id="btnCopiar" title="Copiar">
                                            <i class="fas fa-copy"></i> Copiar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Countdown -->
                            <div id="pixCountdown" class="text-muted mt-2">
                                Expira em: <span id="countdownTimer">--:--</span>
                            </div>

                            <hr>
                            <button type="button" class="btn btn-outline-success" id="btnNovoPix">
                                <i class="fas fa-redo"></i> <?php echo makeString(['gerar_novo_pix']); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sucesso -->
        <div id="pixSuccessArea" style="display:none;">
            <div class="row">
                <div class="col-md-6 offset-md-3 text-center">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h2><i class="fas fa-check-circle"></i></h2>
                            <h3><?php echo makeString(['pagamento_confirmado']); ?></h3>
                            <p>Baixa registrada automaticamente.</p>
                            <a href="<?php echo base_url('financeiro/contas_receber/lista'); ?>" class="btn btn-light btn-lg mt-2">
                                <i class="fas fa-arrow-left"></i> Voltar para Lista
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</div>

<?php if (!empty($historico_pix)): ?>
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-history"></i> Historico de Cobrancas PIX</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>TxID</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Pago em</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($historico_pix as $h): ?>
                <tr>
                    <td><small><?php echo html_escape($h->txid); ?></small></td>
                    <td>R$ <?php echo number_format($h->valor, 2, ',', '.'); ?></td>
                    <td>
                        <?php
                        $badge = 'secondary';
                        if ($h->status === 'confirmed') $badge = 'success';
                        elseif ($h->status === 'pending') $badge = 'warning';
                        elseif ($h->status === 'expired') $badge = 'dark';
                        elseif ($h->status === 'error') $badge = 'danger';
                        ?>
                        <span class="badge badge-<?php echo $badge; ?>"><?php echo html_escape(ucfirst($h->status)); ?></span>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($h->created_at)); ?></td>
                    <td><?php echo $h->paid_at ? date('d/m/Y H:i', strtotime($h->paid_at)) : '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="csrf_name" value="<?php echo $this->security->get_csrf_token_name(); ?>">
<input type="hidden" id="csrf_hash" value="<?php echo $this->security->get_csrf_hash(); ?>">
<input type="hidden" id="conta_id" value="<?php echo (int) $conta->id; ?>">
<input type="hidden" id="active_charge_id" value="<?php echo $pix_ativo ? (int) $pix_ativo->id : ''; ?>">
<input type="hidden" id="active_expiracao" value="<?php echo $pix_ativo ? (int) $pix_ativo->expiracao : ''; ?>">
<input type="hidden" id="active_created_at" value="<?php echo $pix_ativo ? html_escape($pix_ativo->created_at) : ''; ?>">

<script>
$(document).ready(function() {
    var base_url = $('#base_url').val();
    var csrf_name = $('#csrf_name').val();
    var csrf_hash = $('#csrf_hash').val();
    var pollInterval = null;
    var countdownInterval = null;

    function updateCsrf(token) {
        if (token) {
            csrf_hash = token;
            $('#csrf_hash').val(token);
        }
    }

    // Gerar PIX
    $('#btnGerarPix').click(function() {
        var btn = $(this);
        var valor = $('#pixValor').val();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Gerando...');

        var data = {};
        data[csrf_name] = csrf_hash;
        data['conta_id'] = $('#conta_id').val();
        data['valor'] = valor;

        $.ajax({
            url: base_url + 'financeiro/contas_receber/criar_cobranca_pix',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(r) {
                updateCsrf(r.csrf_token);
                btn.prop('disabled', false).html('<i class="fas fa-qrcode"></i> <?php echo makeString(["gerar_pix"]); ?>');

                if (r.success) {
                    $('#pixQrCodeImg').html('<img src="' + r.qrcode_base64 + '" alt="QR Code" class="img-fluid" style="max-width:300px;">');
                    $('#pixCopiaCola').val(r.pix_copia_cola);
                    $('#pixValorDisplay').html('<strong>R$ ' + r.valor + '</strong>');
                    $('#active_charge_id').val(r.charge_id);

                    $('#pixGenerateArea').hide();
                    $('#pixDisplayArea').show();

                    startPolling(r.charge_id);
                    startCountdown(r.expiracao);
                } else {
                    showToast('Erro: ' + r.message, 'error');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fas fa-qrcode"></i> <?php echo makeString(["gerar_pix"]); ?>');
                showToast('Erro de conexao.', 'error');
            }
        });
    });

    // Copiar copia e cola
    $('#btnCopiar').click(function() {
        var input = $('#pixCopiaCola')[0];
        input.select();
        document.execCommand('copy');
        $(this).html('<i class="fas fa-check"></i> Copiado!');
        var btn = $(this);
        setTimeout(function() { btn.html('<i class="fas fa-copy"></i> Copiar'); }, 2000);
    });

    // Novo PIX
    $('#btnNovoPix').click(function() {
        stopPolling();
        $('#pixDisplayArea').hide();
        $('#pixGenerateArea').show();
    });

    // Polling
    function startPolling(chargeId) {
        stopPolling();
        pollInterval = setInterval(function() {
            var data = {};
            data[csrf_name] = csrf_hash;
            data['charge_id'] = chargeId;

            $.ajax({
                url: base_url + 'financeiro/contas_receber/check_pix_status',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(r) {
                    updateCsrf(r.csrf_token);
                    if (r.status === 'confirmed') {
                        stopPolling();
                        $('#pixDisplayArea').hide();
                        $('#pixSuccessArea').show();
                    } else if (r.status === 'expired') {
                        stopPolling();
                        $('#pixStatusBadge').html('<span class="badge badge-dark p-2" style="font-size:1.1em;"><i class="fas fa-clock"></i> <?php echo makeString(["pix_expirado"]); ?></span>');
                        $('#countdownTimer').text('Expirado');
                    }
                }
            });
        }, 5000);
    }

    function stopPolling() {
        if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
        if (countdownInterval) { clearInterval(countdownInterval); countdownInterval = null; }
    }

    // Countdown
    function startCountdown(seconds) {
        if (countdownInterval) clearInterval(countdownInterval);
        var remaining = seconds;
        countdownInterval = setInterval(function() {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                $('#countdownTimer').text('Expirado');
                return;
            }
            var min = Math.floor(remaining / 60);
            var sec = remaining % 60;
            $('#countdownTimer').text(String(min).padStart(2, '0') + ':' + String(sec).padStart(2, '0'));
        }, 1000);
    }

    // Se ja tem charge ativa, iniciar polling
    var activeId = $('#active_charge_id').val();
    if (activeId) {
        var createdAt = new Date($('#active_created_at').val()).getTime() / 1000;
        var expiracao = parseInt($('#active_expiracao').val());
        var remaining = Math.max(0, (createdAt + expiracao) - Math.floor(Date.now() / 1000));
        if (remaining > 0) {
            startPolling(activeId);
            startCountdown(remaining);
        }
    }
});
</script>
