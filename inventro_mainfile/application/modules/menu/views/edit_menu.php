<div class="row">
  <!-- left column -->
  <div class="col-md-12">
    <?php
    $error = $this->session->userdata('error');
    if (validation_errors() || $error) {
    $this->session->unset_userdata('error');
    }else{
    
    $success = $this->session->userdata('success');
    if (validation_errors() || $success) {
    ?>
    
    <div class="alert alert-success">
      <?php
      if (validation_errors()) {
      echo validation_errors();
      }else{
      echo $success;
      }
      ?>
    </div>
    <?php
    }
    $this->session->unset_userdata('success');
    
    }
    
    $parent_menu=$this->db->select('*')
    ->from('sec_menu_item')
    ->get()
    ->result();
    ?>
    <!-- general form elements -->
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title"><?php echo html_escape($title);?></h3>
        
      </div>
      <!-- /.card-header -->
      <!-- form start -->
      <?php echo form_open_multipart('menu/menu_setting/update_menu/'.$menu_edit->menu_id,'class="form-inner"') ?>
      <input type="hidden" name="menu_id" value="<?php echo $menu_edit->menu_id;?>">
      <div class="card-body">
        <div class="form-group row">
          <label for="menu_title" class="col-sm-3 col-form-label text-right"><?php echo makeString(['menu_title']); ?> <i class="text-danger"> * </i></label>
          <div class="col-sm-6">
            <input type="text" name="menu_title" class="form-control" id="menu_title" value="<?php echo html_escape($menu_edit->menu_title);?>">
          </div>
        </div>
        <div class="form-group row">
          <label for="page_url" class="col-sm-3 col-form-label text-right"><?php echo makeString(['page_url']); ?></label>
          <div class="col-sm-6">
            <input type="text" name="page_url" class="form-control" id="page_url" value="<?php echo html_escape($menu_edit->page_url);?>">
          </div>
        </div>
        <div class="form-group row">
          <label for="module" class="col-sm-3 col-form-label text-right"><?php echo makeString(['module_name']); ?> <i class="text-danger"> * </i></label>
          <div class="col-sm-6">
            <input type="module" name="module_name" class="form-control" id="module" value="<?php echo html_escape($menu_edit->module);?>">
          </div>
        </div>
        <div class="form-group row">
          <label for="parent_menu" class="col-sm-3 col-form-label text-right"><?php echo makeString(['parent_menu']); ?></label>
          <div class="col-sm-6">
            <select name="parent_menu" class="form-control select2">
              <option value=""><?php echo makeString(['select_one']); ?></option>
              <?php
              foreach (@$parent_menu as $value) {
              ?>
              <option value="<?php echo $value->menu_id;?>"<?php if($menu_edit->parent_menu==$value->menu_id){ echo "selected"  ;}?>><?php echo html_escape($value->menu_title);?></option>
              <?php }?>
            </select>
          </div>
        </div>
      </div>
      <!-- /.card-body -->
      <div class="form-group row">
        <div class="col-sm-offset-1 col-sm-3"></div>
        <div class="col-sm-6">
          
          <button type="submit" class="btn btn-primary"><?php echo makeString(['submit']); ?></button>
          
        </div>
      </div>
      <?php echo form_close() ?>
    </div>
    <!-- /.card -->
  </div>
  <!--/.col (left) -->
  
</div>