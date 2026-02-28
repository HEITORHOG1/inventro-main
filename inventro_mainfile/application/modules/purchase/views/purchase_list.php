<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>application/modules/purchase/assets/css/style.css">
<div class="card card-primary card-outline">

    <div class="card-header">

        <?php if($this->permission->method('purchase','create')->access()): ?>
        <h4><?php echo  makeString(['purchase_list']);?> <small class="float-right"><a
                    href="<?php echo  base_url('purchase/purchase/create_purchase')?>" class="btn btn-primary btn-md"><i
                        class="ti-plus" aria-hidden="true"></i>
                    <?php echo  makeString(['new_purchase']);?></a> </small></h4>
        <?php endif; ?>

        <button class="btn btn-primary" id="filterid"><?php echo  makeString(['filter']);?></button>

        <div class="row" id="filterdiv">
            <div class="col-sm-12 filterbox">
                <form action="" class="form-inline" method="post" accept-charset="utf-8">
                    <div class="col-sm-5">
                        <div class="form-group row">
                            <label class="col-sm-4" for="from_date"><?php echo  makeString(['from_date']);?></label>
                            <div class="col-sm-6">
                                <input type="text" name="from_date" class="form-control datepicker" id="from_date"
                                    value="" placeholder="<?php echo  makeString(['from_date']);?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group row">
                            <label class="col-sm-4" for="to_date"><?php echo  makeString(['to_date']);?></label>
                            <div class="col-sm-6">
                                <input type="text" name="to_date" class="form-control datepicker" id="to_date"
                                    placeholder="<?php echo  makeString(['to_date']);?>" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button type="button" id="btn-filter"
                            class="btn btn-success"><?php echo makeString(['find']); ?></button>
                    </div>

                </form>
            </div>

        </div>
    </div>


    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="PurList" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo html_escape('sl');?></th>
                            <th><?php echo  makeString(['chalan_no']);?></th>
                            <th><?php echo  makeString(['purchase_id']);?></th>
                            <th><?php echo  makeString(['supplier_name']);?></th>
                            <th><?php echo  makeString(['date']);?></th>
                            <th><?php echo  makeString(['total_amount']);?></th>
                            <th><?php echo makeString(['action']); ?></th>
                            <input type="hidden" name="" id="base_url" value="<?php echo  base_url();?>">
                            <input type="hidden" name="" id="totalpurchase" value="<?php echo html_escape($totalpurchase);?>">

                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <th colspan="5" style="text-align:right"><?php echo  makeString(['total'])?>:</th>

                        <th></th>
                        <th></th>
                    </tfoot>

                </table> <!-- /.table-responsive -->
            </div>
        </div>

    </div>
</div>
<script src="<?php echo base_url() ?>application/modules/purchase/assets/js/purchase.js.php" type="text/javascript">
</script>