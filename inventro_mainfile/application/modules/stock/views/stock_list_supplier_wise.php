<?php 
 $CI = & get_instance();
 $CI->load->model('Stock_model');
 ?>
<div class="col-sm-12">
    <button class="btn btn-primary mb-3" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="true" aria-controls="collapseExample">
        <?php echo html_escape('Search by Supplier')?>
    </button>
    <div class="card card-primary card-outline collapse in" id="collapseExample" aria-expanded="true" style="">
        <div class="card-body">
        <?php echo form_open_multipart('stock/stock/stock_report_supplier_wise', array('class' => 'form-inline', 'id' => 'stock_report_supplier_wise'))?>
            
                <label class="mr-3"><?php echo makeString(['select_supplier']); ?>:</label>
                <select class="form-control" id="supplier_id" name="supplier_id" required="">
                    <option value=""><?php echo makeString(['select_one']); ?></option>
                    <?php foreach ($suppliers as $supplier) { ?>
                        <option value="<?php echo $supplier->supplier_id; ?>"><?php echo html_escape($supplier->name); ?> </option>
                    <?php } ?>
                </select>
                <button type="submit" class="btn btn-success ml-3"><?php echo html_escape('Search')?></button>
            </form>
        </div>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h4><?php echo makeString(['stock_report_supplier_wise']) ?></h4>
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
                            <th><?php echo makeString(['sales_price']);   ?></th>
                            <th><?php echo makeString(['purchase_price']); ?></th>
                            <th><?php echo makeString(['total_purchase']); ?></th>
                            <th><?php echo makeString(['total_sales']); ?></th>
                            
                            <th><?php echo makeString(['stock']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        $total_sales = 0;
                        $total_purchases = 0;
                        $total_purchases_quantity = 0;
                        $total_sales_quantity =  $total_supplier_return = 0;
                        $total_stock = 0;
                        $total_salesreturn=0;
                        foreach ($stocks as $stock) {  
                                $this->db->select('SUM(quantity) as total_in');
                                $this->db->from('product_purchase_details');
                                $this->db->where('product_id', $stock->product_id);
                                $stock_in = $this->db->get()->row();
                                $ttl_purchase = $stock_in->total_in;

                                $this->db->select('SUM(quantity) as total_out');
                                $this->db->from('invoice_details');
                                $this->db->where('product_id', $stock->product_id);
                                $stock_out = $this->db->get()->row(); 
                                $ttle_sale = $stock_out->total_out;


                                // supplier return part 
                                $this->db->select('SUM(return_qty) as total_return_out');
                                $this->db->from('return_details');
                                $this->db->where('product_id', $stock->product_id);
                                $this->db->where('status', 2);
                                $supplier_return = $this->db->get()->row();
                                $total_supplier_return += $supplier_return->total_return_out;


                            $this->db->select('SUM(return_qty) as total_return_in');
                            $this->db->from('return_details');
                            $this->db->where('product_id', $stock->product_id);
                            $this->db->where('status', 1);
                            $cutomrer_return = $this->db->get()->row();
                            $total_salesreturn += $cutomrer_return->total_return_in;

                            ?>


                          
                            <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>">
                                <td><?php echo  html_escape($sl); ?></td>
                                <td><?php echo  html_escape($stock->product_name); ?></td>
                                <td class="center">
                                    <?php echo  html_escape($stock->model) ?>
                                </td>
                                <td class="center">
                                    <?php echo  html_escape($stock->category_name); ?>
                                </td>
                                <td class="center">
                                    <?php echo  html_escape($stock->price) ?>
                                </td>
                                <td class="center">
                                    <?php echo  html_escape($stock->purchase_price) ?>
                                </td>
                                <td class="center">
                                    <?php echo  $in = (!empty($ttl_purchase)?$ttl_purchase:0); ?>
                                </td>
                                <td class="center">
                                    <?php echo  $out  = (!empty($ttle_sale)?$ttle_sale:0); ?>
                                </td>
                               
                                <td class="center">
                                    <?php  
                                    $supplier_return = $supplier_return->total_return_out;
                                    echo $totalstock = ($in + (!empty($cutomrer_return->total_return_in)?$cutomrer_return->total_return_in:0) - ($out + (!empty($supplier_return->total_return_out)?$supplier_return->total_return_out:0) )) ?>
                                </td>

                            </tr>
                            <?php
                            $sl++;
                            $total_sales = (int) $total_sales + (int) $stock->price;
                            $total_purchases = (int) $total_purchases + (int) $stock->purchase_price;
                            $total_purchases_quantity += (int) $in;
                            $total_sales_quantity = (int) $total_sales_quantity + (int) $out;
                           $total_stock = ($total_purchases_quantity +(!empty($total_salesreturn)?$total_salesreturn:0) ) - ($total_sales_quantity + (!empty($total_supplier_return)?$total_supplier_return:0));
                            ?>

                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" align="right"><b><?php echo html_escape('Grand Total')?></b></td>
                            <td><?php echo html_escape($total_sales); ?></td>
                            <td><?php echo html_escape($total_purchases); ?></td>
                            <td><?php echo html_escape($total_purchases_quantity); ?></td>
                            <td><?php echo html_escape($total_sales_quantity); ?></td>
                            
                            <td><?php echo html_escape($total_stock); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>


