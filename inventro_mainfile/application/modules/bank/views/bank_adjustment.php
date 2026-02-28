<div class="card card-primary card-outline">

    <div class="card-header">
        <h4><?php echo html_escape($title); ?><small class="float-right"></small></h4>
    </div>




    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="panel">
                            <div class="card-body">
                                <?php echo form_open_multipart('bank/bank/add_adjustment', 'id="bank_adjustment"') ?>
                                <div class="form-group row">
                                    <label for="payment_date" class="col-sm-3 control-label"><?php echo makeString(['bank_name']) ?></label>
                                    <div class="col-sm-9">
                                        <?php echo form_dropdown('bank_id', $bank_list, '', 'class="form-control select2 content-width1" id="bank_id"') ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="payment_date" class="col-sm-3 control-label"><?php echo makeString(['payment_date']) ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="payment_date" class="form-control datepicker" id="payment_date" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="payment_type" class="col-sm-3 control-label"><?php echo makeString(['payment_type']) ?></label>
                                    <div class="col-sm-9">
                                        <select type="text" name="payment_type" class="form-control select2"  id="payment_type" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                            <option value=""></option>
                                            <option value="d"><?php echo makeString(['debit']); ?></option>
                                            <option value="c"><?php echo makeString(['credit']); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="amount" class="col-sm-3 control-label"><?php echo makeString(['amount']) ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="amount" class="form-control" value="" id="amount" placeholder="<?php echo makeString(['amount']); ?>" min="1">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="details" class="col-sm-3 control-label"><?php echo makeString(['details']) ?></label>
                                    <div class="col-sm-9">
                                        <input type="text" name="details" class="form-control" value="" id="details" placeholder="<?php echo makeString(['details']); ?>" min="1">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success"><?php echo makeString(['save']) ?></button>
                                </div>

                                <?php echo form_close() ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>