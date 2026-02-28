      <div class="modal fade" id="salary_payment">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><?php echo makeString(['salary','payment'])?></h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>

            </div>
            <?php echo form_open_multipart('', array('name'=>'salary_payment','id'=>'dip')) ?>

            <div class="modal-body">
                <div class="card-body">

                  <div class="form-group">
                    <label for="title"><?php echo makeString(['employee','name'])?><span class="text-danger">*</span></label>
                    <input type="text" name="employee_name" class="form-control" id="employee_name" readonly="" requerd>
                  </div> 

                  <div class="form-group">
                    <label for="title"><?php echo makeString(['salary','amount'])?> <span class="text-danger">*</span></label>
                    <input type="text" name="salary_amount" class="form-control" id="salary_amount" readonly="" requerd>
                  </div>

                  <div class="form-group">
                    <label for="title"><?php echo makeString(['paid','salary','amount'])?> <span class="text-danger">*</span></label>
                    <input type="text" name="paid_salary_amount" class="form-control" id="paid_salary_amount" requerd>
                  </div>

                  <div class="form-group">
                    <label for="title"><?php echo makeString(['payment','note'])?> <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="payment_note"></textarea>
                  </div>

                
                  <input type="hidden" name="generate_id" id="generate_id">
                  <input type="hidden" name="employee_id" id="employee_id">


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