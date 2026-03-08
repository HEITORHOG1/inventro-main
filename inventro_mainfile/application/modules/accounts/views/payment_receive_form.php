<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/modules/accounts/assets/css/bootstrap-toggle.css'); ?>">

<div class="row">
    <div class="col-sm-12">
        <div class="mb-2">
            <a href="<?php echo base_url('accounts/account/manage_transaction') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-align-justify"> </i>
                <?php echo makeString(['manage_transaction']); ?>
            </a>
        </div>

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
            <div class="card-header">
                <h3 class="card-title"><?php echo makeString(['payment_received_transaction']); ?></h3>
            </div>
            <?php echo form_open('accounts/account/transaction_save', array('class' => 'form-vertical', 'id' => 'validate')) ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label for="customer_id"
                                   class="col-sm-2 col-form-label"><?php echo makeString(['choose_transaction']); ?> <i
                                    class="text-danger"> * </i></label>
                            <div class="col-sm-4">
                                <div class="switch col-sm-9">
                                    <input type="radio" name="transection_type" class="transaction_type" id="weekSW-0"
                                           value="1"/>
                                    <label for="weekSW-0" id="yes"><i class="fa fa-credit-card" aria-hidden="true"></i>
                                        <strong><?php echo makeString(['payment']); ?></strong></label>
                                    <input type="radio" name="transection_type" class="transaction_type" id="weekSW-1"
                                           value="2" checked=checked/>
                                    <label for="weekSW-1" id="no"><i class="fa fa-credit-card" aria-hidden="true"></i>
                                        <strong><?php echo makeString(['receive']); ?></strong></label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label for="date" class="col-sm-2 col-form-label"><?php echo makeString(['date']); ?> <i class="text-danger"> * </i></label>
                            <div class="col-sm-4">
                                <input class="form-control datepicker" name="date" id="date" type="text"
                                       placeholder="DD-MM-YYYY" required="" value="<?php echo date('Y-m-d') ?>">
                            </div>
                            <label for="description" class="col-sm-2 col-form-label"><?php echo makeString(['description']); ?> <i class="text-danger"> * </i></label>
                            <div class="col-sm-4">
                                <textarea name="description" id="description" class="form-control" placeholder="Digite uma descrição"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label for="transactioncategory" class="col-sm-2 col-form-label"><?php echo makeString(['transaction_category']); ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-4">
                                <select class="form-control select2" id="transactioncategory" onchange="transactionCategory1(this.value)" name="transactioncategory" data-placeholder="<?php echo makeString(['select_one']); ?>">
                                    <option value=""></option>
                                    <option value="1"><?php echo makeString(['customer']); ?> </option>
                                    <option value="2"><?php echo makeString(['supplier']); ?> </option>
                                </select>
                            </div>
                            <label for="payment_type" class="col-sm-2 col-form-label"><?php echo makeString(['transaction_mode']); ?> <span class="text-danger"> * </span></label>
                            <div class="col-sm-4">
                                <select name="payment_type" id="payment_type" class="form-control select2" data-placeholder="-- selecione --">
                                    <option value=""></option>
                                    <option value="1"> <?php echo makeString(['cash_payment']); ?> </option>
                                    <option value="2"> <?php echo makeString(['bank_payment']); ?>  </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label for="select_name" class="col-sm-2"><?php echo makeString(['select_name']); ?> </label>
                            <div class="col-sm-4">
                                <div class="loaded"></div>
                            </div>
                            <label for="amount" class="col-sm-2"><?php echo makeString(['amount']); ?> </label>
                            <div class="col-sm-4">
                                <input class="form-control amount" name="amount" id="amount" type="number" placeholder="<?php echo makeString(['amount']); ?> " min="1">
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group row">
                            <label for="cheque_bank_name" class="col-sm-2 col-form-label bank_name_lbl"></label>
                            <div class="col-sm-4 bank_name_input"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label for="example-text-input" class="offset-2 col-sm-3 col-form-label"></label>
                    <div class="col-sm-2">
                        <input type="submit" id="" class="btn btn-primary btn-large text-right pay_receipt_btn" name="" value="<?php echo makeString(['submit']); ?>"/>
                    </div>
                </div>

            </div>            
            <?php echo form_close() ?>
        </div>
    </div>
</div>

<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">

<script src="<?php echo base_url('application/modules/accounts/assets/js/script.js') ?>" type="text/javascript"></script> 
<script src="<?php echo base_url('application/modules/accounts/assets/js/bootstrap-toggle.min.js'); ?>" type="text/javascript"></script>