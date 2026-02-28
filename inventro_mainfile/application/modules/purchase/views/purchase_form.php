<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>application/modules/purchase/assets/css/style.css">
<div class="row">
    <div class="col-sm-12">

        <div class="card card-primary card-outline">
            <div class="card-header">

                <h3 class="card-title"><?php echo  makeString(['new_purchase'])?></h3>
            </div>
            <div class="card-body">
                <?php echo form_open_multipart('purchase/purchase/create_purchase',array('class' => 'form-vertical', 'id' => 'insert_purchase','name' => 'insert_purchase'))?>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label"><?php echo makeString(['supplier']); ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('supplier_id',$supplier_list,'','class="form-control select2 supplierlist" id="supplier_id"') ?>


                            </div>
                        </div>
                    </div>
 
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label"><?php echo makeString(['purchase_date']); ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <?php $date = date('Y-m-d'); ?>
                                <input type="text" tabindex="2" class="form-control datepicker" name="purchase_date"
                                    value="<?php echo date('Y-m-d'); ?>" id="date" required />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="invoice_no" class="col-sm-4 col-form-label"><?php echo makeString(['chalan_no']); ?>
                                <i class="text-danger"></i>
                            </label>
                            <div class="col-sm-6">
                                <input type="text" tabindex="3" class="form-control" name="chalan_no"
                                    placeholder="<?php echo makeString(['chalan_no']) ?>" id="invoice_no" />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="adress" class="col-sm-4 col-form-label"><?php echo makeString(['details']); ?>
                            </label>
                            <div class="col-sm-8">
                                <textarea class="form-control" tabindex="4" id="adress" name="purchase_details"
                                    placeholder=" <?php echo makeString(['details']) ?>" rows="1"></textarea>
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
                                <select name="paytype" class="form-control select2" required=""
                                    onchange="bankPaymets(this.value)">
                                    <option value="1"><?php echo  makeString(['cash_payment'])?></option>
                                    <option value="2"><?php echo  makeString(['bank_payment'])?></option>
                                    <option value="3"><?php echo  makeString(['due_payment'])?></option>
                                </select>



                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6 bankdiv" id="bank_div">
                        <div class="form-group row">
                            <label for="bank" class="col-sm-4 col-form-label"><?php
                                    echo makeString(['bank_name']);
                                    ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php echo form_dropdown('bank_id',$bank_list,'','class="form-control select2" id="bank_idss"') ?>

                            </div>

                        </div>
                    </div>
                </div>


                <div class="table-responsive purchaselisttable">
                    <table class="table table-bordered table-hover" id="purchaseTable">
                        <thead>
                            <tr>
                                <th class="text-center" id="itemhead"><?php echo makeString(['item_name']); ?><i
                                        class="text-danger">*</i></th>
                                <th class="text-center"><?php echo makeString(['stock']);?></th>
                                <th class="text-center"><?php echo makeString(['unit_qty']); ?> <i class="text-danger">*</i>
                                </th>
                                <th class="text-center"><?php echo makeString(['box_qty']); ?> <i class="text-danger">*</i>
                                </th>
                                <th class="text-center"><?php echo makeString(['rate']); ?><i class="text-danger">*</i></th>
                                <th class="text-center" style="width:200px;"><?php echo makeString(['total']); ?></th>
                                <th class="text-center"><?php echo makeString(['action']) ?></th>
                            </tr>
                        </thead>
                        <tbody id="addPurchaseItem">
                            <tr>
                                <td class="span3 supplier">
                                    <input type="text" name="product_name" required
                                        class="form-control product_name productSelection" onkeypress="productList(1);"
                                        placeholder="<?php echo makeString(['item_name']) ?>" id="product_name_1"
                                        tabindex="5">

                                    <input type="hidden" class="autocompletevalue product_id_1" name="product_id[]"
                                        id="hiddenid" />

                                    <input type="hidden" class="sl" value="1">
                                </td>

                                <td class="wt">
                                    <input type="text" id="available_quantity_1"
                                        class="form-control text-right stock_ctn_1" placeholder="0.00" readonly />
                                </td>

                                <td class="text-right">
                                    <input type="text" name="product_quantity[]" id="uqty1" value="1"
                                        class="form-control text-right store_cal_1" onkeyup="calculate_store(1);"
                                        onchange="calculate_store(1);" placeholder="0.00" value="" min="0"
                                        tabindex="6" />
                                </td>
                                <td class="text-right">
                                    <input type="hidden" name="" id="boxamount_1">
                                    <input type="text" name="box_qty[]" id="box_qty_1"
                                        class="form-control text-right box_qty_1" placeholder="0.00" value=""
                                        tabindex="7" readonly="" />
                                </td>
                                <td class="test">
                                    <input type="text" name="product_rate[]" onkeyup="calculate_store(1);"
                                        onchange="calculate_store(1);" id="product_rate_1"
                                        class="form-control product_rate_1 text-right" placeholder="0.00" value=""
                                        min="0" tabindex="8" />
                                </td>


                                <td class="text-right">
                                    <input class="form-control total_price text-right" type="text" name="total_price[]"
                                        id="total_price_1" value="0.00" readonly="readonly" />
                                </td>
                                <td>



                                    <button class="btn btn-danger content-width1 red" type="button"
                                        value="<?php echo makeString(['delete'])?>" onclick="deleteRow(this)" tabindex="8"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="content-width1" colspan="5"><b><?php echo makeString(['discount']); ?>:</b></td>
                                <td class="text-right">
                                    <input type="text" id="discount" class="text-right form-control" name="discount"
                                        value="" placeholder="0.00" onkeyup="calculate_store(1);" />
                                </td>
                                <td> <button type="button" id="add_purchase_item" class="btn btn-info"
                                        name="add-invoice-item" onclick="addPurchaseOrderField1('addPurchaseItem');"
                                        tabindex="9"><i class="fa fa-plus"></i></button>

                                    <input type="hidden" name="baseUrl" class="baseUrl"
                                        value="<?php echo base_url();?>" />
                                </td>

                            </tr>
                            <tr>

                                <td class="content-width1" colspan="5"><b><?php echo makeString(['grand_total']); ?>:</b>
                                </td>
                                <td class="text-right">
                                    <input type="text" id="grandTotal" class="text-right form-control"
                                        name="grand_total_price" value="0.00" readonly="readonly" />
                                </td>

                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="form-group row">
                    <div class="col-sm-6">
                        <input type="submit" id="add_purchase" class="btn btn-success btn-large" name="add-purchase"
                            value="<?php echo makeString(['save']) ?>" />

                    </div>
                </div>
                <?php echo form_close()?>
            </div>
        </div>
    </div>
</div>
 
<script src="<?php echo base_url() ?>application/modules/purchase/assets/js/purchase.js" type="text/javascript">
</script>