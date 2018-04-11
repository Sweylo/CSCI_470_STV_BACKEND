<?php

require_once($dir_depth . 'model/user_db.php');
require_once($dir_depth . 'model/match_db.php');
require_once($dir_depth . 'model/space_db.php');
require_once($dir_depth . 'model/piece_db.php');
require_once($dir_depth . 'model/card_db.php');
require_once($dir_depth . 'model/match_move_log_db.php');

// move error constants
const MOVE_ERROR_NOT_YOUR_TURN = 1;

/**
 * function to get and validate the match the user is currently in
 * 
 * @param int $match_status the match status to check the match is in
 * @return object the sql object of the match the user is currently in
 */
function get_match_and_validate_match_status($match_status, $no_more_than = false) {
    
    global $me;
    
    if (!$me) {
        send_to_client(401, ['match_error' => MATCH_ERROR_NOT_LOGGED_IN]);
    }

    $match = get_match_by_user($me['user_id']);

    if (!$match->data) {
        send_to_client(400, ['match_error' => MATCH_ERROR_NOT_IN_A_MATCH]);
    }

    if (!$no_more_than && $match['match_status'] != $match_status) {
        send_to_client(400, ['match_status' => $match['match_status']]);
    } else if ($no_more_than && $match['match_status'] > $match_status) {
        send_to_client(400, ['match_status' => $match['match_status']]);
    }
    
    return $match;
    
}

switch ($action) {
    
    case 'check_match_status':
        
        $match = get_match_and_validate_match_status(MATCH_PLAYING, true);
        $match_user = get_match_user($me['user_id']);
		$log = get_last_move_log_by_match_id($match['match_id']);
        
		print_r($log);
		
        if ($match_user['match_user_color'] == 'white') {
            $is_my_turn = $match['match_turn_count'] % 2 != 0;
        } else if ($match_user['match_user_color'] == 'black') {
            $is_my_turn = $match['match_turn_count'] % 2 == 0;
        }
        
        $output = [
            'match_status' => $match['match_status'],
            'is_my_turn' => $is_my_turn,
            'turn_count' => $match['match_turn_count'],
        ];
		
		if ($log) {
			$output['last_move'] = [
				'moving_piece_id' => $log['match_move_relative_piece_id'],
				'space_x' => $log['match_move_coord_x'],
				'space_y' => $log['match_move_coord_y']
			];
		}
		
		if ($log['match_move_captured_relative_piece_id'] > 0) {
			$output['last_move']['captured_piece_id'] = 
				$log['match_move_captured_relative_piece_id'];
		}
        
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
		
		if (!is_null($input['cards']) && is_array($input['cards'])) {
			
			

			// get all cards belonging to the user (the deck)
			$user_cards = get_cards_by_user($me['user_id']);

			if (!is_array($user_cards) && is_array($input['cards'])) {
				send_to_client(404);
			}

			// check each card input from the client
			foreach ($input['cards'] as $card_data) {

				// get the card data from the database
				$card_id = filter_var($card_data['card_id'], FILTER_VALIDATE_INT);
				$card = get_card_by_id($card_id);
				//print_r($card);

				// boolean flag to determine whether or not the user has the card specified
				$user_has_card = false;

				// check to see if the user has the card available
				foreach ($user_cards as $user_card) {

					// check if the user has the card, and if it has been used already
					if (
						$user_card['card_id'] == $card['card_id'] 
						&& 
						is_null($user_card['card_match_id'])
					) {
						$user_has_card = true;
						break;
					}

				}

				if (!$user_has_card) {
					// return error if user doesn't have card
					send_to_client(400, [
						'card_error' => CARD_NOT_IN_DECK, 
						'card_id' => $card_data['card_id']
					]);
				}

				// get piece if is a power card
				if ($card['card_type'] == 'power' && isset($input['piece_id'])) {
					$piece_id = filter_var($card_data['piece_id'], FILTER_VALIDATE_INT);
				// get space if is a trap card
				} else if ($card['card_type'] == 'trap' && isset($input['space_id'])) {
					$space_id = filter_var($card_data['space_id'], FILTER_VALIDATE_INT);
				}

				// set the match_id field to indicate that the card has been used for this match
				assign_card($card_data['card_id'], $me['user_id'], $match['match_id']);

				// if card is used in the beginning, send to discard
				if ($card['card_play_opportunity'] == 0 && $card['card_type'] == 'power') {
					use_power_card($card['card_id'], $piece_id);
				} else if ($card['card_play_opportunity'] == 0 && $card['card_type'] == 'trap') {
					use_trap_card($card['card_id'], $space_id);
				}

			}
		
		}
        
        $me_match_user = $match_users[$me_index];
        $me_match_user['match_user_is_ready'] = true;
        $me_match_user->update();
        
        if ((bool) $match_users[$them_index]['match_user_is_ready']) {
            $match['match_status'] = MATCH_PLAYING;
            $match->update();
            send_to_client(202, ['match_message' => MATCH_MESSAGE_MATCH_IS_STARTING]);
        } else {
            send_to_client(202, ['match_message' => MATCH_MESSAGE_WAITING_FOR_OPPONENT_READY]);
        }
        
        break;
    
    case 'move_piece':
        
        $match = get_match_and_validate_match_status(MATCH_PLAYING);
		$match_user = get_match_user($me['user_id']);
        
		if ($match_user['match_user_color'] == 'white') {
            $is_my_turn = $match['match_turn_count'] % 2 != 0;
        } else if ($match_user['match_user_color'] == 'black') {
            $is_my_turn = $match['match_turn_count'] % 2 == 0;
        }
		
		if (!$is_my_turn) {
			send_to_client(400, ['move_error' => MOVE_ERROR_NOT_YOUR_TURN]);
		}
		
		$relative_piece_id = filter_var($input['piece_id'], FILTER_VALIDATE_INT);
		$moving_piece = get_piece_by_relative_id($me['user_id'], $relative_piece_id);
		
		//print_r($moving_piece);
		//die();
        
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
		
		//print_r($new_space);
		
		$board = get_board_by_id($match['match_board_id']);
		
		// get pieces move code for the coordinate specified
		$move_code = get_piece_move_code($board, $moving_piece, $new_coord_x, $new_coord_y);
		
		//echo "move_code: $move_code";
		//die();
		
		if ($move_code < 1) {
			send_to_client(400, ['move_ability_error' => $move_code]);
		}

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
			$moving_piece['piece_kill_count']++;
            
        // there is a piece in this space and it's yours, can't move here
        } else if ($other_piece['piece_id'] && $other_piece['piece_user_id'] == $me['user_id']) {
            send_to_client(400, ['space_error' => SPACE_ERROR_INVALID_MOVE]);
        } else {
            
        }
        
        // move moving piece to new space
        $moving_piece['piece_space_id'] = $new_space['space_id'];
        $moving_piece->update();
		
		// increment match turn count
		$match['match_turn_count']++;
		
		// check for check
		$opp_is_in_check = get_check_status($me['user_id'], $match);
		
		// output array to be passed as json to client
		$output = [];
		
		if ($opp_is_in_check) {
			
			// check for checkmate
			if (get_checkmate_status($me['user_id'], $match, $moving_piece)) {
				
				$match['match_status'] = $match_user['match_user_color'] == 'white' 
					? MATCH_WHITE_WIN : MATCH_BLACK_WIN;
				
				$victor = get_user_by_id($match_user['match_user_user_id']);
				
				array_push($output, [
					'match_status' => $match['match_status'],
					'match_victor' => $victor['user_name']
				]);
				
			} 
			
		} else {
			
		}
		
		array_push($output, ['check_status' => $opp_is_in_check]);
		
		// update match record
		$match->update();
		
		// add record to match move log
		try {
			log_move($match, $moving_piece, $other_piece, $new_space);
		} catch (Exception $e) {
			echo "error logging move: $e";
		}
		
        send_to_client(202, $output);
        
        break;
		
	case 'get_check_status':
		
		$match = get_match_and_validate_match_status(MATCH_PLAYING);
		$check_status = get_check_status($me['user_id'], $match);
		
		send_to_client(200, ['match_id' => $match['match_id'], 'check_status' => $check_status]);
		
		break;
	
	/*case 'get_checkmate_status':
		
		$match = get_match_and_validate_match_status(MATCH_PLAYING);
		$checkmate_status = get_checkmate_status($me['user_id'], $match);
		
		send_to_client(200, [
			'match_id' => $match['match_id'], 
			'checkmate_status' => $checkmate_status
		]);
		
		break;*/
        
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