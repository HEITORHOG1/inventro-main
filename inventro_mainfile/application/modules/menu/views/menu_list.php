<div class="row">
    <!--  table area -->
    <div class="col-sm-12">
        <?php
        $success = $this->session->userdata('success');
        if (validation_errors() || $success) {
            ?>

            <div class="alert alert-success">
                <?php
                if (validation_errors()) {
                    echo validation_errors();
                } else {
                    echo $success;
                }
                ?>
            </div>
            <?php
        }
        $this->session->unset_userdata('success');
        ?>
        <div class="card-body">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h2 class="card-title"><?php echo makeString(['menu_list']); ?></h2>
                </div>
                <div class="card-body">
                    <table id="datagrid" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?php echo makeString(['sl']); ?></th>
                                <th><?php echo makeString(['menu_title']); ?></th>
                                <th><?php echo makeString(['page_url']); ?></th>
                                <th><?php echo makeString(['module_name']); ?></th>
                                <th><?php echo makeString(['parent_menu']); ?></th>
                                <th><?php echo makeString(['status']); ?></th>
                                <th><?php echo makeString(['action']); ?></th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($menu_list)) {
                                ?>
                                <?php $sl = 1; ?>
                                <?php
                                foreach ($menu_list as $menus) {
                                    $parent_menu = $this->db->select('menu_title')
                                            ->from('sec_menu_item')
                                            ->where('menu_id', $menus->parent_menu)
                                            ->get()
                                            ->row();
                                    ?>
                                    <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>"> 
                                        <td><?php echo $sl; ?></td>
                                        <td><?php echo html_escape($menus->menu_title); ?></td>
                                        <td><?php echo html_escape($menus->page_url); ?></td>
                                        <td><?php echo html_escape($menus->module); ?></td>

                                        <td><?php
                                            if (!empty($parent_menu->menu_title)) {
                                                echo html_escape($parent_menu->menu_title);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($menus->status == 1) {
                                                ?>
                                                <a href="<?php echo base_url('menu/menu_setting/active_menu/' . $menus->menu_id); ?>" class="btn btn-primary btn-sm">
                                                    <?php echo html_escape('active')?></a>
                                            <?php } else {
                                                ?>
                                                <a href="<?php echo base_url('menu/menu_setting/deactive_menu/' . $menus->menu_id); ?>" class="btn btn-danger btn-sm">
                                                    <?php echo html_escape('Inactive')?></a>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td><a href="<?php echo base_url("menu/menu_setting/edit_menu/$menus->menu_id") ?>" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> </a> <a href="<?php echo base_url("menu/menu_setting/delete/$menus->menu_id") ?>" onclick="return confirm('<?php echo makeString(["are_you_sure"]) ?>')" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Delete "><i class="fa fa-trash" aria-hidden="true"></i></a></td>



                                    </tr>
                                    <?php $sl++; ?>
                                <?php } ?> 
                            <?php } ?> 
                        </tbody>
                    </table>  
                </div>
            </div>

        </div>
    </div>
</div>
</div>