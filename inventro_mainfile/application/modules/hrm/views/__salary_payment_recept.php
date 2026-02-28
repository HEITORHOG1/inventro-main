<div class="row">

    <div class="col-12">

        <div class="card card-primary card-outline">

            <div class="card-header">
                <h2 class="card-title"><?php echo html_escape($title) ?></h2>
            </div>

            <!-- /.card-header -->
            <div class="card-body">

                <div class="invoice p-3 mb-3">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <img src="<?php echo base_url($setting->logo) ?>" alt="Picture" class="img-thumbnail" />
                                <small class="float-right"><?php echo makeString(['date']) ?>: <?php echo date('Y/m/d') ?></small>
                            </h4>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">

                            <strong><?php echo makeString(['salary', 'date']) ?>: <?php echo html_escape($salary->payment_date) ?></strong><br>
                            <strong ><?php echo makeString(['employee']) ?>: <?php echo html_escape($salary->employee_name); ?></strong><br>
                            <strong ><?php echo makeString(['department']) ?>: <?php echo html_escape($salary->department_name); ?></strong><br>
                            <strong ><?php echo makeString(['designation']) ?>: <?php echo html_escape($salary->designation_name); ?></strong><br>

                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">
                            <h4><?php echo makeString(['salary', 'receipt']) ?> : <?php echo date('d, D, Y') ?></h4>
                            <h2><?php echo html_escape($setting->title) ?></h2>
                            <small><?php echo html_escape($setting->address) ?></small>

                        </div>
                        <!-- /.col -->
                        <div class="col-sm-4 invoice-col">

                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table">
                                <tbody><tr>
                                        <td class="left-panel" style="border-right: 1px solid #ccc;"> 
                                            <table class="" width="100%">
                                                <thead>
                                                    <tr class="employee">
                                                        <th class="name text-center" colspan="2"><?php echo makeString(['earnings']); ?></th>
                                                    </tr>
                                                </thead>

                                                <tbody class="details">
                                                    <tr class="entry">
                                                        <td> <?php echo makeString(['main_salary']); ?></td>
                                                        <td> <?php echo html_escape($salary->salary_amount) ?> </td>
                                                    </tr>
                                                    <tr>
                                                        <td><?php echo makeString(['paid', 'amount']) ?></td>
                                                        <td><?php echo html_escape($salary->paid_amount) ?></td>
                                                    </tr>             
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>





