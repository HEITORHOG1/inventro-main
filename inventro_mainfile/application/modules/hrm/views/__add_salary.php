<div class="row">
    <div class="col-12">

        <?php $this->load->view("hrm/modals/_salary_modal"); ?>



        <div class="card card-primary card-outline">
            <div class="card-header">
                <?php if ($this->permission->check_label('salary_setup')->create()->access()) { ?>
                    <button class="btn btn-sm btn-success float-right" onclick="addSalary()"> <?php echo makeString(['add', 'salary']) ?></button>
                <?php } ?>
                <h2 class="card-title"><?php echo html_escape($title) ?></h2>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <table id="salarytbl" class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th><?php echo makeString(['sl']) ?></th>
                            <th><?php echo makeString(['employee', 'name']) ?></th>
                            <th><?php echo makeString(['salary', 'amount']) ?></th>
                            <th width="100"><?php echo makeString(['action']) ?></th> 
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $i = 1;
                        foreach ($salaryes as $salary) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo html_escape($salary->employee_name); ?></td>
                                <td><?php echo html_escape($salary->salary_amount); ?></td>
                                <td>
                                    <?php if ($this->permission->check_label('salary_setup')->update()->access()) { ?>
                                        <a href="javascript:void(0)" onClick="editSalary('<?php echo $salary->salary_id ?>')" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                                    <?php } ?>
                                    <?php if ($this->permission->check_label('salary_setup')->delete()->access()) { ?>
                                        <a href="javascript:void(0)" onClick="deleteSalary('<?php echo $salary->salary_id ?>')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
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



<script src="<?php echo base_url() ?>application/modules/hrm/assets/js/custom_script.js" type="text/javascript"></script>


