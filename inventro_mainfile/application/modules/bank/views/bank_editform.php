<div class="card card-primary card-outline">

    <div class="card-header">

        <?php if ($this->permission->method('bank', 'create')->access()): ?>
            <h4><?php echo makeString(['update']); ?> <?php echo makeString(['bank']); ?> <small class="float-right"><a href="<?php echo base_url('bank/bank/bank_list') ?>" class="btn btn-primary btn-md" ><i class="ti-plus" aria-hidden="true"></i>
                        <?php echo makeString(['bank_list']); ?></a> </small></h4>
        <?php endif; ?>
    </div>

    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <?php echo form_open_multipart('', 'id="upbank_form"') ?>
                <div class="form-group row">
                    <label for="bank_name" class="col-sm-3 control-label"><?php echo makeString(['bank_name']) ?></label>

                    <div class="col-sm-9">
                        <input type="hidden" name="id" value="<?php echo html_escape($banks->id) ?>">
                        <input type="hidden" name="bank_id" value="<?php echo html_escape($banks->bank_id) ?>">

                        <input type="text" name="bank_name" class="form-control" value="<?php echo html_escape((!empty($banks->bank_name) ? $banks->bank_name : '')) ?>" id="bank_name" placeholder="Nome do Banco">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="account_no" class="col-sm-3 control-label"><?php echo makeString(['account_no']) ?></label>

                    <div class="col-sm-9">
                        <input type="text" name="account_no" class="form-control" value="<?php echo html_escape((!empty($banks->account_no) ? $banks->account_no : '')) ?>" id="account_no" placeholder="Nº da Conta">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="branch_name" class="col-sm-3 control-label"><?php echo makeString(['branch_name']) ?></label>

                    <div class="col-sm-9">
                        <input type="text" name="branch_name" class="form-control" value="<?php echo html_escape((!empty($banks->branch_name) ? $banks->branch_name : '')) ?>" id="branch_name" placeholder="Nome da Agência">
                    </div>
                </div>
                <div class="form-group">
                    <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>">
                    <button type="submit" class="btn btn-success"><?php echo makeString(['update']) ?></button>


                </div>

                <?php echo form_close() ?>
            </div>
        </div>

    </div>


</div>

<script src="<?php echo base_url() ?>application/modules/bank/assets/js/bank.js" type="text/javascript"></script> 
