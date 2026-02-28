<?php

// module name
$HmvcMenu["item"] = array(
    //set icon
    "icon"           => "<i class='fas fa-database'></i> ", 
    
    //menu name
     "unit" => array( 
        "controller" => "Unit",
        "method"     => "unit_form",
        "permission" => "create"
    ),
     "category" => array( 
        "controller" => "Category",
        "method"     => "category_form",
        "permission" => "create"
    ),
    "add_item" => array( 
        "controller" => "Item",
        "method"     => "item_form",
        "permission" => "create"
    ),

    "item_list" => array( 
        "controller" => "Item",
        "method"     => "item_list",
        "permission" => "read"
    )

);

