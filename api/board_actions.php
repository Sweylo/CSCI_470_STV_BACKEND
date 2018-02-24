<?php

require_once('../model/board_db.php');

switch ($action) {
    
    // returns board data identified by the board_id
    case 'get_board':
        
        $board = get_board_by_id(filter_var($input['board_id'], FILTER_VALIDATE_INT));
        
        if (!$board) {
            send_to_client(400);
        }
        
        $board_init_spaces = get_board_init_spaces($board['board_id']);
        $board_coords = [];
        
        //print_r($board);
        //print_r($board_init_spaces);
        
        foreach ($board_init_spaces as $space) {
            
            $this_coord = [
                'row' => $space['board_init_coord_x'],
                'col' => $space['board_init_coord_y'],
                'class_id' => $space['board_init_class_id']
            ];
            
            if ($space['board_init_class_id'] > 0) {
                $this_coord['piece_color'] = $space['board_init_piece_color'];
            }
            
            array_push($board_coords, $this_coord);
        }
        
        send_to_client(200, ['info' => $board->data, 'spaces' => $board_coords]);
        
        break;
    
}