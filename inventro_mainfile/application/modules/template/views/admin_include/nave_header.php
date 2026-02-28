<!-- Left navbar links -->

<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
      
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
     
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
      
    </form>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
              <i class="far fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
               

                  <a href="<?php echo base_url('dashboard/home/profile_setting') ?>" class="dropdown-item">
                    <i class="fas fa-cogs mr-2"></i> <?php echo html_escape('Configurações');?>
                  </a>
              <div class="dropdown-divider"></div>
                  <a href="<?php echo base_url('logout') ?>" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> <?php echo html_escape('Sair')?>
                  </a>
            </div>
        </li>
    </ul>

</nav>