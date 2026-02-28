<?php
$currency = $get_appsetting->currencyname;
$position = $get_appsetting->position;
?>

<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-dollar-sign"></i> <?php echo makeString(['baixa_pagamento']); ?>
        </h3>
        <div class="card-tools">
            <a href="<?php echo base_url('financeiro/contas_pagar/lista') ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> <?php echo makeString(['back']); ?>
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <?php
        $exception = $this->session->flashdata('exception');
        if ($exception) echo '<div class="alert alert-danger">'.$exception.'</div>';
        ?>
        
        <!-- Informações da Conta -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="callout callout-info">
                    <h5><strong><?php echo $conta->codigo; ?></strong> - <?php echo $conta->descricao; ?></h5>
                    <div class="row">
                        <div class="col-md-3">
                            <strong><?php echo makeString(['supplier']); ?>:</strong><br>
                            <?php echo $conta->fornecedor_nome ?: 'Não informado'; ?>
                        </div>
                        <div class="col-md-3">
                            <strong><?php echo makeString(['data_vencimento']); ?>:</strong><br>
                            <?php 
                            $venc = date('d/m/Y', strtotime($conta->data_vencimento));
                            $atrasado = $conta->data_vencimento < date('Y-m-d');
                            echo $atrasado ? '<span class="text-danger font-weight-bold">'.$venc.' (VENCIDO)</span>' : $venc;
                            ?>
                        </div>
                        <div class="col-md-2 text-right">
                            <strong><?php echo makeString(['valor_original']); ?>:</strong><br>
                            <span class="text-primary">R$ <?php echo number_format($conta->valor_original, 2, ',', '.'); ?></span>
                        </div>
                        <div class="col-md-2 text-right">
                            <strong><?php echo makeString(['valor_pago']); ?>:</strong><br>
                            <span class="text-success">R$ <?php echo number_format($conta->valor_pago, 2, ',', '.'); ?></span>
                        </div>
                        <div class="col-md-2 text-right">
                            <strong><?php echo makeString(['valor_pendente']); ?>:</strong><br>
                            <span class="text-danger font-weight-bold" style="font-size:1.2em;">R$ <?php echo number_format($valor_pendente, 2, ',', '.'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulário de Baixa -->
        <?php echo form_open('financeiro/contas_pagar/registrar_baixa', array('class' => 'form-horizontal', 'id' => 'formBaixa')) ?>
        <input type="hidden" name="conta_id" value="<?php echo $conta->id; ?>">
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="valor"><?php echo makeString(['amount']); ?> <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">R$</span>
                        </div>
                        <input type="number" step="0.01" min="0.01" max="<?php echo $valor_pendente; ?>" 
                               class="form-control" id="valor" name="valor" 
                               value="<?php echo $valor_pendente; ?>" required>
                    </div>
                    <small class="text-muted">Máximo: R$ <?php echo number_format($valor_pendente, 2, ',', '.'); ?></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="data_baixa"><?php echo makeString(['date']); ?> <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="data_baixa" name="data_baixa" 
                           value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="forma_pagamento"><?php echo makeString(['forma_pagamento']); ?> <span class="text-danger">*</span></label>
                    <select class="form-control" id="forma_pagamento" name="forma_pagamento" required>
                        <option value="">-- Selecione --</option>
                        <option value="dinheiro"><?php echo makeString(['dinheiro']); ?></option>
                        <option value="pix"><?php echo makeString(['pix']); ?></option>
                        <option value="cartao_debito"><?php echo makeString(['cartao_debito']); ?></option>
                        <option value="cartao_credito"><?php echo makeString(['cartao_credito']); ?></option>
                        <option value="boleto"><?php echo makeString(['boleto']); ?></option>
                        <option value="transferencia"><?php echo makeString(['transferencia']); ?></option>
                        <option value="cheque"><?php echo makeString(['cheque']); ?></option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="banco_id"><?php echo makeString(['bank']); ?></label>
                    <select class="form-control" id="banco_id" name="banco_id">
                        <option value="">-- Opcional --</option>
                        <?php foreach($bancos as $b): ?>
                        <option value="<?php echo $b->bank_id; ?>"><?php echo $b->bank_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="observacao"><?php echo makeString(['observacao']); ?></label>
                    <textarea class="form-control" id="observacao" name="observacao" rows="2" 
                              placeholder="Observação do pagamento..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-check"></i> <?php echo makeString(['registrar_baixa']); ?>
                </button>
                <a href="<?php echo base_url('financeiro/contas_pagar/lista') ?>" class="btn btn-secondary">
                    <?php echo makeString(['cancel']); ?>
                </a>
            </div>
        </div>
        
        <?php echo form_close() ?>
    </div>
</div>

<!-- Histórico de Baixas -->
<?php if(!empty($historico)): ?>
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-history"></i> <?php echo makeString(['historico_baixas']); ?>
        </h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th><?php echo makeString(['date']); ?></th>
                    <th><?php echo makeString(['amount']); ?></th>
                    <th><?php echo makeString(['forma_pagamento']); ?></th>
                    <th><?php echo makeString(['observacao']); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($historico as $h): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($h->data_baixa)); ?></td>
                    <td class="text-success font-weight-bold">R$ <?php echo number_format($h->valor, 2, ',', '.'); ?></td>
                    <td><?php echo ucfirst(str_replace('_', ' ', $h->forma_pagamento)); ?></td>
                    <td><?php echo $h->observacao ?: '-'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
