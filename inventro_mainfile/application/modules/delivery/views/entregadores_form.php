<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <?php echo html_escape($title); ?>
        </h3>
        <div class="card-tools">
            <a href="<?php echo base_url('delivery/entregadores/index'); ?>" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> <?php echo makeString(['back']); ?>
            </a>
        </div>
    </div>

    <?php echo form_open('delivery/entregadores/save', array('class' => 'form-horizontal', 'id' => 'formEntregador')); ?>

    <div class="card-body">
        <?php
        $exception = $this->session->flashdata('exception');
        if ($exception) echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button>' . $exception . '</div>';
        ?>

        <input type="hidden" name="id" value="<?php echo isset($entregador->id) ? (int) $entregador->id : ''; ?>">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome">
                        <?php echo makeString(['nome']); ?> <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control"
                           id="nome"
                           name="nome"
                           placeholder="<?php echo makeString(['nome_placeholder']); ?>"
                           value="<?php echo isset($entregador->nome) ? html_escape($entregador->nome) : ''; ?>"
                           maxlength="255"
                           required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="telefone">
                        <?php echo makeString(['telefone']); ?> <span class="text-danger">*</span>
                    </label>
                    <input type="tel"
                           class="form-control"
                           id="telefone"
                           name="telefone"
                           placeholder="(00) 00000-0000"
                           value="<?php echo isset($entregador->telefone) ? html_escape($entregador->telefone) : ''; ?>"
                           maxlength="20"
                           required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="veiculo">
                        <?php echo makeString(['veiculo']); ?> <span class="text-danger">*</span>
                    </label>
                    <select class="form-control" id="veiculo" name="veiculo" required>
                        <option value="">-- <?php echo makeString(['select']); ?> --</option>
                        <option value="moto"
                            <?php echo (isset($entregador->veiculo) && $entregador->veiculo === 'moto') ? 'selected' : ''; ?>>
                            <?php echo makeString(['veiculo_moto']); ?>
                        </option>
                        <option value="bicicleta"
                            <?php echo (isset($entregador->veiculo) && $entregador->veiculo === 'bicicleta') ? 'selected' : ''; ?>>
                            <?php echo makeString(['veiculo_bicicleta']); ?>
                        </option>
                        <option value="carro"
                            <?php echo (isset($entregador->veiculo) && $entregador->veiculo === 'carro') ? 'selected' : ''; ?>>
                            <?php echo makeString(['veiculo_carro']); ?>
                        </option>
                        <option value="a_pe"
                            <?php echo (isset($entregador->veiculo) && $entregador->veiculo === 'a_pe') ? 'selected' : ''; ?>>
                            <?php echo makeString(['veiculo_a_pe']); ?>
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <hr>
        <h5><i class="fas fa-mobile-alt"></i> Portal do Motoboy</h5>
        <small class="text-muted d-block mb-3">Configure o acesso do entregador ao portal mobile em <strong><?php echo base_url('motoboy'); ?></strong></small>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="senha">
                        Senha do Portal <?php if (empty($entregador->id)): ?><span class="text-danger">*</span><?php endif; ?>
                    </label>
                    <input type="password"
                           class="form-control"
                           id="senha"
                           name="senha"
                           placeholder="<?php echo !empty($entregador->id) ? 'Deixe vazio para manter a atual' : 'Senha de acesso ao portal'; ?>"
                           minlength="4"
                           <?php echo empty($entregador->id) ? 'required' : ''; ?>>
                    <?php if (!empty($entregador->id) && !empty($entregador->senha)): ?>
                        <small class="text-success"><i class="fas fa-check-circle"></i> Senha cadastrada</small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="taxa_entrega_fixa">
                        Taxa por Entrega (R$) <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           class="form-control"
                           id="taxa_entrega_fixa"
                           name="taxa_entrega_fixa"
                           placeholder="5.00"
                           step="0.50"
                           min="0"
                           value="<?php echo isset($entregador->taxa_entrega_fixa) ? html_escape($entregador->taxa_entrega_fixa) : '5.00'; ?>"
                           required>
                    <small class="text-muted">Valor que o entregador ganha por cada entrega</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> <?php echo makeString(['save']); ?>
        </button>
        <a href="<?php echo base_url('delivery/entregadores/index'); ?>" class="btn btn-secondary">
            <?php echo makeString(['cancel']); ?>
        </a>
    </div>

    <?php echo form_close(); ?>
</div>
