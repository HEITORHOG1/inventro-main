<div class="row">
          <!-- left column -->
          <div class="col-md-6 offset-md-3">
            <!-- general form elements -->
            <div class="card card-primary card-outline">
              <div class="card-header">
                <h3 class="card-title"><?php echo html_escape($title);?></h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
               <?php echo form_open_multipart('dashboard/setting/create','class="form-inner"') ?>
                    <?php echo form_hidden('id',$setting->id) ?>
                <div class="card-body">

                  <div class="form-group">
                    <label for="title"><?php echo makeString(['application_title']) ?></label>
                    <input type="text" name="title" class="form-control" id="title" value="<?php echo html_escape($setting->title) ?>" placeholder="<?php echo makeString(['application_title']); ?>">
                  </div> 

                  <div class="form-group">
                    <label for="address"><?php echo makeString(['address']) ?></label>
                    <input type="text" name="address" class="form-control" id="address" value="<?php echo html_escape($setting->address) ?>" placeholder="<?php echo makeString(['address']) ?>">
                  </div>

                  <div class="form-group">
                    <label for="email"><?php echo makeString(['email'])?></label>
                    <input type="email" name="email" class="form-control" id="email" value="<?php echo html_escape($setting->email) ?>" placeholder="<?php echo makeString(['email'])?>">
                  </div>

                  <div class="form-group">
                    <label for="phone"><?php echo makeString(['phone']) ?></label>
                    <input type="text" name="phone" class="form-control" id="phone" value="<?php echo html_escape($setting->phone) ?>" placeholder="Telefone">
                  </div>

                  <?php if(!empty($setting->favicon)) {  ?>
                    <div class="form-group ">
                        <label for="faviconPreview" class="col-xs-3 col-form-label"></label>
                        <div class="col-xs-9">
                            <img src="<?php echo base_url(html_escape($setting->favicon)) ?>" alt="Favicon" class="img-thumbnail" />
                        </div>
                    </div>
                    <?php } ?>


                  <div class="form-group">
                    <label for="favicon"><?php echo makeString(['favicon']) ?></label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="favicon" class="custom-file-input" id="favicon">
                        <label class="custom-file-label" for="exampleInputFile"><?php echo makeString(['choose_file']); ?></label>
                        <input type="hidden" name="old_favicon" value="<?php echo html_escape($setting->favicon) ?>">
                      </div>
                      
                    </div>
                  </div>


                   <!-- if setting logo is already uploaded -->
                    <?php if(!empty($setting->logo)) {  ?>
                    <div class="form-group ">
                        <label for="logoPreview" class="col-xs-3 col-form-label"></label>
                        <div class="col-xs-9">
                            <img src="<?php echo html_escape(base_url($setting->logo)) ?>" alt="Picture" class="img-thumbnail" />
                        </div>
                    </div>
                    <?php } ?>

                  <div class="form-group">
                    <label for="logo"><?php echo makeString(['logo']) ?></label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" name="logo" class="custom-file-input" id="logo">
                        <label class="custom-file-label" for="exampleInputFile"><?php echo makeString(['choose_file']); ?></label>
                        <input type="hidden" name="old_logo" value="<?php echo html_escape($setting->logo) ?>">
                      </div>
                      
                    </div>
                  </div>
                <div class="form-group">
                    <label for="language"><?php echo makeString(['currency']) ?></label>
                     <?php echo  form_dropdown('currency',$currencyList,$setting->currency, 'class="form-control"') ?>
                  </div>
                  <div class="form-group">
                    <label for="language"><?php echo makeString(['language']) ?></label>
                     <?php echo  form_dropdown('language',$languageList,$setting->language, 'class="form-control"') ?>
                  </div>

                  <div class="form-group">
                    <label for="language"><?php echo makeString(['footer_text']) ?></label>
                    <textarea name="footer_text" class="form-control"  placeholder="Texto do Rodapé" maxlength="140" rows="7"><?php echo html_escape($setting->footer_text) ?></textarea>
                  </div>

                 


                  <div class="form-group">
                    <label for="timezone"><?php echo makeString(['timezone']) ?></label>
                    <select name="timezone" class="form-control" id="Timezone">
                          <option value=""><?php echo makeString(['select_one']); ?></option>
                          <?php foreach (timezone_identifiers_list() as $value) { ?>
                              <option value="<?php echo html_escape($value) ?>" <?php echo ((@$setting->timezone==$value)?'selected':null) ?>><?php echo html_escape($value) ?></option>";
                          <?php } ?>
                      </select>
                  </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit"  class="btn btn-primary"><?php echo makeString(['submit']); ?></button>
                </div>

              <?php echo form_close() ?>
            </div>
            <!-- /.card -->


          </div>
          <!--/.col (left) -->
         
        </div>