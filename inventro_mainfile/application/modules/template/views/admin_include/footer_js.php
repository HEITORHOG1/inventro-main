
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script src="<?php echo base_url('admin_assets') ?>/dist/js/noscript.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables/jquery.dataTables.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/js/jszip.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/js/pdfmake.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/js/vfs_fonts.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->

<!-- jQuery Knob Chart -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/moment/moment.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- Admin App -->
<script src="<?php echo base_url('admin_assets') ?>/dist/js/adminlte.js"></script>
<!-- dashboard demo (This is only for demo purposes) -->
<?php if($title=="Home"){?>
<script src="<?php echo base_url('admin_assets') ?>/dist/js/pages/dashboard.js"></script>
<?php } ?>
<!-- Admi for demo purposes -->
<script src="<?php echo base_url('admin_assets') ?>/dist/js/demo.js"></script>
<!-- SweetAlert2 -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?php echo base_url('admin_assets') ?>/plugins/toastr/toastr.min.js"></script>
<script src="<?php echo base_url('admin_assets') ?>/dist/js/myscript1.js"></script>
<!-- Máscaras e funções brasileiras (CPF, CNPJ, CEP, Telefone) -->
<script src="<?php echo base_url('admin_assets') ?>/js/brasil.js"></script>
<!-- DataTables -->






<?php
    $path = 'application/modules/';
    $map  = directory_map($path);
    if (is_array($map) && sizeof($map) > 0)
    foreach ($map as $key => $value) {
        $js   = str_replace("\\", '/', $path.$key.'assets/js/script.js'); 
        if (file_exists($js)) {
           echo '<script src="'.base_url($js).'" type="text/javascript"></script>';
        }   
    }   
?>






