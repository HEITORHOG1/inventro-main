<?php

// module name
$HmvcMenu["stock"] = array(
    //set icon
    "icon"           => "<i class='fa fa-fw fa-life-ring'></i>", 
    
    //menu name
    "stock_report" => array(
        "controller" => "stock",
        "method"     => "index",
        "permission" => "read"
    ),

    "stock_report_supplier_wise" => array(
            "controller" => "stock",
            "method"     => "stock_report_supplier_wise",
            "permission" => "read"
    ),

    "stock_report_product_wise" => array(
            "controller" => "stock",
            "method"     => "stock_report_product_wise",
            "permission" => "read"
    )

);

