<?php

/**
 *	controller for board management
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$dir_depth = '../../';

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/input.php');
require_once($dir_depth . 'model/user_db.php');
require_once($dir_depth . 'model/board_db.php');

$referer = input(INPUT_SERVER, 'HTTP_REFERER');
$action = input(INPUT_GET, 'action', true) === null 
	? input(INPUT_POST, 'action', true) 
	: input(INPUT_GET, 'action', true);

switch ($action) {

	case 'board_designer':
		include('board_designer.php');
		break;
	
	case 'edit_board':
		$board = get_board_by_id(input(INPUT_GET, 'board_id'));
		$board_data = $board['board_data'];
		include('board_designer.php');
		break;
    
    case 'add_board':
        
        $board_name = input(INPUT_POST, 'boardName');
        $row_count = input(INPUT_POST, 'rowCount');
        $col_count = input(INPUT_POST, 'colCount');
        $home_col = input(INPUT_POST, 'homeCol');
        
        try {
			$board = add_board($board_name, $row_count, $col_count, $home_col);
		} catch(mysqli_sql_exception $e) {
			$message = 'error adding board to the database: ' . $e;
			include('board_designer.php');
		}
        
        for ($i = 0; $i <= $row_count; $i++) {
			
            for ($j = 0; $j <= $col_count; $j++) {
                
                $is_space_active = input(INPUT_POST, "$i-$j-is-active");
                $class_id = input(INPUT_POST, "$i-$j-class-id");
                $piece_color = input(INPUT_POST, "$i-$j-piece-color");
                
                //print_r($class_id);
                //print_r($piece_color);
                
                if ($is_space_active) {
                    add_board_init_space($board['board_id'], $i, $j, (int) $class_id, 
                        $class_id ? $piece_color : null);
                }
                
            }
			
        }
        
		$message = 'board successfully added';
		include('board_designer.php');
        
        break;
	
}

?>