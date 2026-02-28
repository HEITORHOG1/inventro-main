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
        <h4><?php echo makeString(['supplier_return_list']); ?>
            <small class="float-right">
                <a href="<?php echo base_url('return/returns/supplier_return'); ?>" class="btn btn-primary"> 
                    <i class="ti-plus" aria-hidden="true"></i><?php echo makeString(['supplier_return']); ?>
                </a>
            </small>
        </h4>
    </div>

    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="dataTableExample2" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="10%"><?php echo makeString(['sl_no']); ?> </th>
                            <th width="15%"><?php echo makeString(['purchase_id']); ?> </th>
                            <th width="18%"><?php echo makeString(['supplier_name']); ?> </th>
                            <th width="15%" class="text-center"><?php echo makeString(['return']).' '.makeString(['date']); ?> </th>
                            <th width="15%" class="text-center"><?php echo makeString(['total_amount']); ?> </th>
                             <th width="12%" class="text-center"><?php echo makeString(['reason']); ?> </th>
                            <th width="15%" class="text-center"><?php echo makeString(['action']); ?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 0;
                        if ($return_list) {
                            foreach ($return_list as $return) {
                             
                                $sl++;
                                ?>
                                <tr>
                                    <td><?php echo $sl; ?></td>
                                    <td>
                                        <a href="<?php echo base_url('purchase/purchase/purchase_details/' . $return->purchase_id); ?>">
                                            <?php echo html_escape($return->purchase_id); ?>
                                        </a>
                                    </td>
                                    <td>

                                        <?php echo html_escape($return->name); ?>
                                    </td>
                                    <td>

                                        <?php echo html_escape($return->return_date); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        echo (($position == 0) ? "$currency $return->total_amount" : "$return->total_amount $currency");
                                        ?>
                                    </td>

                                    <td>

                                        <?php echo html_escape($return->reason); ?>
                                    </td>
                                    <td class="text-center">
                                        <a class="btn btn-success btn-sm" href="<?php echo base_url('return/returns/supplier_return_details/' . $return->return_id); ?>">
                                            <i class="fa fa-window-restore"></i>
                                        </a>
                                       
                                        <a href="<?php echo base_url('return/returns/delete/' . $return->return_id); ?>" data-toggle='tooltip' data-placement='top' data-original-title='Delete' class="btn btn-danger btn-sm" onclick="return confirm('Do you want to delete it?')"><i class="fas fa-trash"></i></a> 
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                    <?php if (empty($return_list)) { ?>
                        <tfoot>
                            <tr>
                                <th class="text-danger text-center" colspan="8">
                                    <?php echo makeString(['record_not_found']); ?>
                                </th>
                            </tr>
                        </tfoot>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div