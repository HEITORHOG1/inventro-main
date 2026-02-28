<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tags"></i> <?php echo html_escape($title); ?>
        </h3>
        <div class="card-tools">
            <a href="<?php echo base_url('delivery/cupons/index'); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> <?php echo makeString(['back']); ?>
            </a>
        </div>
    </div>

    <?php echo form_open('delivery/cupons/save', array('class' => 'form-horizontal', 'id' => 'formCupom')); ?>

    <div class="card-body">
        <?php
        $exception = $this->session->flashdata('exception');
        if ($exception) echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $exception . '</div>';
        ?>

        <input type="hidden" name="id" value="<?php echo isset($cupom->id) ? (int) $cupom->id : ''; ?>">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="codigo"><?php echo makeString(['codigo']); ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="codigo" name="codigo"
                           value="<?php echo isset($cupom->codigo) ? html_escape($cupom->codigo) : ''; ?>"
                           placeholder="Ex: DESCONTO10" maxlength="20"
                           style="text-transform: uppercase;" required>
                    <small class="form-text text-muted"><?php echo makeString(['codigo_cupom_help']); ?></small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="tipo"><?php echo makeString(['type']); ?> <span class="text-danger">*</span></label>
                    <select class="form-control" id="tipo" name="tipo" required>
                        <option value="percentual" <?php echo (isset($cupom->tipo) && $cupom->tipo === 'percentual') ? 'selected' : ''; ?>>
                            <?php echo makeString(['percentual']); ?>
                        </option>
                        <option value="valor_fixo" <?php echo (isset($cupom->tipo) && $cupom->tipo === 'valor_fixo') ? 'selected' : ''; ?>>
                            <?php echo makeString(['valor_fixo']); ?>
                        </option>
                        <option value="frete_gratis" <?php echo (isset($cupom->tipo) && $cupom->tipo === 'frete_gratis') ? 'selected' : ''; ?>>
                            <?php echo makeString(['frete_gratis']); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group" id="valor_group">
                    <label for="valor">
                        <span id="valor_label"><?php echo makeString(['amount']); ?></span> <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="valor_prefix">R$</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="valor" name="valor"
                               value="<?php echo isset($cupom->valor) ? html_escape($cupom->valor) : ''; ?>"
                               placeholder="0.00" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="valor_minimo_pedido"><?php echo makeString(['valor_minimo_pedido']); ?></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="valor_minimo_pedido" name="valor_minimo_pedido"
                               value="<?php echo isset($cupom->valor_minimo_pedido) ? html_escape($cupom->valor_minimo_pedido) : ''; ?>"
                               placeholder="0.00">
                    </div>
                    <small class="form-text text-muted"><?php echo makeString(['valor_minimo_help']); ?></small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="uso_maximo"><?php echo makeString(['uso_maximo']); ?></label>
                    <input type="number" min="1" class="form-control" id="uso_maximo" name="uso_maximo"
                           value="<?php echo isset($cupom->uso_maximo) && $cupom->uso_maximo !== null ? html_escape($cupom->uso_maximo) : ''; ?>"
                           placeholder="<?php echo makeString(['ilimitado']); ?>">
                    <small class="form-text text-muted"><?php echo makeString(['uso_maximo_help']); ?></small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="ativo"><?php echo makeString(['status']); ?></label>
                    <div class="custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1"
                               <?php echo (!isset($cupom) || $cupom->ativo) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="ativo"><?php echo makeString(['ativo']); ?></label>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="validade_inicio"><?php echo makeString(['validade_inicio']); ?></label>
                    <input type="date" class="form-control" id="validade_inicio" name="validade_inicio"
                           value="<?php echo isset($cupom->validade_inicio) ? html_escape($cupom->validade_inicio) : ''; ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="validade_fim"><?php echo makeString(['validade_fim']); ?></label>
                    <input type="date" class="form-control" id="validade_fim" name="validade_fim"
                           value="<?php echo isset($cupom->validade_fim) ? html_escape($cupom->validade_fim) : ''; ?>">
                </div>
            </div>
        </div>

        <?php if (isset($cupom->uso_atual)): ?>
        <div class="row">
            <div class="col-md-4">
                <div class="callout callout-info">
                    <h5><?php echo makeString(['uso']); ?></h5>
                    <p>
                        <?php echo makeString(['uso_atual']); ?>: <strong><?php echo (int) $cupom->uso_atual; ?></strong>
                        <?php if ($cupom->uso_maximo !== null): ?>
                            / <?php echo (int) $cupom->uso_maximo; ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?php echo makeString(['save']); ?>
        </button>
        <a href="<?php echo base_url('delivery/cupons/index'); ?>" class="btn btn-secondary">
            <?php echo makeString(['cancel']); ?>
        </a>
    </div>

    <?php echo form_close(); ?>
</div>

<script>
$(document).ready(function() {
    // Forcar uppercase no campo codigo
    $('#codigo').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Ajustar campo valor conforme o tipo selecionado
    function ajustarCampoValor() {
        var tipo = $('#tipo').val();
        var $valorGroup = $('#valor_group');
        var $valorPrefix = $('#valor_prefix');
        var $valorLabel = $('#valor_label');
        var $valorInput = $('#valor');

        if (tipo === 'percentual') {
            $valorPrefix.text('%');
            $valorLabel.text('<?php echo makeString(["percentual"]); ?>');
            $valorInput.attr('max', '100');
            $valorInput.prop('disabled', false);
            $valorGroup.show();
        } else if (tipo === 'valor_fixo') {
            $valorPrefix.text('R$');
            $valorLabel.text('<?php echo makeString(["amount"]); ?>');
            $valorInput.removeAttr('max');
            $valorInput.prop('disabled', false);
            $valorGroup.show();
        } else if (tipo === 'frete_gratis') {
            $valorInput.val('0');
            $valorInput.prop('disabled', true);
            $valorGroup.hide();
        }
    }

    $('#tipo').on('change', ajustarCampoValor);
    ajustarCampoValor();

    // Validacao: validade_fim deve ser >= validade_inicio
    $('#validade_fim').on('change', function() {
        var inicio = $('#validade_inicio').val();
        var fim    = $(this).val();
        if (inicio && fim && fim < inicio) {
            alert('<?php echo makeString(["validade_fim_antes_inicio"]); ?>');
            $(this).val('');
        }
    });
});
</script>
