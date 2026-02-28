<?php

// module name
$HmvcMenu["supplier"] = array(
    //set icon
    "icon"           => " <i class='fa fa-fw fa-users'></i> ", 
    
    //menu name
    "supplier_list" => array( 
        "controller" => "supplierlist",
        "method"     => "index",
        "permission" => "read"
    ),
    "supplier_ledger" => array( 
        "controller" => "supplierlist",
        "method"     => "supplierledger",
        "permission" => "read"
    )

);

