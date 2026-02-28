    <div class="row">
        <div class="col-12">
          <?php $this->load->view("hrm/modals/_designation_modal"); ?>

            
          <div class="card card-primary card-outline">
            <div class="card-header">
              <?php if($this->permission->check_label('designation')->create()->access()){ ?>
              <button class="btn btn-sm btn-success float-right" onClick="addDesignation()"><?php echo makeString(['name','designation'])?></button>
              <?php } ?>
              <h2 class="card-title"><?php echo html_escape($title) ?></h2>
            </div>
            <!-- /.card-header -->
            <div class="card-body" i>
              <table id="designationtbl" class="table table-bordered table-striped">
                  <thead>
                  
                     <tr>
                        <th><?php echo makeString(['sl'])?></th>
                        <th><?php echo makeString(['designation'])?></th>
                        <th><?php echo makeString(['designation','description']) ?></th>
                        <th width="100"><?php echo makeString(['action']) ?></th> 
                    </tr>

                  </thead>
                <tbody>

                <?php 
                $i=1;
                foreach($designations as $designation){

                ?>
                  <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo html_escape($designation->designation_name);?></td>
                    <td><?php echo html_escape($designation->designation_description);?></td>
                    <td>
                      <?php if($this->permission->check_label('designation')->update()->access()){ ?>
                      <a href="javascript:void(0)" onClick="editDesignation('<?php echo $designation->designation_id?>')" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                    <?php } ?>
                    <?php if($this->permission->check_label('designation')->delete()->access()){ ?>
                      <a href="javascript:void(0)" onClick="deleteDesignation('<?php echo $designation->designation_id?>')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
                    <?php }?>
                     
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

  <script src="<?php echo base_url() ?>application/modules/hrm/assets/js/designation.js" type="text/javascript"></script>


