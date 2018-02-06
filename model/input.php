<?php

function input($input_type, $var_name, $is_action = false, $validation = null) {

	if ($validation) {
		$input = filter_input($input_type, $var_name, $validation);
	} else {
		$input = filter_input($input_type, $var_name);
	}

	if ($input == null || $input == false) {
		if ($is_action) {
			return null;
		} else {
			// creates error when db not connected
			//err::out("invalid data for '$var_name': $input");
		}
	} else {
		return $input;
	}

}

?>