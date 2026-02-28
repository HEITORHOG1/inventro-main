<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-<?php echo isset($zone) ? 'edit' : 'plus'; ?>"></i>
            <?php echo isset($zone) ? 'Editar Zona' : 'Nova Zona de Entrega'; ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Painel</a></li>
            <li><a href="<?php echo base_url('delivery/zones'); ?>">Zonas de Entrega</a></li>
            <li class="active"><?php echo isset($zone) ? 'Editar' : 'Nova'; ?></li>
        </ol>
    </section>

    <section class="content">
        <!-- Mensagens -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-warning"></i> <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-map-marker"></i> Informações da Zona
                        </h3>
                    </div>
                    <form action="<?php echo base_url('delivery/zones/save'); ?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo isset($zone) ? $zone->id : ''; ?>">
                        
                        <div class="box-body">
                            <div class="form-group">
                                <label for="nome">
                                    <i class="fa fa-tag"></i> Nome do Bairro/Região *
                                </label>
                                <input type="text" 
                                       class="form-control input-lg" 
                                       id="nome" 
                                       name="nome" 
                                       placeholder="Ex: Centro, Vila Maria, Pinheiros..."
                                       value="<?php echo isset($zone) ? htmlspecialchars($zone->nome) : ''; ?>"
                                       required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="taxa">
                                            <i class="fa fa-money"></i> Taxa de Entrega (R$) *
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon">R$</span>
                                            <input type="number" 
                                                   class="form-control input-lg" 
                                                   id="taxa" 
                                                   name="taxa" 
                                                   step="0.01" 
                                                   min="0"
                                                   placeholder="0.00"
                                                   value="<?php echo isset($zone) ? number_format($zone->taxa, 2, '.', '') : '0.00'; ?>"
                                                   required>
                                        </div>
                                        <p class="help-block">
                                            <i class="fa fa-info-circle text-info"></i> 
                                            Digite <strong>0.00</strong> para entrega GRÁTIS
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>
                                            <i class="fa fa-clock-o"></i> Tempo Estimado (minutos)
                                        </label>
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Min</span>
                                                    <input type="number" 
                                                           class="form-control" 
                                                           name="tempo_min" 
                                                           min="5"
                                                           placeholder="20"
                                                           value="<?php echo isset($zone) ? $zone->tempo_min : '20'; ?>">
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Máx</span>
                                                    <input type="number" 
                                                           class="form-control" 
                                                           name="tempo_max" 
                                                           min="10"
                                                           placeholder="40"
                                                           value="<?php echo isset($zone) ? $zone->tempo_max : '40'; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" 
                                           name="ativo" 
                                           value="1" 
                                           <?php echo (!isset($zone) || $zone->ativo) ? 'checked' : ''; ?>>
                                    <i class="fa fa-check-circle text-success"></i> 
                                    Zona Ativa (disponível no cardápio)
                                </label>
                            </div>
                        </div>

                        <div class="box-footer">
                            <a href="<?php echo base_url('delivery/zones'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-save"></i> Salvar Zona
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Preview -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-eye"></i> Preview</h3>
                    </div>
                    <div class="box-body">
                        <div class="callout callout-info" id="preview">
                            <h4><i class="fa fa-map-marker"></i> <span id="previewNome">Nome do Bairro</span></h4>
                            <p>
                                <strong>Taxa:</strong> 
                                <span id="previewTaxa" class="label label-success">GRÁTIS</span>
                            </p>
                            <p>
                                <i class="fa fa-clock-o"></i>
                                <span id="previewTempo">20 - 40</span> minutos
                            </p>
                        </div>
                        <p class="text-muted">
                            <small>Assim ficará no cardápio para o cliente.</small>
                        </p>
                    </div>
                </div>

                <!-- Dicas -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Dicas</h3>
                    </div>
                    <div class="box-body">
                        <ul class="list-unstyled">
                            <li><i class="fa fa-check text-success"></i> Use nomes claros para os bairros</li>
                            <li><i class="fa fa-check text-success"></i> Taxa GRÁTIS atrai mais clientes</li>
                            <li><i class="fa fa-check text-success"></i> Seja realista no tempo estimado</li>
                            <li><i class="fa fa-check text-success"></i> Desative zonas fora da área de entrega</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Preview em tempo real
    function updatePreview() {
        var nome = $('#nome').val() || 'Nome do Bairro';
        var taxa = parseFloat($('#taxa').val()) || 0;
        var tempoMin = $('input[name="tempo_min"]').val() || 20;
        var tempoMax = $('input[name="tempo_max"]').val() || 40;

        $('#previewNome').text(nome);
        
        if (taxa == 0) {
            $('#previewTaxa').removeClass('label-info').addClass('label-success').html('<i class="fa fa-gift"></i> GRÁTIS');
        } else {
            $('#previewTaxa').removeClass('label-success').addClass('label-info').text('R$ ' + taxa.toFixed(2).replace('.', ','));
        }
        
        $('#previewTempo').text(tempoMin + ' - ' + tempoMax);
    }

    $('#nome, #taxa, input[name="tempo_min"], input[name="tempo_max"]').on('input', updatePreview);
    
    // Inicializar preview
    updatePreview();
});
</script>
