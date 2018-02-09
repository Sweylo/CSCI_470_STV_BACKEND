<?php

/**
 *	api controller
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

require_once('../model/sql.php');
require_once('../model/input.php');
require_once('../model/board_db.php');

// set output to json
header('Content-Type: text/json');

$action = $_REQUEST['action'];

switch ($action) {

	case 'get_board_by_id':
		$board = get_board_by_id($_REQUEST['board_id']);
		echo $board['board_data'];
		break;
	
}

?>