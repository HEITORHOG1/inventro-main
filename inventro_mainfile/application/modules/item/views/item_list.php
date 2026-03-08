<div class="card card-primary card-outline">
    <div class="card-header">
        <?php if ($this->permission->method('item', 'create')->access()): ?>
            <h4><?php echo makeString(['item']) . ' ' . makeString(['list']) ?> <small class="float-right"><a href="<?php echo base_url('item/item/item_form') ?>" class="btn btn-primary btn-md" ><i class="ti-plus" aria-hidden="true"></i>
                        <?php echo makeString(['add']) . ' ' . makeString(['item']); ?></a> </small></h4>
        <?php endif; ?>
    </div>
    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="productList" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo makeString(['sl']) ?></th>
                            <th><?php echo makeString(['item_name']) ?></th>
                            <th><?php echo makeString(['item_model']) ?></th>
                            <th><?php echo makeString(['supplier_name']) ?></th>
                            <th><?php echo makeString(['sale_price']) ?></th>
                            <th><?php echo makeString(['purchase_price']) ?></th>
                            <th><?php echo makeString(['unit']) ?></th>
                            <th><?php echo makeString(['category']) ?></th>
                            <th><?php echo makeString(['image']) ?></th>
                            <th><?php echo makeString(['action']) ?></th>

                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <input type="hidden" name="" id="base_url" value="<?php echo base_url(); ?>">
                    <input type="hidden" name="" id="totalitem" value="<?php echo html_escape($totalitem); ?>">
                </table>  <!-- /.table-responsive -->
            </div>
        </div>
    </div>
    <div id="barcodebody" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo 'Código de Barras/QR Code'; ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body barcodeqrcodinfo">
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>application/modules/item/assets/js/item.js" type="text/javascript"></script>     
