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

// set output to json
header('Content-Type: text/json');

$action = $_REQUEST['action'];

switch ($action) {

	// returns board data identified by the board_id
	case 'get_board_by_id':
		$board = get_board_by_id($_REQUEST['board_id']);
		echo $board['board_data'];
		break;
	
	// checks if there are available matches and if so, outputs them, if not, creates an entry
	//	in the database to indicate the user is looking for a game.
	case 'search_matches':
		
		$user = get_user_by_id($_REQUEST['user_id']);
		
		//echo json_encode($_REQUEST);
		
		if ($_REQUEST['user_token'] == $user['user_token']) {
			
			$matches = get_avail_matches(5);
			
			
			//print_r($matches);
			
			if (count($matches) > 1) {
				
				$matches_array = array();
				
				foreach ($matches as $match) {
					if (!empty($match)) {
						array_push($matches_array, $match->data);
					}
				}
				
				echo json_encode(array('results' => $matches_array));
				
			} else if (count($matches) == 1) {
				echo json_encode(array('results' => $matches[0]->data));
			} else {
				
				try {
					add_match($user['user_id'], 3, rand(0,1) ? 'white' : 'black');
				} catch(mysqli_sql_exception $e) {
					echo $e;
					header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
				}
				
				header('HTTP/1.1 201 Created');
				
			}
			
		} else {
			header('HTTP/1.1 401 Unauthorized');
		}
		
		break;
		
}

?>