<?php 

/**
 * Make Translate String
 **/
if (!function_exists("makeString")) {
	function makeString ($data = [])
	{
		$output = "";
		$i = 0;
		foreach ($data as $val) {
			$output .= ($i>0?" ":"");
			$output .= display("$val");
			$i++;
		}

		return $output;
        
	}
}
if (!function_exists("dd")) {
	function dd ($data)
	{
		print_r($data);
		exit();
		
        
	}
}

