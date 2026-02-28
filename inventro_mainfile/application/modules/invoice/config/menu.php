<?php

// module name
$HmvcMenu["invoice"] = array(
    //set icon
    "icon"           => " <i class='fa fa-fw fa-clipboard'> </i> ", 
    
    //menu name
    "add_invoice" => array(
        "controller" => "invoice",
        "method"     => "index",
        "permission" => "create"
    ),
     "add_pos_invoice" => array(
        "controller" => "invoice",
        "method"     => "add_pos",
        "permission" => "create"
    ),
    //menu name
    "invoice_list" => array(
        "controller" => "invoice",
        "method"     => "invoice_list",
        "permission" => "read"
    ),

);

