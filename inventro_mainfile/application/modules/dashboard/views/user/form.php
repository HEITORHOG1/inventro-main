<div class="row">
          <!-- left column -->
          <div class="col-md-6 offset-md-3">
            <!-- general form elements -->
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title"><?php echo html_escape((!empty($title)?$title:''));?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
               <?php echo form_open_multipart('dashboard/user/form/'.$user->id,'class="form-inner"') ?>
                  
                <?php echo form_hidden('id',$user->id) ?>

                <div class="card-body">

                  <div class="form-group">
                    <label for="title"><?php echo html_escape('Nome')?></label>
                    <input type="text" name="firstname" class="form-control" id="firstname" value="<?php echo html_escape($user->firstname) ?>" placeholder="Nome">
                  </div> 

                  <div class="form-group">
                    <label for="lastname"><?php echo makeString(['lastname']) ?></label>
                    <input type="text" name="lastname" class="form-control" id="lastname" value="<?php echo html_escape($user->lastname) ?>" placeholder="<?php echo makeString(['address']) ?>">
                  </div>

                  <div class="form-group">
                    <label for="email"><?php echo makeString(['email'])?></label>
                    <input type="email" name="email" class="form-control" id="email" value="<?php echo html_escape($user->email) ?>" placeholder="<?php echo makeString(['email'])?>">
                  </div>

                  <div class="form-group">
                    <label for="password"><?php echo makeString(['password'])?></label>
                    <input type="password" name="password" class="form-control" id="password">.
                    
                  </div>


                  <?php if(!empty($user->image)) {  ?>
                    <div class="form-group ">
                        <label for="faviconPreview" class="col-xs-3 col-form-label"></label>
                        <div class="col-xs-9">
                            <img src="<?php echo base_url(html_escape($user->image)) ?>" alt="Favicon" class="img-thumbnail" />
                        </div>
                    </div>
                    <?php } ?>


                  <div class="form-group">
                    <label for="image"><?php echo makeString(['image']) ?></label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="image" class="custom-file-input" id="image">
                        <label class="custom-file-label" for="exampleInputFile">Escolher arquivo</label>
                        <input type="hidden" name="old_image" value="<?php echo html_escape($user->image) ?>">
                      </div>
                      
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="about"><?php echo makeString(['about']) ?></label>
                    <textarea name="about" class="form-control"  placeholder="Sobre" maxlength="140" rows="7"><?php echo html_escape($user->about) ?></textarea>
                  </div>

                  <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" name="status" <?php echo ($user->status==1?'checked':'')?> type="checkbox" id="statuscheck" value="1">
                    <label for="statuscheck" class="custom-control-label"><?php echo html_escape('Status')?></label>
                  </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary"><?php echo html_escape('Enviar');?></button>
                </div>

              <?php echo form_close() ?>
            </div>
            <!-- /.card -->


          </div>
          <!--/.col (left) -->
         
        </div>