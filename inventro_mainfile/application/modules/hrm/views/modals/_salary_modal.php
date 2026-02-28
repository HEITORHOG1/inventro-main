      <div class="modal fade" id="salary_form">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>

            </div>
            <?php echo form_open_multipart('', array('name'=>'salary_form','id'=>'dip')) ?>
            <div class="modal-body">
                <div class="card-body">

                  <div class="form-group">
                    <label for="title"><?php echo makeString(['salary','amount'])?> <span class="text-danger">*</span></label>
                    <input type="text" name="salary_amount" class="form-control" id="salary_amount" value="<?php echo @$user->salary_amount ?>" requerd>
                  </div> 

                  <div class="form-group">
                    <label for="employee_id"><?php echo makeString(['employee'])?> <span class="text-danger">*</span></label>

                    <select name="employee_id" class="form-control" id="employee_id" required="">
                      <option value="">--<?php echo makeString(['select','employee'])?></option>
                      <?php foreach($employees as $employee){?>
                        <option value="<?php echo $employee->employee_id?>"> <?php echo html_escape($employee->employee_name);?></option>
                      <?php }?>
                    </select>
                  </div>
                  <input type="hidden" name="salary_id" id="salary_id">
                </div>

            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo makeString(['close'])?></button>
              <button type="submit" class="btn btn-primary dbtn"><?php echo makeString(['save'])?></button>
            </div>
            <?php echo form_close() ?>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->