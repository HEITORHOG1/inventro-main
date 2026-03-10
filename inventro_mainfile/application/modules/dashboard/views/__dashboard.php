 <!-- Caixas pequenas (Caixa de estatísticas) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- caixa pequena -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo html_escape($totalinvoice);?></h3>
                <p><?php echo makeString(['total','invoice']);?></p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="<?php echo base_url('invoice/invoice/invoice_list')?>" class="small-box-footer"><?php echo makeString(['more_info']); ?> <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- caixa pequena -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo html_escape($totalpurchase);?><sup class="fontsz"></sup></h3>

                <p><?php echo makeString(['total','purchase']);?></p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="<?php echo base_url('purchase/Purchase/purchase_list')?>" class="small-box-footer"><?php echo makeString(['more_info'] ); ?> <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- caixa pequena -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo html_escape($totalproduct) ;?></h3>

                <p><?php echo makeString(['total','item']);?></p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="<?php echo base_url('item/Item/item_list')?>" class="small-box-footer"><?php echo makeString(['more_info']); ?><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
            <div class="col-lg-3 col-6">
            <!-- caixa pequena -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo html_escape($totalcustomer) ;?></h3>

                <p><?php echo makeString(['total','customer']);?></p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="<?php echo base_url('customer/customer_info/index')?>" class="small-box-footer"><?php echo makeString(['more_info']); ?> <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Linha principal -->
        <div class="row">
          <!-- Coluna esquerda -->
          <section class="col-lg-7 connectedSortable">
            <!-- Abas personalizadas (Gráficos com abas)-->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fas fa-chart-pie mr-1"></i>
                  <?php echo makeString(['purchase_and_sales_report']); ?>
                </h3>
                <div class="card-tools">
                 
                </div>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content p-0">
                  <!-- Gráfico Morris - Vendas -->
                  <div class="chart tab-pane mrchart active" id="revenue-chart">
                      <canvas id="revenue-chart-canvas" class="mchart-canva" height="300"></canvas>                         
                   </div>
                
                </div>
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->

                        <!--/.direct-chat -->
            <!-- Lista de Tarefas -->
          
            <!-- /.card -->
          </section>
          <!-- /.Coluna esquerda -->
          <!-- coluna direita (Estamos apenas adicionando o ID para tornar os widgets ordenáveis)-->
          <section class="col-lg-5 connectedSortable">

            <div class="card card-success">
            <div class="card-header with-border">
              <h3 class="card-title"><?php echo makeString(['purchase_and_sales_report']); ?></h3>
            </div>
            <div class="card-body">
              <div class="tab-content p-0">
              <canvas id="pieChart" height="235"></canvas>
            </div>
            </div>
            <!-- /.box-body -->
          </div>
            <!-- /.card -->

            <!-- Calendário -->
            
            <!-- /.card -->
          </section>
          <!-- coluna direita -->
          <?php     $month='';
                    for ($i=1; $i <= 12; $i++) {
                        if ($i==1) {
                            $month.='"Janeiro",';
                        }elseif ($i==2) {
                            $month.='"Fevereiro",';
                        }elseif ($i==3) {
                            $month.='"Março",';
                        }elseif ($i==4) {
                            $month.='"Abril",';
                        }elseif ($i==5) {
                            $month.='"Maio",';
                        }elseif ($i==6) {
                          $month.='"Junho",';
                        }elseif ($i==7) {
                           $month.='"Julho",';
                        }elseif ($i==8) {
                           $month.='"Agosto",';
                        }elseif ($i==9) {
                           $month.='"Setembro",';
                        }elseif ($i==10) {
                           $month.='"Outubro",';
                        }elseif ($i==11) {
                           $month.='"Novembro",';
                        }elseif ($i==12) {
                           $month.='"Dezembro"';
                        }
                    }
             
                ?>
                <input type="hidden" id="totalpurchase" value="<?php echo html_escape($purchasetotal);?>"/>
                <input type="hidden" id="nitsale" value="<?php echo html_escape($saleamount->totalsale);?>"/>
                 <input type="hidden" id="nitpurchase" value="<?php echo html_escape($purchaseamount->totalpurchase);?>"/>
                <input type="hidden" id="totalsale" value="<?php echo html_escape($saletotal);?>"/>
                <input type="hidden" id="currencyicon" value="<?php echo html_escape((!empty($currency->curr_icon)?$currency->curr_icon:''));?>"/>
                <input type='hidden' id='month' value='<?php echo html_escape($month);?>'/>
                <input type='hidden' id='baseurl' value='<?php echo base_url();?>'/>
        </div>
        <script src="<?php echo base_url() ?>application/modules/dashboard/assets/js/dashboardgrraph.js.php" type="text/javascript"></script>
       
