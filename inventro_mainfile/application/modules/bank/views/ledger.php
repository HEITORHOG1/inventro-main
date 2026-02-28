
<div class="card card-primary card-outline">

    <div class="card-header">


        <h4><?php echo html_escape($title); ?> <small class="float-right"> </small></h4>
        <center>
            <h2><?php echo html_escape($bankinfo->bank_name); ?></h2>
            <h3><?php echo html_escape($bankinfo->account_no); ?></h3>
            <h4><?php echo html_escape($bankinfo->branch_name); ?></h4>
        </center>

    </div>


    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="bankledger" class="table table-bordered table-striped" id="datagrid">
                    <thead>
                        <tr>
                            <th><?php echo makeString(['sl']); ?></th>
                            <th><?php echo makeString(['date']); ?></th>
                            <th><?php echo makeString(['description']); ?></th>
                            <th><?php echo makeString(['debit']); ?></th>
                            <th><?php echo makeString(['credit']); ?></th>
                            <th><?php echo makeString(['balance']); ?></th>


                        </tr>
                    </thead>
                    <tbody>


                        <?php
                 
                        $balance = 0;
                        if ($ledgers) {

                            $sl = 1;

                            foreach ($ledgers as $ledger) {
                                $debit = $this->db->select('amount')->from('ledger_tbl')->where('id', $ledger->id)->where('d_c', 'd')->get()->row();
                                $total_debit = (!empty($debit->amount) ? $debit->amount : 0);
                                $credit = $this->db->select('amount')->from('ledger_tbl')->where('id', $ledger->id)->where('d_c', 'c')->get()->row();
                                $total_credit = (!empty($credit->amount) ? $credit->amount : 0);
                                $balance = $balance + ($total_debit - $total_credit);
                                ?>
                                <tr>
                                    <td><?php echo html_escape($sl); ?></td>
                                    <td><?php echo html_escape($ledger->date); ?></td>
                                    <td><?php echo html_escape($ledger->description); ?></td>
                                    <td><?php echo html_escape($total_debit); ?></td>
                                    <td><?php echo html_escape($total_credit); ?></td>
                                    <td><?php echo html_escape($balance); ?></td>
                                </tr>
                                <?php
                                $sl++;
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="6"><center>No Record Found</center></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    <tfoot>
                    <th colspan="5" style="text-align:right"><?php echo makeString(['total']) ?>:</th>

                    <th><?php echo html_escape($balance); ?></th> 
                    </tfoot>


                </table>  <!-- /.table-responsive -->
            </div>
        </div>

    </div>
</div>    


