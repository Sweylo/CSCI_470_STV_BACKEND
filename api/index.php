<?php

/**
 *	api controller
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

require_once('../model/sql.php');
require_once('../model/input.php');
require_once('../model/user_db.php');
require_once('../model/board_db.php');
require_once('../model/match_db.php');

// decode the json input data
$input = json_decode(file_get_contents('php://input'), true);

include('board_actions.php');
include('match_actions.php');

switch ($input['action']) {
    
    default: 
        header('HTTP/1.1 404');
    
}

?>