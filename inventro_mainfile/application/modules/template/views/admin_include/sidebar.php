<div class="sidebar">
      <!-- Painel do usuário da barra lateral (opcional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <?php $image = $this->session->userdata('image') ?>
          <img src="<?php echo base_url((!empty($image)?$image:'admin_assets/img/user/m.png')) ?>" class="img-circle elevation-2" alt="Imagem do Usuário">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $this->session->userdata('fullname') ?></a>
        </div>
      </div>

      <!-- Menu da Barra Lateral -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Adicione ícones aos links usando a classe .nav-icon
               com font-awesome ou qualquer outra biblioteca de ícones -->

          <li class="nav-item">
            <a href="<?php echo base_url('dashboard/home') ?>" class="nav-link <?php echo (($this->uri->segment(2)=="home" || $this->uri->segment(2)=="")?"\active":null) ?>">
              <i class="nav-icon fas fa-home"></i>
              <p>
                Painel
              </p>
            </a>
          </li>

          




    <!-- *************************************
    **********INÍCIO DOS MÓDULOS PERSONALIZADOS*********
    ************************************* -->
        <?php  

        $path = 'application/modules/';
        $map  = directory_map($path);
        $HmvcMenu2   = array();


	  $HmvcMenu2["customer"] = array("icon"           => " <i class='nav-icon fa fa-fw fa-users'></i> ", 
    	  "customer_list" => array("controller" => "customer_info","method"     => "index","permission" => "read"),
          "customer_ledger" => array("controller" => "customer_info","method"     => "customerledger","permission" => "read")
      );
	  $HmvcMenu2["supplier"] = array("icon"           => " <i class='nav-icon fa fa-fw fa-users'></i> ", 
    	  "supplier_list" => array("controller" => "supplierlist","method"     => "index","permission" => "read"),
          "supplier_ledger" => array("controller" => "supplierlist","method"     => "supplierledger","permission" => "read")
     );
	  $HmvcMenu2["item"] = array("icon"           => "<i class='nav-icon fas fa-database'></i> ", 
    	 "unit" => array("controller" => "Unit","method"     => "unit_form","permission" => "create"),
         "category" => array("controller" => "Category","method"     => "category_form","permission" => "create"),
         "add_item" => array("controller" => "Item","method"     => "item_form","permission" => "create"),
         "item_list" => array("controller" => "Item","method"     => "item_list","permission" => "read")
	  );
	 $HmvcMenu2["invoice"] = array("icon"           => " <i class='nav-icon fa fa-fw fa-clipboard'> </i> ", 
    	 "add_invoice" => array("controller" => "invoice","method"     => "index","permission" => "create"), 
       "add_pos_invoice" => array("controller" => "invoice","method"     => "add_pos","permission" => "create"),
         "invoice_list" => array("controller" => "invoice","method" => "invoice_list","permission" => "read")
	 );
	 $HmvcMenu2["purchase"] = array("icon"           => " <i class='nav-icon fa fa-shopping-basket'></i> ", 
    	  "new_purchase" => array("controller" => "Purchase","method"     => "create_purchase","permission" => "create"),
          "purchase_list"   => array("controller" => "Purchase","method"     => "purchase_list","permission" => "read")
        );

    $HmvcMenu2["return"] = array("icon"           => " <i class='nav-icon fa fa-reply-all' ></i>", 
        "customer_return" => array("controller" => "returns","method"     => "customer_return","permission" => "create"),
        "customer_return_list" => array("controller" => "returns","method"     => "customer_return_list","permission" => "read"),
          "supplier_return"   => array("controller" => "returns","method"     => "supplier_return","permission" => "create"),
           "supplier_return_list"   => array("controller" => "returns","method"     => "supplier_return_list","permission" => "read")
	 );
	 $HmvcMenu2["report"] = array("icon"           => " <i class='nav-icon fas fa-dolly-flatbed'></i> ", 
    	  "purchase_report" => array("controller" => "report","method"     => "purchase_report","permission" => "read"),
          "sales_report" => array("controller" => "report","method"     => "sales_report","permission" => "read"),
          "cash_book" => array("controller" => "report","method"        => "cash_book","permission" => "read"),
          "bank_book" => array("controller" => "report","method"     => "bank_book","permission" => "read")
	 );
	 $HmvcMenu2["stock"] = array("icon"           => "<i class='nav-icon fa fa-fw fa-life-ring'></i>", 
         "stock_report" => array("controller" => "stock","method"     => "index","permission" => "read"),
         "stock_report_supplier_wise" => array("controller" => "stock","method"     => "stock_report_supplier_wise","permission" => "read"),
         "stock_report_product_wise" => array("controller" => "stock","method"     => "stock_report_product_wise","permission" => "read")
	 );
	 $HmvcMenu2["bank"] = array("icon"=> "<i class='nav-icon fas fa-money-check'></i>", 
		   "bank" => array("controller" => "Bank","method"     => "bank_list","permission" => "read"),
           "bank_ledger" => array("controller" => "Bank","method"     => "bank_ledger","permission" => "read"),
           "bank_adjustment" => array("controller" => "Bank","method"     => "bank_adjustment","permission" => "creat")   
	  );
	  $HmvcMenu2["hrm"] = array("icon"           => " <i class='nav-icon fas fa-user'></i> ", 
    	  "department" => array("controller" => "department","method"     => "index","permission" => "read"),
          "designation" => array("controller" => "designation","method"     => "index","permission" => "read"),
          "salary" => array(
        			"salary_setup" => array("controller" => "salary","method"     => "salary_setup","permission" => "read"),
        			"salary_generat_list" => array("controller" => "salary","method"     => "salary_generat_list","permission" => "read")
    			),
          "attendance" => array( 
        			  "attendance" => array("controller" => "attendance","method"     => "index","permission" => "read"),
        			  "attendance_report" => array("controller" => "attendance","method"     => "report","permission" => "read")
    			),
          "employee" => array( 
                     "add_employee" => array("controller" => "employee","method"     => "add_employee","permission" => "read"),
                     "manage_employee" => array("controller" => "employee","method"     => "manage_employee","permission" => "read")
           )
      );
	  $HmvcMenu2["accounts"] = array(
			"icon" => " <i class='nav-icon fa fa-fw fa-user-secret'></i> ",
            "payment_or_receive" => array("controller" => "account","method"     => "payment_receive_form","permission" => "create"),
            "manage_transaction" => array("controller" => "account","method"     => "manage_transaction","permission" => "read"),
            "account_adjustment" => array("controller" => "account","method"     => "account_adjustment","permission" => "create"),
             "cash_closing" => array("controller" => "account", "method" => "closing_form","permission" => "create"),
              "closing_list" => array("controller" => "account", "method" => "closing_list","permission" => "create")
		);
	  // Módulo Financeiro - Contas a Pagar e Receber
	  $HmvcMenu2["financeiro"] = array(
			"icon" => " <i class='nav-icon fas fa-file-invoice-dollar'></i> ",
            "contas_a_pagar" => array("controller" => "contas_pagar","method" => "lista","permission" => "read"),
            "nova_conta_pagar" => array("controller" => "contas_pagar","method" => "form","permission" => "create"),
            "contas_a_receber" => array("controller" => "contas_receber","method" => "lista","permission" => "read"),
            "nova_conta_receber" => array("controller" => "contas_receber","method" => "form","permission" => "create")
		);
	 $HmvcMenu2["menu"] = array("icon"           => " <i class='nav-icon fa fa-fw fa-list'></i> ", 
    	 "add_role" => array("controller" => "crole","method"     => "add_role","permission" => "create"),     
    	 "role_list" => array("controller" => "crole","method"     => "role_list","permission" => "read"),  
         "role_assign" => array("controller" => "crole","method"     => "role_assign","permission" => "create"),     
         "assigned_userrole_list" => array("controller" => "crole","method"     => "assigned_role_list","permission" => "read")   
	 );
	 
	
 if(isset($HmvcMenu2) && $HmvcMenu2!=null && sizeof($HmvcMenu2) > 0)
        	foreach ($HmvcMenu2 as $moduleName => $moduleData) {
            	if ($this->permission->module($moduleName)->access()) {
              			$this->permission->module($moduleName)->access();
        ?>


                <li class="nav-item has-treeview <?php echo (($this->uri->segment(1)==$moduleName)?"menu-open":null) ?>">
                  
                  <a href="javascript:void(0)" class="nav-link">
                    <?php echo (($moduleData['icon']!=null)?$moduleData['icon']:null) ?>  
                    <p>
                      <?php echo makeString([$moduleName])?>
                       <i class="fas fa-angle-left right"></i>
                    </p>
                    
                  </a>

                  <ul class="nav nav-treeview">

                    <?php foreach ($moduleData as $groupLabel => $label) {
                      if ($groupLabel!='icon') 
                        if((isset($label['controller']) && $label['controller']!=null) && ($label['method']!=null))  {
                          if($this->permission->check_label($groupLabel)->access()){?> 
                            <!-- menu/link de nível único -->
                            <li class="nav-item <?php echo (($this->uri->segment(1)==$moduleName && $label['controller']==$this->uri->segment(2) && $this->uri->segment(3)==$label['method'])?"display-bk":null) ?>">
                                <a href="<?php echo base_url($moduleName."/".$label['controller']."/".$label['method']) ?>" class="nav-link <?php echo (($this->uri->segment(1)==$moduleName && $label['controller']==$this->uri->segment(2) && $this->uri->segment(3)==$label['method'])?"active":null) ?>">
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i class="fas fa-angle-double-right"></i>
                                  <p> <?php echo makeString([$groupLabel]) ?></p>
                                </a>
                            </li>

                      <?php 
                          } 
                        } else { 
                            if($this->permission->check_label($groupLabel)->access()){
                            foreach ($label as $url) 
							$liclass='';
								$ulclass='';
							
								if(($this->uri->segment(1)==$moduleName && $this->uri->segment(2)==$url['controller']) || ($this->uri->segment(2)==$url['controller'] && $this->uri->segment(3)==$url['method'])){
								$liclass='menu-open';
								   $ulclass='block';
									}
                            ?>

                            <li class=" nav-item has-treeview <?php echo $liclass; ?>">
                                
                              <a href="#" class="nav-link <?php echo (($this->uri->segment(2)==$url['controller'])?"active":null) ?>">
                                <i class="nav-icon fas fa-circle"></i>
                                <p>
                                  <?php echo makeString([$groupLabel]) ?>
                                  <i class="right fas fa-angle-left"></i>
                                </p>
                              </a>

                                <ul class="nav nav-treeview <?php echo $ulclass;?>">

                                    <?php 
                                    foreach ($label as $name => $value) {
                                        if($this->permission->check_label($name)->access()){
                                    ?>
                                         <li class="nav-item <?php echo (($this->uri->segment(1)==$moduleName && $this->uri->segment(3)==$value['method'])?"display-bk":null) ?>">
                                            <a href="<?php echo base_url($moduleName."/".$value['controller']."/".$value['method']) ?>" class="nav-link <?php echo (($this->uri->segment(1)==$moduleName && $this->uri->segment(3)==$value['method'])?"active":null) ?>">
                                              <i class="fas fa-angle-double-right"></i>
                                              <p><?php echo makeString([$name]) ?></p>
                                            </a>
                                          </li>
                                    <?php 
                                        } //endif
                                    } //endforeach
                                    ?> 
                                </ul>
                            </li> 

                        <?php } ?>    

                        <!-- endif -->
                        <?php } ?>
                    <!-- endforeach -->
                    <?php } ?>              
                  </ul>
                </li>

          <!-- end if -->
        <?php } ?>
      <!-- end foreach -->
      <?php } ?> 
      
      <?php if($this->session->userdata('isAdmin')) {?> 
      <li class="nav-item has-treeview <?php echo (($this->uri->segment(1)=="dashboard" && $this->uri->segment(2)!="home")?"menu-open":null) ?>">
            <a href="javascript:void(0)" class="nav-link">
              <i class="nav-icon fas fa-cogs"></i>
              <p>
                Configurações
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview ">

              <li class="nav-item <?php echo (($this->uri->segment(3)=="form")?"display-bk":null) ?>">
                <a href="<?php echo base_url('dashboard/user/form') ?>" class="nav-link <?php echo (($this->uri->segment(3)=="form")?"active":null) ?>">
                  <i class="nav-icon far fa-user"></i>
                  <p> Adicionar usuário</p>
                </a>
              </li>
              

              <li class="nav-item <?php echo (($this->uri->segment(3)=="index")?"display-bk":null) ?>">
                <a href="<?php echo base_url('dashboard/user/index') ?>" class="nav-link <?php echo (($this->uri->segment(3)=="index")?"active":null) ?>">
                  <i class="nav-icon fas fa-align-left"></i>
                  <p> Lista de Usuários</p>
                </a>
              </li>


              <li class="nav-item <?php echo (($this->uri->segment(2)=="setting")?"display-bk":null) ?>">
                <a href="<?php echo base_url('dashboard/setting') ?>" class="nav-link appsetting <?php echo (($this->uri->segment(2)=="setting")?"active":null) ?>">
                  <i class="nav-icon fas fa-cogs"></i>
                  <p> Configurações da Aplicação</p>
                </a>
              </li>
              
              <li class="nav-item <?php echo (($this->uri->segment(2)=="language")?"display-bk":null) ?>">
                <a href="<?php echo base_url('dashboard/language') ?>" class="nav-link language <?php echo (($this->uri->segment(2)=="language")?"active":null) ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Configurações de Idioma</p>
                </a>
              </li>
              <li class="nav-item <?php echo (($this->uri->segment(2)=="currency")?"display-bk":null) ?>">
                <a href="<?php echo base_url('dashboard/currency') ?>" class="nav-link <?php echo (($this->uri->segment(2)=="currency")?"active":null) ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Moeda</p>
                </a>
              </li>
            </ul>
          </li>
      <?php } ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>