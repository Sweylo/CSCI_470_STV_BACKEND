<?php

require_once($dir_depth . 'model/sql.php');

// card errors
const CARD_NOT_FOUND = 0;
const CARD_NOT_IN_DECK = 1;
const CARD_ALREADY_USED = 2;

/**
 * gets all the cards in the database
 * 
 * @param int $limit number of cards to get
 * @return array array of sql objects`
 */
function get_cards($limit = null) {
	$sql = new sql('cards');
	$cards = $sql->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
	return $cards;
}

function get_card_by_name($cardname) {
	$card = new sql('cards');
	$card->select(array(
		'column' => 'card_name', 
		'value' => $cardname
	));
	return $card;
}

function get_card_by_id($id) {
	
	$sql = 'SELECT * FROM (

				SELECT 
					trap_card_card_id as card_id,
					trap_card_name as card_name, 
					trap_card_description as card_description, 
					\'trap\' as card_type
				FROM cards c
					JOIN trap_cards tc ON c.card_id = tc.trap_card_card_id

			UNION

				SELECT 
					power_card_card_id as card_id,
					power_card_name as card_name, 
					power_card_description as card_description, 
					\'power\' as card_type
				FROM cards c
					JOIN power_cards pc ON c.card_id = pc.power_card_card_id

			) as result
			WHERE card_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('i', $id);
	$stmt->execute();
	$result = $stmt->get_result();
    $card = $result->fetch_array(MYSQLI_ASSOC);
	
    return $card;
	
}

function get_cards_by_user($user_id) {
	
	$sql = new sql('user_cards');
	$user_cards = $sql->select(array(
		'column' => 'user_card_user_id', 
		'value' => $user_id
	));
	
	//print_r($user_cards);
	
	if (!is_array($user_cards[0])) {
		$user_cards = [$user_cards];
	}
	
	$cards = [];

	foreach ($user_cards as $key => $user_card) {
		$cards[$key] = get_card_by_id($user_card['user_card_card_id']);
		$cards[$key]['card_match_id'] = $user_card['user_card_match_id'];
	}

	return $cards;
	
}

function use_card($card_id, $user_id, $match_id) {
	
	$sql = 'UPDATE user_cards 
				SET user_card_match_id = ? 
			WHERE user_card_card_id = ?
				AND user_card_user_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('iii', $match_id, $card_id, $user_id);
	$stmt->execute();
	
}

function add_card($match_id, $coord_x, $coord_y) {
	return sql::insert('cards', array(
		'card_match_id' => $match_id, 
		'card_coord_x' => $coord_x,
		'card_coord_y' => $coord_y
	), true);
}

function edit_card($id, $cardname, $password, $email) {
	$card = new sql('cards');
	$card->select(array('card_id', $id));
	$card['card_name'] = $cardname;
	$card['card_password'] = password_hash($cardname . $password, PASSWORD_DEFAULT);
	$card['card_email'] = $email;
	$card->update();
}

function delete_card($id) {
	
	/*global $db;
	
	$sql = 'DELETE FROM cards 
			WHERE card_id = ?';
	
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $card_id);
	$stmt->execute();
	$stmt->closeCursor();*/
    
    $card = new sql('cards');
    $card->select(array('card_id', $id));
    $card->delete();
	
}

?>