<?php

/**
 *	api controller
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

require_once('../model/sql.php');
require_once('../model/input.php');

// set output to json
header('Content-Type: text/json');

// decode the json input data
$input = json_decode(file_get_contents('php://input'), true);

include('board_actions.php');
include('match_actions.php');
include('user_actions.php');
include('gameplay_actions.php');

// if the action is never found in the switch statements included above, return 404
header('HTTP/1.1 404');