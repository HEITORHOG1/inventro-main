<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>application/modules/report/assets/css/custom.css">
<div class="card card-primary card-outline">

    <div class="card-header">
        <h4><?php echo html_escape($title); ?> <small class="float-right"> </small></h4>


        <form action="" class="form-inline" method="post" accept-charset="utf-8">

            <div class="col-sm-12">
                <div class="form-group row">
                    <label for="customer" class="col-sm-1 control-label"><?php echo makeString(['customer']) ?></label>

                    <div class="col-sm-2">
                        <?php echo form_dropdown('customerid', $customer_list, '', 'class="form-control select2" id="customer_id"') ?>
                    </div>
                    <label for="from_date" class="col-sm-1 control-label"><?php echo makeString(['from_date']); ?></label>

                    <div class="col-sm-2">
                        <input type="text" name="from_date" class="form-control datepicker" id="from_date" value=""
                               placeholder="<?php echo makeString(['from_date']); ?>">
                    </div>
                    <label for="to_date" class="col-sm-2 control-label"><?php echo makeString(['to_date']); ?></label>

                    <div class="col-sm-2">
                        <input type="text" name="to_date" class="form-control datepicker" id="to_date"
                               placeholder="<?php echo makeString(['to_date']); ?>" value="">
                    </div>

                    <div class="col-sm-2">
                        <button type="button" id="btn-filter"
                                class="btn btn-success"><?php echo makeString(['find']); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <div class="card-body">
                <table id="SaleList" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th><?php echo 'sl'; ?></th>
                        <th><?php echo makeString(['invoice']); ?></th>
                        <th><?php echo makeString(['customer_name']); ?></th>
                        <th><?php echo makeString(['date']); ?></th>
                        <th><?php echo makeString(['total_amount']); ?></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                    <th colspan="4" class="sales_report_total"><?php echo makeString(['total']) ?>:</th>

                   
                    <th></th>
                    </tfoot>
                    <input type="hidden" name="" id="base_url" value="<?php echo base_url(); ?>">
                    <input type="hidden" name="" id="totalsales" value="<?php echo html_escape($totalsales); ?>">
                </table> 
            </div>
        </div>

    </div>
</div>
<script src="<?php echo base_url() ?>application/modules/report/assets/js/sales.js" type="text/javascript"></script>

