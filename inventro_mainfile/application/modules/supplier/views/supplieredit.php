<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel">
        
            <div class="panel-body">
                <?php echo  form_open('supplier/supplierlist/create') ?>
                <?php echo form_hidden('id', (!empty($intinfo->id) ? html_escape($intinfo->id) : null)) ?>
                <input name="supplier_id" type="hidden" value="<?php echo html_escape($intinfo->supplier_id); ?>" />
                <input name="lid" type="hidden" value="<?php
                if (!empty($intinfo->lid)) {
                    echo html_escape($intinfo->lid);
                }
                ?>" />
                <input name="trsid" type="hidden" value="<?php
                if (!empty($intinfo->transaction_id)) {
                    echo html_escape($intinfo->transaction_id);
                }
                ?>" />
                <div class="form-group row">
                    <label for="suppliername" class="col-sm-4 col-form-label"><?php echo makeString(['supplier_name']) ?> <span class="txt-color">*</span></label>
                    <div class="col-sm-8">
                        <input name="suppliername" class="form-control" type="text" placeholder="Add <?php echo makeString(['supplier_name']) ?>" id="suppliername" value="<?php echo (!empty($intinfo->name) ? html_escape($intinfo->name) : null) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-4 col-form-label"><?php echo makeString(['email']) ?> <span class="txt-color">*</span></label>
                    <div class="col-sm-8">
                        <input name="email" class="form-control" type="text" placeholder="Add <?php echo makeString(['email']) ?>" id="email" value="<?php echo (!empty($intinfo->email) ? html_escape($intinfo->email) : null) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="mobile" class="col-sm-4 col-form-label"><?php echo makeString(['mobile']) ?> <span class="txt-color">*</span></label>
                    <div class="col-sm-8">
                        <input name="mobile" class="form-control" type="number" placeholder="Add <?php echo makeString(['mobile']) ?>" id="mobile" value="<?php echo (!empty($intinfo->mobile) ? html_escape($intinfo->mobile) : null) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="mobile" class="col-sm-4 col-form-label"><?php echo makeString(['previous_balance']) ?> </label>
                    <div class="col-sm-8">
                        <input name="previous_balance" class="form-control" type="number" placeholder="Add <?php echo makeString(['previous_balance']) ?>" id="previous_balance" value="<?php echo (!empty($intinfo->amount) ? html_escape($intinfo->amount) : null) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['isreceipt']) ?> </label>
                    <div class="col-sm-8">
                        <select class="form-control select2 content-width1" name="paytype">
                            <option value="c" <?php
                            if ($intinfo->d_c == 'c') {
                                echo html_escape("selected");
                            }
                            ?>><?php echo html_escape('Received Amount');?></option>
                            <option value="d" <?php
                            if ($intinfo->d_c == 'd') {
                                echo html_escape("selected");
                            }
                            ?>><?php echo html_escape('Payment Amount')?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['address']) ?></label>
                    <div class="col-sm-8">
                        <textarea name="address" cols="50" rows="3" class="form-control" placeholder="Add <?php echo makeString(['address']) ?>" id="address" ><?php echo (!empty($intinfo->address) ? html_escape($intinfo->address) : null) ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['status']) ?> </label>
                    <div class="col-sm-8">
                        <select class="form-control select2 content-width1" name="status">
                            <option <?php
                            if ($intinfo->status == 1) {
                                echo html_escape("selected");
                            }
                            ?> value="1"><?php echo html_escape('Active');?></option>
                            <option value="0" <?php
                            if ($intinfo->status == 0) {
                                echo html_escape("selected");
                            }
                            ?>><?php echo html_escape('Inactive');?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success w-md m-b-5"><?php echo makeString(['update']) ?></button>
                </div>
                <?php echo form_close() ?>
            </div>  
        </div>
    </div>
</div>