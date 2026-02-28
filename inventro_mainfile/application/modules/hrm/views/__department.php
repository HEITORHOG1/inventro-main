<div class="row">
    <div class="col-12">

        <?php $this->load->view("hrm/modals/_department_modal"); ?>



        <div class="card card-primary card-outline">
            <div class="card-header">
                <?php if ($this->permission->check_label('department')->create()->access()) { ?>

                    <button class="btn btn-sm btn-success float-right" onclick="addDepartment()"> <?php echo makeString(['add', 'department']) ?></button>
                <?php } ?>
                <h2 class="card-title"><?php echo html_escape($title) ?></h2>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="departmenttbl" class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th><?php echo makeString(['sl']) ?></th>
                            <th><?php echo makeString(['department_name']) ?></th>
                            <th><?php echo makeString(['department_description']) ?></th>
                            <th width="100"><?php echo makeString(['action']) ?></th> 
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $i = 1;
                        foreach ($departments as $department) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo html_escape($department->department_name); ?></td>
                                <td><?php echo html_escape($department->department_description); ?></td>
                                <td>
                                    <?php if ($this->permission->check_label('department')->update()->access()) { ?>
                                        <a href="javascript:void(0)" onClick="editDepartment('<?php echo $department->department_id ?>')" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                                    <?php } ?>
                                    <?php if ($this->permission->check_label('department')->delete()->access()) { ?>
                                        <a href="javascript:void(0)" onClick="deleteDepartment('<?php echo $department->department_id ?>')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
                                    <?php } ?>

                                </td>
                            </tr>

                        <?php } ?>

                    </tbody>

                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<input type="hidden" id="adddptstring" value="<?php echo makeString(['add', 'department']) ?>"/>
<input type="hidden" id="updateptstring" value="<?php echo makeString(['update', 'department']) ?>"/>


<script src="<?php echo base_url() ?>application/modules/hrm/assets/js/department.js" type="text/javascript"></script>


