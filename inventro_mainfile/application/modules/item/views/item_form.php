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
                    <label for="ean_gtin" class="col-sm-2 control-label">EAN/GTIN (Código de Barras)</label>

                    <div class="col-sm-4">
                        <input type="text" name="ean_gtin" class="form-control" id="ean_gtin" placeholder="Ex: 7891234567890" maxlength="14" pattern="\d{0,14}" value="<?php echo html_escape($item->ean_gtin ?? ''); ?>">
                        <small id="ean_gtin_feedback" class="form-text text-danger" style="display:none;"></small>
                    </div>

                    <label for="estoque_minimo" class="col-sm-2 control-label">
                        Estoque Mínimo
                        <i class="fas fa-info-circle text-muted" data-toggle="tooltip" title="Alerta no PDV quando estoque ficar abaixo deste valor"></i>
                    </label>

                    <div class="col-sm-4">
                        <input type="number" name="estoque_minimo" class="form-control" id="estoque_minimo" min="0" step="1" value="<?php echo html_escape($item->estoque_minimo ?? 0); ?>">
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
                    <div class="col-sm-2"></div>
                    <div class="col-sm-10">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" name="pesavel" id="pesavel" value="1" <?php echo (!empty($item->pesavel) && $item->pesavel == 1) ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="pesavel"><strong>Produto Pesável</strong></label>
                        </div>
                    </div>
                </div>

                <div id="pesavel_fields" style="<?php echo (!empty($item->pesavel) && $item->pesavel == 1) ? '' : 'display:none;'; ?>">
                    <div class="form-group row">
                        <label for="codigo_balanca" class="col-sm-2 control-label">Código Balança</label>

                        <div class="col-sm-4">
                            <input type="text" name="codigo_balanca" class="form-control" id="codigo_balanca" placeholder="Ex: 00123" maxlength="5" pattern="\d{5}" value="<?php echo html_escape($item->codigo_balanca ?? ''); ?>">
                            <small id="codigo_balanca_feedback" class="form-text text-danger" style="display:none;"></small>
                        </div>

                        <label class="col-sm-2 control-label">Tipo Barcode Balança</label>

                        <div class="col-sm-4">
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="tipo_barcode_balanca" id="tipo_peso" value="peso" <?php echo (empty($item->tipo_barcode_balanca) || ($item->tipo_barcode_balanca ?? 'peso') === 'peso') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="tipo_peso">Peso (KG)</label>
                            </div>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="tipo_barcode_balanca" id="tipo_preco" value="preco" <?php echo (($item->tipo_barcode_balanca ?? '') === 'preco') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="tipo_preco">Preço (R$)</label>
                            </div>
                        </div>
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

<script>
$(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    var baseUrl = '<?php echo base_url(); ?>';
    var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    var productId = '<?php echo html_escape($item->product_id ?? ''); ?>';

    // Toggle pesavel fields visibility
    $('#pesavel').on('change', function() {
        if ($(this).is(':checked')) {
            $('#pesavel_fields').slideDown();
        } else {
            $('#pesavel_fields').slideUp();
            $('#codigo_balanca').val('');
            $('#codigo_balanca_feedback').hide();
        }
    });

    // EAN/GTIN: allow only digits
    $('#ean_gtin').on('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });

    // Codigo Balanca: allow only digits
    $('#codigo_balanca').on('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });

    // AJAX: Check EAN/GTIN uniqueness on blur
    $('#ean_gtin').on('blur', function() {
        var ean = $(this).val().trim();
        var $feedback = $('#ean_gtin_feedback');
        $feedback.hide().text('');

        if (!ean) return;

        var postData = {};
        postData[csrfName] = csrfHash;
        postData['ean_gtin'] = ean;
        postData['product_id'] = productId;

        $.ajax({
            url: baseUrl + 'item/item/check_ean',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(r) {
                if (r.csrf_token) csrfHash = r.csrf_token;
                if (r.error) {
                    $feedback.text(r.error).show();
                } else if (!r.unique) {
                    $feedback.text('EAN/GTIN já cadastrado para: ' + r.product_name).show();
                }
            }
        });
    });

    // AJAX: Check codigo_balanca uniqueness on blur
    $('#codigo_balanca').on('blur', function() {
        var codigo = $(this).val().trim();
        var $feedback = $('#codigo_balanca_feedback');
        $feedback.hide().text('');

        if (!codigo) return;

        var postData = {};
        postData[csrfName] = csrfHash;
        postData['codigo_balanca'] = codigo;
        postData['product_id'] = productId;

        $.ajax({
            url: baseUrl + 'item/item/check_codigo_balanca',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(r) {
                if (r.csrf_token) csrfHash = r.csrf_token;
                if (r.error) {
                    $feedback.text(r.error).show();
                } else if (!r.unique) {
                    $feedback.text('Código Balança já cadastrado para: ' + r.product_name).show();
                }
            }
        });
    });
});
</script>
