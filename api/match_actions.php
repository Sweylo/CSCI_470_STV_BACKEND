<?php

require_once('../model/user_db.php');
require_once('../model/match_db.php');

switch ($input['action']) {
    
    // checks if there are available matches and if so, outputs them, if not, creates an entry
	//	in the database to indicate the user is looking for a match.
	case 'search_matches':
		
		$user = get_user_by_id($input['user_id']);
        
        // check if user is already associated with a match
        //$user_matches = get_user_matches();
		
		//echo json_encode($_REQUEST);
		
		if ($input['user_token'] == $user['user_token'] && empty($user_matches)) {
			
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
					add_match($user['user_id'], 1, rand(0,1) ? 'white' : 'black');
				} catch(mysqli_sql_exception $e) {
					//echo $e;
					//header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
                    die($e);
				}
				
				header('HTTP/1.1 201 Created');
				
			}
			
		} else if ($input['user_token'] != $user['user_token'] && empty($user_matches)) {
			header('HTTP/1.1 401 Unauthorized');
		} else if ($input['user_token'] == $user['user_token'] && !empty($user_matches)) {
            
			switch ($user_matches('match_status')) {
                
                // return message associated with the current match status, i.e. 'in progress'
                
            }
            
		}
		
		break;
        
    case 'join_match':
        
        //echo json_encode($input);
        
        $match = get_match_by_id($input['match_id']);
        
        //echo $match['match_status'];
        
        if ($match['match_status'] == MATCH_WAITING) {
            // check_match_avail($match['match_id']);
        } else {
            echo json_encode(['match_error_code' => 1]);
        }
        
        break;
        
}