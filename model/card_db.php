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
					trap_card_trap_id as attr_id,
					card_play_opportunity as card_play_opportunity,
					\'trap\' as card_type
				FROM cards c
					JOIN trap_cards tc ON c.card_id = tc.trap_card_card_id

			UNION

				SELECT 
					power_card_card_id as card_id,
					power_card_name as card_name, 
					power_card_description as card_description, 
					power_card_ability_id as attr_id,
					power_card_upgrade_id as card_upgrade_id,
					card_play_opportunity as card_play_opportunity,
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

function assign_card($card_id, $user_id, $match_id) {
	
	$sql = 'UPDATE user_cards 
				SET user_card_match_id = ? 
			WHERE user_card_card_id = ?
				AND user_card_user_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('iii', $match_id, $card_id, $user_id);
	$stmt->execute();
	
}

function use_power_card($card_id, $user_id, $piece_id) {
	
	$card = get_card_by_id($card_id);
	
	$sql = 'SELECT * FROM upgrades u 
			JOIN abilities a 
				ON u.upgrade_ability_id = a.ability_id
			WHERE upgrade_id = ?';
			
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('i', $card['card_upgrade_id']);
	$stmt->execute();
	$result = $stmt->get_result();
    $upgrade = $result->fetch_array(MYSQLI_ASSOC); 
	
	$piece = get_piece_by_id($piece_id);
	$piece['piece_kill_count'] = $upgrade['upgrade_kill_count'];
	$piece->update();
	
	send_card_to_discard($card_id, $user_id);
	
}

function use_trap_card($card, $user, $space_id) {
	
}

function send_card_to_discard($card_id, $user_id) {
	
	$sql = 'UPDATE user_cards 
				SET user_card_is_used = 1
			WHERE user_card_card_id = ?
				AND user_card_user_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('iii', $card_id, $user_id);
	$stmt->execute();
	
}

function add_card() {
	return sql::insert('cards', [], true);
}

function add_power_card($card_name, $card_desc, $card_ability_id) {
	
	$card = add_card();
	
	return sql::insert('power_cards', [
		'power_card_name' => $card_name, 
		'power_card_description' => $card_desc,
		'power_card_card_id' => $card['card_id'],
		'power_card_ability_id' => $card_ability_id
	], true);
	
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