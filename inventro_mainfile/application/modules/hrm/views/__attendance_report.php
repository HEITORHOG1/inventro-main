<div class="row">
    <div class="col-12">

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h2 class="card-title"><?php echo html_escape($title) ?></h2>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <table id="attendance_report" class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th><?php echo makeString(['sl']) ?></th>
                            <th><?php echo makeString(['employee', 'name']) ?></th>
                            <th><?php echo makeString(['date']) ?></th>
                            <th><?php echo makeString(['in_time']) ?></th>
                            <th><?php echo makeString(['out_time']) ?></th>
                            <th><?php echo makeString(['stay_time']) ?></th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $i = 1;
                        foreach ($attendances as $val) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo html_escape($val->employee_name); ?></td>
                                <td><?php echo html_escape($val->date); ?></td>
                                <td><?php echo html_escape($val->in_time); ?></td>
                                <td><?php echo @$val->out_time; ?></td>
                                <td><?php echo @$val->staytime; ?></td>

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



