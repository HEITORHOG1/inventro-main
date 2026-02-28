
<div class="card card-primary card-outline">

    <div class="card-header">


        <h4><?php echo html_escape($title); ?> <small class="float-right"> </small></h4>

        <form action="" class="form-inline" method="post" accept-charset="utf-8">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="form-group row">
                                <label for="bank" class="col-sm-1 control-label"><?php echo makeString(['bank']) ?></label>
                                <div class="col-sm-4">
                                    <?php echo form_dropdown('bank_id', $bank_list, '', 'class="form-control select2" id="bank_id"') ?>
                                </div>
                                <label for="from_date" class="col-sm-2 control-label"><?php echo makeString(['from_date']); ?></label>

                                <div class="col-sm-4">
                                    <input type="text" name="from_date" class="form-control datepicker" id="from_date" value="" placeholder="<?php echo makeString(['from_date']); ?>" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="to_date" class="col-sm-2 control-label"><?php echo makeString(['to_date']); ?></label>

                            <div class="col-sm-4">
                                <input type="text" name="to_date" class="form-control datepicker" id="to_date" placeholder="<?php echo makeString(['to_date']); ?>" value="">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" id="btn-filter" class="btn btn-success"><?php echo html_escape('find'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>   
        </form> 



    </div>


    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="ledger" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo 'sl'; ?></th>
                            <th><?php echo makeString(['bank_name']); ?></th>
                            <th><?php echo makeString(['date']); ?></th>
                            <th><?php echo makeString(['description']); ?></th>
                            <th><?php echo makeString(['debit']); ?></th>
                            <th><?php echo makeString(['credit']); ?></th>
                            <th><?php echo makeString(['balance']); ?></th>


                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="txt-alignrt"><?php echo makeString(['total']) ?>:</th>

                            <th></th> 

                        </tr>
                    </tfoot>


                </table>  <!-- /.table-responsive -->
            </div>
        </div>
        <input type="hidden" name="" id="base_url" value="<?php echo base_url(); ?>">
    </div>
</div>    
<script src="<?php echo base_url() ?>application/modules/bank/assets/js/bank.js" type="text/javascript"></script>     

