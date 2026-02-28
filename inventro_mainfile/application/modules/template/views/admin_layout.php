<!DOCTYPE html>
<html>
    <head>
      <?php $this->load->view('admin_include/head') ?>
    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <input type="hidden" id="mainsiteurl" value="<?php echo base_url(); ?>"/>
            <input type="hidden" id='csrf_token' value="<?php echo $this->security->get_csrf_hash();?>" />
          <!-- Navbar -->
          <?php $this->load->view('admin_include/nave_header') ?>
          <!-- /.navbar -->

          <!-- Main Sidebar Container -->
          <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?php echo base_url('dashboard/home') ?>" class="brand-link">
              <img src="<?php echo base_url((!empty($setting->logo)?$setting->logo:'admin_assets/img/icons/mini-logo.png')) ?>" alt="Expart6" class="brand-image opcity-cl">
              <span class="brand-text font-weight-light"><br/></span>
            </a>
            <!-- Sidebar -->
            <?php $this->load->view('admin_include/sidebar') ?>
            <!-- /.sidebar -->
          </aside>


          <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <div class="content-header">
                  <div class="container-fluid">
                    <div class="row mb-2">
                      <div class="col-sm-6">
                        
                      </div><!-- /.col -->
                        <div class="col-sm-6">
                           
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                  </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->


                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                   <!-- load messages -->
                      <?php $this->load->view('admin_include/messages') ?>
                      <!-- load custom page -->
                      <?php echo $this->load->view($module.'/'.$page) ?>
                    <!-- /.row (main row) -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>


          <!-- /.content-wrapper -->
          <footer class="main-footer">
                <strong>
                  <?php echo (!empty($setting->footer_text)?$setting->footer_text:null) ?> <a href="<?php echo current_url() ?>"><?php echo (!empty($setting->title)?$setting->title:null) ?></a>.
                </strong>
                <?php echo (!empty($setting->address)?$setting->address:null) ?> 
                <div class="float-right d-none d-sm-inline-block">
                  <b>Versão</b> 3.0.1
                </div>
          </footer>

          <!-- Control Sidebar -->
          <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
          </aside>
          <!-- /.control-sidebar -->
        </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
         <input type="hidden" id="sigmment1" value="<?php echo $this->uri->segment(1); ?>"/>
          <input type="hidden" id="sigmment2" value="<?php echo $this->uri->segment(2); ?>"/>
          <input type="hidden" id="sigmment3" value="<?php echo $this->uri->segment(3); ?>"/>
        <?php $this->load->view('admin_include/footer_js') ?>

    </body>
</html>
