<div class="">
    <?php
    $error = $this->session->flashdata('error');
    $success = $this->session->flashdata('success');
    if ($error != '') {
        echo $error;
    }
    if ($success != '') {
        echo $success;
    }
    ?>
</div>
<div class="card card-primary card-outline">
    <div class="card-header">
        <h4><?php echo html_escape($title); ?>
        </h4>
    </div>
    <div class="card-body">
        <?php echo form_open_multipart('accounts/account/account_adjustment_save', 'id="bank_form"') ?>
        <div class="form-group row">
            <label for="payment_date" class="col-sm-3 control-label"><?php echo makeString(['date']); ?></label>
            <div class="col-sm-6">
                <input type="text" name="payment_date" class="form-control datepicker" id="payment_date" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
        <div class="form-group row">
            <label for="payment_type" class="col-sm-3 control-label"><?php echo makeString(['payment_type']); ?></label>
            <div class="col-sm-6">
                <select type="text" name="payment_type" class="form-control select2"  id="payment_type" data-placeholder="<?php echo makeString(['select_one']); ?>" required>
                    <option value=""></option>
                    <option value="d"><?php echo makeString(['debit']); ?></option>
                    <option value="c"><?php echo makeString(['credit']); ?></option>
                </select>
            </div>
        </div>
        <div class="form-group row">
            <label for="amount" class="col-sm-3 control-label"><?php echo makeString(['amount']); ?></label>
            <div class="col-sm-6">
                <input type="text" name="amount" class="form-control" value="" id="amount" placeholder="Valor" min="1" required>
            </div>
        </div>
        <div class="form-group row">
            <label for="details" class="col-sm-3 control-label"><?php echo makeString(['details']); ?></label>
            <div class="col-sm-6">
                <input type="text" name="details" class="form-control" value="" id="details" placeholder="<?php echo makeString(['details']); ?>" min="1">
            </div>
        </div>
        <div class="form-group col-sm-4 text-right">
            <button type="submit" class="btn btn-success"><?php echo makeString(['save']); ?></button>
        </div>
        <?php echo form_close() ?>
    </div>

</div>

