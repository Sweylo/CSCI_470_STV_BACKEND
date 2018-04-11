<?php

require_once($dir_depth . 'model/user_db.php');
require_once($dir_depth . 'model/match_db.php');

switch ($action) {
    
    case 'create_match':
        
        // if user isn't logged in or is a guest
        if (!$me || $me['user_account_type_id'] < USER_TYPE_USER) {
            send_to_client(401);
        }
		
		$user_match = get_match_by_user($me['user_id']);
		
        // checks if the results of getting matches by user returns any data
        if ($user_match && $user_match['match_status'] <= MATCH_PLAYING) {
            send_to_client(403, null, 'user is already in a match');
        }
		
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

		break;
        
    case 'list_matches':
        
        // if user isn't logged in or is a guest
        if (!$me || $me['user_account_type_id'] < USER_TYPE_USER) {
            send_to_client(401);
        }
		
		// get the first 5 matches
		$matches = get_avail_matches(5);
		$matches_array = [];

		//print_r($matches);

		// encode available match data to json and output the array of matches
		if (count($matches) > 0) {

			$matches_array = [];

			foreach ($matches as $match) {
				if (!empty($match)) {
					array_push($matches_array, $match->data);
				}
			}

			send_to_client(200, ['match_list' => $matches_array]);

		} else {
			send_to_client(204);
		}
        
        break;
        
    case 'join_match':
        
        // if user isn't logged in or is a guest
        if (!$me || $me['user_account_type_id'] < USER_TYPE_USER) {
            send_to_client(401);
        }
        
        $match = get_match_by_id(filter_var($input['match_id'], FILTER_VALIDATE_INT));
        
        if (!$match->data) {
            send_to_client(400, null, 'match requested is not in the database');
        }
		
		$user_match = get_match_by_user($me['user_id']);
		
		// checks if the user is playing a match (match status <= 3)
        if ($user_match && $user_match['match_status'] <= MATCH_PLAYING) {
            send_to_client(403, null, 'user is already in a match');
        }
		
        //echo $match['match_status'];
        
        if ($match['match_status'] == MATCH_WAITING) {
            
            // update the database with the new user
            try {
                join_match($match['match_id'], $me['user_id']);
            } catch (Exception $e) {
                send_to_client(409, null, $e);
            }
            
            // create database records for the match data (spaces and pieces)
            init_match($match['match_id']);
            
            send_to_client(202);
            
        } else {
            send_to_client(403, ['match_error' => 1]);
        }
        
        break;
		
	case 'join_random_match':
		
		// if user isn't logged in or is a guest
        if (!$me || $me['user_account_type_id'] < USER_TYPE_USER) {
            send_to_client(401);
        }
		
		$match_user = get_match_user($me['user_id']);
		
		// Same issue we had with the join_match request, could not join a match if there was one that
		// had already finished in the database
		if (!is_null($match_user->data) && $match_user['match_status'] <= MATCH_PLAYING) {
			send_to_client(403, ['match_error' => MATCH_ERROR_ALREADY_IN_A_MATCH]);
		}
		
		$matches = get_avail_matches(10);
		
		//print_r($matches);
		
		// echo is_null($matches[0]) ? 'true' : 'false';
		
		if (is_null($matches[0])) {
			
			//send_to_client(404);
			
			try {
				$new_match_id = add_match($me['user_id'], get_board_by_id(1)['board_id']);
			} catch(mysqli_sql_exception $e) {
				send_to_client(500, ['add_match_error' => $e]);
			}
			
			$match = get_match_by_id($new_match_id);
			
			//print_r($new_match_id);
			
			$code = 201;
			
		} else {
			
			$match = $matches[0];

			// update the database with the new user
			try {
				join_match($match['match_id'], $me['user_id']);
			} catch (Exception $e) {
				send_to_client(409, ['join_match_error' => $e]);
			}

			// create database records for the match data (spaces and pieces)
			init_match($match['match_id']);
			
			$code = 202;
			
		}
			
		send_to_client($code, ['match_id' => $match['match_id']]);
		
		break;
        
    /*case 'init_match':
        $match = get_match_by_id(filter_var($input['match_id'], FILTER_VALIDATE_INT));
        init_match($match['match_id']);
        send_to_client();
        break;*/
        
}
