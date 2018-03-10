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
require_once($dir_depth . 'model/match_db.php');

$referer = input(INPUT_SERVER, 'HTTP_REFERER');
$action = input(INPUT_GET, 'action', true);

switch ($action) {

	case 'list_matches':
        $matches = get_matches(input(INPUT_GET, 'limit'));
		include('list_matches.php');
		break;
	
	case 'watch_match': 
		
		$match = get_match_by_id(input(INPUT_GET, 'match_id'));
		$board = get_board_by_id($match['match_board_id']);
		$spaces = get_spaces_by_match($match['match_id']);
		$match_users = get_match_users($match['match_id'])->data;
		
		if ($match_users[0]['match_user_color'] == 'white') {
			$white_match_user = $match_users[0];
			$black_match_user = $match_users[1];
		} else if ($match_users[0]['match_user_color'] == 'black') {
			$black_match_user = $match_users[0];
			$white_match_user = $match_users[1];
		}
		
		include('watch_match.php');
		break;
		
	case 'get_match_board_table':
		
		require_once('../view/html.php');
		
		$match = get_match_by_id(input(INPUT_GET, 'match_id'));
		$board = get_board_by_id($match['match_board_id']);
		$match_users = get_match_users($match['match_id'])->data;
		
		if ($match_users[0]['match_user_color'] == 'white') {
			$white_match_user = $match_users[0];
			$black_match_user = $match_users[1];
		} else if ($match_users[0]['match_user_color'] == 'black') {
			$black_match_user = $match_users[0];
			$white_match_user = $match_users[1];
		}
		
		include('get_match_board_table.php');
		break;
    
    case 'delete_match':
        
        if (!$is_admin) {
            die('you must be an admin to delete matches');
        }
		
		//delete_match(input(INPUT_GET, 'match_id'));
        
        $match = get_match_by_id(input(INPUT_GET, 'match_id'));
        
        if (!$match->data) {
            die('match not found');
        }
        
        $match_users = get_match_users($match['match_id']);
        $spaces = get_spaces_by_match($match['match_id']);
        
        //print_r($match);
        //print_r($match_users);
        //print_r($spaces->data);
        
        // *** may want to add a confirm screen at some point
        
        // delete spaces and pieces
        foreach ($spaces->data as $space) {
            //$piece = get_piece_by_space($space['space_id']);
            //$piece->delete();
			delete_piece_by_space($space['space_id']);
            $space->delete();
        }
        
        foreach ($match_users->data as $match_user) {
            $match_user->delete();
        }
        
        $match->delete();
        
		header('Location: ./?action=list_matches');
        break;
    
    case 'init_match_test':
        init_match(input(INPUT_GET, 'match_id'));
        break;
	
}

?>