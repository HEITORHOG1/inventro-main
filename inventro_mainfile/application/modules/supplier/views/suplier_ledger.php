<div class="card card-primary card-outline">

    <div class="card-header">
        <?php if ($this->permission->method('supplier', 'read')->access()): ?>
            <h4><?php echo  makeString(['supplier_ledger']); ?> </h4>
        <?php endif; ?>
    </div>
    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="Supledger" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo html_escape('sl'); ?></th>
                            <th><?php echo  makeString(['supplier_name']); ?></th>
                            <th><?php echo  makeString(['total_creadit']); ?></th>
                            <th><?php echo  makeString(['total_debit']); ?></th>
                            <th><?php echo  makeString(['balance']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <th colspan="2" class="txt-alignrt"><?php echo makeString(['total']); ?></th>
                    <th class="txt-alignrt">:</th>
                    <th class="txt-alignrt">:</th>
                    <th class="txt-alignrt">:</th>
                    </tfoot>
                </table>  <!-- /.table-responsive -->
            </div>
        </div>

    </div>
</div>   
<input type="hidden" name="" id="base_url" value="<?php echo  base_url(); ?>">



