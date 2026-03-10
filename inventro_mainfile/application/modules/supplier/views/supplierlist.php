<div class="card card-primary card-outline">
    <?php if ($this->permission->method('supplier', 'create')->access()): ?>
        <div class="card-header">
            <h4><?php echo makeString(['supplier_add']) ?>
                <small class="float-right">
                    <button type="button" class="btn btn-success btn-md" data-target="#csvModal" data-toggle="modal"  ><i class="ti-plus" aria-hidden="true"></i>
                        <?php echo makeString(['import_csv']); ?></button>
                    <button type="button" class="btn btn-primary btn-md" data-target="#add0" data-toggle="modal"  ><i class="ti-plus" aria-hidden="true"></i>
                        <?php echo makeString(['supplier_add']) ?></button> 
                </small>
            </h4>
        </div>
    <?php endif; ?>
    <div id="csvModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['import_csv']); ?></strong>
                    <a href="<?php echo base_url('admin_assets/csv/supplier_csv_sample.csv'); ?>" class="btn btn-primary pull-right">
                        <i class="fa fa-download"> </i> <?php echo makeString(['download_sample_file']); ?>
                    </a>
        
                </div>
                <div class="modal-body">
                <?php echo form_open_multipart('supplier/Supplierlist/supplier_csv_upload')?>
                    
                        <div class="form-group row">
                            <label for="firstname" class="col-sm-4 col-form-label"><?php echo makeString(['upload_csv_file']) ?> <span class="txt-color">*</span></label>
                            <div class="col-sm-8">
                                <input name="csv_file" class="form-control" type="file" id="customer_name" required>
                            </div>
                        </div>
                        <div class="form-group-row">
                            <label class="col-sm-4">&nbsp;</label>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-success w-md m-b-5"><?php echo makeString(['upload']) ?></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="add0" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['supplier_add']); ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="card-body">
                                <?php echo  form_open('supplier/supplierlist/create') ?>
                                <?php echo form_hidden('id', (!empty($intinfo->id) ? html_escape($intinfo->id) : null)) ?>
                                <div class="form-group row">
                                    <label for="suppliername" class="col-sm-4 col-form-label"><?php echo makeString(['supplier_name']) ?> <span class="txt-color">*</span></label>
                                    <div class="col-sm-8">
                                        <input name="suppliername" class="form-control" type="text" placeholder="Adicionar <?php echo makeString(['supplier_name']) ?>" id="suppliername" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-4 col-form-label"><?php echo makeString(['email']) ?> <span class="txt-color">*</span></label>
                                    <div class="col-sm-8">
                                        <input name="email" class="form-control" type="text" placeholder="Adicionar <?php echo makeString(['email']) ?>" id="email" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-4 col-form-label"><?php echo makeString(['mobile']) ?> <span class="txt-color">*</span></label>
                                    <div class="col-sm-8">
                                        <input name="mobile" class="form-control" type="number" placeholder="Adicionar <?php echo makeString(['mobile']) ?>" id="mobile" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-4 col-form-label"><?php echo makeString(['previous_balance']) ?> </label>
                                    <div class="col-sm-8">
                                        <input name="previous_balance" class="form-control" type="number" placeholder="Adicionar <?php echo makeString(['previous_balance']) ?>" id="previous_balance" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['isreceipt']) ?> </label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2 content-width1" name="paytype">
                                            <option selected="selected" value="c"><?php echo html_escape('Valor Recebido')?></option>
                                            <option value="d"><?php echo html_escape('Valor do Pagamento');?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['address']) ?></label>
                                    <div class="col-sm-8">
                                        <textarea name="address" class="form-control" cols="50" rows="3" placeholder="Adicionar <?php echo makeString(['address']) ?>" id="address" ></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-4 col-form-label"><?php echo makeString(['status']) ?> </label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2 content-width1" name="status">
                                            <option selected="selected" value="1"><?php echo html_escape('Ativo')?></option>
                                            <option value="0"><?php echo html_escape('Inativo')?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group text-right">
                                    <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo makeString(['reset']) ?></button>
                                    <button type="submit" class="btn btn-success w-md m-b-5"><?php echo makeString(['Ad']) ?></button>
                                </div>
                                <?php echo form_close() ?>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
    <div id="edit" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['supplier_edit']); ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body editinfo">

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
                            <th><?php echo makeString(['sl_no']) ?></th>
                            <th><?php echo makeString(['supplier_name']) ?></th>
                            <th><?php echo makeString(['email']) ?></th>
                            <th><?php echo makeString(['mobile']) ?></th>
                            <th><?php echo makeString(['address']) ?></th>
                            <th><?php echo makeString(['status']) ?></th>
                            <th><?php echo makeString(['action']) ?></th> 

                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($supplierlist)) { ?>
                            <?php $sl = 1; ?>
                            <?php foreach ($supplierlist as $supplier) { ?>
                                <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>">
                                    <td><?php echo html_escape($sl); ?></td>
                                    <td><?php echo html_escape($supplier->name); ?></td>
                                    <td><?php echo html_escape($supplier->email); ?></td>
                                    <td><?php echo html_escape($supplier->mobile); ?></td>
                                    <td><?php echo html_escape($supplier->address); ?></td>
                                    <td><?php
                                        if ($supplier->status == 1) {
                                            echo html_escape("Ativo");
                                        } else {
                                            echo html_escape("Inativo");
                                        }
                                        ?></td>
                                    <td class="center">
                                        <?php if ($this->permission->method('supplier', 'update')->access()): ?>
                                            <input name="url" type="hidden" id="url_<?php echo html_escape($supplier->id); ?>" value="<?php echo base_url("supplier/supplierlist/updateintfrm") ?>" />
                                            <a onclick="editinfo('<?php echo html_escape($supplier->id); ?>')" class="btn btn-info btn-sm text-cl1 text-white" data-toggle="tooltip" data-placement="left" title="Editar"><i class="fas fa-edit" aria-hidden="true"></i></a> 
                                            <?php
                                        endif;
                                        if ($this->permission->method('supplier', 'delete')->access()):
                                            ?>
                                            <a href="<?php echo base_url("supplier/supplierlist/delete/$supplier->id") ?>" onclick="event.preventDefault(); var u=this.href; showConfirm('<?php echo makeString(["are_you_sure"]) ?>', function(){ window.location.href=u; })" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Excluir"><i class="fa fa-trash" aria-hidden="true"></i></a> 
                                            <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $sl++; ?>
                            <?php } ?> 
                        <?php } ?> 
                    </tbody>
                </table>  <!-- /.table-responsive -->
            </div>
        </div>
    </div>


