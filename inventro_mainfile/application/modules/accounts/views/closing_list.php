
<div class="row">
    <div class="col-sm-12">
        <div class="mb-2">
            <a href="<?php echo base_url('accounts/account/closing_form') ?>" class="btn btn-success m-b-5 m-r-2"><i class="ti-align-justify"> </i>
                <?php echo makeString(['cash_closing']); ?>
            </a>
        </div>
        <div class="">
            <?php
            $error = $this->session->flashdata('error');
            $success = $this->session->flashdata('success');
            if ($error != '') {
                echo html_escape($error);
            }
            if ($success != '') {
                echo html_escape($success);
            }
            $currency = $get_appsetting->currencyname;
            $position = $get_appsetting->position;
            ?>
        </div>
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo makeString(['closing_list']); ?></h3>
            </div>
            <div class="card-body">
                <table id="datagrid" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="text-left"><?php echo makeString(['date']); ?></th>
                            <th class="text-left"><?php echo makeString(['last_closing_balance']); ?></th>
                            <th class="text-left"><?php echo makeString(['receipt']); ?></th>
                            <th class="text-right"><?php echo makeString(['payment']); ?></th>
                            <th class="text-right"><?php echo makeString(['balance']); ?></th>
                            <th class="text-right"><?php echo makeString(['adjustment']); ?></th>
                            <th class="text-center"><?php echo makeString(['action']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($cash_data) {
                            $sl = 0;
                           
                            foreach ($cash_data as $cash) {
                                $sl++;
                                ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?php echo date('d M Y', strtotime($cash->date)); ?>
                                        </strong>
                                    </td>
                                    <td class="text-left">
                                        <?php
                                        echo html_escape(($position == 0) ? "$currency $cash->last_day_closing" : "$cash->last_day_closing $currency");
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo html_escape(($position == 0) ? "$currency $cash->cash_in" : "$cash->cash_in $currency");
                                        ?>
                                    </td>
                                    <td>
                                         <?php
                                        echo html_escape(($position == 0) ? "$currency $cash->cash_out" : "$cash->cash_out $currency");
                                        ?>
                                    </td>
                                    <td align="right">
                                        <?php
                                        echo html_escape(($position == 0) ? "$currency $cash->amount" : "$cash->amount $currency");
                                        ?>
                                    </td>
                                    <td>
                                         <?php
                                        echo html_escape(($position == 0) ? "$currency $cash->adjustment" : "$cash->adjustment $currency");
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        
                                        <a class="btn btn-danger btn-sm"  href="<?php echo base_url('accounts/account/cash_closing_delete/' . $cash->id); ?>" title="Delete" onclick="event.preventDefault(); var u=this.href; showConfirm('Deseja excluir este registro?', function(){ window.location.href=u; })"><i class="fas fa-trash" aria-hidden="true"></i></a>
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

