<?php

require_once('../model/user_db.php');
require_once('../model/match_db.php');
require_once('../model/space_db.php');
require_once('../model/piece_db.php');

switch ($action) {
    
    case 'check_match_status':
        
        if (!$me) {
            send_to_client(401, null, 'must be logged in');
        }
        
        $match = get_match_by_user($me['user_id']);
        
        //print_r($match);
        
        if (!$match->data) {
            send_to_client(200, null, 'you are not in a match');
        }
        
        if ($match['match_status'] != MATCH_PLAYING) {
            send_to_client(200, ['match_status' => (int) $match['match_status']]);
        }
        
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
        
        if (!$me) {
            send_to_client(401, null, 'must be logged in');
        }
        
        $match = get_match_by_user($me['user_id']);
        
        if (!$match->data) {
            send_to_client(400, null, 'you are not in a match');
        }
        
        if ($match['match_status'] != MATCH_PREGAME) {
            send_to_client(400, ['match_status' => $match['match_status']]);
        }
        
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
        
        if (!$me) {
            send_to_client(401);
        }
        
        $match = get_match_by_id(filter_var($input['match_id'], FILTER_VALIDATE_INT));
        $match_user = get_match_by_user($me['user_id']);
        
        // check if the user is not in the requested match
        if ($match['match_id'] != $match_user['match_id']) {
            send_to_client(403);
        }
        
        // read in coordinates from json
        $old_coord_x = filter_var($input['old_coord']['coord_x'], FILTER_VALIDATE_INT);
        $old_coord_y = filter_var($input['old_coord']['coord_y'], FILTER_VALIDATE_INT);
        $new_coord_x = filter_var($input['new_coord']['coord_x'], FILTER_VALIDATE_INT);
        $new_coord_y = filter_var($input['new_coord']['coord_y'], FILTER_VALIDATE_INT);

        // get space records from the database
        $old_space = get_space_by_coords($match, $old_coord_x, $old_coord_y);
        $new_space = get_space_by_coords($match, $new_coord_x, $new_coord_y);

        // check to make sure the space is a normal space (not an obstacle)
        if ($new_space['space_type_id'] != SPACE_TYPE_NORMAL) {
            send_to_client(409, json_encode(['space_error' => SPACE_ERROR_OBSTACLE]));
        }
        
        // get the piece from the old space
        $old_piece = get_piece_by_space($old_space['space_id']);
        
        if (!$old_piece) {
            send_to_client(400);
        }
        
        // get the piece from the new space (if there is one)
        $new_piece = get_piece_by_space($new_space['space_id']);
        
        // there is a piece in this space and it's not yours, capture it
        if ($new_piece && $new_piece['piece_user_id'] != $me['user_id']) {
            
            // update space_id column to null to indicate it's been captured
            $new_piece['space_id'] = null;
            $new_piece->update();
            
        // there is a piece in this space and it's yours, can't move here
        } else if ($new_piece && $new_piece['piece_user_id'] == $me['user_id']) {
            send_to_client(400);
        } else {
            
        }
        
        // move old piece to new space
        $old_piece['piece_space_id'] = $new_space['space_id'];
        $old_piece->update();
        
        die();
        
        break;
    
}

?>