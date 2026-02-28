<div class="modal fade" id="attendance_form">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Large Modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <?php echo form_open_multipart('', array('name' => 'attendance_form', 'id' => 'dip')) ?>
            <div class="modal-body">
                <div class="card-body">


                    <div class="form-group">

                        <label for="employee_id"> <?php echo makeString(['select_employee']); ?> <span class="text-danger">*</span></label>

                        <select name="employee_id" class="form-control" id="employee_id" required="">

                            <option value=""><?php echo makeString(['select_one']); ?></option>
                            <?php foreach ($employees as $employee) { ?>
                                <option value="<?php echo $employee->employee_id ?>"> <?php echo html_escape($employee->employee_name); ?></option>
                            <?php } ?>

                        </select>

                    </div>


                    <input type="hidden" name="attendance_id" id="attendance_id">


                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo makeString(['close']); ?></button>
                <button type="submit" class="btn btn-primary dbtn"><?php echo makeString(['save']); ?></button>
            </div>
            <?php echo form_close() ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<div class="modal fade" id="attendance_edit">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <?php echo form_open_multipart('', array('name' => 'attendance_edit', 'id' => 'edit_at')) ?>
            <div class="modal-body">
                <div class="card-body">


                    <div class="form-group">

                        <label for="employee_id"> <?php echo makeString(['select_employee']); ?><span class="text-danger">*</span></label>

                        <select name="employee_id" class="form-control" id="employee_id" required="">

                            <option value=""><?php echo makeString(['select_employee']); ?></option>
                            <?php foreach ($employees as $employee) { ?>
                                <option value="<?php echo $employee->employee_id ?>"> <?php echo html_escape($employee->employee_name); ?></option>
                            <?php } ?>

                        </select>

                    </div>



                    <div class="form-group">

                        <label for="in_time"> <?php echo makeString(['in_time']); ?> <span class="text-danger">*</span></label>

                        <input type="text" name="in_time" id="in_time" class="form-control">

                    </div>


                    <div class="form-group">

                        <label for="out_time"> <?php echo makeString(['out_time']); ?> <span class="text-danger">*</span></label>

                        <input type="text" name="out_time" id="out_time" class="form-control">

                    </div>


                    <div class="form-group">

                        <label for="stay_time"> <?php echo makeString(['stay_time']); ?> <span class="text-danger">*</span></label>

                        <input type="text" name="stay_time" id="stay_time" class="form-control">
                    </div>
                    <input type="hidden" name="attendance_id" id="attendance_id1">
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo makeString(['close']); ?></button>
                <button type="submit" class="btn btn-primary dbtn"><?php echo makeString(['save']); ?></button>
            </div>
            <?php echo form_close() ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->      