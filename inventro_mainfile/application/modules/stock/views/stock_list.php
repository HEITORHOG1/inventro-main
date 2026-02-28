<div class="card card-primary card-outline">
    <div class="card-header">
        <h4><?php echo html_escape('Stock Report')?></h4>
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
                            <th><?php echo makeString(['stock_in']); ?></th>
                            <th><?php echo makeString(['stock_out']); ?></th>
                            <th><?php echo makeString(['customer']).' '.makeString(['return']); ?></th>
                            <th><?php echo makeString(['supplier']).' '.makeString(['return']); ?></th>
                            <th><?php echo makeString(['stock']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        $total_sales = 0;
                        $total_purchases = 0;
                        $total_purchases_quantity = 0;
                        $total_sales_quantity =  $total_supplier_return = $total_cutomer_return=0;
                        $total_stock = 0;
                        foreach ($stocks as $stock) {

                            // customer return part
                            $this->db->select('SUM(return_qty) as total_return_in');
                            $this->db->from('return_details');
                            $this->db->where('product_id', $stock->product_id);
                            $this->db->where('status', 1);
                            $cutomrer_return = $this->db->get()->row();

                            $total_cutomer_return+=$cutomrer_return->total_return_in;
                            // supplier return part 
                            $this->db->select('SUM(return_qty) as total_return_out');
                            $this->db->from('return_details');
                            $this->db->where('product_id', $stock->product_id);
                            $this->db->where('status', 2);
                            $supplier_return = $this->db->get()->row();
                            $total_supplier_return += $supplier_return->total_return_out;
                            ?>

                            <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>">
                                <td><?php echo html_escape( $sl); ?></td>
                                <td><?php echo html_escape( $stock->product_name); ?></td>
                                <td class="center">
                                    <?php echo html_escape( $stock->model) ?>
                                </td>
                                <td class="center">
                                    <?php echo html_escape( $stock->category_name); ?>
                                </td>
                                <td class="center">
                                    <?php echo html_escape( $stock->price) ?>
                                </td>
                                <td class="center">
                                    <?php echo html_escape( $stock->purchase_price) ?>
                                </td>
                                <td class="center">
                                    <?php echo  $in = html_escape($stock->total_purchase_quantity); ?>
                                </td>
                                <td class="center">
                                    <?php echo  $out  = html_escape($stock->total_sales_quantity); ?>
                                </td>

                                <td class="center">
                                    <?php echo    html_escape($cutomrer_return->total_return_in) ?>
                                </td>
                                <td class="center">
                                    <?php echo  html_escape( $supplier_return->total_return_out) ?>
                                </td>
                                <td class="center">
                                    <?php echo  $totalstock = ($in +(!empty($cutomrer_return->total_return_in)?$cutomrer_return->total_return_in:0)) - ($out + (!empty($supplier_return->total_return_out)?$supplier_return->total_return_out:0)) ?>
                                </td>

                            </tr>
                            <?php
                            $sl++;
                            $total_sales = (int) $total_sales + (int) $stock->price;
                            $total_purchases = (int) $total_purchases + (int) $stock->purchase_price;
                            $total_purchases_quantity += (int) $stock->total_purchase_quantity;
                            $total_sales_quantity = (int) $total_sales_quantity + (int) $stock->total_sales_quantity;
                           $total_stock = ($total_purchases_quantity + $total_cutomer_return) - ( $total_supplier_return + $total_sales_quantity);
                            ?>

                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" align="right"><b><?php echo html_escape('Grand Total');?></b></td>
                            <td><?php echo html_escape($total_sales); ?></td>
                            <td><?php echo html_escape($total_purchases); ?></td>
                            <td><?php echo html_escape($total_purchases_quantity); ?></td>
                            <td><?php echo html_escape($total_sales_quantity); ?></td>
                            <td><?php echo html_escape($total_cutomer_return); ?></td>
                            <td><?php echo html_escape($total_supplier_return); ?></td>
                            <td><?php echo html_escape($total_stock); ?></td>
                        </tr>
                    </tfoot>
                </table>
                
            </div>
        </div>
    </div>
</div>


