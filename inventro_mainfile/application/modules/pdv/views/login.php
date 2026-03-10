<!DOCTYPE html>
<html lang="pt-BR" class="pdv-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDV — Login | <?php echo html_escape($setting->title ?? 'Inventro'); ?></title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('admin_assets/plugins/fontawesome-free/css/all.min.css'); ?>">
    <!-- PDV CSS -->
    <link rel="stylesheet" href="<?php echo base_url('application/modules/pdv/assets/css/pdv.css'); ?>">

    <style>
        /* Prevenir autocomplete amarelo do Chrome */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #f8f9fa inset !important;
            -webkit-text-fill-color: #23314b !important;
        }
    </style>
</head>
<body>
    <div class="pdv-container">

        <!-- ====== ÁREA PRINCIPAL — LOGIN ====== -->
        <div class="pdv-login-wrapper">
            <div class="pdv-login-card">

                <!-- Logo -->
                <div class="pdv-login-logo">
                    <h1><span>INVENTRO</span> PDV</h1>
                    <div class="pdv-login-subtitle">Frente de Caixa</div>
                </div>

                <!-- Terminal Info -->
                <div class="pdv-login-terminal">
                    <i class="fas fa-cash-register"></i>
                    Terminal: <?php echo html_escape($terminal->numero); ?> &mdash; <?php echo html_escape($terminal->nome); ?>
                </div>

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

                <!-- Formulário de Login -->
                <?php echo form_open('pdv/autenticar', ['id' => 'pdv-login-form', 'autocomplete' => 'off']); ?>

                    <input type="hidden" name="terminal_numero" value="<?php echo htmlspecialchars($terminal->numero, ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="pdv-form-group">
                        <label for="matricula">
                            <i class="fas fa-id-badge"></i> Matrícula
                        </label>
                        <input type="text"
                               id="matricula"
                               name="matricula"
                               class="pdv-form-control"
                               placeholder="Ex: 00142"
                               autofocus
                               autocomplete="off"
                               inputmode="numeric"
                               maxlength="20"
                               required>
                    </div>

                    <div class="pdv-form-group">
                        <label for="senha">
                            <i class="fas fa-lock"></i> Senha
                        </label>
                        <input type="password"
                               id="senha"
                               name="senha"
                               class="pdv-form-control"
                               placeholder="********"
                               autocomplete="off"
                               required>
                    </div>

                    <button type="submit" class="pdv-btn-primary" id="btn-entrar">
                        <i class="fas fa-sign-in-alt"></i> ENTRAR
                    </button>

                <?php echo form_close(); ?>

            </div>
        </div>

        <!-- ====== STATUS BAR ====== -->
        <div class="pdv-status-bar">
            <span><i class="fas fa-store"></i> LOJA: <?php echo html_escape($setting->title ?? 'Inventro'); ?></span>
            <span><i class="fas fa-cash-register"></i> CAIXA: <?php echo html_escape($terminal->numero); ?></span>
            <span class="status-offline"><i class="fas fa-user-slash"></i> DESCONECTADO</span>
            <span>v1.0</span>
        </div>

    </div>

    <!-- JS -->
    <script src="<?php echo base_url('admin_assets/plugins/jquery/jquery.min.js'); ?>"></script>
    <script>
    (function($) {
        'use strict';

        var $form   = $('#pdv-login-form');
        var $btn    = $('#btn-entrar');
        var $mat    = $('#matricula');

        // Foco automático no campo matrícula
        $mat.trigger('focus');

        // Prevenir duplo envio
        $form.on('submit', function() {
            $btn.prop('disabled', true).addClass('pdv-btn-loading');
        });

        // Enter no campo matrícula → foco na senha
        $mat.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#senha').trigger('focus');
            }
        });
    })(jQuery);
    </script>
</body>
</html>
