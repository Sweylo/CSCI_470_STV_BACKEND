<?php

require_once('../model/board_db.php');

switch ($action) {
    
    // returns board data identified by the board_id
    case 'get_board':
        
        $board = get_board_by_id(filter_var($input['board_id'], FILTER_VALIDATE_INT));
        
        if (!$board) {
            send_to_client(400);
        }
        
        $board_coords = get_board_init_space_array($board['board_id']);
        
        send_to_client(200, ['info' => $board->data, 'spaces' => $board_coords]);
        
        break;
    
}