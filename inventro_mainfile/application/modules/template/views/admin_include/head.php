  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo html_escape((!empty($setting->title)?$setting->title:null)) ?> :: <?php echo html_escape((!empty($title)?$title:null)) ?></title>


<link rel="shortcut icon" href="<?php echo base_url((!empty($setting->favicon)?$setting->favicon:'assets/img/icons/favicon.png')) ?>" type="image/x-icon">

  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/bootstrap/css/bootstrap.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/select2/css/select2.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/datatables-bs4/css/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/toastr/toastr.min.css">
  
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/dist/css/custome.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/daterangepicker/daterangepicker.css">
   <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/jquery-ui/jquery-ui.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/summernote/summernote-bs4.css">
 

  <script src="<?php echo base_url('admin_assets') ?>/plugins/jquery/jquery.min.js"></script>

  <!-- Include module style -->
<?php
    $path = 'application/modules/';
    $map  = directory_map($path);
    if (is_array($map) && sizeof($map) > 0)
    foreach ($map as $key => $value) {
        $css  = str_replace("\\", '/', $path.$key.'assets/css/style.css');  
        if (file_exists($css)) {
           echo '<link href="'.base_url($css).'" rel="stylesheet">';
        }   
    }   
?>
