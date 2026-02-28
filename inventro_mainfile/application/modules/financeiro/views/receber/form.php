<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><?php echo $title; ?></h3>
        <div class="card-tools">
            <a href="<?php echo base_url('financeiro/contas_receber/lista') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> <?php echo makeString(['back']); ?>
            </a>
        </div>
    </div>
    
    <?php echo form_open('financeiro/contas_receber/salvar', array('class' => 'form-horizontal', 'id' => 'formContaReceber')) ?>
    
    <div class="card-body">
        <?php
        $exception = $this->session->flashdata('exception');
        if ($exception) echo '<div class="alert alert-danger">'.$exception.'</div>';
        ?>
        
        <input type="hidden" name="id" value="<?php echo isset($conta->id) ? $conta->id : ''; ?>">
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="descricao"><?php echo makeString(['description']); ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="descricao" name="descricao" 
                           value="<?php echo isset($conta->descricao) ? $conta->descricao : ''; ?>" 
                           placeholder="Ex: Venda a prazo, Fiado, Serviço prestado..." required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tipo"><?php echo makeString(['type']); ?></label>
                    <select class="form-control" id="tipo" name="tipo">
                        <option value="venda" <?php echo (isset($conta->tipo) && $conta->tipo == 'venda') ? 'selected' : ''; ?>>
                            <?php echo makeString(['tipo_venda']); ?>
                        </option>
                        <option value="fiado" <?php echo (isset($conta->tipo) && $conta->tipo == 'fiado') ? 'selected' : ''; ?>>
                            <?php echo makeString(['tipo_fiado']); ?>
                        </option>
                        <option value="servico" <?php echo (isset($conta->tipo) && $conta->tipo == 'servico') ? 'selected' : ''; ?>>
                            <?php echo makeString(['tipo_servico']); ?>
                        </option>
                        <option value="outro" <?php echo (isset($conta->tipo) && $conta->tipo == 'outro') ? 'selected' : ''; ?>>
                            <?php echo makeString(['tipo_outro']); ?>
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="categoria_id"><?php echo makeString(['categoria']); ?></label>
                    <select class="form-control select2" id="categoria_id" name="categoria_id">
                        <option value="">-- Selecione --</option>
                        <?php foreach($categorias as $cat): ?>
                        <option value="<?php echo $cat->id; ?>" 
                                <?php echo (isset($conta->categoria_id) && $conta->categoria_id == $cat->id) ? 'selected' : ''; ?>>
                            <?php echo $cat->nome; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="cliente_id"><?php echo makeString(['customer']); ?> <span class="text-danger">*</span></label>
                    <select class="form-control select2" id="cliente_id" name="cliente_id" required>
                        <option value="">-- Selecione o Cliente --</option>
                        <?php foreach($clientes as $c): ?>
                        <option value="<?php echo $c->customerid; ?>" 
                                <?php echo (isset($conta->cliente_id) && $conta->cliente_id == $c->customerid) ? 'selected' : ''; ?>>
                            <?php echo $c->name; ?> (<?php echo $c->mobile; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="valor_original"><?php echo makeString(['valor_original']); ?> <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                        </div>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="valor_original" name="valor_original" 
                               value="<?php echo isset($conta->valor_original) ? $conta->valor_original : ''; ?>" 
                               placeholder="0.00" required>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="total_parcelas"><?php echo makeString(['total_parcelas']); ?></label>
                    <select class="form-control" id="total_parcelas" name="total_parcelas" <?php echo isset($conta->id) ? 'disabled' : ''; ?>>
                        <?php for($i=1; $i<=12; $i++): ?>
                        <option value="<?php echo $i; ?>" 
                                <?php echo (isset($conta->total_parcelas) && $conta->total_parcelas == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?>x
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="data_emissao"><?php echo makeString(['data_emissao']); ?></label>
                    <input type="date" class="form-control" id="data_emissao" name="data_emissao" 
                           value="<?php echo isset($conta->data_emissao) ? $conta->data_emissao : date('Y-m-d'); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="data_vencimento"><?php echo makeString(['data_vencimento']); ?> <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" 
                           value="<?php echo isset($conta->data_vencimento) ? $conta->data_vencimento : ''; ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="valor_parcela">Valor por Parcela</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                        </div>
                        <input type="text" class="form-control" id="valor_parcela" readonly value="0,00">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="observacao"><?php echo makeString(['observacao']); ?></label>
                    <textarea class="form-control" id="observacao" name="observacao" rows="3" 
                              placeholder="Observações adicionais..."><?php echo isset($conta->observacao) ? $conta->observacao : ''; ?></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?php echo makeString(['save']); ?>
        </button>
        <a href="<?php echo base_url('financeiro/contas_receber/lista') ?>" class="btn btn-secondary">
            <?php echo makeString(['cancel']); ?>
        </a>
    </div>
    
    <?php echo form_close() ?>
</div>

<script>
$(document).ready(function() {
    function calcularParcela() {
        var valor = parseFloat($('#valor_original').val()) || 0;
        var parcelas = parseInt($('#total_parcelas').val()) || 1;
        var valorParcela = valor / parcelas;
        $('#valor_parcela').val(valorParcela.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }
    
    $('#valor_original, #total_parcelas').on('change keyup', calcularParcela);
    calcularParcela();
    
    if($.fn.select2) {
        $('.select2').select2({ theme: 'bootstrap4' });
    }
});
</script>
