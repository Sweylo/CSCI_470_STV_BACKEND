<?php

/**
 *	controller for home page
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

require_once('../model/sql.php');
require_once('../model/input.php');
require_once('../model/user_db.php');
require_once('../model/match_db.php');

$page = 'match';
$referer = input(INPUT_SERVER, 'HTTP_REFERER');
$action = input(INPUT_GET, 'action', true);

switch ($action) {

	case 'list_matches':
        $matches = get_matches(input(INPUT_GET, 'limit'));
		include('list_matches.php');
		break;
	
}

?>