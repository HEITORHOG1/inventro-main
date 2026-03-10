<link rel="stylesheet" href="<?php echo base_url() ?>application/modules/item/assets/css/style.css">
<div class="card card-primary card-outline">
    <?php if ($this->permission->method('add_unit', 'create')->access()): ?>
    <div class="card-header">
        <h4 class="buttoncolor"><?php echo makeString(['unit_list']) ?><small class="float-right"><a data-toggle="modal"
                    data-target="#unitform" class="btn btn-primary buttoncolor">
                    <i class="ti-plus" aria-hidden="true"></i>
                    <?php echo makeString(['add_unit']) ?></a></small></h4>
    </div>
    <?php endif; ?>
    <div id="unitform" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['add_unit']); ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="panel">
                                <div class="card-body">
                                    <?php echo form_open_multipart('item/unit/unit_form', 'class="form-inner"') ?>
                                    <?php echo form_hidden('id', $units->id) ?>
                                    <div class="form-group row">
                                        <label for="unitname"
                                            class="col-sm-3 control-label"><?php echo makeString(['unit_name']) ?></label>

                                        <div class="col-sm-9">
                                            <input type="text" name="unitname" class="form-control"
                                                value="<?php echo html_escape($units->unit_name) ?>" id="unitname"
                                                placeholder="Unit Name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?php if (empty($units->id)) { ?>
                                        <button type="submit"
                                            class="btn btn-success"><?php echo makeString(['save']) ?></button>
                                        <?php } else { ?>
                                        <button type="submit"
                                            class="btn btn-success"><?php echo makeString(['update']) ?></button>
                                        <?php } ?>
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
    <div id="edit" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['update']); ?></strong>
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
        <div class="col-sm-12">
            <div class="card-body">
                <table id="datagrid" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo makeString(['sl']); ?></th>
                            <th><?php echo makeString(['unit_name']); ?></th>
                            <th><?php echo makeString(['action']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        foreach ($unitlist as $unit) {
                            ?>
                        <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>">
                            <td><?php echo html_escape($sl); ?></td>
                            <td><?php echo html_escape($unit->unit_name); ?></td>
                            <td class="center">
                                <?php if ($this->permission->method('unit', 'update')->access()): ?>
                                <input name="url" type="hidden" id="url_<?php echo html_escape($unit->id); ?>"
                                    value="<?php echo base_url("item/unit/editfrm") ?>" />
                                <a onclick="editinfo('<?php echo $unit->id; ?>')" class="btn btn-info btn-xs text-cl1"
                                    data-toggle="tooltip" data-placement="left" title="Update"><i class="fas fa-edit"
                                        aria-hidden="true"></i></a>
                                <?php endif; ?>

                                <?php if ($this->permission->method('unit', 'delete')->access()): ?>
                                <a href="<?php echo base_url('item/unit/delete_unit/' . html_escape($unit->id)) ?>"
                                    class="btn btn-danger btn-xs"
                                    onclick="event.preventDefault(); var u=this.href; showConfirm('Tem certeza que deseja excluir?', function(){ window.location.href=u; })"><i
                                        class="fas fa-trash-alt"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php $sl++; ?>

                        <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>