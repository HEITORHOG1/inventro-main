<div class="card card-primary card-outline">

    <div class="card-header">


        <h4><?php echo html_escape($title); ?> <small class="float-right"> </small></h4>

        <form action="" class="form-inline" method="post" accept-charset="utf-8">

            <div class="col-sm-12">
                <div class="form-group row">
                   
                    <label for="from_date" class="col-sm-2 control-label"><?php echo makeString(['from_date']); ?></label>

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
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="BankBookList" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th><?php echo makeString(['bank_name']); ?></th>
                        <th><?php echo makeString(['date']); ?></th>
                        <th><?php echo makeString(['deposit']); ?></th>
                        <th><?php echo makeString(['withdraw']); ?></th>
                        <th><?php echo makeString(['balance']); ?></th>

                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                    <th colspan="2" style="text-align:right"><?php echo makeString(['total']) ?>:</th>

                    <th></th>
                    <th></th>
                    <th></th>
                    </tfoot>
                    <input type="hidden" name="" id="base_url" value="<?php echo base_url(); ?>">
                </table>  
            </div>
        </div>

    </div>
</div>
<script src="<?php echo base_url() ?>application/modules/report/assets/js/bank_book.js.php" type="text/javascript"></script>

