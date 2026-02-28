<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel">

            <div class="panel-body">
                <?php echo form_open('dashboard/currency/create') ?>
                <?php echo form_hidden('currencyid', (!empty($intinfo->currencyid) ? $intinfo->currencyid : null)) ?>

                <div class="form-group row">
                    <label for="currencyname" class="col-sm-4 col-form-label"><?php echo makeString(['currency_name']) ?> <span class="txt-color">*</span></label>
                    <div class="col-sm-8">
                        <input name="currencyname" class="form-control" type="text" placeholder="<?php echo makeString(['currency_name']) ?>" id="currencyname" value="<?php echo html_escape((!empty($intinfo->currencyname) ? $intinfo->currencyname : null)) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="icon" class="col-sm-4 col-form-label"><?php echo makeString(['currency_icon']) ?> <span class="txt-color">*</span></label>
                    <div class="col-sm-8">
                        <input name="icon" class="form-control" type="text" placeholder="Add <?php echo makeString(['currency_icon']) ?>" id="icon" value="<?php echo html_escape((!empty($intinfo->curr_icon) ? $intinfo->curr_icon : null)) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="rate" class="col-sm-4 col-form-label"><?php echo makeString(['currency_rate']) ?> <span class="txt-color">*</span></label>
                    <div class="col-sm-8">
                        <input name="rate" class="form-control" type="text" placeholder="<?php echo makeString(['currency_rate']) ?>" id="rate" value="<?php echo html_escape((!empty($intinfo->curr_rate) ? $intinfo->curr_rate : null)) ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="position" class="col-sm-4 col-form-label"><?php echo makeString(['currency_position']) ?> <span class="txt-color">*</span></label>
                    <div class="col-sm-8 customesl">
                        <select name="position" class="form-control">
                            <option value=""  selected="selected"><?php echo makeString(['select_one']); ?></option>
                            <option value="1" <?php
                            if ($intinfo->position == 1) {
                                echo html_escape("selected");
                            }
                            ?>><?php echo makeString(['left']); ?></option>
                            <option value="2" <?php
                            if ($intinfo->position == 2) {
                                echo html_escape("selected");
                            }
                            ?>><?php echo makeString(['right']); ?></option>
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