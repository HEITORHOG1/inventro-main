<link rel="stylesheet" href="<?php echo base_url('application/modules/invoice/assets/css/invoice.css'); ?>">
<div class="card card-primary card-outline">
    <div class="card-header">
 
        <?php echo form_open_multipart('return/returns/customer_return')?>
                   <div class="col-sm-12" >
          <div class="form-group row">
            
             <label for="invoice_id" class="col-sm-2 col-form-label"><?php echo makeString(['invoice_id']); ?> <i class="text-danger"> * </i></label>
                                <div class="col-sm-4">
                                    <input type="text" required class="form-control" name="invoiceid" id="invoice" value="<?php echo (!empty($invoiceid)?$invoiceid:'')?>">
                                </div>
                                <div class="col-sm-2">
                                    <button type="submit" class="btn btn-success form-control"><?php echo html_escape('Search')?></button>
                                </div>
                           
          </div>
      </div>
       </form>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-body">
          <?php if(!empty($invoiceid)){ ?>
                
                <?php echo form_open_multipart('return/returns/save_customer_return')?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label for="customer_id" class="col-sm-2 col-form-label"><?php echo makeString(['customer_name']); ?> <i class="text-danger"> * </i></label>
                                <div class="col-sm-4">
                                    <input type="text" name="customer_name" class="form-control" value="<?php echo html_escape($edit_invoice->customer_name);?>" readonly>
                                    <input type="hidden" name="customer_id" id="customer_id" value="<?php echo html_escape($edit_invoice->customer_id);?>">
                                    
                                </div>
                                <label for="date" class="col-sm-2 col-form-label"><?php echo makeString(['return']).' '.makeString(['date']); ?> <i class="text-danger"> * </i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control datepicker" name="date" id="date" required="" value="<?php echo date('Y-m-d');?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-6" id="payment_from_1">
                                    <div class="form-group row">
                                        <label for="payment_type" class="col-sm-4 col-form-label"><?php echo makeString(['payment_type']); ?> <i class="text-danger">*</i></label>
                                        <div class="col-sm-6">
                                            <select name="paytype" class="form-control select2" required="" onchange="returnBank_paymet(this.value)" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                                <option value=""></option>
                                                <option value="1" <?php
                                                if ($edit_invoice->payment_method == 1) {
                                                    echo html_escape('selected');
                                                }
                                                ?>><?php echo makeString(['cash_payment']) ?></option>
                                                <option value="2" <?php
                                                if ($edit_invoice->payment_method == 2) {
                                                    echo html_escape('selected');
                                                }
                                                ?>><?php echo makeString(['bank_payment']) ?></option>
                                                <option value="3" <?php
                                                if ($edit_invoice->payment_method == 3) {
                                                    echo html_escape('selected');
                                                }
                                                ?>><?php echo makeString(['due_payment']) ?></option> 
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="bank_area">
                                        <?php if ($edit_invoice->payment_method == 2) { ?>
                                            <div class="form-group row bankdiv_selected displayflex" id="bankdiv_old">
                                                <label for="bank" class="col-sm-4 col-form-label"><?php
                                                    echo makeString(['bank_name']);
                                                    ?> <i class="text-danger">*</i></label>
                                                <select class="form-control select2 col-sm-8" name="bank_id" id="bank_id" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                                    <option value=""></option>
                                                    <?php foreach ($bank_list as $bank) { ?>
                                                        <option value="<?php echo $bank->bank_id; ?>" <?php
                                                        if ($edit_invoice->bank_id == $bank->bank_id) {
                                                            echo html_escape('selected');
                                                        }
                                                        ?>>
                                                                    <?php echo html_escape($bank->bank_name); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>                                     
                                            </div>
                                        <?php } else { ?>
                                            <div class="form-group row" id="bank_div">
                                                <label for="bank" class="col-sm-4 col-form-label"><?php echo makeString(['bank_name']); ?> <i class="text-danger">*</i></label>
                                                <select class="form-control select2 col-sm-8" name="bank_id" id="bank_id" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                                    <option value=""><?php echo makeString(['select_one']); ?></option>
                                                </select>  
                                            </div>
                                        <?php } ?>
                                    </div>       
                                </div>       
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover" id="returntable">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="20%"><?php echo makeString(['item_name']); ?> </th>
                                        <th class="text-center" width="10%"><?php echo makeString(['sold_qty']); ?> </th>
                                        <th class="text-center" width="12%"><?php echo makeString(['return_qty']); ?> </th>
                                        <th class="text-center" width="10%"><?php echo makeString(['box_qty']); ?> </th>
                                        <th class="text-center" width="15%"><?php echo makeString(['rate']); ?> </th>
                                        
                                        <th class="text-center" width="15%"><?php echo makeString(['total_price']); ?>  </th>
                                        <th class="text-center" width="5%"><?php echo makeString(['action']); ?>  </th>
                                    </tr>
                                </thead>
                                <tbody id="addinvoiceItem">
                                    <?php
                                    $sl = 0;
                                    foreach ($edit_invoicedetails as $single) {
                                        $available_productinfo = 0;
                                        $sl++;
                                        ?>
                                        <tr>
                                            <td>
                                                <input type="text" name="product_name" class="form-control" value="<?php echo html_escape($single->name); ?>" readonly>
                                                <input type="hidden" name="product_id[]" value="<?php echo html_escape($single->product_id);?>">
                                               
                                            </td>
                                            <td class="text-right">
                                                <input type="text" name="soldqty[]" class="form-control text-right soldqty" id="soldqty_<?php echo $sl; ?>" readonly value="<?php echo html_escape($single->quantity); ?>">
                                            </td>
                                            <td class="text-right">
                                                <input type="text" name="product_quantity[]" id="quantity_<?php echo $sl; ?>" class="form-control text-right common_qnt store_cal_<?php echo $sl; ?>" onkeyup="Return_calculate('<?php echo $sl; ?>');" onchange="Return_calculate('<?php echo $sl; ?>');" placeholder="0.00" value="" min="0" tabindex="6"/>
                                            </td>
                                            <td class="text-right">
                                                <input type="text" name="box_quantity[]" id="box_quantity_<?php echo $sl; ?>" class="form-control text-right common_qnt store_cal_<?php echo $sl; ?>" onkeyup="Return_calculate('<?php echo $sl; ?>');" onchange="Return_calculate(<?php echo $sl; ?>);" placeholder="0.00" value="<?php
                                                $box_qty = $single->quantity / $single->cartoon_qty;
                                                echo number_format($box_qty, 2)
                                                ?>" min="0" tabindex="6" readonly/>
                                                <input type="hidden" name="" id="boxqty_hide_<?php echo $sl; ?>" class="form-control" value="<?php echo html_escape($single->cartoon_qty); ?>">
                                            </td>
                                            <td class="test">
                                                <input type="text" name="product_rate[]" onkeyup="Return_calculate('<?php echo $sl; ?>');" onchange="Return_calculate('<?php echo $sl; ?>');" id="product_rate_<?php echo $sl; ?>" class="form-control common_rate product_rate_<?php echo $sl; ?> text-right" placeholder="0.00" value="<?php echo html_escape($single->price); ?>" min="0" tabindex="7" readonly required=""/>
                                            </td>
                                           
                                            <td class="text-right">
                                                <input class="form-control total_price text-right " type="text" name="total_price[]" id="total_price_<?php echo $sl; ?>" value="<?php echo html_escape($single->total_price); ?>" readonly="readonly" />
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-danger btn-xs" type="button" value="Delete" onclick="deleteItem(this)"><i class="fa fa-trash"> </i></button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" rowspan="3">
                                <center>
                                    <label for="details" class="  col-form-label"><?php echo makeString(['reason']); ?></label>
                                </center>
                                <textarea name="details" class="form-control" placeholder="<?php echo makeString(['reason']); ?>"></textarea>
                                </td>
                                <td class="text-right" colspan="2"><b><?php echo makeString(['invoice_discount']); ?> </b>:</td>
                                <td class="text-right">
                                
                                     <input type="text"   class="form-control text-right" name="inv_discount" placeholder="0.00" value="<?php echo html_escape($edit_invoice->total_discount); ?>" id="invdiscount" readonly />
                                </td>
                                </tr>
                                 <tr>
                                    <td class="text-right" colspan="2"><b><?php echo makeString(['deduction']); ?> </b>:</td>
                                    <td class="text-right">
                                           <input type="text" onkeyup="Return_calculate(1);"  onchange="Return_calculate('<?php echo $sl; ?>');" id="deduction" class="form-control text-right" name="invoice_discount" placeholder="0.00" value="" />
                                           <input type="hidden" name="invoice_id" value="<?php echo html_escape($edit_invoice->invoice_id);?>">
                                        
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td class="text-right" colspan="2"><b><?php echo makeString(['grand_total']); ?> </b>:</td>
                                    <td class="text-right">
                                        <input type="hidden" onkeyup="Return_calculate('<?php echo $sl; ?>');"  onchange="Return_calculate('<?php echo $sl; ?>');" id="total_discount" class="form-control text-right" name="total_discount" placeholder="0.00" value="<?php echo html_escape($edit_invoice->total_discount); ?>" readonly />
                                         <input type="text" id="grandTotal" class="form-control text-right" name="grand_total_price" value="" readonly="readonly" />
                                    </td>
                                </tr>
                                
                               
                                </tfoot>
                            </table> 
                            <div class="row text-right">
                                <button type="submit" class="btn btn-success"><?php echo html_escape('Return');?></button>
                            </div> 
                        </div>
                    </div>
                </form>
<?php } ?>
            
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
 <script src="<?php echo base_url() ?>application/modules/return/assets/js/return.js" type="text/javascript"></script>     

