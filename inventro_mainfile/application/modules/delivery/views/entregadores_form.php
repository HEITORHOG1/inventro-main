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
