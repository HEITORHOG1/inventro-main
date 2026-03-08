<?php
$currency = $get_appsetting->currencyname;
$position = $get_appsetting->position;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="mb-2">
            <a href="<?php echo base_url('invoice/invoice/invoice_list') ?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-align-justify"> </i>
                <?php echo makeString(['invoice_list']); ?>
            </a>
        </div>
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo makeString(['payment_received_transaction']); ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <img src="<?php echo base_url($get_appsetting->logo); ?>" class="img-responsive" alt="">
                        <br>
                        <address>
                            <strong><?php echo html_escape($get_appsetting->title); ?></strong><br>
                            <?php echo html_escape($get_appsetting->address); ?><br>
                            <?php echo html_escape($get_appsetting->email); ?><br>
                            <abbr title="Phone"></abbr> <?php echo html_escape($get_appsetting->phone); ?>
                        </address>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h4 class="m-t-0">Invoice : <?php echo html_escape($get_invoice_info->invoice_id); ?></h4>
                        <div><?php echo date('d F Y', strtotime($get_invoice_info->date)); ?></div>
                        <address>
                            <strong><?php echo html_escape( $get_invoice_info->name); ?></strong><br>
                            <?php echo html_escape($get_invoice_info->address); ?><br>
                            <?php echo html_escape($get_invoice_info->email); ?><br>
                            <abbr title="<?php echo makeString(['mobile']); ?>"></abbr> <?php echo html_escape($get_invoice_info->mobile); ?>
                        </address>
                    </div>
                </div> <hr>
                <div class="table-responsive m-b-20">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo makeString(['sl_no']); ?></th>
                                <th><?php echo makeString(['item']); ?></th>
                                <th><?php echo makeString(['quantity']); ?></th>
                                <th><?php echo makeString(['price']); ?></th>
                                <th><?php echo makeString(['total_price']); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 0;
                            foreach ($get_invoice_details as $single) {
                                $sl++;
                                ?>
                                <tr>
                                    <td><?php echo $sl; ?></td>
                                    <td>
                                        <div>
                                            <strong><?php echo html_escape($single->name); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo html_escape($single->quantity); ?></td>
                                    <td><?php
                                        echo html_escape(($position == 0) ? "$currency $single->price" : "$single->price $currency");
                                        ?></td>
                                    <td><?php
                                        $totalPrice = $single->quantity * $single->price;
                                        echo html_escape(($position == 0) ? "$currency $totalPrice" : "$totalPrice $currency");
                                        ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <p>
                            <?php echo html_escape($get_invoice_info->description); ?>
                        </p>
                        <p><strong><?php echo makeString(['thank_you_very_much']); ?></strong></p>

                    </div>
                    <div class="col-sm-4">
                        <ul class="list-unstyled text-left">
                            <li>
                                <strong><?php echo makeString(['total_amount']); ?>:</strong> <?php echo html_escape(($position == 0) ? "$currency $get_invoice_info->total_amount" : "$get_invoice_info->total_amount $currency"); ?> </li>
                            <li>
                                <strong><?php echo makeString(['paid_amount']); ?>:</strong> <?php echo html_escape(($position == 0) ? "$currency $get_invoice_info->paid_amount" : "$get_invoice_info->paid_amount $currency"); ?> </li>
                            <li>
                                <strong><?php echo makeString(['due_amount']); ?>:</strong> <?php echo html_escape(($position == 0) ? "$currency $get_invoice_info->due_amount" : "$get_invoice_info->due_amount $currency"); ?> </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

