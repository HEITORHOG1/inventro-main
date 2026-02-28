<?php

// module name
$HmvcMenu["report"] = array(
    //set icon
    "icon"           => " <i class='fas fa-dolly-flatbed'></i> ", 
    
    //menu name
    "purchase_report" => array( 
        "controller" => "report",
        "method"     => "purchase_report",
        "permission" => "read"
    ),
     "sales_report" => array(
            "controller" => "report",
            "method"     => "sales_report",
            "permission" => "read"
        ),
    "cash_book" => array(
        "controller" => "report",
        "method"     => "cash_book",
        "permission" => "read"
    ),
   "bank_book" => array(
        "controller" => "report",
        "method"     => "bank_book",
        "permission" => "read"
    ),

);

