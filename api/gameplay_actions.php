<?php

require_once($dir_depth . 'model/user_db.php');
require_once($dir_depth . 'model/match_db.php');
require_once($dir_depth . 'model/space_db.php');
require_once($dir_depth . 'model/piece_db.php');

/**
 * function to get and validate the match the user is currently in
 * 
 * @param int $match_status the match status to check the match is in
 * @return object the sql object of the match the user is currently in
 */
function get_match_and_validate_match_status($match_status) {
    
    global $me;
    
    if (!$me) {
        send_to_client(401, null, 'must be logged in');
    }

    $match = get_match_by_user($me['user_id']);

    if (!$match->data) {
        send_to_client(400, null, 'you are not in a match');
    }

    if ($match['match_status'] != $match_status) {
        send_to_client(400, ['match_status' => $match['match_status']]);
    }
    
    return $match;
    
}

switch ($action) {
    
    case 'check_match_status':
        
        $match = get_match_and_validate_match_status(MATCH_PLAYING);
        $match_user = get_match_user($me['user_id']);
        
        if ($match_user['match_user_color'] == 'white') {
            $is_my_turn = $match['match_turn_count'] % 2 != 0;
        } else if ($match_user['match_user_color'] == 'black') {
            $is_my_turn = $match['match_turn_count'] % 2 == 0;
        }
        
        $output = [
            'match_status' => $match['match_status'],
            'is_my_turn' => $is_my_turn,
            'turn_count' => $match['match_turn_count']
        ];
        
        send_to_client(200, $output);
        
        break;
        
    case 'ready_to_play':
        
        $match = get_match_and_validate_match_status(MATCH_PREGAME);
        $match_users = get_match_users($match['match_id']);
        
        //print_r($match_users);
        
        $me_index = $match_users[0]['match_user_user_id'] == $me['user_id'] ? 0 : 1;
        $them_index = $me_index ? 0 : 1;
        
        // check if the user has already readied up
        if ($match_users[$me_index]['match_user_is_ready']) {
            send_to_client(208);
        }
        
        $me_match_user = $match_users[$me_index];
        $me_match_user['match_user_is_ready'] = true;
        $me_match_user->update();
        
        if ($match_users[$them_index]['match_user_is_ready']) {
            $match['match_status'] = MATCH_PLAYING;
            $match->update();
            send_to_client(202, null, 'match is starting now');
        } else {
            send_to_client(202, null, 'waiting for opponent to ready');
        }
        
        break;
    
    case 'move_piece':
        
        $match = get_match_and_validate_match_status(MATCH_PLAYING);
        $match_user = get_match_by_user($me['user_id']);
		$moving_piece = get_piece_by_id(filter_var($input['piece_id'], FILTER_VALIDATE_INT));
        
		if (!$moving_piece) {
            send_to_client(400, ['space_error' => SPACE_ERROR_NO_PIECE_TO_MOVE]);
        }
		
		if ($moving_piece['piece_user_id'] != $me['user_id']) {
			send_to_client(400, ['space_error' => SPACE_ERROR_NOT_YOUR_PIECE]);
		}
		
        // read in coordinates from json
        $new_coord_x = filter_var($input['new_coord_x'], FILTER_VALIDATE_INT);
        $new_coord_y = filter_var($input['new_coord_y'], FILTER_VALIDATE_INT);

        // get space records from the database
        $old_space = get_space_by_id($moving_piece['piece_space_id']);
        $new_space = get_space_by_coords($match, $new_coord_x, $new_coord_y);
		
		// *** make sure new space is within piece's ability to move

        // check to make sure the space is a normal space (not an obstacle)
        if ($new_space['space_type_id'] != SPACE_TYPE_NORMAL) {
            send_to_client(409, ['space_error' => SPACE_ERROR_OBSTACLE]);
        }
        
        // get the piece from the new space (if there is one)
        $other_piece = get_piece_by_space($new_space['space_id']);
        
        // there is a piece in this space and it's not yours, capture it
        if ($other_piece['piece_id'] && $other_piece['piece_user_id'] != $me['user_id']) {
            
            // update space_id column to null to indicate it's been captured
            $other_piece['piece_space_id'] = null;
            $other_piece->update();
			
			// update the kill count on the moving piece
			$moving_piece['piece_kill_count'] += 1;
            
        // there is a piece in this space and it's yours, can't move here
        } else if ($other_piece['piece_id'] && $other_piece['piece_user_id'] == $me['user_id']) {
            send_to_client(400, ['space_error' => SPACE_ERROR_INVALID_MOVE]);
        } else {
            
        }
        
        // move moving piece to new space
        $moving_piece['piece_space_id'] = $new_space['space_id'];
        $moving_piece->update();
		
		// increment match turn count
		$match['match_turn_count'] += 1;
		$match->update();
        
        send_to_client(202);
        
        break;
        
    case 'resign':
        
        $match = get_match_and_validate_match_status(MATCH_PLAYING);
        $match_user = get_match_user($me['user_id']);
        
        if ($match_user['match_user_color'] == 'white') {
            $match['match_status'] = MATCH_BLACK_WIN;
        } else if ($match_user['match_user_color'] == 'black') {
            $match['match_status'] = MATCH_WHITE_WIN;
        }
        
        $match->update();
        
        send_to_client(202);
        
        break;
    
}

?>