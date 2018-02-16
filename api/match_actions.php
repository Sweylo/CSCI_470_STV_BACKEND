<?php

require_once('../model/user_db.php');
require_once('../model/match_db.php');

$is_token_valid = $me['user_token'] == $input['user_token'];

switch ($input['action']) {
    
    case 'create_match':
        
        $match_user_is_in = get_match_by_user($me['user_id']);
        
        // checks if the results of getting matches by user returns any data
        if ($match_user_is_in) {
            header('HTTP/1.1 403');
            die();
        }
        
        if ($is_token_valid) {
        
            try {
                //add_match($me['user_id'], $input['board_id'], rand(0,1) ? 'white' : 'black');
            } catch(mysqli_sql_exception $e) {
                echo $e;
                header('HTTP/1.1 500');
                die();
            }

            header('HTTP/1.1 202');
            
        } else {
            header('HTTP/1.1 401');
        }
        
        die();
        
        break;
    
    case 'list_matches':
        
        if ($is_token_valid) {
			
            // get the first 5 available matches
			$matches = get_avail_matches(5);
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
				
				echo json_encode(array('match_list' => $matches_array));
                
			}
            
        }
        
        die();
            
        break;
        
    case 'list_all_matches':
        
        if ($is_token_valid) {
			
            // get the first 5 available matches
			$matches = get_matches(20);
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
				
				echo json_encode(array('match_list' => $matches_array));
                
			}
            
        }
        
        die();
        
        break;
        
    case 'join_match':
        
        // if user isn't logged in or is a guest
        if (!$me || $me['user_account_type_id'] < USER_TYPE_USER) {
            header('HTTP/1.1 401');
        }
        
        $match = get_match_by_id($input['match_id']);
        
        //$is_token_valid = $me['user_token'] == $input['user_token'];
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
            
        } else if ($is_token_valed && !$is_match_waiting) {
            echo json_encode(['match_error_code' => 1]);
            header('HTTP/1.1 403');
        } else if (!$is_token_valed && $is_match_waiting) {
            echo json_encode(['match_error_code' => 2]);
            header('HTTP/1.1 401');
        }    
        
        die();
        
        break;
        
}