<?php

// module name
$HmvcMenu["purchase"] = array(
    //set icon
    "icon"           => " <i class='fa fa-shopping-basket'></i> ", 
    
    //menu name
    "new_purchase" => array( 
        "controller" => "Purchase",
        "method"     => "create_purchase",
        "permission" => "create"
    ),

    "purchase_list"   => array( 
        "controller" => "Purchase",
        "method"     => "purchase_list",
        "permission" => "read"
    )

);

