<?php

require_once('../model/user_db.php');
require_once('../model/match_db.php');

//$is_token_valid = $me['user_token'] == $input['user_token'];
$is_token_valid = true; // for testing

switch ($action) {
    
    case 'create_match':
        
		if (!$me) {
			send_to_client(401);
		}
		
        // checks if the results of getting matches by user returns any data
        if (get_match_by_user($me['user_id'])) {
            send_to_client(403, null, 'user is already in a match');
        }
        
        if ($is_token_valid) {
            
			$board = get_board_by_id(filter_var($input['board_id'], FILTER_VALIDATE_INT));
			
			if (!$board->data) {
				send_to_client(400, null, "no board found for id={$input['board_id']}");
			}
			
            try {
                $new_match_id = add_match($me['user_id'], $board['board_id'], 
					rand(0,1) ? 'white' : 'black');
            } catch(mysqli_sql_exception $e) {
                send_to_client(500, null, $e);
            }

            send_to_client(202, ['new_match_id' => $new_match_id]);
            
        } else {
            send_to_client(500);
        }
        
        die();
        
        break;
        
    case 'list_matches':
        
        if ($is_token_valid) {
			
            // get the first 50 matches
			$matches = get_matches(50);
            $matches_array = [];
			
			//print_r($matches);
			
            // encode available match data to json and output the array of matches
			if (count($matches) > 0) {
				
				$matches_array = array();
				
				foreach ($matches as $match) {
                    if (!empty($match)) {
                        array_push($matches_array, $match->data);
                    }
                }
				
                send_to_client(200, ['match_list' => $matches_array]);
                
			} else {
                send_to_client(204);
            }
            
        } else {
            send_to_client(401);
        }
        
        //die();
        
        break;
        
    case 'join_match':
        
        // if user isn't logged in or is a guest
        if (!$me || $me['user_account_type_id'] < USER_TYPE_USER) {
            send_to_client(401);
        }
        
        $match = get_match_by_id(filter_var($input['match_id'], FILTER_VALIDATE_INT));
        
        if (!$match) {
            send_to_client(400, null, 'match requested is not in the database');
        }
        
        //$is_token_valid = $me['user_token'] == $input['user_token'];
        $is_match_waiting = $match['match_status'] == MATCH_WAITING;
        
        //echo $match['match_status'];
        //echo $is_token_valid ? 'true' : 'false';
        //echo $is_match_waiting ? 'true' : 'false';
        
        if ($is_token_valid && $is_match_waiting) {
            
            // update the database with the new user
            try {
                join_match($match['match_id'], $me['user_id']);
            } catch (Exception $e) {
                send_to_client(409, null, $e);
            }
            
            // create database records for the match data
            init_match($match['match_id']);
            
            send_to_client(202);
            
        } else if ($is_token_valid && !$is_match_waiting) {
            send_to_client(403, ['match_error_code' => 1]);
        } else if (!$is_token_valid && $is_match_waiting) {
            send_to_client(403, ['match_error_code' => 2]);
        } else {
            
        }
        
        break;
        
    /*case 'init_match':
        $match = get_match_by_id(filter_var($input['match_id'], FILTER_VALIDATE_INT));
        init_match($match['match_id']);
        send_to_client();
        break;*/
        
}