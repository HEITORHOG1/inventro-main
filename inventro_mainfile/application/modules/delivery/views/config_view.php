<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-cog"></i> Configurações do Cardápio</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo base_url('delivery/orders'); ?>">Delivery</a></li>
                        <li class="breadcrumb-item active">Configurações</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if ($this->session->flashdata('msg')): ?>
                <div class="alert alert-<?php echo $this->session->flashdata('msg_type') == 'success' ? 'success' : 'danger'; ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?php echo $this->session->flashdata('msg'); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo base_url('delivery/config/save'); ?>" method="POST">
                <div class="row">
                    <!-- Configurações de Entrega -->
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-motorcycle"></i> Entrega</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="taxa_entrega">
                                        <i class="fas fa-dollar-sign"></i> Taxa de Entrega (R$)
                                    </label>
                                    <input type="number" step="0.01" class="form-control" 
                                           id="taxa_entrega" name="taxa_entrega" 
                                           value="<?php echo html_escape($configs['taxa_entrega']); ?>"
                                           placeholder="0.00">
                                    <small class="text-muted">Deixe 0 para entrega grátis</small>
                                </div>

                                <div class="form-group">
                                    <label for="pedido_minimo">
                                        <i class="fas fa-shopping-cart"></i> Pedido Mínimo (R$)
                                    </label>
                                    <input type="number" step="0.01" class="form-control" 
                                           id="pedido_minimo" name="pedido_minimo" 
                                           value="<?php echo html_escape($configs['pedido_minimo']); ?>"
                                           placeholder="0.00">
                                    <small class="text-muted">Deixe 0 para não ter mínimo</small>
                                </div>

                                <div class="form-group">
                                    <label for="tempo_medio_entrega">
                                        <i class="fas fa-clock"></i> Tempo Médio de Entrega (minutos)
                                    </label>
                                    <input type="number" class="form-control" 
                                           id="tempo_medio_entrega" name="tempo_medio_entrega" 
                                           value="<?php echo html_escape($configs['tempo_medio_entrega']); ?>"
                                           placeholder="45">
                                </div>
                            </div>
                        </div>

                        <!-- Horário de Funcionamento -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-clock"></i> Horário de Funcionamento</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="horario_abertura">Abertura</label>
                                            <input type="time" class="form-control" 
                                                   id="horario_abertura" name="horario_abertura" 
                                                   value="<?php echo html_escape($configs['horario_abertura']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="horario_fechamento">Fechamento</label>
                                            <input type="time" class="form-control" 
                                                   id="horario_fechamento" name="horario_fechamento" 
                                                   value="<?php echo html_escape($configs['horario_fechamento']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formas de Pagamento -->
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-credit-card"></i> Formas de Pagamento</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="aceita_dinheiro" name="aceita_dinheiro" value="1"
                                               <?php echo $configs['aceita_dinheiro'] == '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="aceita_dinheiro">
                                            <i class="fas fa-money-bill-wave"></i> Aceitar Dinheiro
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="aceita_cartao" name="aceita_cartao" value="1"
                                               <?php echo $configs['aceita_cartao'] == '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="aceita_cartao">
                                            <i class="fas fa-credit-card"></i> Aceitar Cartão na Entrega
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="aceita_pix" name="aceita_pix" value="1"
                                               <?php echo $configs['aceita_pix'] == '1' ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="aceita_pix">
                                            <i class="fas fa-qrcode"></i> Aceitar Pix
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group" id="pix_chave_group">
                                    <label for="pix_chave">Chave Pix</label>
                                    <input type="text" class="form-control" 
                                           id="pix_chave" name="pix_chave" 
                                           value="<?php echo html_escape($configs['pix_chave']); ?>"
                                           placeholder="CPF, CNPJ, Email ou Chave aleatória">
                                </div>
                            </div>
                        </div>

                        <!-- Mensagem -->
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-comment"></i> Mensagem de Confirmação</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <textarea class="form-control" id="mensagem_confirmacao" 
                                              name="mensagem_confirmacao" rows="3"
                                              placeholder="Mensagem exibida após o pedido"><?php echo html_escape($configs['mensagem_confirmacao']); ?></textarea>
                                    <small class="text-muted">Exibida na página de confirmação do pedido</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-eye"></i> Preview</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="info-box bg-gradient-primary">
                                            <span class="info-box-icon"><i class="fas fa-motorcycle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Taxa de Entrega</span>
                                                <span class="info-box-number" id="preview_taxa">
                                                    <?php echo $configs['taxa_entrega'] == 0 ? 'GRÁTIS' : 'R$ ' . number_format($configs['taxa_entrega'], 2, ',', '.'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="info-box bg-gradient-success">
                                            <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Pedido Mínimo</span>
                                                <span class="info-box-number" id="preview_minimo">
                                                    <?php echo $configs['pedido_minimo'] == 0 ? 'Sem mínimo' : 'R$ ' . number_format($configs['pedido_minimo'], 2, ',', '.'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="info-box bg-gradient-info">
                                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Tempo de Entrega</span>
                                                <span class="info-box-number" id="preview_tempo">
                                                    <?php echo $configs['tempo_medio_entrega']; ?> min
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                        <a href="<?php echo base_url('cardapio'); ?>" target="_blank" class="btn btn-info btn-lg">
                            <i class="fas fa-external-link-alt"></i> Ver Cardápio
                        </a>
                        <a href="<?php echo base_url('delivery/config/n8n'); ?>" class="btn btn-success btn-lg">
                            <i class="fab fa-whatsapp"></i> WhatsApp & Automacao
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Pix key field
    const aceitaPix = document.getElementById('aceita_pix');
    const pixChaveGroup = document.getElementById('pix_chave_group');
    
    function togglePixField() {
        pixChaveGroup.style.display = aceitaPix.checked ? 'block' : 'none';
    }
    
    aceitaPix.addEventListener('change', togglePixField);
    togglePixField();

    // Preview atualização em tempo real
    document.getElementById('taxa_entrega').addEventListener('input', function() {
        const val = parseFloat(this.value) || 0;
        document.getElementById('preview_taxa').textContent = val == 0 ? 'GRÁTIS' : 'R$ ' + val.toFixed(2).replace('.', ',');
    });

    document.getElementById('pedido_minimo').addEventListener('input', function() {
        const val = parseFloat(this.value) || 0;
        document.getElementById('preview_minimo').textContent = val == 0 ? 'Sem mínimo' : 'R$ ' + val.toFixed(2).replace('.', ',');
    });

    document.getElementById('tempo_medio_entrega').addEventListener('input', function() {
        const val = parseInt(this.value) || 0;
        document.getElementById('preview_tempo').textContent = val + ' min';
    });
});
</script>
