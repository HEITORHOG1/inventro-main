<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo (!empty($setting->title)?$setting->title:null) ?> :: <?php echo (!empty($title)?$title:null) ?></title>
  <!-- Dizer ao navegador para ser responsivo à largura da tela -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Estilo do tema -->
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo base_url('admin_assets') ?>/dist/css/custome.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo base_url('admin_assets') ?>/index2.html"><img src="<?php echo base_url((!empty($setting->logo)?$setting->logo:'assets/img/icons/logo2.png')) ?>" alt="" class="img-fluid"></a>
  </div>

    <?php if ($this->session->flashdata('message') != null) {  ?>
    <div class="alert alert-info alert-dismissable">
        <button type="button" class="close" data-dismiss="alert"
            aria-hidden="true">&times;</button>
        <?php echo $this->session->flashdata('message'); ?>
    </div>
    <?php } ?>
    <?php if ($this->session->flashdata('exception') != null) {  ?>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert"
            aria-hidden="true">&times;</button>
        <?php echo $this->session->flashdata('exception'); ?>
    </div>
    <?php } ?>
    <?php if (validation_errors()) {  ?>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert"
            aria-hidden="true">&times;</button>
        <?php echo validation_errors(); ?>
    </div>
    <?php } ?>


  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Entre para iniciar sua sessão</p>

      <?php echo form_open('login','id="loginForm" novalidate'); ?>
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" id="email" placeholder="E-mail">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" id="password" placeholder="Senha">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>  

        <div class="input-group mb-3">

        </div>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Lembrar-me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
          </div>
          <!-- /.col -->
        </div>
      <?php echo form_close();?>

     


  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('admin_assets') ?>/dist/js/adminlte.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/dist/js/myscript2.js"></script>

</body>
</html>
