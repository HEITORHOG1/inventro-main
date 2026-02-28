    <div class="row">
        <div class="col-12">

          <?php $this->load->view("hrm/modals/_attendance_modal"); ?>

          
          <div class="card card-primary card-outline">
            <div class="card-header">
              <?php if($this->permission->check_label('attendance')->create()->access()){ ?>
               <button class="btn btn-sm btn-success float-right" onclick="addAttendance()"> <?php echo makeString(['add','attendance'])?></button>
             <?php } ?>
              <h2 class="card-title"><?php echo html_escape($title)?></h2>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              <table id="attendancetbl" class="table table-bordered table-striped">

                  <thead>
                  
                    <tr>
                        <th><?php echo makeString(['sl'])?></th>
                        <th><?php echo makeString(['employee','name']) ?></th>
                        <th><?php echo makeString(['date']) ?></th>
                        <th><?php echo makeString(['in_time']) ?></th>
                        <th><?php echo makeString(['out_time']) ?></th>
                        <th><?php echo makeString(['stay_time']) ?></th>
                        <th width="100"><?php echo makeString(['action']) ?></th> 
                    </tr>

                  </thead>

                <tbody>

                  <?php 
                  $i=1;
                  foreach($attendances as $val){

                  ?>
                    <tr>
                      <td><?php echo $i++;?></td>
                      <td><?php echo html_escape($val->employee_name);?></td>
                      <td><?php echo html_escape($val->date);?></td>
                      <td><?php echo html_escape($val->in_time);?></td>
                      <td><?php echo @$val->out_time;?></td>
                      <td><?php echo @$val->staytime;?></td>
                      <td>
                        <?php if(empty($val->out_time)){?>
                        <a href="javascript:void(0)" onClick="addOutTime('<?php echo $val->attandence_id?>')" title="Out time" class="btn btn-xs btn-success"><i class="fa fa-clock"></i></a>
                        <?php } ?>
                        <?php if($this->permission->check_label('attendance')->update()->access()){ ?>
                        <a href="javascript:void(0)" onClick="editAttendance('<?php echo $val->attandence_id?>')" title="Edit" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                        <?php } ?>
                        <?php if($this->permission->check_label('attendance')->delete()->access()){ ?>
                        <a href="javascript:void(0)" onClick="deleteAttendance('<?php echo $val->attandence_id?>')" title="Delete" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
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



    <script src="<?php echo base_url() ?>application/modules/hrm/assets/js/attendance.js" type="text/javascript"></script>


