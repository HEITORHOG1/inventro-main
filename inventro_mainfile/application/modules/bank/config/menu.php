<?php

// module name
$HmvcMenu["bank"] = array(
    //set icon
    "icon"           => "<i class='fas fa-money-check'></i>", 
    
    //menu name
     "bank" => array( 
        "controller" => "Bank",
        "method"     => "bank_list",
        "permission" => "read"
    ),
      "bank_ledger" => array( 
        "controller" => "Bank",
        "method"     => "bank_ledger",
        "permission" => "read"
    ),
    "bank_adjustment" => array( 
        "controller" => "Bank",
        "method"     => "bank_adjustment",
        "permission" => "creat"
    ),
   

);

