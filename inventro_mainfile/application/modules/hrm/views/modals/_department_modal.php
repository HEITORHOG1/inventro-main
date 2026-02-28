      <div class="modal fade" id="department_form">
        <div class="modal-dialog modal-md">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Large Modal</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>

            </div>
            <?php echo form_open_multipart('', array('name'=>'department_form','id'=>'dip')) ?>
            <div class="modal-body">
                <div class="card-body">

                  <div class="form-group">
                    <label for="title"><?php echo makeString(['department','name'])?> <span class="text-danger">*</span></label>
                    <input type="text" name="department" class="form-control" id="department" value="<?php echo @$user->department ?>" requerd>
                  </div> 

                  <div class="form-group">
                    <label for="description"><?php echo makeString(['description'])?></label>
                    <textarea class="form-control" name="description" id="description"><?php echo @$user->description ?></textarea>
                    
                  </div>

                  <input type="hidden" name="department_id" id="department_id">


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