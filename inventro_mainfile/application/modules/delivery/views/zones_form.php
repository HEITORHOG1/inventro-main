<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-<?php echo isset($zone) ? 'edit' : 'plus'; ?>"></i>
            <?php echo html_escape($title); ?>
        </h3>
        <div class="card-tools">
            <a href="<?php echo base_url('delivery/zones/index'); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> <?php echo makeString(['back']); ?>
            </a>
        </div>
    </div>

    <?php echo form_open('delivery/zones/save', array('class' => 'form-horizontal', 'id' => 'formZona')); ?>

    <div class="card-body">
        <?php
        $exception = $this->session->flashdata('exception');
        if ($exception) echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $exception . '</div>';
        ?>

        <input type="hidden" name="id" value="<?php echo isset($zone->id) ? (int)$zone->id : ''; ?>">

        <div class="row">
            <!-- Formulário -->
            <div class="col-md-8">
                <div class="form-group">
                    <label for="nome">
                        <i class="fas fa-tag"></i> <?php echo makeString(['nome']); ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control"
                           id="nome"
                           name="nome"
                           placeholder="Ex: Centro, Vila Maria, Pinheiros..."
                           value="<?php echo isset($zone->nome) ? html_escape($zone->nome) : ''; ?>"
                           maxlength="100"
                           required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="taxa">
                                <i class="fas fa-dollar-sign"></i> <?php echo makeString(['delivery_fee']); ?> (R$) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">R$</span>
                                </div>
                                <input type="number"
                                       class="form-control"
                                       id="taxa"
                                       name="taxa"
                                       step="0.01"
                                       min="0"
                                       placeholder="0.00"
                                       value="<?php echo isset($zone->taxa) ? number_format($zone->taxa, 2, '.', '') : '0.00'; ?>"
                                       required>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle text-info"></i> Digite <strong>0.00</strong> para entrega GRÁTIS
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-clock"></i> Tempo Estimado (minutos)
                            </label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Mín</span>
                                        </div>
                                        <input type="number"
                                               class="form-control"
                                               name="tempo_min"
                                               min="5"
                                               placeholder="20"
                                               value="<?php echo isset($zone->tempo_min) ? (int)$zone->tempo_min : '20'; ?>">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Máx</span>
                                        </div>
                                        <input type="number"
                                               class="form-control"
                                               name="tempo_max"
                                               min="10"
                                               placeholder="40"
                                               value="<?php echo isset($zone->tempo_max) ? (int)$zone->tempo_max : '40'; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="ativo"
                               name="ativo"
                               value="1"
                               <?php echo (!isset($zone) || $zone->ativo) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="ativo">
                            Zona Ativa (disponível no cardápio)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="col-md-4">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> Preview</h3>
                    </div>
                    <div class="card-body">
                        <div class="callout callout-info" id="preview">
                            <h5><i class="fas fa-map-marker-alt"></i> <span id="previewNome">Nome do Bairro</span></h5>
                            <p>
                                <strong>Taxa:</strong>
                                <span id="previewTaxa" class="badge badge-success">GRÁTIS</span>
                            </p>
                            <p>
                                <i class="fas fa-clock"></i>
                                <span id="previewTempo">20 - 40</span> minutos
                            </p>
                        </div>
                        <small class="text-muted">Assim ficará no cardápio para o cliente.</small>
                    </div>
                </div>

                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-lightbulb"></i> Dicas</h3>
                    </div>
                    <div class="card-body p-2">
                        <ul class="list-unstyled mb-0" style="font-size:0.85rem;">
                            <li class="mb-1"><i class="fas fa-check text-success"></i> Use nomes claros para os bairros</li>
                            <li class="mb-1"><i class="fas fa-check text-success"></i> Taxa GRÁTIS atrai mais clientes</li>
                            <li class="mb-1"><i class="fas fa-check text-success"></i> Seja realista no tempo estimado</li>
                            <li><i class="fas fa-check text-success"></i> Desative zonas fora da área de entrega</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?php echo makeString(['save']); ?>
        </button>
        <a href="<?php echo base_url('delivery/zones/index'); ?>" class="btn btn-secondary">
            <?php echo makeString(['cancel']); ?>
        </a>
    </div>

    <?php echo form_close(); ?>
</div>

<script>
$(document).ready(function() {
    function updatePreview() {
        var nome = $('#nome').val() || 'Nome do Bairro';
        var taxa = parseFloat($('#taxa').val()) || 0;
        var tempoMin = $('input[name="tempo_min"]').val() || 20;
        var tempoMax = $('input[name="tempo_max"]').val() || 40;

        $('#previewNome').text(nome);

        if (taxa == 0) {
            $('#previewTaxa').removeClass('badge-info').addClass('badge-success').html('<i class="fas fa-gift"></i> GRÁTIS');
        } else {
            $('#previewTaxa').removeClass('badge-success').addClass('badge-info').text('R$ ' + taxa.toFixed(2).replace('.', ','));
        }

        $('#previewTempo').text(tempoMin + ' - ' + tempoMax);
    }

    $('#nome, #taxa, input[name="tempo_min"], input[name="tempo_max"]').on('input', updatePreview);
    updatePreview();
});
</script>
