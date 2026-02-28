<?php

// module name
$HmvcMenu["customer"] = array(
    //set icon
    "icon"           => " <i class='fa fa-fw fa-users'></i> ", 
    //menu name
    "customer_list" => array( 
        "controller" => "customer_info",
        "method"     => "index",
        "permission" => "read"
    ),
    "customer_ledger" => array( 
        "controller" => "customer_info",
        "method"     => "customerledger",
        "permission" => "read"
    )

);


 