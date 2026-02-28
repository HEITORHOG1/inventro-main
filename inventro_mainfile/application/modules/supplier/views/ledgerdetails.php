<div class="invoice p-3 mb-3">
    <!-- title row -->
    <div class="row">
        <div class="col-12">
            <h4>
                <img src="<?php echo base_url(); ?><?php echo html_escape($storeinfo->logo); ?>" alt="logo" width="80"> <?php echo html_escape($storeinfo->title); ?>
                <small class="float-right"><?php echo html_escape('Date')?>: <?php echo date('d/m/Y'); ?></small>
            </h4>
        </div>
        <!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            <address>
                <strong><?php echo html_escape($supplier->name); ?></strong><br>
                <?php echo html_escape($supplier->address); ?><br>
                <?php echo html_escape('Phone')?>: <?php echo html_escape($supplier->mobile); ?><br>
                <?php echo html_escape('Email');?>: <?php echo html_escape($supplier->email); ?>
            </address>
        </div>
    </div>
    <!-- /.row -->

    <!-- Table row -->
    <div class="row">
        <div class="col-12 table-responsive">
            <table id="ledgerdetails" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th><?php echo html_escape('sl'); ?></th>
                        <th><?php echo  makeString(['transactionid']); ?></th>
                        <th><?php echo  makeString(['details']); ?></th>
                        <th><?php echo  makeString(['total_creadit']); ?></th>
                        <th><?php echo  makeString(['total_debit']); ?></th>
                        <th><?php echo  makeString(['balance']); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    $deittotal = 0;
                    $credittotal = 0;
                    foreach ($ledgerinfo as $ledger) {
                        $i++;
                        $debit = 0;
                        $credit = 0;
                        if ($ledger->d_c == 'd') {
                            $debit = $ledger->amount;
                            $deittotal = $deittotal + $ledger->amount;
                        }
                        if ($ledger->d_c == 'c') {
                            $credit = $ledger->amount;
                            $credittotal = $credittotal + $ledger->amount;
                        }
                        $total = $credit - $debit;
                        ?>
                        <tr>

                            <td><?php echo $i; ?></td>
                            <td><?php echo html_escape($ledger->transaction_id); ?></td>
                            <td><?php echo html_escape($ledger->description); ?></td>
                            <td class="txt-alignrt"><?php echo html_escape($credit); ?></td>
                            <td class="txt-alignrt"><?php echo html_escape($debit); ?></td>
                            <td class="txt-alignrt"><?php echo html_escape($total); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                <th colspan="3" class="txt-alignrt"><?php echo html_escape('Total');?></th>
                <th class="txt-alignrt"><?php echo html_escape($credittotal); ?></th>
                <th class="txt-alignrt"><?php echo html_escape($deittotal); ?></th>
                <th class="txt-alignrt"><?php echo html_escape($credittotal) - html_escape($deittotal); ?></th>
                </tfoot>
            </table>
        </div>
    </div>
</div>