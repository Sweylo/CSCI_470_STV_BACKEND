<?php

require_once('../model/board_db.php');

switch ($action) {
    
    // returns board data identified by the board_id
    case 'get_board_by_id':
        $board = get_board_by_id(filter_var($input['board_id'], FILTER_VALIDATE_INT));
        send_to_client(200, $board['board_data']);
        break;
    
}