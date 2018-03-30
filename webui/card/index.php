<?php

/**
 *	controller for card management
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$dir_depth = '../../';

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/input.php');
require_once($dir_depth . 'model/user_db.php');
require_once($dir_depth . 'model/card_db.php');
require_once($dir_depth . 'model/ability_db.php');

$referer = input(INPUT_SERVER, 'HTTP_REFERER');
$action = input(INPUT_GET, 'action', true) === null 
	? input(INPUT_POST, 'action', true) 
	: input(INPUT_GET, 'action', true);

switch ($action) {

	default: case 'list_cards':
		
		$cards = get_cards(input(INPUT_GET, 'limit'));
		$abilities = get_abilities();
		
		include('list_cards.php');
		break;
	
	case 'card_designer':
		
		$abilities = get_abilities();
		//$traps = get_traps();
		
		include('card_designer.php');
		break;
	
	case 'edit_card':
		$card = get_card_by_id(input(INPUT_GET, 'card_id'));
		$card_data = $card['card_data'];
		include('card_designer.php');
		break;
    
    case 'add_card':
        
        $card_name = input(INPUT_POST, 'card_name');
		$card_desc = input(INPUT_POST, 'card_desc');
		$card_type = input(INPUT_POST, 'card_type');
		
        try {
			
			if ($card_type == 'power') {
				$ability_id = input(INPUT_POST, 'power_card_ability_id');
				$card = add_power_card($card_name, $card_desc, $ability_id);
			} else if ($card_type == 'trap') {
				//$trap_id = input(INPUT_POST, 'trap_card_trap_id');
				//$card = add_trap_card($card_name, $card_desc, $trap_id);
			}
			
			//$card = add_card($card_name, $row_count, $col_count, $home_col);
			
		} catch(mysqli_sql_exception $e) {
			$message = 'error adding card to the database: ' . $e;
			include('card_designer.php');
			die();
		}
        
		$message = 'card successfully added';
		include('card_designer.php');
        
        break;
	
}

?>