<link rel="stylesheet" href="<?php echo base_url('application/modules/invoice/assets/css/invoice.css'); ?>">
<div class="card card-primary card-outline">
    <div class="card-header">
        Invoice Edit<?php echo makeString(['invoice_edit']); ?>
        <small class="float-right">
            <a href="<?php echo base_url('invoice/invoice/invoice_list'); ?>" class="btn btn-primary"> 
                <i class="ti-plus" aria-hidden="true"></i><?php echo makeString(['invoice_list']); ?>
            </a>
        </small>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-body">
                <?php echo  form_open('invoice/invoice/invoice_update'); ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label for="customer_id" class="col-sm-2 col-form-label"><?php echo makeString(['customer_name']); ?> <i class="text-danger"> * </i></label>
                                <div class="col-sm-4">
                                    <select class="form-control select2" name="customer_id" id="customer_id" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                        <option value=""></option>
                                        <?php foreach ($get_customer as $customer) { ?>
                                            <option value="<?php echo html_escape($customer->customerid); ?>" <?php
                                            if ($edit_invoice->customer_id == $customer->customerid) {
                                                echo 'selected';
                                            }
                                            ?>>
                                                        <?php echo html_escape($customer->name); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <label for="date" class="col-sm-2 col-form-label"><?php echo makeString(['date']); ?> <i class="text-danger"> * </i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control datepicker" name="date" id="date" required="" value="<?php echo html_escape($edit_invoice->date); ?>">
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
                                            <select name="paytype" class="form-control select2" required="" onchange="bank_paymet(this.value)" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                                <option value=""></option>
                                                <option value="1" <?php
                                                if ($edit_invoice->payment_method == 1) {
                                                    echo 'selected';
                                                }
                                                ?>><?php echo makeString(['cash_payment']) ?></option>
                                                <option value="2" <?php
                                                if ($edit_invoice->payment_method == 2) {
                                                    echo 'selected';
                                                }
                                                ?>><?php echo makeString(['bank_payment']) ?></option>
                                                <option value="3" <?php
                                                if ($edit_invoice->payment_method == 3) {
                                                    echo 'selected';
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
                                                            echo 'selected';
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
                            <table class="table table-bordered table-hover" id="normalinvoice">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="20%"><?php echo makeString(['item_name']); ?> </th>
                                        <th class="text-center" width="10%"><?php echo makeString(['stock']); ?> </th>
                                        <th class="text-center" width="10%"><?php echo makeString(['unit_qty']); ?> </th>
                                        <th class="text-center" width="10%"><?php echo makeString(['box_qty']); ?> </th>
                                        <th class="text-center" width="10%"><?php echo makeString(['rate']); ?> </th>
                                        <th class="text-center" width="12%"><?php echo makeString(['discount']); ?>  %</th>
                                        <th class="text-center" width="15%"><?php echo makeString(['total_price']); ?>  </th>
                                        <th class="text-center" width="13%"><?php echo makeString(['action']); ?>  </th>
                                    </tr>
                                </thead>
                                <tbody id="addinvoiceItem">
                                    <?php
                                    $sl = 0;
                                    foreach ($edit_invoicedetails as $single) {
                                        $available_productinfo = $this->Invoice_model->get_only_service_info($single->product_id);
                                        $sl++;
                                        ?>
                                        <tr>
                                            <td>
                                                <select class="form-control product_id select2 common_product" id="product_id_<?php echo $sl; ?>" onchange="service_cals('<?php echo $sl; ?>')" name="product_id[]" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                                    <option value=""></option>
                                                    <?php foreach ($get_products as $products) { ?>
                                                        <option value="<?php echo html_escape($products->product_id); ?>" <?php
                                                        if ($single->product_id == $products->product_id) {
                                                            echo 'selected';
                                                        }
                                                        ?>>
                                                                    <?php echo html_escape($products->name); ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td class="text-right">
                                                <input type="text" name="available_qnt[]" class="form-control text-right common_available_qtn" id="available_qnt_<?php echo $sl; ?>" readonly value="<?php echo $available_productinfo['total_product']; ?>">
                                            </td>
                                            <td class="text-right">
                                                <input type="text" name="product_quantity[]" id="quantity_<?php echo $sl; ?>" class="form-control text-right common_qnt store_cal_<?php echo $sl; ?>" onkeyup="quantity_calculate('<?php echo $sl; ?>');" onchange="quantity_calculate('<?php echo $sl; ?>');" placeholder="0.00" value="<?php echo $single->quantity; ?>" min="0" tabindex="6"/>
                                            </td>
                                            <td class="text-right">
                                                <input type="text" name="box_quantity[]" id="box_quantity_<?php echo $sl; ?>" class="form-control text-right common_qnt store_cal_<?php echo $sl; ?>" onkeyup="quantity_calculate('<?php echo $sl; ?>');" onchange="quantity_calculate(<?php echo $sl; ?>);" placeholder="0.00" value="<?php
                                                $box_qty = $single->quantity / $single->cartoon_qty;
                                                echo number_format($box_qty, 2)
                                                ?>" min="0" tabindex="6" readonly/>
                                                <input type="hidden" name="" id="boxqty_hide_<?php echo $sl; ?>" class="form-control" value="<?php echo $single->cartoon_qty; ?>">
                                            </td>
                                            <td class="test">
                                                <input type="text" name="product_rate[]" onkeyup="quantity_calculate('<?php echo $sl; ?>');" onchange="quantity_calculate('<?php echo $sl; ?>');" id="product_rate_<?php echo $sl; ?>" class="form-control common_rate product_rate_<?php echo $sl; ?> text-right" placeholder="0.00" value="<?php echo html_escape($single->price); ?>" min="0" tabindex="7" required=""/>
                                            </td>
                                            <td class="test">
                                                <input type="text" name="product_discount[]" onkeyup="quantity_calculate('<?php echo $sl; ?>');" onchange="quantity_calculate('<?php echo $sl; ?>');" id="product_discount_<?php echo $sl; ?>" class="form-control common_discount product_discount_<?php echo $sl; ?> text-right" placeholder="0.00" value="<?php echo html_escape($single->discount); ?>" min="0" tabindex="7"/>
                                            </td>
                                            <td class="text-right">
                                                <input class="form-control total_price text-right common_totalprice" type="text" name="total_price[]" id="total_price_<?php echo $sl; ?>" value="<?php echo html_escape($single->total_price); ?>" readonly="readonly" />
                                            </td>
                                            <td class="text-center">
                                                <input type="hidden" id="all_discount_<?php echo $sl; ?>" class="all_discount" name="discount_amount[]" />
                                                <button class="btn btn-danger btn-xs" type="button" value="Delete" onclick="deleteRow(this)"><i class="fa fa-trash"> </i></button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" rowspan="3">
                                <center>
                                    <label for="details" class="  col-form-label"><?php echo makeString(['description']); ?></label>
                                </center>
                                <textarea name="details" class="form-control" placeholder="<?php echo makeString(['description']); ?>"><?php echo html_escape($edit_invoice->description); ?></textarea>
                                </td>
                                <td class="text-right" colspan="2"><b><?php echo makeString(['invoice_discount']); ?> </b>:</td>
                                <td class="text-right">
                                    <input type="text" onkeyup="quantity_calculate(1);"  onchange="quantity_calculate('<?php echo $sl; ?>');" id="invoice_discount" class="form-control text-right" name="invoice_discount" placeholder="0.00" value="<?php echo html_escape($edit_invoice->invoice_discount); ?>" />
                                </td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="2"><b><?php echo makeString(['total_discount']); ?> </b>:</td>
                                    <td class="text-right">
                                        <input type="text" onkeyup="quantity_calculate('<?php echo $sl; ?>');"  onchange="quantity_calculate('<?php echo $sl; ?>');" id="total_discount" class="form-control text-right" name="total_discount" placeholder="0.00" value="<?php echo html_escape($edit_invoice->total_discount); ?>" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"  class="text-right"><b><?php echo makeString(['grand_total']); ?>  :</b></td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right" name="grand_total_price" value="<?php echo html_escape($edit_invoice->total_amount); ?>" readonly="readonly" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input id="add-invoice-item" class="btn btn-info" name="add-new-item" onclick="addInputField('addinvoiceItem');" value="Add New<?php //echo makeString(['add_new');                                                         ?> " type="button">
                                    </td>
                                    <td class="text-right" colspan="5"><b><?php echo makeString(['paid_amount']); ?>  :</b></td>
                                    <td class="text-right">
                                        <input type="text" id="paidAmount" 
                                               onkeyup="invoice_paidamount();" class="form-control text-right" name="paid_amount" placeholder="0.00" value="<?php echo html_escape($edit_invoice->paid_amount); ?>" tabindex="13"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="hidden" name="invoice_id" value="<?php echo html_escape($edit_invoice->invoice_id); ?>">
                                    
                                        <input type="submit" id="add_invoice" class="btn btn-success" name="add-invoice" value="Update" tabindex="15"/>
                                    </td>

                                    <td class="text-right" colspan="5"><b><?php echo makeString(['due']); ?> :</b></td>
                                    <td class="text-right">
                                        <input type="text" id="dueAmmount" class="form-control text-right" name="due_amount" value="<?php echo html_escape($edit_invoice->due_amount); ?>" readonly="readonly"/>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>  
                        </div>
                    </div>
                    <?php echo form_close() ?>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">


