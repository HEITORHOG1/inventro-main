<?php


// module name
$HmvcMenu["accounts"] = array(
    //set icon
    "icon"           => " <i class='fa fa-fw fa-user-secret'></i> ",

    //menu name
    "payment_or_receive" => array(
        "controller" => "account",
        "method"     => "payment_receive_form",
        "permission" => "create"
    ),
    "manage_transaction" => array(
        "controller" => "account",
        "method"     => "manage_transaction",
        "permission" => "read"
    ),
    "account_adjustment" => array(
        "controller" => "account",
        "method"     => "account_adjustment",
        "permission" => "create"
    ),
    "cash_closing" => array(
        "controller" => "account",
        "method"     => "closing_form",
        "permission" => "create"
    ),
    "closing_list" => array(
    "controller"   => "account",
     "method"      => "closing_list",
     "permission"  => "create")


);



   

 