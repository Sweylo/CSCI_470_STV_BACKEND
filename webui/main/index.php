<?php

/**
 *	controller for home page
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$dir_depth = '../../';

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/input.php');
require_once($dir_depth . 'model/user_db.php');

$action = input(INPUT_GET, 'action', true);

switch ($action) {

	case 'home': default: 
		include('home.php');
		break;
	
}

?>