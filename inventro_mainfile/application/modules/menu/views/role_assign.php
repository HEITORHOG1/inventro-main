<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <?php
        $error = $this->session->userdata('error');
        if (validation_errors() || $error) {
            $this->session->unset_userdata('error');
        } else {

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
        }

        $parent_menu = $this->db->select('*')
                ->from('sec_menu_item')
                ->get()
                ->result();
        ?>
        <!-- general form elements -->
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><?php echo html_escape($title); ?></h3>

            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <?php echo form_open_multipart('menu/crole/assing_user_role_save', 'class="form-inner"') ?>

            <div class="card-body">

                <div class="form-group row">
                    <label for="parent_menu" class="col-sm-2 col-form-label text-right"><?php echo html_escape('User Name');?> <i class="text-danger"> * </i></label>
                    <div class="col-sm-6">

                        <select name="user_id" class="form-control select2" onchange="userRole(this.value)" id="user_id" required>
                            <option value=""><?php echo html_escape('Select Role')?></option>
                            <?php
                            foreach ($user_list as $value) {
                                $fullname = $value->firstname . ' ' . $value->lastname;
                                ?>  
                                <option value="<?php echo $value->id; ?>">
                                    <?php echo html_escape($fullname) . '->' . html_escape($value->email); ?>

                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-3 before_assign_role_list" >
                        <div id="existrole" class="existroles">
                            <h4><?php echo makeString(['assigned_role']); ?></h4>         
                            <ul class="exis_ul">

                            </ul>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="parent_menu" class="col-sm-2 col-form-label text-right"><?php echo makeString(['role_name']); ?> <i class="text-danger"> * </i></label>
                    <div class="col-sm-6">
                        <select name="role_id[]" class="select2 select22_role" multiple="multiple" data-placeholder="Select a State" data-dropdown-css-class="select2-purple"  required>
                            <?php foreach (@$role_list as $value) { ?>  
                                <option value="<?php echo $value->role_id; ?>"><?php echo html_escape($value->role_name); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                </div>

            </div>
            <!-- /.card-body -->
            <div class="form-group row">
                <div class="col-sm-offset-1 col-sm-2"></div>
                <div class="col-sm-4">
                    <button type="submit" class="btn btn-primary edit_user_access_role_btn" ><?php echo makeString(['submit']); ?></button>

                </div>
            </div>

            <?php echo form_close() ?>
        </div>
        <!-- /.card -->


    </div>
    <!--/.col (left) -->
    <input type="hidden" name="" id="base_url" value="<?php echo base_url(); ?>">	
</div>
<script src="<?php echo base_url() ?>application/modules/menu/assets/js/scripts.js" type="text/javascript"></script>
