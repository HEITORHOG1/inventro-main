<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="card-body">
            <?php echo form_open_multipart('item/category/category_form', 'class="form-inner"') ?>
            <?php echo form_hidden('id', $categorys->id) ?>
            <div class="form-group row">
                <label for="categoryname" class="col-sm-3 control-label"><?php echo makeString(['category_name']); ?></label>

                <div class="col-sm-9">
                    <input type="text" name="categoryname" class="form-control" value="<?php echo html_escape($categorys->name) ?>" id="categoryname" placeholder="category Name">
                    <input type="hidden" name="category_id" value="<?php echo html_escape($categorys->category_id) ?>">
                </div>
            </div>

            <div class="form-group row">
                <label for="parentcategory" class="col-sm-3 control-label"><?php echo makeString(['parent_cateogry']); ?></label>

                <div class="col-sm-9">
                    <select name="parent_category" class="form-control select2" >
                        <option value=""><?php echo makeString(['select_one']); ?></option>
                        <?php foreach ($categorylist as $category) { ?>
                            <option value="<?php echo $category->category_id; ?>" <?php
                            if ($category->category_id == $categorys->parent_id) {
                                echo 'selected';
                            }
                            ?>><?php echo html_escape($category->name); ?></option>
                                <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <?php if (empty($categorys->id)) { ?>
                    <button type="submit" class="btn btn-success"><?php echo makeString(['save']); ?></button>
                <?php } else { ?>
                    <button type="submit" class="btn btn-success"><?php echo makeString(['update']); ?></button>
                <?php } ?>
            </div>

            <?php echo form_close() ?>
        </div>
    </div>
</div>