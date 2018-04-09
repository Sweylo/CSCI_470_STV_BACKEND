<?php

/**
 *	api controller
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$dir_depth = '../';

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/input.php');

/**
 * final function to be called in API response to send the data back to the client
 * 
 * @param int the HTTP status code to send in the header of the response
 * @param array the data that will be converted to JSON in the body of the response
 * @param string to be displayed after the data, usually for errors
 */
function send_to_client($code = 200, $data = [], $debug_message = null) {
    
    // set http output to json
    header('Content-Type: text/json');
    
    // set http status code
    header("HTTP/1.1 $code");
    
    // output the encoded data
    echo $data ? json_encode($data) : null;
	
    // stop execution
    //die($debug_message);
    die();
	
}

// decode the json input data to a php array
$input = json_decode(file_get_contents('php://input'), true);

// if there is an error decoding the json input data
if (json_last_error() != JSON_ERROR_NONE) {
    send_to_client(400, null, 'JSON decode error: ' . json_last_error_msg());
}

// filter the action variable
$action = filter_var($input['action']);

include('board_actions.php');
include('match_actions.php');
include('user_actions.php');
include('gameplay_actions.php');

// if no action is matched, return not found
send_to_client(404);