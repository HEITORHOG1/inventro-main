    <div class="row">
        <div class="col-12">
          
            <div class="card card-primary card-outline">

                <div class="card-header">
                  <h3 class="card-title"><?php echo html_escape($title)?></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="user_list" class="table table-bordered table-striped">
                        <thead>
                        
                            <tr>
                                <th>#LS</th>
                                <th><?php echo makeString(['image']) ?></th>
                                <th><?php echo makeString(['username']) ?></th>
                                <th><?php echo makeString(['email']) ?></th>
                                <th><?php echo makeString(['last_login']) ?></th>
                                <th><?php echo makeString(['last_logout']) ?></th>
                                <th><?php echo makeString(['ip_address']) ?></th>
                                <th><?php echo makeString(['status']) ?></th>
                                <th><?php echo html_escape('User type');?></th>
                                <th width="100"><?php echo makeString(['action']) ?></th> 
                            </tr>

                        </thead>

                        <tbody>

                            <?php 
                            $i=1;
                            foreach($users as $user){
                                if($users){
                                  $userimg = base_url().$user->image;
                                }else{
                                  $userimg = base_url('admin_assets/img/user/default-user.png');
                                }
                            ?>
                              <tr>
                                <td><?php echo $i++;?></td>
                                <td><img src="<?php echo html_escape($userimg);?>" width="50"></td>
                                <td><?php echo html_escape($user->fullname);?></td>
                                <td><?php echo html_escape($user->email);?></td>
                                <td><?php echo html_escape($user->last_login);?></td>
                                <td><?php echo html_escape($user->last_logout);?></td>
                                <td><?php echo html_escape($user->ip_address);?></td>
                                <td><?php echo html_escape(($user->status==1?'Active':'Inactive'));?></td>
                                <td>
                                  <?php 
                                    if($user->is_admin==1){ echo html_escape("Super admin");}
                                    if($user->is_admin==2){ echo html_escape("User");}
                                  ?>
                                    
                                </td>
                                <td>
                                  <?php if($user->is_admin!=1){?>
                                  <a href="<?php echo base_url()?>dashboard/user/form/<?php echo html_escape($user->id)?>" class="btn btn-xs btn-success"><i class="fa fa-edit"></i></a>
                                  <a href="<?php echo base_url()?>dashboard/user/delete/<?php echo html_escape($user->id)?>" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>
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


