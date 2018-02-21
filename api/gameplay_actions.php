<?php

require_once('../model/user_db.php');
require_once('../model/match_db.php');
require_once('../model/space_db.php');
require_once('../model/piece_db.php');

switch ($action) {
    
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