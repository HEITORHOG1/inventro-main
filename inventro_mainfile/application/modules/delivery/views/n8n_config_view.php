<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fab fa-whatsapp"></i> WhatsApp & Automacao</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('delivery/orders'); ?>">Delivery</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('delivery/config'); ?>">Config</a></li>
                        <li class="breadcrumb-item active">WhatsApp & Automacao</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('msg')): ?>
                <div class="alert alert-<?php echo $this->session->flashdata('msg_type') === 'success' ? 'success' : 'danger'; ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('msg'); ?>
                </div>
            <?php endif; ?>

            <!-- Status da Integracao / Wizard -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tasks"></i> Assistente de Configuracao</h3>
                </div>
                <div class="card-body">
                    <?php $passo = (int)($configs['setup_whatsapp_passo'] ?? 0); ?>
                    <div class="progress mb-3" style="height: 25px;">
                        <div class="progress-bar bg-success progress-bar-striped" role="progressbar"
                             style="width: <?php echo min($passo * 20, 100); ?>%"
                             aria-valuenow="<?php echo $passo; ?>" aria-valuemin="0" aria-valuemax="5">
                            Passo <?php echo html_escape($passo); ?> de 5
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <span class="badge badge-<?php echo $passo >= 1 ? 'success' : 'secondary'; ?> p-2">
                                1. n8n ativo
                            </span>
                        </div>
                        <div class="col">
                            <span class="badge badge-<?php echo $passo >= 2 ? 'success' : 'secondary'; ?> p-2">
                                2. Webhook OK
                            </span>
                        </div>
                        <div class="col">
                            <span class="badge badge-<?php echo $passo >= 3 ? 'success' : 'secondary'; ?> p-2">
                                3. WhatsApp API
                            </span>
                        </div>
                        <div class="col">
                            <span class="badge badge-<?php echo $passo >= 4 ? 'success' : 'secondary'; ?> p-2">
                                4. Templates
                            </span>
                        </div>
                        <div class="col">
                            <span class="badge badge-<?php echo $passo >= 5 ? 'success' : 'secondary'; ?> p-2">
                                5. Concluido
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <form id="n8n-config-form" action="<?php echo base_url('delivery/config/save_n8n'); ?>" method="POST">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

                <div class="row">
                    <!-- Automacao n8n -->
                    <div class="col-md-6">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-robot"></i> Automacao n8n</h3>
                                <div class="card-tools">
                                    <span class="badge badge-<?php echo ($configs['n8n_status'] ?? 'nao_configurado') === 'conectado' ? 'success' : 'warning'; ?>">
                                        <?php echo html_escape($configs['n8n_status'] ?? 'nao_configurado'); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="n8n_ativo" name="n8n_ativo" value="1"
                                               <?php echo ($configs['n8n_ativo'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="n8n_ativo">
                                            <strong>Ativar automacao n8n</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Quando desativado, o sistema usa o fluxo manual (wa.me/)</small>
                                </div>

                                <div id="n8n-fields" style="display: <?php echo ($configs['n8n_ativo'] ?? '0') === '1' ? 'block' : 'none'; ?>;">
                                    <div class="form-group">
                                        <label for="n8n_webhook_url">
                                            <i class="fas fa-link"></i> Webhook URL
                                        </label>
                                        <input type="url" class="form-control" id="n8n_webhook_url" name="n8n_webhook_url"
                                               value="<?php echo html_escape($configs['n8n_webhook_url'] ?? 'http://n8n:5678/webhook/inventro'); ?>"
                                               placeholder="http://n8n:5678/webhook/inventro">
                                        <small class="text-muted">URL interna do webhook n8n (dentro da rede Docker)</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="n8n_webhook_secret">
                                            <i class="fas fa-key"></i> Chave Secreta (HMAC)
                                        </label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="n8n_webhook_secret" name="n8n_webhook_secret"
                                                   value="<?php echo html_escape($configs['n8n_webhook_secret'] ?? ''); ?>"
                                                   placeholder="Gere uma chave aleatoria">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" id="btn-gerar-secret">
                                                    <i class="fas fa-random"></i> Gerar
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">Usada para assinar e validar webhooks (HMAC-SHA256)</small>
                                    </div>

                                    <button type="button" class="btn btn-info btn-sm" id="btn-testar-n8n">
                                        <i class="fas fa-plug"></i> Testar Conexao n8n
                                    </button>
                                    <span id="n8n-test-result" class="ml-2"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Business API -->
                    <div class="col-md-6">
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fab fa-whatsapp"></i> WhatsApp Business API</h3>
                                <div class="card-tools">
                                    <span class="badge badge-<?php echo ($configs['whatsapp_api_status'] ?? 'nao_configurado') === 'conectado' ? 'success' : 'warning'; ?>">
                                        <?php echo html_escape($configs['whatsapp_api_status'] ?? 'nao_configurado'); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="whatsapp_api_ativa" name="whatsapp_api_ativa" value="1"
                                               <?php echo ($configs['whatsapp_api_ativa'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="whatsapp_api_ativa">
                                            <strong>Ativar WhatsApp Business API</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Credenciais obtidas no <a href="https://developers.facebook.com/" target="_blank">Meta Developers</a></small>
                                </div>

                                <div id="whatsapp-fields" style="display: <?php echo ($configs['whatsapp_api_ativa'] ?? '0') === '1' ? 'block' : 'none'; ?>;">
                                    <div class="form-group">
                                        <label for="whatsapp_api_phone_id">
                                            <i class="fas fa-phone"></i> Phone Number ID
                                        </label>
                                        <input type="text" class="form-control" id="whatsapp_api_phone_id" name="whatsapp_api_phone_id"
                                               value="<?php echo html_escape($configs['whatsapp_api_phone_id'] ?? ''); ?>"
                                               placeholder="Ex: 123456789012345">
                                    </div>

                                    <div class="form-group">
                                        <label for="whatsapp_api_token">
                                            <i class="fas fa-key"></i> Token de Acesso (System User Token)
                                        </label>
                                        <input type="password" class="form-control" id="whatsapp_api_token" name="whatsapp_api_token"
                                               value="<?php echo html_escape($configs['whatsapp_api_token'] ?? ''); ?>"
                                               placeholder="Token permanente do System User">
                                    </div>

                                    <div class="form-group">
                                        <label for="whatsapp_api_business_id">
                                            <i class="fas fa-building"></i> WABA ID (Business Account ID)
                                        </label>
                                        <input type="text" class="form-control" id="whatsapp_api_business_id" name="whatsapp_api_business_id"
                                               value="<?php echo html_escape($configs['whatsapp_api_business_id'] ?? ''); ?>"
                                               placeholder="Ex: 123456789012345">
                                    </div>

                                    <button type="button" class="btn btn-success btn-sm" id="btn-testar-whatsapp">
                                        <i class="fab fa-whatsapp"></i> Testar Conexao WhatsApp
                                    </button>
                                    <span id="whatsapp-test-result" class="ml-2"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notificacoes -->
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bell"></i> Notificacoes por Evento</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5><i class="fas fa-user"></i> Cliente</h5>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_pedido_criado_cliente" name="notif_pedido_criado_cliente" value="1"
                                               <?php echo ($configs['notif_pedido_criado_cliente'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_pedido_criado_cliente">Pedido criado</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_status_confirmado" name="notif_status_confirmado" value="1"
                                               <?php echo ($configs['notif_status_confirmado'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_status_confirmado">Confirmado</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_status_preparando" name="notif_status_preparando" value="1"
                                               <?php echo ($configs['notif_status_preparando'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_status_preparando">Em preparo</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_status_pronto" name="notif_status_pronto" value="1"
                                               <?php echo ($configs['notif_status_pronto'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_status_pronto">Pronto p/ coleta</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_status_saiu_entrega" name="notif_status_saiu_entrega" value="1"
                                               <?php echo ($configs['notif_status_saiu_entrega'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_status_saiu_entrega">Saiu p/ entrega</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_status_entregue" name="notif_status_entregue" value="1"
                                               <?php echo ($configs['notif_status_entregue'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_status_entregue">Entregue</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_status_cancelado" name="notif_status_cancelado" value="1"
                                               <?php echo ($configs['notif_status_cancelado'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_status_cancelado">Cancelado</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h5><i class="fas fa-motorcycle"></i> Entregador</h5>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_pedido_criado_motoboy" name="notif_pedido_criado_motoboy" value="1"
                                               <?php echo ($configs['notif_pedido_criado_motoboy'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_pedido_criado_motoboy">Novo pedido disponivel</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_motoboy_atribuido" name="notif_motoboy_atribuido" value="1"
                                               <?php echo ($configs['notif_motoboy_atribuido'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_motoboy_atribuido">Atribuido a entrega</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_cupom_fiscal" name="notif_cupom_fiscal" value="1"
                                               <?php echo ($configs['notif_cupom_fiscal'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_cupom_fiscal">Enviar cupom fiscal (PDF)</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h5><i class="fas fa-user-shield"></i> Administrador</h5>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="notif_resumo_diario" name="notif_resumo_diario" value="1"
                                               <?php echo ($configs['notif_resumo_diario'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="notif_resumo_diario">Resumo diario</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="notif_resumo_horario">Horario do resumo</label>
                                    <input type="time" class="form-control" id="notif_resumo_horario" name="notif_resumo_horario"
                                           value="<?php echo html_escape($configs['notif_resumo_horario'] ?? '23:00'); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="notif_admin_telefone">Telefone do admin</label>
                                    <input type="text" class="form-control" id="notif_admin_telefone" name="notif_admin_telefone"
                                           value="<?php echo html_escape($configs['notif_admin_telefone'] ?? ''); ?>"
                                           placeholder="(11) 99999-1234">
                                    <small class="text-muted">Para receber resumo e alertas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Templates WhatsApp -->
                <div class="card card-outline card-dark">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-alt"></i> Nomes dos Templates WhatsApp</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool btn-sm" id="btn-verificar-templates" title="Verificar status dos templates na Meta">
                                <i class="fas fa-sync-alt"></i> Verificar Status
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Estes nomes devem corresponder exatamente aos templates cadastrados no
                            <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank">Meta Business Suite</a>.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Cliente</h6>
                                <?php
                                $templates_cliente = array(
                                    'wpp_template_pedido_criado'     => 'Pedido recebido',
                                    'wpp_template_pedido_confirmado' => 'Pedido confirmado',
                                    'wpp_template_pedido_preparando' => 'Em preparo',
                                    'wpp_template_pedido_pronto'     => 'Pronto p/ coleta',
                                    'wpp_template_pedido_saiu'       => 'Saiu p/ entrega',
                                    'wpp_template_pedido_entregue'   => 'Entregue',
                                    'wpp_template_pedido_cancelado'  => 'Cancelado',
                                );
                                foreach ($templates_cliente as $key => $label): ?>
                                    <div class="form-group">
                                        <label for="<?php echo $key; ?>"><?php echo html_escape($label); ?></label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control template-input" id="<?php echo $key; ?>" name="<?php echo $key; ?>"
                                                   value="<?php echo html_escape($configs[$key] ?? ''); ?>"
                                                   placeholder="nome_do_template">
                                            <div class="input-group-append">
                                                <span class="input-group-text template-status" data-key="<?php echo $key; ?>">
                                                    <i class="fas fa-question-circle text-muted"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary">Entregador</h6>
                                <?php
                                $templates_motoboy = array(
                                    'wpp_template_motoboy_novo'      => 'Novo pedido p/ motoboys',
                                    'wpp_template_motoboy_atribuido' => 'Entrega atribuida',
                                );
                                foreach ($templates_motoboy as $key => $label): ?>
                                    <div class="form-group">
                                        <label for="<?php echo $key; ?>"><?php echo html_escape($label); ?></label>
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control template-input" id="<?php echo $key; ?>" name="<?php echo $key; ?>"
                                                   value="<?php echo html_escape($configs[$key] ?? ''); ?>"
                                                   placeholder="nome_do_template">
                                            <div class="input-group-append">
                                                <span class="input-group-text template-status" data-key="<?php echo $key; ?>">
                                                    <i class="fas fa-question-circle text-muted"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <h6 class="text-primary mt-4">Outros</h6>
                                <div class="form-group">
                                    <label for="wpp_template_cupom_fiscal">Cupom fiscal (PDF)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control template-input" id="wpp_template_cupom_fiscal" name="wpp_template_cupom_fiscal"
                                               value="<?php echo html_escape($configs['wpp_template_cupom_fiscal'] ?? ''); ?>"
                                               placeholder="nome_do_template">
                                        <div class="input-group-append">
                                            <span class="input-group-text template-status" data-key="wpp_template_cupom_fiscal">
                                                <i class="fas fa-question-circle text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="templates-check-result" class="mt-2"></div>
                    </div>
                </div>

                <!-- Botoes -->
                <div class="row mb-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Salvar Configuracoes
                        </button>
                        <a href="<?php echo base_url('delivery/config'); ?>" class="btn btn-default btn-lg">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
var base_url = <?php echo json_encode(base_url()); ?>;
var csrf_name = <?php echo json_encode($this->security->get_csrf_token_name()); ?>;
var csrf_hash = <?php echo json_encode($this->security->get_csrf_hash()); ?>;

// Atualiza o token CSRF no hidden input do form e na variavel global
function updateCsrf(newHash) {
    if (newHash) {
        csrf_hash = newHash;
        var hiddenInput = document.querySelector('input[name="' + csrf_name + '"]');
        if (hiddenInput) hiddenInput.value = newHash;
    }
}

document.addEventListener('DOMContentLoaded', function() {

    // Toggle n8n fields
    var n8nAtivo = document.getElementById('n8n_ativo');
    var n8nFields = document.getElementById('n8n-fields');
    n8nAtivo.addEventListener('change', function() {
        n8nFields.style.display = this.checked ? 'block' : 'none';
    });

    // Toggle WhatsApp fields
    var wppAtiva = document.getElementById('whatsapp_api_ativa');
    var wppFields = document.getElementById('whatsapp-fields');
    wppAtiva.addEventListener('change', function() {
        wppFields.style.display = this.checked ? 'block' : 'none';
    });

    // Gerar chave secreta aleatoria
    document.getElementById('btn-gerar-secret').addEventListener('click', function() {
        var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var result = '';
        for (var i = 0; i < 32; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('n8n_webhook_secret').value = result;
    });

    // Testar conexao n8n
    document.getElementById('btn-testar-n8n').addEventListener('click', function() {
        var btn = this;
        var result = document.getElementById('n8n-test-result');
        btn.disabled = true;
        result.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testando...';

        var data = {};
        data[csrf_name] = csrf_hash;

        $.ajax({
            url: base_url + 'delivery/config/test_n8n',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                updateCsrf(response.csrf_token);
                if (response.success) {
                    result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> ' + response.message + '</span>';
                } else {
                    result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> ' + response.message + '</span>';
                }
                btn.disabled = false;
            },
            error: function() {
                result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Erro de comunicacao</span>';
                btn.disabled = false;
            }
        });
    });

    // Testar conexao WhatsApp
    document.getElementById('btn-testar-whatsapp').addEventListener('click', function() {
        var btn = this;
        var result = document.getElementById('whatsapp-test-result');
        btn.disabled = true;
        result.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testando...';

        var data = {};
        data[csrf_name] = csrf_hash;

        $.ajax({
            url: base_url + 'delivery/config/test_whatsapp',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                updateCsrf(response.csrf_token);
                if (response.success) {
                    result.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> ' + response.message + '</span>';
                } else {
                    result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> ' + response.message + '</span>';
                }
                btn.disabled = false;
            },
            error: function() {
                result.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Erro de comunicacao</span>';
                btn.disabled = false;
            }
        });
    });

    // Verificar status dos templates
    document.getElementById('btn-verificar-templates').addEventListener('click', function() {
        var btn = this;
        var resultDiv = document.getElementById('templates-check-result');
        btn.disabled = true;
        resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando templates na Meta...';

        var data = {};
        data[csrf_name] = csrf_hash;

        $.ajax({
            url: base_url + 'delivery/config/check_templates',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                updateCsrf(response.csrf_token);
                if (response.success && response.templates) {
                    // Atualizar indicadores de status
                    for (var key in response.templates) {
                        var status = response.templates[key];
                        var indicator = document.querySelector('.template-status[data-key="' + key + '"]');
                        if (indicator) {
                            if (status === 'APPROVED') {
                                indicator.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                            } else if (status === 'PENDING') {
                                indicator.innerHTML = '<i class="fas fa-clock text-warning"></i>';
                            } else if (status === 'REJECTED') {
                                indicator.innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
                            } else {
                                indicator.innerHTML = '<i class="fas fa-question-circle text-muted" title="' + status + '"></i>';
                            }
                        }
                    }
                    resultDiv.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Verificacao concluida</span>';
                } else {
                    resultDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> ' + (response.message || 'Erro ao verificar') + '</span>';
                }
                btn.disabled = false;
            },
            error: function() {
                resultDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Erro de comunicacao</span>';
                btn.disabled = false;
            }
        });
    });
});
</script>
