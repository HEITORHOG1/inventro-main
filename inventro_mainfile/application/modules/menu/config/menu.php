<?php
// module name
$HmvcMenu["menu"] = array(
    //set icon
    "icon"           => " <i class='fa fa-fw fa-list'></i> ", 
    //menu name
    "add_role" => array( 
        "controller" => "crole",
        "method"     => "add_role",
        "permission" => "create"
    ),     
    "role_list" => array( 
        "controller" => "crole",
        "method"     => "role_list",
        "permission" => "read"
    ) ,  
     "role_assign" => array( 
        "controller" => "crole",
        "method"     => "role_assign",
        "permission" => "create"
    ),     

     "assigned_userrole_list" => array( 
        "controller" => "crole",
        "method"     => "assigned_role_list",
        "permission" => "read"
    )   

);

