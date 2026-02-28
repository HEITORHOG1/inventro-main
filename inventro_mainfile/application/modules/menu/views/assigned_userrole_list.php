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
            }else{
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
                    <h2 class="card-title"><?php echo html_escape('Assigned List')?></h2>
                </div>
                <div class="card-body">
                    <table  class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo makeString(['sl']); ?></th>
                                <th><?php echo makeString(['user_name']); ?></th>
                                <th><?php echo makeString(['role_name']); ?></th>
                                <th><?php echo makeString(['action']); ?></th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($sec_user_access_tbl)) {
                            ?>
                            <?php $sl = 1; ?>
                            <?php
                            foreach ($sec_user_access_tbl as $key => $value) {
                            
                            $sql = "SELECT a.fk_role_id, a.fk_user_id, b.role_name FROM sec_user_access_tbl a
                            JOIN sec_role_tbl b ON b.role_id = a.fk_role_id
                            WHERE a.fk_user_id = '$value->fk_user_id'";
                            $query = $this->db->query($sql)->result();
                            
                            ?>
                            <tr class="<?php echo ($sl & 1)?"odd gradeX":"even gradeC" ?>">
                                <td><?php echo $sl; ?></td>
                                <td><?php echo html_escape($value->firstname).' '.html_escape($value->lastname);?></td>
                                <!-- lastname -->
                                <td>
                                    <ul>
                                        <?php
                                        foreach ($query as $role) {
                                        echo "<li>" . html_escape($role->role_name) . "</li>";
                                        }
                                        ?>
                                    </ul>
                                </td>
                                <td class="text-center"><a href="<?php echo base_url("menu/crole/edit_assigned_role/$value->role_acc_id") ?>" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> </a>
                            </td>
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