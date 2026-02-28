<link rel="stylesheet" href="<?php echo base_url('application/modules/invoice/assets/css/invoice.css'); ?>">
<div class="">
    <?php
    $error = $this->session->flashdata('error');
    $success = $this->session->flashdata('success');
    if ($error != '') {
        echo $error;
    }
    if ($success != '') {
        echo $success;
    }
    ?>
</div>
<div class="card card-primary card-outline">
    <?php if ($this->permission->method('customer', 'create')->access()): ?>
        <div class="card-header">
            <?php echo makeString(['add_invoice']); ?>
            <small class="float-right">
                <a href="<?php echo base_url('invoice/Invoice/invoice_list'); ?>" class="btn btn-primary"> 
                    <i class="ti-plus" aria-hidden="true"></i><?php echo makeString(['invoice_list']); ?>
                </a>
            </small>
        </div>
    <?php endif; ?>

    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
          
                <?php echo  form_open('invoice/invoice/invoice_save'); ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row">
                                <label for="customer_id" class="col-sm-2 col-form-label"><?php echo makeString(['customer_name']); ?> <i class="text-danger"> * </i></label>
                                <div class="col-sm-4">
                                    <select class="form-control select2" name="customer_id" id="customer_id" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                        <option value=""></option>
                                        <?php foreach ($get_customer as $customer) { ?>
                                            <option value="<?php echo $customer->customerid; ?>">
                                                <?php echo $customer->name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <label for="date" class="col-sm-2 col-form-label"><?php echo makeString(['date']); ?> <i class="text-danger"> * </i></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control datepicker" name="date" id="date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" id="payment_from_1">
                            <div class="form-group row">
                                <label for="payment_type" class="col-sm-4 col-form-label"><?php
                                    echo makeString(['payment_type']);
                                    ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <select name="paytype" class="form-control select2" required="" onchange="bank_paymet(this.value)" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                        <option value=""></option>
                                        <option value="1"><?php echo makeString(['cash_payment']) ?></option>
                                        <option value="2"><?php echo makeString(['bank_payment']) ?></option>
                                        <option value="3"><?php echo makeString(['due_payment']) ?></option> 
                                    </select>
                                </div>
                            </div>
                        </div>
                      <div class="col-sm-6">
                            <div class="bank_area">
                                <div class="form-group row" id="bank_div">
                                    <label for="bank" class="col-sm-4 col-form-label"><?php echo makeString(['bank_name']);
                                    ?> <i class="text-danger">*</i></label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2" name="bank_id" id="bank_id" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                            <option value=""><?php echo makeString(['select_one']); ?></option>
                                        </select>  
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
                                    <tr>
                                        <td>
                                            <select class="form-control product_id select2 common_product" id="product_id_1" onchange="service_cals(1)" name="product_id[]" data-placeholder='<?php echo makeString(['select_one']); ?>'>
                                                <option value=""></option>
                                                <?php
                                                foreach ($get_products as $products) {
                                                    echo "<option value=" . $products->product_id . ">" . $products->name . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td class="text-right">
                                            <input type="text" name="available_qnt[]" class="form-control text-right common_available_qtn" id="available_qnt_1" readonly>
                                        </td>
                                        <td class="text-right">
                                            <input type="text" name="product_quantity[]" id="quantity_1" class="form-control text-right common_qnt store_cal_1" onkeyup="quantity_calculate(1);" onchange="quantity_calculate(1);" placeholder="0.00" value="" min="0" tabindex="6"/>
                                        </td>
                                        <td class="text-right">
                                            <input type="text" name="box_quantity[]" id="box_quantity_1" class="form-control text-right common_qnt store_cal_1" onkeyup="quantity_calculate(1);" onchange="quantity_calculate(1);" placeholder="0.00" value="" min="0" tabindex="6" readonly/>
                                            <input type="hidden" name="" id="boxqty_hide_1" class="form-control">
                                        </td>
                                        <td class="test">
                                            <input type="text" name="product_rate[]" onkeyup="quantity_calculate(1);" onchange="quantity_calculate(1);" id="product_rate_1" class="form-control common_rate product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" required=""/>
                                        </td>
                                        <td class="test">
                                            <input type="text" name="product_discount[]" onkeyup="quantity_calculate(1);" onchange="quantity_calculate(1);" id="product_discount_1" class="form-control common_discount product_discount_1 text-right" placeholder="0.00" value="" min="0" tabindex="7"/>
                                        </td>
                                        <td class="text-right">
                                            <input class="form-control total_price text-right common_totalprice" type="text" name="total_price[]" id="total_price_1" value="0.00" readonly="readonly" />
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" id="all_discount_1" class="all_discount" name="discount_amount[]" />
                                            <button class="btn btn-danger btn-xs text-right" type="button" value="Delete" onclick="deleteRow(this)"><i class="fa fa-trash"> </i></button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" rowspan="3">
                                <center><label for="details" class="text-center col-form-label"><?php echo makeString(['description']); ?></label></center>
                                <textarea name="details" class="form-control" placeholder="Detalhes da Fatura"></textarea>
                                </td>
                                <td class="text-right" colspan="2"><b><?php echo makeString(['invoice_discount']); ?> </b>:</td>
                                <td class="text-right">
                                    <input type="text" onkeyup="quantity_calculate(1);"  onchange="quantity_calculate(1);" id="invoice_discount" class="form-control text-right" name="invoice_discount" placeholder="0.00"  />
                                </td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="2"><b><?php echo makeString(['total_discount']); ?> </b>:</td>
                                    <td class="text-right">
                                        <input type="text" onkeyup="quantity_calculate(1);"  onchange="quantity_calculate(1);" id="total_discount" class="form-control text-right" name="total_discount" placeholder="0.00" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"  class="text-right"><b> <?php echo makeString(['grand_total']); ?>  :</b></td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right" name="grand_total_price" value="0.00" readonly="readonly" />
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input id="add-invoice-item" class="btn btn-info" name="add-new-item" onclick="addInputField('addinvoiceItem');" value="<?php echo makeString(['add_new']);       ?> " type="button">
                                    </td>
                                    <td class="text-right" colspan="5"><b> <?php echo makeString(['paid_amount']); ?>  :</b></td>
                                    <td class="text-right">
                                        <input type="text" id="paidAmount" onkeyup="invoice_paidamount();" class="form-control text-right" name="paid_amount" placeholder="0.00" tabindex="13"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <input type="submit" id="add_invoice" class="btn btn-success" name="add-invoice" value="<?php echo makeString(['submit']); ?> " tabindex="15"/>
                                    </td>

                                    <td class="text-right" colspan="5"><b><?php echo makeString(['due']); ?> :</b></td>
                                    <td class="text-right">
                                        <input type="text" id="dueAmmount" class="form-control text-right" name="due_amount" value="0.00" readonly="readonly"/>
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
<script src="<?php echo base_url('application/modules/invoice/assets/js/script.js') ?>" type="text/javascript"></script> 

