
<div class="row">
    <div class="col-sm-12">
        <div class="mb-2">
            <a href="<?php echo base_url('accounts/account/payment_receive_form') ?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-align-justify"> </i>
                <?php echo makeString(['payment_or_receive']); ?>
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
            $currency = $get_appsetting->currencyname;
            $position = $get_appsetting->position;
            ?>
        </div>
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo makeString(['payment_received_transaction']); ?></h3>
            </div>
            <div class="card-body">
                <table id="datagrid" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-left"><?php echo makeString(['date']); ?></th>
                            <th class="text-left"><?php echo makeString(['name']); ?></th>
                            <th class="text-left"><?php echo makeString(['description']); ?></th>
                            <th class="text-right"><?php echo makeString(['amount']); ?></th>
                            <th class="text-center"><?php echo makeString(['action']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($get_transaction_info) {
                            $sl = 0;
                            $debit = $credit = $balance = 0;

                            foreach ($get_transaction_info as $ledger) {
                                $sl++;
                                ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?php echo date('d M Y', strtotime($ledger->date)); ?>
                                        </strong>
                                    </td>
                                    <td class="text-left">
                                        <?php
                                        if ($ledger->supplier_name) {
                                            echo html_escape($ledger->supplier_name);
                                        } elseif ($ledger->customer_name) {
                                            echo html_escape($ledger->customer_name);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo html_escape($ledger->description); ?>
                                    </td>
                                    <td align="right">
                                        <?php
                                        echo (($position == 0) ? "$currency $ledger->amount" : "$ledger->amount $currency");
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-info btn-sm"  href="<?php echo base_url('accounts/account/transaction_edit/' . $ledger->transaction_id); ?>" title="Edit"><i class="fas fa-pen" aria-hidden="true"></i></a>
                                        <a class="btn btn-danger btn-sm"  href="<?php echo base_url('accounts/account/transaction_delete/' . $ledger->transaction_id); ?>" title="Delete" onclick="return confirm('Do you want to delete it?')"><i class="fas fa-trash" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>   
        </div>
    </div>
</div>

