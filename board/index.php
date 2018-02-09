<?php

/**
 *	controller for board management
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

require_once('../model/sql.php');
require_once('../model/input.php');
require_once('../model/board_db.php');

$page = 'board';
$referer = input(INPUT_SERVER, 'HTTP_REFERER');
$action = input(INPUT_GET, 'action', true) === null 
	? input(INPUT_POST, 'action', true) 
	: input(INPUT_GET, 'action', true);

switch ($action) {

	case 'board_designer':
		include('board_designer.php');
		break;
    
    case 'add_board':
        
        //print_r($_POST);
        
        $board_array = array(
            'board_name' => input(INPUT_POST, 'boardName'),
            'row_count' => input(INPUT_POST, 'rowCount'),
            'col_count' => input(INPUT_POST, 'colCount'),
            'home_col' => input(INPUT_POST, 'homeCol'),
            'coords' => array()
        );
        
        for ($i = 0; $i <= $board_array['row_count']; $i++) {
			
            for ($j = 0; $j <= $board_array['col_count']; $j++) {
                
                $is_space_active = input(INPUT_POST, $i . '-' . $j);
                
                if ($is_space_active) {
                    //echo "<p>$i, $j</p>";
                    //print_r(array('row' => $i, 'col' => $j));
                    array_push($board_array['coords'], array(
                        'row' => $i, 
                        'col' => $j,
                        'piece' => null
                    ));
                }
                
            }
			
        }
        
        echo json_encode($board_array);
        
        break;
	
}

?>