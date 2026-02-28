<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/modules/invoice/assets/css/pos.css')?>">


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

        $currency = $get_appsetting->currencyname;
        $position = $get_appsetting->position;
    ?>
</div>
<div class="card card-primary card-outline">
    <?php if ($this->permission->method('invoice', 'create')->access()): ?>
    <div class="card-header">
        <?php echo makeString(['add_pos_invoice']); ?>
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

                <div class="container-fluid pl-2 pr-2">
                    <div class="row sell-pos">

                        <!-- item info start -->

                        <div class="col-md-7">
                            <div class="card mb-2 rounded-0">
                                <div class="card-header bg-info rounded-0">
                                    <div class="row w-104">
                                        <div class="col-md-4">
                                            <div class="form-group"><input type="text" class="form-control"
                                                    placeholder="Item, Model, Item Code" id="searchitem"></div>
                                        </div>
                                        <div class="col-md-8 p-0">
                                            <div class="filter-category"><a onclick="CategorySearch('all')"
                                                    class="btn btn-outline-warning rounded-0 mt-1 mb-1">All</a>
                                                <?php foreach($category_list as $categories){?>
                                                <a onclick="CategorySearch(<?php echo $categories->category_id;?>)"
                                                    class="btn btn-outline-warning rounded-0 mr-1 mt-1 mb-1"><?php echo html_escape($categories->name);?></a>
                                                <?php }?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body card-body-all-products scroll-bar">
                                    <div class="row justify-content-center all-products">
                                        <?php foreach ($get_products as $products) {?>
                                        <div class="col-md-2 p-1"
                                            onclick="onselectimage(<?php echo $products->product_id;?>)">
                                            <div class="single-product">
                                                <div class="img">
                                                    <!----> <img src="<?php echo base_url().$products->picture;?>"
                                                        class="img-fluid">
                                                </div>
                                                <div class="description">
                                                    <p class="product-title">
                                                        <strong><?php echo html_escape($products->name);?></strong>
                                                    </p>
                                                    <div class="d-flex sku-price">
                                                        <div class="col-12 pl-0 pt-0">
                                                            <span><?php echo html_escape($products->model);?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="price"> <?php
                                        echo (($position == 0) ? "$currency $products->price" : "$products->price $currency");
                                        ?></div>
                                            </div>
                                        </div>
                                        <?php }?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- item info end -->
                        <div class="col-md-5 pr-0">

                            <?php echo  form_open('invoice/invoice/insert_pos_sale'); ?>
                            <div class="card mb-2 rounded-0">
                                <div class="card-header bg-info rounded-0">
                                    <div class="row text-right">
                                        <div class="col-4 pl-0 mml-5">
                                            <div class="form-group"><input type="text" class="form-control"
                                                    placeholder="Barcode/Qr Code" id="barcode" /></div>
                                        </div>
                                        <div class="col-8 pr-0 select-customer">
                                            <div dir="auto" class="v-select vs--single vs--searchable"><select
                                                    class="form-control select2" name="customer_id" id="customer_id"
                                                    data-placeholder="<?php echo 'Select Customer'; ?>">
                                                    <option value=""></option>
                                                    <?php foreach ($get_customer as $customer) { ?>
                                                    <option value="<?php echo $customer->customerid; ?>">
                                                        <?php echo html_escape($customer->name); ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>
                                                <!---->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0 text-right">
                                    <div class="cart-products pr-2 scroll-bar pb-2" style="min-height: 21vh;">
                                        <table class="table table-sm nowrap gui-products-table" id="addinvoice">
                                            <thead></thead>
                                            <tbody></tbody>
                                        </table>
                                        <!---->
                                    </div>
                                    <div class="cart-footer">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table
                                                    class="table text-right  table table-sm nowrap gui-products-table">
                                                    <tbody>
                                                        <tr>
                                                            <td> Total</td>
                                                            <td width="30%"></td>
                                                            <td width="30%" class="text-12"><input type="text"
                                                                    id="item_total" readonly="readonly"
                                                                    class="form-control font-12"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Invoice Discount </td>
                                                            <td></td>
                                                            <td><input type="number" id="invoice_discount"
                                                                    name="invoice_discount" onkeyup="TotalCalculation()"
                                                                    onchange="TotalCalculation()"
                                                                    class="form-control font-12"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Total Discount </td>
                                                            <td></td>
                                                            <td><input type="number" name="total_discount"
                                                                    class="form-control font-12" id="total_discount"
                                                                    readonly=""></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Grand Total</td>
                                                            <td width="30%"></td>
                                                            <td width="30%" class="text-12"><input type="number"
                                                                    name="grand_total_price" readonly="readonly"
                                                                    class="form-control font-12" id="grand_total"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Paid Amount</td>
                                                            <td></td>
                                                            <td><input type="number" name="paid_amount"
                                                                    class="form-control font-12" id="paid_amount"
                                                                    step="0.01" min="0"
                                                                    onchange="PaidAmount()" onkeyup="PaidAmount()"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Due Amount</td>
                                                            <td></td>
                                                            <td><input type="number" name="due_amount"
                                                                    class="form-control font-12" id="due_amount"
                                                                    readonly="readonly">

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <input type="hidden" name="payment_type" id="payment_type" value="">
                                            <input type="hidden" name="bank_id" id="bank_id" value="">
                                            <div class="col-md-12" id="possubmit">
                                                <a data-toggle="modal" data-target="#paymenttype" class="btn btn-block"
                                                    style="height: 35px;background-color: #2874A6;color:#fff;"
                                                    id="ptypebutton">
                                                    <i class="ti-plus" aria-hidden="true"></i>
                                                    <?php echo makeString(['payment_now']) ?></a>
                                                <button type="submit" id="possubmit2"
                                                    class="btn btn-block btn-sm display-nk"
                                                    style="height: 35px;background-color: #2874A6;color:#fff;">Save
                                                    Invoice</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php echo form_close() ?>
                        </div>


                    </div>
                </div>


            </div>
        </div>
    </div>
    <div id="paymenttype" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['payment_type']); ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="panel">

                                <div class="panel-body">

                                    <form action="" id="paymentform" method="post">
                                        <div class="form-group row">
                                            <label for="payment_type" class="col-sm-4 col-form-label"><?php
                                        echo makeString(['payment_type']);
                                        ?> <i class="text-danger">*</i></label>
                                            <div class="col-sm-6">
                                                <select name="paytype" id="paytype" style="width:100%"
                                                    class="form-control select2" required=""
                                                    onchange="bankPaymet(this.value)">
                                                    <option value="1"><?php echo  makeString(['cash_payment'])?>
                                                    </option>
                                                    <option value="2"><?php echo  makeString(['bank_payment'])?>
                                                    </option>

                                                </select>



                                            </div>
                                        </div>

                                        <div class="form-group row bankdiv" id="bank_div">
                                            <label for="bank" class="col-sm-4 col-form-label"><?php
                                    echo makeString(['bank_name']);
                                    ?> <i class="text-danger">*</i></label>
                                            <div class="col-sm-6">
                                                <select name="bank" style="width:100%" class="form-control select2"
                                                    id="bank">
                                                    <option value="">Select Bank</option>
                                                    <?php foreach($bank_list as $bank){?>
                                                    <option value="<?php echo $bank->bank_id;?>">
                                                        <?php echo $bank->bank_name;?></option>
                                                    <?php }?>
                                                </select>


                                            </div>

                                        </div>

                                        <div class="form-group text-right">

                                            <button type="button" class="btn btn-success w-md m-b-5"
                                                onclick="Savepayment()"><?php echo makeString(['save']) ?></button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>



                </div>

            </div>



        </div>

    </div>
</div>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
<script src="<?php echo base_url('application/modules/invoice/assets/js/script.js') ?>" type="text/javascript"></script>
<script>
window.onload = function() {
    $('body').addClass("sidebar-mini sidebar-collapse");
}
</script>