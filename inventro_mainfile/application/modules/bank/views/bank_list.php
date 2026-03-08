<div class="card card-primary card-outline">
    <?php if ($this->permission->method('add_bank', 'create')->access()): ?>
        <div class="card-header">
            <h4><?php echo html_escape($title); ?><small class="float-right"><a data-toggle="modal" data-target="#bankform" class="btn btn-primary" > 
                        <i class="ti-plus" aria-hidden="true"></i>
                        <?php echo makeString(['add_bank']) ?></a></small></h4>
        </div>
    <?php endif; ?>
    <div id="bankform" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['add_bank']); ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="panel">
                                <div class="card-body">
                                    <?php echo form_open_multipart('', 'id="bank_form"') ?>

                                    <div class="form-group row">
                                        <label for="bank_name" class="col-sm-3 control-label"><?php echo makeString(['bank_name']) ?></label>

                                        <div class="col-sm-9">
                                            <input type="text" name="bank_name" class="form-control" value="<?php echo html_escape((!empty($banks->bank_name) ? $banks->bank_name : '')) ?>" id="" placeholder="Nome do Banco">
                                            <input type="hidden" name="bank_id">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="account_no" class="col-sm-3 control-label"><?php echo makeString(['account_no']) ?></label>

                                        <div class="col-sm-9">
                                            <input type="text" name="account_no" class="form-control" value="<?php echo html_escape((!empty($banks->account_no) ? $banks->account_no : '')) ?>" id="" placeholder="Nº da Conta">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="branch_name" class="col-sm-3 control-label"><?php echo makeString(['branch_name']) ?></label>

                                        <div class="col-sm-9">
                                            <input type="text" name="branch_name" class="form-control" value="<?php echo html_escape((!empty($banks->branch_name) ? $banks->branch_name : '')) ?>" id="" placeholder="Nome da Agência">
                                        </div>
                                    </div>
                                    <div class="form-group">

                                        <button type="submit" class="btn btn-success"><?php echo makeString(['save']) ?></button>

                                    </div>

                                    <?php echo form_close() ?>
                                </div>

                            </div>
                        </div>
                    </div>



                </div>

            </div>
            <div class="modal-footer">

            </div>

        </div>

    </div>


    <div class="row">
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="datagrid" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo makeString(['sl']); ?></th>
                            <th><?php echo makeString(['bank_name']); ?></th>
                            <th><?php echo makeString(['account_no']); ?></th>
                            <th><?php echo makeString(['branch_name']); ?></th>
                            <th><?php echo makeString(['action']);?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        foreach ($banklist as $bank) {
                            ?>

                            <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>">
                                <td><?php echo $sl; ?></td>
                                <td><a href="<?php echo base_url('bank/bank/Ledger/' . $bank->bank_id) ?>"><?php echo html_escape($bank->bank_name); ?></a></td>
                                <td><?php echo html_escape($bank->account_no); ?></td>
                                <td><?php echo html_escape($bank->branch_name); ?></td>
                                <td class="center">
                                    <?php if ($this->permission->method('bank', 'update')->access()): ?>
                                        <a href="<?php echo base_url('bank/bank/editfrm/' . $bank->id) ?>"  class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="Update"><i class="fas fa-edit" aria-hidden="true"></i></a> 
                                    <?php endif; ?> 

                                    <?php if ($this->permission->method('bank', 'delete')->access()): ?>
                                        <a href="javascript:void(0)" class="btn btn-danger btn-xs" onclick="deletebank('<?php echo $bank->id; ?>')"><i class="fas fa-trash-alt"></i></a>
                                    <?php endif; ?>
                                </td>

                            </tr>
                            <?php $sl++; ?>

                        <?php } ?> 
                    </tbody>
                </table>  
      
                <input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>">
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>application/modules/bank/assets/js/bank.js" type="text/javascript"></script> 
