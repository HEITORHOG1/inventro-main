<?php
namespace Php;

class DbImport 
{ 
	private $table_name = 'user';      //Login Table Name
    private $table_fields = array('email', 'password','status','is_admin'); // Table Field Names
   private $field_values = array('{input}', '{input}',1,1); // Table Field Values
    // Function to the database and tables and fill them with the default data
    function createDatabase($data = [])
    {
        // Connect to the database
        @$mysqli = new \mysqli($data['hostname'], $data['username'], $data['password'], '');

        // Check for errors
        if (mysqli_connect_errno()){
            return false;
        }

        // Create the prepared statement
        $createDb = $mysqli->query("CREATE DATABASE IF NOT EXISTS ".$data['database']);

        // Close the connection
        $mysqli->close();

        if($createDb) {
            return true;
        } else {
            return false;
        }
    }

    // Function to create the tables and fill them with the default data
    function createTables($data = [])
    {
        // Connect to the database
        @$mysqli = new \mysqli(
            $data['hostname'],
            $data['username'],
            $data['password'],
            $data['database']
        );

        // Check for errors
        if (mysqli_connect_errno())
            return false;

        // Open the default SQL file
        $query = file_get_contents('sql/install.sql');

        // Execute a multi query
        $multi_query = $mysqli->multi_query($query);

        // Close the connection
        $mysqli->close();
		
		 // Store Database information into session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['hostname'] = $data['hostname'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['password'] = $data['password'];
        $_SESSION['database'] = $data['database'];

        if ($multi_query){
            return true;
        } else {
            return false;
        }
    }

	// Insert Login info
    function insert_login($data = [])
    {
	    $email = $data['email'];
        $password = $data['password'];

        // Connect to the database
        @$mysqli = new \mysqli(
            $_SESSION['hostname'],
            $_SESSION['username'],
            $_SESSION['password'],
            $_SESSION['database']
        );

        // Check for errors
        if (mysqli_connect_errno())
            return false;

        $password = md5($password);

        $fields_num = count($this->table_fields);
        $fields = '';
        $values = '';
        for($i=0; $i<$fields_num; $i++){

            $fields .= "`".$this->table_fields[$i]."`,"; // set field values

            if($i==0){

                $values .= "'".$email."',"; // Set Email values

            }else if($i==1){

                 $values .= "'".$password."',"; // Set Password Values

            }else{

                 $values .= ((gettype($this->field_values[$i])=='integer')?$this->field_values[$i]:"'".$this->field_values[$i]."'").",";

            }
        }

        // Make Query
        $query = "INSERT INTO `$this->table_name` (".rtrim($fields,',').") VALUES (".rtrim($values,',').")";

        // Run Query
        $insert_query = $mysqli->query($query);

        // Close the connection
        $mysqli->close();

        if ($insert_query){
            return true;
        } else {
            return false;
        }
    }

}
    