<!DOCTYPE html>
<html lang="pt-BR" class="pdv-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDV — Abertura de Caixa | <?php echo html_escape($setting->title ?? 'Inventro'); ?></title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/fontawesome-free/css/all.min.css'); ?>">
    <!-- PDV CSS -->
    <link rel="stylesheet" href="<?php echo base_url('application/modules/pdv/assets/css/pdv.css'); ?>">
</head>
<body>
    <div class="pdv-container">

        <!-- ====== ÁREA PRINCIPAL — ABERTURA ====== -->
        <div class="pdv-abertura-wrapper">
            <div class="pdv-abertura-card">

                <h2><i class="fas fa-cash-register"></i> Abertura de Caixa</h2>

                <!-- Info do terminal e operador -->
                <dl class="pdv-abertura-info">
                    <dt>Terminal:</dt>
                    <dd><?php echo html_escape($terminal->numero); ?> &mdash; <?php echo html_escape($terminal->nome); ?></dd>

                    <dt>Operador:</dt>
                    <dd><?php echo html_escape($operador->nome); ?></dd>

                    <dt>Data:</dt>
                    <dd><?php echo date('d/m/Y'); ?></dd>

                    <dt>Hora:</dt>
                    <dd><?php echo date('H:i'); ?></dd>
                </dl>

                <!-- Alertas (flashdata) -->
                <?php if ($this->session->flashdata('exception')): ?>
                    <div class="pdv-alert pdv-alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $this->session->flashdata('exception'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('message')): ?>
                    <div class="pdv-alert pdv-alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $this->session->flashdata('message'); ?>
                    </div>
                <?php endif; ?>

                <!-- Formulário de Abertura -->
                <?php echo form_open('pdv/abrir_caixa', ['id' => 'pdv-abertura-form', 'autocomplete' => 'off']); ?>

                    <div class="pdv-form-group">
                        <label for="valor_abertura">
                            <i class="fas fa-coins"></i> Fundo de Troco (R$)
                        </label>
                        <div class="pdv-money-input">
                            <input type="text"
                                   id="valor_abertura"
                                   name="valor_abertura"
                                   class="pdv-form-control"
                                   placeholder="200,00"
                                   autofocus
                                   autocomplete="off"
                                   inputmode="decimal"
                                   required>
                        </div>
                    </div>

                    <div class="pdv-form-group">
                        <label for="observacao">
                            <i class="fas fa-comment"></i> Observação (opcional)
                        </label>
                        <input type="text"
                               id="observacao"
                               name="observacao"
                               class="pdv-form-control"
                               placeholder="Ex: Troco recebido do cofre"
                               maxlength="255"
                               autocomplete="off">
                    </div>

                    <button type="submit" class="pdv-btn-primary" id="btn-abrir">
                        <i class="fas fa-door-open"></i> ABRIR CAIXA
                    </button>

                    <div class="pdv-text-center pdv-mt-16">
                        <a href="<?php echo base_url('pdv/logout'); ?>"
                           style="color: #6c757d; font-size: 14px; text-decoration: none;">
                            <i class="fas fa-arrow-left"></i> Voltar ao login
                        </a>
                    </div>

                <?php echo form_close(); ?>

            </div>
        </div>

        <!-- ====== STATUS BAR ====== -->
        <div class="pdv-status-bar">
            <span><i class="fas fa-store"></i> LOJA: <?php echo html_escape($setting->title ?? 'Inventro'); ?></span>
            <span><i class="fas fa-cash-register"></i> CAIXA: <?php echo html_escape($terminal->numero); ?></span>
            <span class="status-warning"><i class="fas fa-lock"></i> FECHADO</span>
            <span>OP: <?php echo html_escape($operador->nome); ?></span>
            <span>v1.0</span>
        </div>

    </div>

    <!-- JS -->
    <script src="<?php echo base_url('admin_assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script>
    (function($) {
        'use strict';

        var $form = $('#pdv-abertura-form');
        var $btn  = $('#btn-abrir');
        var $val  = $('#valor_abertura');

        // Foco automático no campo valor
        $val.trigger('focus');

        // Máscara simples para valor monetário BR
        $val.on('input', function() {
            var v = this.value.replace(/[^\d]/g, '');
            if (v.length === 0) {
                this.value = '';
                return;
            }
            // Converte para centavos → formato BR
            var cents = parseInt(v, 10);
            var reais = (cents / 100).toFixed(2);
            this.value = reais.replace('.', ',');
        });

        // Prevenir duplo envio
        $form.on('submit', function() {
            // Converte valor BR para formato numérico antes de enviar
            var rawVal = $val.val().replace(/\./g, '').replace(',', '.');
            $val.val(rawVal);
            $btn.prop('disabled', true).addClass('pdv-btn-loading');
        });

        // Enter no valor → foco na observação
        $val.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#observacao').trigger('focus');
            }
        });
    })(jQuery);
    </script>
</body>
</html>
