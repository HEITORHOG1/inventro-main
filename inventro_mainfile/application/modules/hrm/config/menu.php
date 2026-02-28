<?php

// module name
$HmvcMenu["hrm"] = array(
    //set icon
    "icon"           => " <i class='fas fa-user'></i> ", 
    

    //menu name
    "department" => array( 
        "controller" => "department",
        "method"     => "index",
        "permission" => "read"
    ),

    "designation" => array( 
        "controller" => "designation",
        "method"     => "index",
        "permission" => "read"
    ),


    "salary" => array( 

        "salary_setup" => array(
            "controller" => "salary",
            "method"     => "salary_setup",
            "permission" => "read"
        ),

        "salary_generat_list" => array( 
            "controller" => "salary",
            "method"     => "salary_generat_list",
            "permission" => "read"
        ),


        
    ),

    "attendance" => array( 

        "attendance" => array( 
            "controller" => "attendance",
            "method"     => "index",
            "permission" => "read"
        ),

        "attendance_report" => array( 
            "controller" => "attendance",
            "method"     => "report",
            "permission" => "read"
        ),
        
    ),



    "employee" => array( 

        "add_employee" => array( 
            "controller" => "employee",
            "method"     => "add_employee",
            "permission" => "read"
        ),

        "manage_employee" => array( 
            "controller" => "employee",
            "method"     => "manage_employee",
            "permission" => "read"
        )
    )

);

