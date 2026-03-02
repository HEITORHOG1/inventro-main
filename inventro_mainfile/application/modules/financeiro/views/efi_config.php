<?php
$c = $configs; // shortcut
$is_ativo = ($c['efipay_ativo'] === '1');
$has_credentials = !empty($c['efipay_client_id']) && !empty($c['efipay_client_secret']);
$has_certificate = !empty($c['efipay_certificate_path']);
$is_connected = ($c['efipay_status'] === 'conectado');
$has_webhook = !empty($c['efipay_webhook_url']);
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-credit-card"></i> <?php echo makeString(['efi_pay_config']); ?>
        </h3>
        <div class="card-tools">
            <a href="<?php echo base_url('financeiro/contas_receber/lista'); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> <?php echo makeString(['back']); ?>
            </a>
        </div>
    </div>

    <div class="card-body">
        <?php
        $message = $this->session->flashdata('message');
        $exception = $this->session->flashdata('exception');
        if ($message) echo '<div class="alert alert-success">'.html_escape($message).'</div>';
        if ($exception) echo '<div class="alert alert-danger">'.html_escape($exception).'</div>';
        ?>

        <!-- Wizard de Progresso -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-center px-2">
                        <span class="badge badge-<?php echo $is_ativo ? 'success' : 'secondary'; ?> p-2">1</span>
                        <br><small>Ativado</small>
                    </div>
                    <div class="flex-fill border-top mx-1" style="height:1px;margin-top:14px;"></div>
                    <div class="text-center px-2">
                        <span class="badge badge-<?php echo $has_credentials ? 'success' : 'secondary'; ?> p-2">2</span>
                        <br><small>Credenciais</small>
                    </div>
                    <div class="flex-fill border-top mx-1" style="height:1px;margin-top:14px;"></div>
                    <div class="text-center px-2">
                        <span class="badge badge-<?php echo $has_certificate ? 'success' : 'secondary'; ?> p-2">3</span>
                        <br><small>Certificado</small>
                    </div>
                    <div class="flex-fill border-top mx-1" style="height:1px;margin-top:14px;"></div>
                    <div class="text-center px-2">
                        <span class="badge badge-<?php echo $is_connected ? 'success' : 'secondary'; ?> p-2">4</span>
                        <br><small><?php echo makeString(['conexao_ok']); ?></small>
                    </div>
                    <div class="flex-fill border-top mx-1" style="height:1px;margin-top:14px;"></div>
                    <div class="text-center px-2">
                        <span class="badge badge-<?php echo $has_webhook ? 'success' : 'secondary'; ?> p-2">5</span>
                        <br><small>Webhook</small>
                    </div>
                </div>
            </div>
        </div>

        <?php echo form_open('financeiro/efi_config/save', array('id' => 'formEfiConfig')); ?>

        <!-- Card: Geral -->
        <div class="card card-outline card-info mb-3">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-cog"></i> Geral</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ativar Efi Pay</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="efipay_ativo" name="efipay_ativo" value="1" <?php echo $c['efipay_ativo'] === '1' ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="efipay_ativo">
                                    <?php echo $c['efipay_ativo'] === '1' ? '<span class="text-success">Ativo</span>' : '<span class="text-muted">Inativo</span>'; ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo makeString(['efi_pay_sandbox']); ?></label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="efipay_sandbox" name="efipay_sandbox" value="1" <?php echo $c['efipay_sandbox'] === '1' ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="efipay_sandbox">
                                    <?php if ($c['efipay_sandbox'] === '1'): ?>
                                        <span class="badge badge-warning">Sandbox (Teste)</span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?php echo makeString(['efi_pay_producao']); ?></span>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label><?php echo makeString(['status']); ?></label><br>
                            <?php
                            $status = $c['efipay_status'];
                            $status_class = 'secondary';
                            if ($status === 'conectado') $status_class = 'success';
                            elseif ($status === 'erro') $status_class = 'danger';
                            ?>
                            <span class="badge badge-<?php echo $status_class; ?> p-2"><?php echo html_escape(ucfirst(str_replace('_', ' ', $status))); ?></span>
                            <?php if (!empty($c['efipay_ultimo_teste'])): ?>
                                <br><small class="text-muted">Ultimo teste: <?php echo html_escape($c['efipay_ultimo_teste']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Cartao de Credito</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="efipay_cartao_ativo" name="efipay_cartao_ativo" value="1" <?php echo $c['efipay_cartao_ativo'] === '1' ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="efipay_cartao_ativo">Ativar cobranças por cartao</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Credenciais -->
        <div class="card card-outline card-warning mb-3">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-key"></i> <?php echo makeString(['efi_credenciais']); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="efipay_client_id">Client ID</label>
                            <input type="text" class="form-control" id="efipay_client_id" name="efipay_client_id"
                                   value="<?php echo html_escape($c['efipay_client_id']); ?>"
                                   placeholder="Client_Id da sua aplicacao Efi Pay">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="efipay_client_secret">Client Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="efipay_client_secret" name="efipay_client_secret"
                                       placeholder="<?php echo !empty($c['efipay_client_secret']) ? '●●●●●●●● (ja configurado)' : 'Client_Secret'; ?>">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="btnToggleSecret">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Deixe em branco para manter o valor atual.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo makeString(['certificado_efi']); ?> (.pem / .p12)</label>
                            <div class="custom-file" id="certUploadArea">
                                <input type="file" class="custom-file-input" id="certificate" name="certificate" accept=".pem,.p12">
                                <label class="custom-file-label" for="certificate" id="certLabel">Selecionar arquivo...</label>
                            </div>
                            <?php if (!empty($c['efipay_certificate_path'])): ?>
                                <small class="text-success mt-1 d-block">
                                    <i class="fas fa-check-circle"></i> Certificado configurado: <?php echo html_escape(basename($c['efipay_certificate_path'])); ?>
                                </small>
                            <?php endif; ?>
                            <div id="certStatus" class="mt-1"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="efipay_account_id">Account ID (para tokenizacao cartao)</label>
                            <input type="text" class="form-control" id="efipay_account_id" name="efipay_account_id"
                                   value="<?php echo html_escape($c['efipay_account_id']); ?>"
                                   placeholder="Identificador da conta (API > Introducao)">
                            <small class="text-muted">Necessario apenas para cobranças de cartao de credito.</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-info" id="btnTestConnection">
                            <i class="fas fa-plug"></i> <?php echo makeString(['testar_conexao_efi']); ?>
                        </button>
                        <span id="testResult" class="ml-2"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: PIX -->
        <div class="card card-outline card-success mb-3">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-qrcode"></i> PIX</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="efipay_pix_chave"><?php echo makeString(['chave_pix']); ?></label>
                            <input type="text" class="form-control" id="efipay_pix_chave" name="efipay_pix_chave"
                                   value="<?php echo html_escape($c['efipay_pix_chave']); ?>"
                                   placeholder="Sua chave PIX cadastrada na Efi">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="efipay_pix_chave_tipo"><?php echo makeString(['tipo_chave_pix']); ?></label>
                            <select class="form-control" id="efipay_pix_chave_tipo" name="efipay_pix_chave_tipo">
                                <option value="">-- Selecione --</option>
                                <option value="cpf" <?php echo $c['efipay_pix_chave_tipo'] === 'cpf' ? 'selected' : ''; ?>>CPF</option>
                                <option value="cnpj" <?php echo $c['efipay_pix_chave_tipo'] === 'cnpj' ? 'selected' : ''; ?>>CNPJ</option>
                                <option value="email" <?php echo $c['efipay_pix_chave_tipo'] === 'email' ? 'selected' : ''; ?>>E-mail</option>
                                <option value="telefone" <?php echo $c['efipay_pix_chave_tipo'] === 'telefone' ? 'selected' : ''; ?>>Telefone</option>
                                <option value="aleatoria" <?php echo $c['efipay_pix_chave_tipo'] === 'aleatoria' ? 'selected' : ''; ?>>Aleatoria</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="efipay_expiracao_padrao"><?php echo makeString(['expiracao_pix']); ?> (segundos)</label>
                            <input type="number" class="form-control" id="efipay_expiracao_padrao" name="efipay_expiracao_padrao"
                                   value="<?php echo html_escape($c['efipay_expiracao_padrao']); ?>"
                                   min="300" max="86400" placeholder="3600">
                            <small class="text-muted">Padrao: 3600 (1 hora). Min: 300 (5 min).</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Webhook -->
        <div class="card card-outline card-purple mb-3">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-satellite-dish"></i> <?php echo makeString(['efi_webhook']); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="efipay_webhook_url">Webhook URL (HTTPS publico)</label>
                            <input type="url" class="form-control" id="efipay_webhook_url" name="efipay_webhook_url"
                                   value="<?php echo html_escape($c['efipay_webhook_url']); ?>"
                                   placeholder="<?php echo html_escape($webhook_url_sugestao); ?>">
                            <small class="text-muted">
                                A Efi vai enviar POST para esta URL quando um PIX for pago.
                                <?php if (!empty($webhook_url_sugestao)): ?>
                                    Sugestao: <code><?php echo html_escape($webhook_url_sugestao); ?></code>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Pular mTLS</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="efipay_skip_mtls" name="efipay_skip_mtls" value="1" <?php echo $c['efipay_skip_mtls'] === '1' ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="efipay_skip_mtls">Usar IP whitelist ao inves de mTLS</label>
                            </div>
                            <small class="text-muted">Recomendado para hosts compartilhados.</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-purple" id="btnRegisterWebhook">
                                <i class="fas fa-satellite-dish"></i> <?php echo makeString(['registrar_webhook_efi']); ?>
                            </button>
                            <span id="webhookResult" class="ml-2 d-block mt-1"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botao Salvar -->
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> <?php echo makeString(['save']); ?>
                </button>
            </div>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="csrf_name" value="<?php echo $this->security->get_csrf_token_name(); ?>">
<input type="hidden" id="csrf_hash" value="<?php echo $this->security->get_csrf_hash(); ?>">

<script>
$(document).ready(function() {
    var base_url = $('#base_url').val();
    var csrf_name = $('#csrf_name').val();
    var csrf_hash = $('#csrf_hash').val();

    function updateCsrf(token) {
        if (token) {
            csrf_hash = token;
            $('#csrf_hash').val(token);
            $('input[name="' + csrf_name + '"]').val(token);
        }
    }

    // Toggle mostrar/ocultar secret
    $('#btnToggleSecret').click(function() {
        var input = $('#efipay_client_secret');
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Upload de certificado
    $('#certificate').change(function() {
        var fileName = $(this).val().split('\\').pop();
        $('#certLabel').text(fileName || 'Selecionar arquivo...');

        if (!fileName) return;

        var formData = new FormData();
        formData.append('certificate', this.files[0]);
        formData.append(csrf_name, csrf_hash);

        $('#certStatus').html('<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Enviando...</span>');

        $.ajax({
            url: base_url + 'financeiro/efi_config/upload_certificate',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(r) {
                updateCsrf(r.csrf_token);
                if (r.success) {
                    $('#certStatus').html('<span class="text-success"><i class="fas fa-check-circle"></i> ' + r.message + '</span>');
                } else {
                    $('#certStatus').html('<span class="text-danger"><i class="fas fa-times-circle"></i> ' + r.message + '</span>');
                }
            },
            error: function() {
                $('#certStatus').html('<span class="text-danger"><i class="fas fa-times-circle"></i> Erro de conexao</span>');
            }
        });
    });

    // Testar conexao
    $('#btnTestConnection').click(function() {
        var btn = $(this);
        btn.prop('disabled', true);
        $('#testResult').html('<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Testando...</span>');

        var data = {};
        data[csrf_name] = csrf_hash;

        $.ajax({
            url: base_url + 'financeiro/efi_config/test_connection',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(r) {
                updateCsrf(r.csrf_token);
                btn.prop('disabled', false);
                if (r.success) {
                    $('#testResult').html('<span class="text-success"><i class="fas fa-check-circle"></i> ' + r.message + '</span>');
                } else {
                    $('#testResult').html('<span class="text-danger"><i class="fas fa-times-circle"></i> ' + r.message + '</span>');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                $('#testResult').html('<span class="text-danger"><i class="fas fa-times-circle"></i> Erro de conexao</span>');
            }
        });
    });

    // Registrar webhook
    $('#btnRegisterWebhook').click(function() {
        var btn = $(this);
        btn.prop('disabled', true);
        $('#webhookResult').html('<span class="text-info"><i class="fas fa-spinner fa-spin"></i> Registrando...</span>');

        var data = {};
        data[csrf_name] = csrf_hash;

        $.ajax({
            url: base_url + 'financeiro/efi_config/register_webhook',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(r) {
                updateCsrf(r.csrf_token);
                btn.prop('disabled', false);
                if (r.success) {
                    $('#webhookResult').html('<span class="text-success"><i class="fas fa-check-circle"></i> ' + r.message + '</span>');
                } else {
                    $('#webhookResult').html('<span class="text-danger"><i class="fas fa-times-circle"></i> ' + r.message + '</span>');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                $('#webhookResult').html('<span class="text-danger"><i class="fas fa-times-circle"></i> Erro de conexao</span>');
            }
        });
    });
});
</script>
