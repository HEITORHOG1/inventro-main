<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>application/modules/purchase/assets/css/style.css">
<div class="row">
    <div class="col-sm-12">
        <div class="card card-primary">
            <div class="card-heading">
                <div class="card-title">
                    <h4><?php echo makeString(['edit_purchase']) ?></h4>
                </div>
            </div>

            <div class="card-body">
                <?php echo form_open_multipart('purchase/purchase/update_purchase',array('class' => 'form-vertical', 'id' => 'purchase_update'))?>


                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss"
                                class="col-sm-4 col-form-label"><?php echo makeString(['supplier']) ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('supplier_id',$supplier_list,$supplier_id,'class="form-control select2" id="supplier_id" style="width:100%"') ?>
                            </div>


                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="date"
                                class="col-sm-4 col-form-label"><?php echo makeString(['purchase_date']); ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <?php $date = date('Y-m-d'); ?>
                                <input type="text" tabindex="2" class="form-control datepicker" name="purchase_date"
                                    value="<?php echo html_escape($purchase_date);?>" id="date" required />
                                <input type="hidden" name="purchase_id" value="<?php echo html_escape($purchase_id);?>">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="invoice_no"
                                class="col-sm-4 col-form-label"><?php echo makeString(['chalan_no']); ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <input type="text" tabindex="3" class="form-control" name="chalan_no"
                                    placeholder="<?php echo makeString(['chalan_no']) ?>" id="invoice_no" required
                                    value="<?php echo html_escape($chalan_no);?>" />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="adress" class="col-sm-4 col-form-label"><?php echo makeString(['details']); ?>
                            </label>
                            <div class="col-sm-6">
                                <textarea class="form-control" tabindex="4" id="adress" name="purchase_details"
                                    placeholder=" <?php echo makeString(['details']) ?>"
                                    rows="1"><?php echo html_escape($purchase_details);?></textarea>
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
                                    <option value="">Select Payment Option</option>
                                    <option value="1" <?php if($paytype ==1){echo 'selected';}?>>
                                        <?php echo makeString(['cash_payment']);?></option>
                                    <option value="2" <?php if($paytype ==2){echo 'selected';}?>>
                                        <?php echo makeString(['bank_payment']);?></option>
                                    <option value="3" <?php if($paytype ==3){echo 'selected';}?>>
                                        <?php echo makeString(['due_payment']);?></option>
                                </select>



                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_div"
                        style="display: <?php if($paytype == 2){echo 'block';}else{echo 'none';}?>;">
                        <div class="form-group row">
                            <label for="bank" class="col-sm-4 col-form-label"><?php echo makeString(['bank_name']);?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('bank_id',$bank_list,$bank_id,'class="form-control select2" id="bank_idss"') ?>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="table-responsive purchaselisttable">
                    <table class="table table-bordered table-hover" id="purchaseTable">
                        <thead>
                            <tr>
                                <th class="text-center" width="20%"><?php echo makeString(['item_name']);?><i
                                        class="text-danger">*</i></th>
                                <th class="text-center"><?php echo makeString(['stock']);?></th>
                                <th class="text-center"><?php echo makeString(['unit_qty']);?> <i
                                        class="text-danger">*</i></th>
                                <th class="text-center"><?php echo makeString(['box_qty']);?> <i
                                        class="text-danger">*</i></th>
                                <th class="text-center"><?php echo makeString(['rate']);?><i class="text-danger">*</i>
                                </th>
                                <th class="text-center"><?php echo makeString(['total']);?></th>
                                <th class="text-center"><?php echo makeString(['action']) ?></th>
                            </tr>
                        </thead>
                        <tbody id="addPurchaseItem">

                            <?php $sl=1;
                                    
                                     foreach($purchase_info as $purchasedetails){ ?>
                            <tr>
                                <td class="prod">
                                    <input type="text" name="product_name" required
                                        class="form-control product_name productSelection"
                                        onkeypress="productList(<?php echo  $sl?>);"
                                        placeholder="<?php echo makeString(['product_name']) ?>" id="product_name_1"
                                        tabindex="5"
                                        value="<?php echo $purchasedetails['product_name'].'('.$purchasedetails['product_model'].')';?>">

                                    <input type="hidden" class="autocompletevalue product_id_<?php echo $sl?>"
                                        name="product_id[]" value="<?php echo $purchasedetails['product_id']?>"
                                        id="hiddenid" />

                                    <input type="hidden" class="sl" value="<?php echo $sl?>">
                                </td>

                                <td class="wt">
                                    <input type="text" id="available_quantity_<?php echo $sl?>"
                                        class="form-control text-right stock_ctn_<?php echo $sl?>" placeholder="0.00"
                                        readonly />
                                </td>

                                <td class="text-right">
                                    <input type="text" name="product_quantity[]" id="uqty<?php echo $sl?>"
                                        class="form-control text-right store_cal_<?php echo $sl?>"
                                        onkeyup="calculate_store(<?php echo $sl?>);"
                                        onchange="calculate_store(<?php echo $sl?>);" placeholder="0.00"
                                        value="<?php echo $purchasedetails['quantity']?>" min="0" tabindex="6" />
                                </td>
                                <td class="text-right">
                                    <input type="hidden" name="" id="boxamount_<?php echo $sl?>"
                                        value="<?php echo $purchasedetails['cartoon_qty']?>">
                                    <input type="text" name="box_qty[]" id="box_qty_<?php echo $sl?>"
                                        class="form-control text-right box_qty_<?php echo $sl?>" placeholder="0.00"
                                        value="" tabindex="7" readonly="" />
                                </td>
                                <td class="test">
                                    <input type="text" name="product_rate[]"
                                        onkeyup="calculate_store(<?php echo $sl?>);"
                                        onchange="calculate_store(<?php echo $sl?>);" id="product_rate_<?php echo $sl?>"
                                        class="form-control product_rate_<?php echo $sl?> text-right" placeholder="0.00"
                                        value="<?php echo $purchasedetails['rate']?>" min="0" tabindex="8" />
                                </td>


                                <td class="text-right">
                                    <input class="form-control total_price text-right" type="text" name="total_price[]"
                                        id="total_price_<?php echo $sl?>"
                                        value="<?php echo $purchasedetails['total_amount']?>" readonly="readonly" />
                                </td>
                                <td>



                                    <button style="text-align: right;" class="btn btn-danger red" type="button"
                                        value="<?php echo makeString(['delete'])?>" onclick="deleteRow(this)"
                                        tabindex="8"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php $sl++; }?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align:right;" colspan="5"><b><?php echo makeString(['discount']);?>:</b>
                                </td>
                                <td class="text-right">
                                    <input type="text" id="discount" class="text-right form-control" name="discount"
                                        value="<?php echo $discount;?>" placeholder="0.00"
                                        onkeyup="calculate_store(1);" />
                                </td>
                                <td> <button type="button" id="add_invoice_item" class="btn btn-info"
                                        name="add-invoice-item" onClick="addPurchaseOrderField1('addPurchaseItem');"
                                        tabindex="9" /><i class="fa fa-plus"></i></button>

                                    <input type="hidden" name="baseUrl" class="baseUrl"
                                        value="<?php echo base_url();?>" />
                                </td>

                            </tr>
                            <tr>

                                <td style="text-align:right;" colspan="5">
                                    <b><?php echo makeString(['grand_total']);?>:</b></td>
                                <td class="text-right">
                                    <input type="text" id="grandTotal" class="text-right form-control"
                                        name="grand_total_price" value="<?php echo html_escape($grand_total);?>"
                                        readonly="readonly" />
                                </td>

                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="form-group row">
                    <div class="col-sm-6">


                    </div>
                    <div class="col-sm-6" style="text-align: right;">
                        <input type="submit" id="add_purchase" class="btn btn-primary btn-large" name="add-purchase"
                            value="<?php echo makeString(['update']) ?>" />

                    </div>
                </div>
                <?php echo form_close()?>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>application/modules/purchase/assets/js/purchase.js" type="text/javascript">
</script>