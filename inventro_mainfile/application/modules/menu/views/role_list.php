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
                    <h2 class="card-title"><?php echo html_escape('Role List')?></h2>
                </div>
                <div class="card-body"> 
                    <table  class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?php echo makeString(['sl']); ?></th>
                                <th><?php echo makeString(['role_name']); ?></th>
                                <th><?php echo makeString(['description']); ?></th>
                                <th  class="text-center"><?php echo makeString(['action']); ?> </th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($role_list)) {
                                ?>
                                <?php $sl = 1; ?>
                                <?php foreach ($role_list as $role) { ?>
                                    <tr class="<?php echo ($sl & 1) ? "odd gradeX" : "even gradeC" ?>"> 
                                        <td><?php echo $sl; ?></td>
                                        <td><?php echo html_escape($role->role_name); ?></td>
                                        <td><?php echo html_escape($role->role_description); ?></td>

                                        <td class="text-center"><a href="<?php echo base_url("menu/crole/edit_role/$role->role_id") ?>" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> </a> 
                                            <a href="<?php echo base_url("menu/crole/delete/$role->role_id") ?>" onclick="event.preventDefault(); var u=this.href; showConfirm('<?php echo makeString(["are_you_sure"]) ?>', function(){ window.location.href=u; })" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Delete "><i class="fa fa-trash" aria-hidden="true"></i></a></td>



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