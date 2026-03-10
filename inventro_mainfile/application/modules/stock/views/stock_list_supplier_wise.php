<div class="col-sm-12">
    <button class="btn btn-primary mb-3" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
        <?php echo makeString(['search_by_supplier']); ?>
    </button>
    <div class="card card-primary card-outline collapse in" id="collapseExample" aria-expanded="true" style="">
        <div class="card-body">
        <?php echo form_open_multipart('stock/stock/stock_report_supplier_wise', array('class' => 'form-inline', 'id' => 'stock_report_supplier_wise'))?>

                <label class="mr-3"><?php echo makeString(['select_supplier']); ?>:</label>
                <select class="form-control" id="supplier_id" name="supplier_id" required="">
                    <option value=""><?php echo makeString(['select_one']); ?></option>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?php echo html_escape($supplier->supplier_id); ?>"><?php echo html_escape($supplier->name); ?> </option>
                    <?php } ?>
                </select>
                <button type="submit" class="btn btn-success ml-3"><?php echo makeString(['search']); ?></button>
            </form>
        </div>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h4><?php echo makeString(['stock_report_supplier_wise']); ?></h4>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php if ($supplierinfo) { ?>
                <div class="text-center">
                    <h2><?php echo html_escape($supplierinfo->name); ?></h2>
                    <h4><?php echo html_escape($supplierinfo->address); ?></h4>
                    <h4><?php echo html_escape($supplierinfo->email); ?></h4>
                    <h4><?php echo html_escape($supplierinfo->mobile); ?></h4>
                </div>
            <?php } ?>
            <div class="card-body">
                <table id="datagrid" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo makeString(['sl']); ?></th>
                            <th><?php echo makeString(['product_name']); ?></th>
                            <th><?php echo makeString(['product_model']); ?></th>
                            <th><?php echo makeString(['category_name']); ?></th>
                            <th><?php echo makeString(['sales_price']); ?></th>
                            <th><?php echo makeString(['purchase_price']); ?></th>
                            <th><?php echo makeString(['stock']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        $total_sales_price = 0;
                        $total_purchase_price = 0;
                        $total_stock = 0;
                        foreach ($stocks as $stock) {
                            $qty = (float) $stock->stock_qty;
                            ?>
                            <tr>
                                <td><?php echo html_escape($sl); ?></td>
                                <td><?php echo html_escape($stock->product_name); ?></td>
                                <td><?php echo html_escape($stock->model); ?></td>
                                <td><?php echo html_escape($stock->category_name); ?></td>
                                <td class="text-right"><?php echo html_escape(number_format((float)$stock->price, 2, ',', '.')); ?></td>
                                <td class="text-right"><?php echo html_escape(number_format((float)$stock->purchase_price, 2, ',', '.')); ?></td>
                                <td class="text-right <?php echo ($qty <= 0) ? 'text-danger font-weight-bold' : ''; ?>">
                                    <?php echo html_escape(number_format($qty, 0, ',', '.')); ?>
                                </td>
                            </tr>
                            <?php
                            $sl++;
                            $total_sales_price += (float) $stock->price;
                            $total_purchase_price += (float) $stock->purchase_price;
                            $total_stock += $qty;
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" align="right"><b><?php echo makeString(['grand_total']); ?></b></td>
                            <td class="text-right"><b><?php echo html_escape(number_format($total_sales_price, 2, ',', '.')); ?></b></td>
                            <td class="text-right"><b><?php echo html_escape(number_format($total_purchase_price, 2, ',', '.')); ?></b></td>
                            <td class="text-right"><b><?php echo html_escape(number_format($total_stock, 0, ',', '.')); ?></b></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
