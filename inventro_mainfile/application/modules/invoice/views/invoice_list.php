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
        <h4><?php echo makeString(['invoice_list']); ?>
            <small class="float-right">
                <a href="<?php echo base_url('invoice/invoice/index'); ?>" class="btn btn-primary"> 
                    <i class="ti-plus" aria-hidden="true"></i><?php echo makeString(['add_invoice']); ?>
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
                            <th width="15%"><?php echo makeString(['invoice_id']); ?> </th>
                            <th width="18%"><?php echo makeString(['customer_name']); ?> </th>
                            <th width="10%" class="text-center"><?php echo makeString(['paid']); ?> </th>
                            <th width="12%" class="text-center"><?php echo makeString(['total_amount']); ?> </th>
                            <th width="15%" class="text-center"><?php echo makeString(['action']); ?> </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 0;
                        if ($sales_list) {
                            foreach ($sales_list as $sale) {
                                $sl++;
                                ?>
                                <tr>
                                    <td><?php echo $sl; ?></td>
                                    <td>
                                        <a href="<?php echo base_url('invoice/invoice/single_invoice/' . $sale->invoice_id); ?>">
                                            <?php echo html_escape($sale->invoice_id); ?>
                                        </a>
                                    </td>
                                    <td>

                                        <?php echo html_escape($sale->name); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        echo html_escape(($position == 0) ? "$currency $sale->paid_amount" : "$sale->paid_amount $currency");
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo html_escape(($position == 0) ? "$currency $sale->total_amount" : "$sale->total_amount $currency"); ?>
                                    </td>

                                    <td class="text-center">
                                        <?php
                                        $status = $sale->status;
                                        if ($status == 1) {
                                            ?>
                                                                                                                                                                        <!--<a href="<?php echo base_url(); ?>sales-inactive/<?php echo $sale->invoice_id; ?>" data-toggle='tooltip' data-placement='top' data-original-title='Inactive' onclick="return confirm('Are you sure inactive it ?')" title="Inactive" class="btn btn-sm btn-danger"><i class="fa fa-times" aria-hidden="true"></i></a>-->
                                            <?php
                                        }
                                        if ($status == 0) {
                                            ?>
                                                                                                                                                                        <!--<a href="<?php echo base_url(); ?>sales-active/<?php echo $sale->invoice_id; ?>" data-toggle='tooltip' data-placement='top' data-original-title='Active' onclick="return confirm('Are you sure active it ?')" title="Active" class="btn btn-sm btn-info"><i class="fa fa-check-circle"></i></a>-->
                                        <?php } ?>

                                        <a href="<?php
                                        if ($sale->status == 2) {
                                            echo 'javascript:void(0)';
                                        } else {
                                            echo base_url('invoice/invoice/invoice_edit/' . $sale->invoice_id);
                                        }
                                        ?>" data-toggle="tooltip" data-placement="top" data-original-title="<?php
                                           if ($sale->status == 2) {
                                               echo 'Já Entregue';
                                           } else {
                                               echo 'Editar';
                                           }
                                           ?>" class="btn btn-info btn-sm"><i class="fas fa-pen"></i></a> 
                                        <a href="<?php echo base_url('invoice/invoice/invoice_delete/' . $sale->invoice_id); ?>" data-toggle='tooltip' data-placement='top' data-original-title='Excluir' class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir esta fatura?')"><i class="fas fa-trash"></i></a> 
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                    <?php if (empty($sales_list)) { ?>
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
</div>