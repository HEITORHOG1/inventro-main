<div class="card card-primary card-outline">
    <div class="card-header">
        <h4><?php echo makeString(['stock_report']); ?></h4>
    </div>

    <div class="row">
        <div class="col-sm-12">
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
