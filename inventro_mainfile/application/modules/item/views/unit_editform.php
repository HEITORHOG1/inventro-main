<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="card-body">
            <?php echo form_open_multipart('item/unit/unit_form', 'class="form-inner"') ?>
            <?php echo form_hidden('id', $units->id) ?>
            <div class="form-group row">
                <label for="unitname" class="col-sm-3 control-label"><?php echo makeString(['unit_name']); ?></label>

                <div class="col-sm-9">
                    <input type="text" name="unitname" class="form-control" value="<?php echo  html_escape($units->unit_name) ?>" id="unitname" placeholder="Unit Name">
                </div>
            </div>
            <div class="form-group">
                <?php if (empty($units->id)) { ?>
                    <button type="submit" class="btn btn-success"><?php echo makeString(['save']); ?></button>
                <?php } else { ?>
                    <button type="submit" class="btn btn-success"><?php echo makeString(['update']); ?></button>

                <?php } ?>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>