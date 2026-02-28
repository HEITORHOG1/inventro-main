<div class="row">
    <div class="col-12">

         

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h2 class="card-title"><?php echo html_escape($title) ?></h2>

            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="employeetbl" class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th><?php echo makeString(['sl']) ?></th>
                            <th><?php echo makeString(['employee', 'name']) ?></th>
                            <th><?php echo makeString(['email']) ?></th>
                            <th><?php echo makeString(['phone']) ?></th>
                            <th><?php echo makeString(['salary']) ?></th>
                            <th width="100"><?php echo makeString(['action']) ?></th> 
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $i = 1;
                        foreach ($employees as $employee) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo html_escape($employee->employee_name); ?></td>
                                <td><?php echo html_escape($employee->em_email); ?></td>
                                <td><?php echo html_escape($employee->em_phone); ?></td>
                                <td><?php echo html_escape($employee->em_salary); ?></td>
                                <td>

                                    <a href="<?php echo base_url('hrm/employee/edit_employee/' . $employee->employee_id) ?>" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                                    <a href="javascript:void(0)" onClick="delEteemployee('<?php echo $employee->employee_id ?>')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>


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



<script src="<?php echo base_url() ?>application/modules/hrm/assets/js/employee_script.js" type="text/javascript"></script>


