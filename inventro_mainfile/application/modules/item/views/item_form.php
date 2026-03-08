<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo html_escape($title); ?></h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <?php echo form_open_multipart('item/item/item_form/' . html_escape($item->product_id), 'class="form-inner"') ?>
            <?php echo form_hidden('product_id', html_escape($item->product_id)) ?>
            <div class="card-body">

                <div class="form-group row">
                    <label for="itemname" class="col-sm-2 control-label"><?php echo makeString(['item_name']) ?></label>

                    <div class="col-sm-4">
                        <input type="text" name="itemname" class="form-control" id="itemname" placeholder="<?php echo makeString(['item_name']); ?>" value="<?php echo html_escape($item->name); ?>">
                    </div>
                    <label for="item_code" class="col-sm-2 control-label"><?php echo makeString(['item_code']) ?></label>

                    <div class="col-sm-4">
                        <input type="text" name="itemcode" class="form-control" id="item_code" placeholder="<?php echo makeString(['item_code']); ?>" value="<?php echo html_escape($item->product_code); ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemmodel" class="col-sm-2 control-label"><?php echo makeString(['item_model']) ?></label>

                    <div class="col-sm-4">
                        <input type="text" name="itemmodel" class="form-control" id="itemmodel" placeholder="<?php echo makeString(['item_model']); ?>" value="<?php echo html_escape($item->model); ?>">
                    </div>
                    <label for="itemunit" class="col-sm-2 control-label"><?php echo makeString(['unit']) ?></label>

                    <div class="col-sm-4">
                        <?php echo form_dropdown('itemunit', $unitlist, $item->unit, 'class="form-control select2" id="itemunit" ') ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="itemcategory" class="col-sm-2 control-label"><?php echo makeString(['category']) ?></label>

                    <div class="col-sm-4">
                        <?php echo form_dropdown('itemcategory', $categorylist, $item->category_id, 'class="form-control select2" id="itemcategory"') ?>
                    </div>
                    <label for="cartoonqty" class="col-sm-2 control-label"><?php echo makeString(['cartoon_qty']) ?></label>

                    <div class="col-sm-4">
                        <input type="text" name="cartoonqty" class="form-control" id="cartoonqty" placeholder="<?php echo makeString(['cartoon_qty']); ?>" value="<?php echo html_escape($item->cartoon_qty); ?>">
                    </div>
                </div>


                <div class="form-group row">
                    <label for="itemprice" class="col-sm-2 control-label"><?php echo makeString(['sale_price']) ?></label>

                    <div class="col-sm-4">
                        <input type="text" name="itemprice" class="form-control" id="itemprice" placeholder="<?php echo makeString(['sale_price']); ?>" value="<?php echo html_escape($item->price); ?>">
                    </div>

                    <label for="purchase_price" class="col-sm-2 control-label"><?php echo makeString(['purchase_price']) ?></label>

                    <div class="col-sm-4">
                        <input type="text" name="purchase_price" class="form-control " id="purchase_price" placeholder="<?php echo makeString(['purchase_price']); ?>" value="<?php echo html_escape($item->purchase_price); ?>">
                    </div>

                </div>
                <div class="form-group row">
                    <label for="itemdetails" class="col-sm-2 control-label"><?php echo makeString(['item']) . makeString(['details']) ?></label>

                    <div class="col-sm-4">

                        <textarea name="itemdetails" class="form-control"><?php echo html_escape($item->description); ?></textarea>
                    </div>

                    <label for="supplier" class="col-sm-2 control-label"><?php echo makeString(['supplier_name']) ?></label>

                    <div class="col-sm-4">
                        <?php echo form_dropdown('supplier_id', $supplier_list, $item->supplier_id, 'class="form-control select2" id="supplier_id"') ?>
                    </div>

                </div>

                <div class="form-group row"> 
                    <label for="picture" class="col-sm-2 control-label"><?php echo makeString(['image']) ?></label>

                    <div class="col-sm-4 custom-file">
                        <input type="file" name="picture" class="custom-file-input" id="picture">
                        <label class="custom-file-label" for="exampleInputFile">Choose Image</label>
                        <input type="hidden" name="old_picture" value="<?php echo (!empty($item->picture) ? html_escape($item->picture) : ''); ?>">
                    </div>

                    <?php if (!empty($item->picture)) {
                        // Converter path do banco para URL via controller de imagem
                        $img_src = $item->picture;
                        if (preg_match('#(\d{4}-\d{2}-\d{2})/([^/]+)$#', $item->picture, $_m)) {
                            $img_src = 'img/product/' . $_m[1] . '/' . $_m[2];
                        }
                    ?>
                        <label for="faviconPreview" class="col-sm-2 col-form-label"></label>
                        <div class="col-sm-4">
                            <img src="<?php echo base_url() . $img_src; ?>" alt="<?php echo makeString(['picture']); ?>" class="img-thumbnail" />
                        </div>

                    <?php } ?> 
                </div> 

            </div>
            <!-- /.card-body -->
            <div class="card-footer">
                <button type="submit" class="btn btn-success"><?php echo (!empty($item->product_id) ? 'Update' : 'Submit') ?></button>
            </div>

            <?php echo form_close() ?>
        </div>
    </div>
</div>
