<?php

/**
 *	controller for ability management
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$dir_depth = '../../';

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/input.php');
require_once($dir_depth . 'model/user_db.php');
require_once($dir_depth . 'model/board_db.php');
require_once($dir_depth . 'model/ability_db.php');

$action = input(INPUT_GET, 'action');

switch ($action) {

	default: case 'list_abilities':
		
		$abilities = get_abilities(input(INPUT_GET, 'limit'));
		
		include('list_abilities.php');
		break;
	
	case 'ability_designer':
		include('ability_designer.php');
		break;
	
	case 'add_ability': 
		
		$output = [];
		
		for ($y = -2; $y <= 2; $y++) {
			
			for ($x = -2; $x <= 2; $x++) {
				
				$piece_class_id = input(INPUT_POST, 'piece_class_id');
				$level = input(INPUT_POST, 'level');
				$range = input(INPUT_POST, "$x,$y-range");
				
				$move_code 
					= (input(INPUT_POST, "$x,$y-defend") == 'on' ? 4 : 0) 
					+ (input(INPUT_POST, "$x,$y-attack") == 'on' ? 2 : 0) 
					+ (input(INPUT_POST, "$x,$y-move") == 'on' ? 1 : 0);
				
				//echo "$x, $y: $move_code<br />";
				
				$output[$x][$y] = [
					'move_code' => $move_code,
					//'relative_x' => $x,
					//'relative_y' => $y,
					'move_range' => $range ? $range : 0
				];
				
			}
			
		}
		
		//die (json_encode($output, JSON_PRETTY_PRINT));
		
		try {
			add_ability(json_encode($output), $piece_class_id, $level);
		} catch (Exception $e) {
			die($e);
		}
		
		$message = 'Ability successfully added.';
		include('ability_designer.php');
		
		break;
	
}

?>