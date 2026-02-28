<div class="row">

    <div class="col-12">

        <div class="card card-primary card-outline">

            <?php $this->load->view('hrm/modals/_salary_payment_modal'); ?>

            <div class="card-header">

                <button class="btn btn-sm btn-success float-right" onClick="salaryGenerate()"> <?php echo makeString(['salary', 'generate']) ?></button>
                <h2 class="card-title"><?php echo html_escape($title) ?></h2>

            </div>

            <!-- /.card-header -->
            <div class="card-body">

                <table id="salaryGenerateList" class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th><?php echo makeString(['sl']) ?></th>
                            <th><?php echo makeString(['employee']) ?></th>
                            <th><?php echo makeString(['salary', 'amount']) ?></th>
                            <th><?php echo makeString(['salary', 'month']) ?></th>
                            <th><?php echo makeString(['generate', 'date']) ?></th>
                            <th><?php echo makeString(['generate', 'by']) ?></th>
                            <th width="150"><?php echo makeString(['action']) ?></th> 
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $i = 1;
                        foreach ($salary_generat as $generat) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo html_escape($generat->employee_name); ?></td>
                                <td><?php echo html_escape($generat->salary_amount); ?></td>
                                <td><?php echo date("M Y", $generat->salary_month); ?></td>
                                <td><?php echo html_escape($generat->generate_date); ?></td>
                                <td><?php echo html_escape($generat->fullname); ?></td>
                                <td>
                                 
                                    <?php if ($generat->status == 0) { ?>
                                        <a href="javascript:void(0)" onClick="paymentSalary('<?php echo $generat->generat_id ?>')" class="btn btn-xs btn-success"><i class="fas fa-fill"></i> <?php echo makeString(['pay_now']) ?></a>
                                    <?php } else { ?>
                                        <a href="javascript:void(0)" class="btn btn-xs btn-success"><i class="fas fa-fill-drip"></i> <?php echo makeString(['paid']) ?></a>
                                        <a href="<?php echo base_url('hrm/salary/paid_recept/') . $generat->generat_id ?>" class="btn btn-xs btn-success"> <i class="fas fa-print"></i> <?php echo makeString(['receipt']) ?></a>
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



