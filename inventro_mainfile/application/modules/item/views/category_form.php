<link rel="stylesheet" href="<?php echo base_url() ?>application/modules/item/assets/css/style.css">
<div class="card card-primary card-outline">
    <?php if ($this->permission->method('add_category', 'create')->access()): ?>
    <div class="card-header">
        <h4 class="buttoncolor"><?php echo makeString(['category_list']) ?><small class="float-right"><a data-toggle="modal"
                    data-target="#categoryform" class="btn btn-primary buttoncolor">
                    <i class="ti-plus" aria-hidden="true"></i>
                    <?php echo makeString(['add_category']) ?></a></small></h4>
    </div>
    <?php endif; ?>
    <div id="categoryform" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <strong><?php echo makeString(['add_category']); ?></strong>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="panel">

                                <div class="panel-body">
                                    <?php echo form_open_multipart('item/category/category_form', 'class="form-inner"') ?>
                                    <?php echo form_hidden('id', $categorys->id) ?>

                                    <div class="form-group row">
                                        <label for="categoryname"
                                            class="col-sm-3 control-label"><?php echo makeString(['category_name']) ?></label>

                                        <div class="col-sm-9">
                                            <input type="text" name="categoryname" class="form-control"
                                                value="<?php echo html_escape($categorys->name) ?>" id="categoryname"
                                                placeholder="category Name">
                                            <input type="hidden" name="category_id"
                                                value="<?php echo html_escape($categorys->category_id) ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="parentcategory"
                                            class="col-sm-3 control-label"><?php echo makeString(['parent_category']) ?></label>

                                        <div class="col-sm-9">
                                            <select name="parent_category" class="form-control select2">
                                                <option value=""><?php echo makeString(['select_one']); ?></option>
                                                <?php foreach ($categorylist as $category) { ?>
                                                <option value="<?php echo $category->category_id; ?>" <?php
                                                    if ($category->category_id == $categorys->parent_id) {
                                                        echo 'selected';
                                                    }
                                                    ?>><?php echo html_escape($category->name); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group text-right">
                                        <button type="reset"
                                            class="btn btn-primary w-md m-b-5"><?php echo makeString(['reset']) ?></button>
                                        <button type="submit"
                                            class="btn btn-success w-md m-b-5"><?php echo makeString(['save']) ?></button>
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
        <!--  table area -->
        <div class="col-sm-12">
            <div class="card-body">
                <table id="datagrid" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php echo makeString(['sl']); ?></th>
                            <th><?php echo makeString(['category_name']); ?></th>
                            <th><?php echo makeString(['action']); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sl = 1;
                        foreach ($categorylist as $category) {
                            ?>

                        <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>">
                            <td><?php echo $sl; ?></td>
                            <td><?php echo html_escape($category->name); ?></td>
                            <td class="center">
                                <?php if ($this->permission->method('category', 'update')->access()): ?>
                                <input name="url" type="hidden" id="url_<?php echo $category->id; ?>"
                                    value="<?php echo base_url("item/Category/editfrm") ?>" />
                                <a onclick="editinfo('<?php echo $category->id; ?>')" class="btn btn-info btn-xs"
                                    data-toggle="tooltip" data-placement="left" title="Update"><i class="fas fa-edit"
                                        aria-hidden="true"></i></a>
                                <?php endif; ?>

                                <?php if ($this->permission->method('category', 'delete')->access()): ?>
                                <a href="<?php echo base_url('item/category/delete_category/' . html_escape($category->id)) ?>"
                                    class="btn btn-danger btn-xs"
                                    onclick="return confirm('Are You Sure to Want to Delete ?')"><i
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