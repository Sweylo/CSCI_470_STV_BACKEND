<?php

// set output to json
header('Content-Type: text/json');

switch ($action) {
    
    // returns board data identified by the board_id
    case 'get_board_by_id':
        $board = get_board_by_id($input['board_id']);
        echo $board['board_data'];
        break;
    
}