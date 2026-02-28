<?php

// module name
$HmvcMenu["returns"] = array(
    //set icon
    "icon"           => "<i class='fa fa-reply-all' ></i>", 
    
    //menu name
    "customer_return" => array( 
        "controller" => "Returns",
        "method"     => "customer_return",
        "permission" => "create"
    ),
    "customer_return_list" => array(
        "controller" => "returns",
        "method"     => "customer_return_list",
        "permission" => "read"
    ),

    "supplier_return"   => array( 
        "controller" => "Returns",
        "method"     => "supplier_return",
        "permission" => "read"
    ),
     "supplier_return_list"   => array(
        "controller" => "returns",
        "method"     => "supplier_return_list",
        "permission" => "create")

);

