<?php

require_once('../model/user_db.php');
require_once('../model/match_db.php');

switch ($input['action']) {
    
    case 'move_piece':
        
        break;
    
    // checks if there are available matches and if so, outputs them, if not, creates an entry
	//	in the database to indicate the user is looking for a match.
    // should probably separate this into two functions: create_match and list_matches
	case 'search_matches':
        
        // check if user is already associated with a match
        //$user_matches = get_user_matches();
		
		//echo json_encode($_REQUEST);
		
		if ($input['user_token'] == $me['user_token'] && empty($user_matches)) {
			
            // get the first 5 available matches
			$matches = get_avail_matches(5);
			
			//print_r($matches);
			
            // encode available match data to json and output the array of matches
			if (count($matches) > 0) {
				
				$matches_array = array();
				
				foreach ($matches as $match) {
					if (!empty($match)) {
						array_push($matches_array, $match->data);
					}
				}
				
				echo json_encode(array('results' => $matches_array));
                
			// add a match to the database. indicates player is looking for a match
			} else {
				
				try {
					$new_match_id = add_match($me['user_id'], 1, rand(0,1) ? 'white' : 'black');
				} catch(mysqli_sql_exception $e) {
					echo $e;
					header('HTTP/1.1 500');
                    die();
				}
				
				header('HTTP/1.1 201');
				
			}
			
		} else if ($input['user_token'] != $me['user_token'] && empty($user_matches)) {
			header('HTTP/1.1 401 Unauthorized');
		} else if ($input['user_token'] == $me['user_token'] && !empty($user_matches)) {
            
			switch ($user_matches('match_status')) {
                
                // return message associated with the current match status, i.e. 'in progress'
                
            }
            
		}
		
		break;
        
    case 'join_match':
        
        // if user isn't logged in or is a guest
        if (!$me || $me['user_account_type_id'] < USER_TYPE_USER) {
            header('HTTP/1.1 401');
        }
        
        $match = get_match_by_id($input['match_id']);
        
        $is_token_valid = $me['user_token'] == $input['user_token'];
        $is_match_waiting = $match['match_status'] == MATCH_WAITING;
        
        //echo $is_token_valid ? 'true' : 'false';
        //echo $is_match_waiting ? 'true' : 'false';
        
        if ($is_token_valid && $is_match_waiting) {
            
            $match['match_status'] = MATCH_PREGAME;
            
            // update the database with the new user
            //$match->update();
            
            try {
                join_match($match['match_id'], $me['user_id']);
            } catch (Exception $e) {
                echo $e;
                header('HTTP/1.1 409');
                die();
            }
            
            // create database records for the match data
            init_match($match['match_id']);
            
            header('HTTP/1.1 202');
            die();
            
        } else if ($is_token_valed && !$is_match_waiting) {
            echo json_encode(['match_error_code' => 1]);
            header('HTTP/1.1 403');
            die();
        } else if (!$is_token_valed && $is_match_waiting) {
            echo json_encode(['match_error_code' => 2]);
            header('HTTP/1.1 401');
            die();
        }    
        
        break;
        
    default:
        header('HTTP/1.1 404');
        
}