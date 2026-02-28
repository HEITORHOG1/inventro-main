<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <?php
        $error = $this->session->flashdata('error');
        $success = $this->session->flashdata('success');
        if ($error != '') {
            echo $error;
        }
        if ($success != '') {
            echo $success;
        }
        ?>
        <div class="card card-primary card-outline">
            <div class="card-header">

                <h3 class="card-title"><?php echo html_escape($title); ?></h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <?php echo form_open_multipart('menu/crole/role_save', 'class="form-inner"') ?>

            <div class="card-body">
                <div class="form-group row">
                    <label for="menu_title" class="col-sm-3 col-form-label text-right"><?php echo makeString(['role_name']); ?><i class="text-danger"> * </i></label>
                    <div class="col-sm-6">
                        <input type="text" name="role_name" class="form-control" id="role_name" placeholder="Role Name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="description" class="col-sm-3 col-form-label text-right"><?php echo makeString(['description']); ?></label>
                    <div class="col-sm-6">
                        <textarea type="text" name="role_description" class="form-control" id="description" placeholder="Description">
                        </textarea>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
            <div class="card">
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="add_role_right" >
                        <label for="select_deselect">
                            <span class="select_cls"><strong><?php echo makeString(['select_deselect']); ?></strong></span>
                            <input type="checkbox" id="select_deselect">
                        </label>
                    </div>
                    <?php
                    $m = 0;
                    foreach ($modules as $module) {
                        $menu_item = $this->db->select('*')->from('sec_menu_item')->where('module', $module->module)->where('status', 1)
                                        ->get()->result();
                        ?>
                        <input type="hidden" name="module[]" value="<?php echo $module->module; ?>">
                        <table id="" class="table table-bordered table-striped">
                            <h4><?php echo ucwords(str_replace("_", " ", $module->module)); ?></h4>
                            <thead>
                                <tr>
                                    <th><?php echo makeString(['sl']); ?>.</th>
                                    <th><?php echo makeString(['menu_name']); ?></th>
                                    <th><?php echo makeString(['can_create']); ?>
                                        <label for="<?php echo $module->module; ?>_can_create_all" class="float-right">
                                            <span class="select_cls">
                                                <strong><?php echo makeString(['all']); ?></strong>
                                            </span>
                                            <input type="checkbox" id="<?php echo $module->module; ?>_can_create_all" class="can_create_all" value="<?php echo $module->module; ?>">
                                        </label>
                                    </th>
                                    <th><?php echo makeString(['can_read']); ?>
                                        <label for="<?php echo $module->module; ?>_can_read_all" class="float-right">
                                            <span class="select_cls">
                                                <strong><?php echo makeString(['all']); ?></strong>
                                            </span>
                                            <input type="checkbox" id="<?php echo $module->module; ?>_can_read_all" class="can_read_all" value="<?php echo $module->module; ?>">
                                        </label>
                                    </th>
                                    <th><?php echo makeString(['can_edit']); ?>
                                        <label for="<?php echo $module->module; ?>_can_edit_all" class="float-right">
                                            <span class="select_cls">
                                                <strong><?php echo makeString(['all']); ?></strong>
                                            </span>
                                            <input type="checkbox" id="<?php echo $module->module; ?>_can_edit_all" class="can_edit_all" value="<?php echo $module->module; ?>">
                                        </label>
                                    </th>
                                    <th><?php echo makeString(['can_delete']); ?>
                                        <label for="<?php echo $module->module; ?>_can_delete_all" class="float-right">
                                            <span class="select_cls">
                                                <strong><?php echo makeString(['all']); ?></strong>
                                            </span>
                                            <input type="checkbox"  id="<?php echo $module->module; ?>_can_delete_all" class="can_delete_all" value="<?php echo $module->module; ?>">
                                        </label></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if (!empty($menu_item)) {
                                    $sl = 0;
                                    foreach ($menu_item as $menu) {
                                        ?>

                                        <tr>
                                            <td><?php echo $sl + 1; ?></td>

                                            <td class="text-<?php echo ($menu->parent_menu ? 'right' : '') ?>"><?php echo ucwords(str_replace("_", " ", html_escape($menu->menu_title))); ?></td>
                                            <td>
                                                <div class="checkbox checkbox-success text-center">
                                                    <input type="checkbox" name="create[<?php echo $m ?>][<?php echo $sl ?>][]" value="1" id="create[<?php echo $m ?>]<?php echo $sl ?>" class="sameChecked <?php echo $menu->module; ?>_can_create">
                                                    <label for="create[<?php echo $m ?>]<?php echo $sl ?>"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="checkbox checkbox-success text-center">
                                                    <input type="checkbox" name="read[<?php echo $m ?>][<?php echo $sl ?>][]" value="1" id="read[<?php echo $m ?>]<?php echo $sl ?>" class="sameChecked <?php echo $menu->module; ?>_can_read">
                                                    <label for="read[<?php echo $m ?>]<?php echo $sl ?>"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="checkbox checkbox-success text-center">
                                                    <input type="checkbox" name="edit[<?php echo $m ?>][<?php echo $sl ?>][]" value="1" id="edit[<?php echo $m ?>]<?php echo $sl ?>" class="sameChecked <?php echo $menu->module; ?>_can_edit">
                                                    <label for="edit[<?php echo $m ?>]<?php echo $sl ?>"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="checkbox checkbox-success text-center">
                                                    <input type="checkbox" name="delete[<?php echo $m ?>][<?php echo $sl ?>][]" value="1" id="delete[<?php echo $m ?>]<?php echo $sl ?>" class="sameChecked <?php echo $menu->module; ?>_can_delete">
                                                    <label for="delete[<?php echo $m ?>]<?php echo $sl ?>"></label>
                                                </div>
                                            </td>
                                    <input type="hidden" name="menu_id[<?php echo $m ?>][<?php echo $sl ?>][]" value="<?php echo $menu->menu_id ?>">
                                    </tr>
                                    <?php
                                    $sl++;
                                }
                                $m++;
                            }
                            ?>
                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                    <?php } ?>
                </div>
                <!-- /.card-body -->
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><?php echo makeString(['submit']); ?></button>
            </div>
            <?php echo form_close() ?>
        </div>
        <!-- /.card -->
    </div>
    <!--/.col (left) -->

</div>
<script src="<?php echo base_url() ?>application/modules/menu/assets/js/scripts.js" type="text/javascript"></script>