<?php

require_once('../model/sql.php');

// status constants
const MATCH_WAITING_FOR_OPPONENT = 1;
const MATCH_PREGAME = 2;
const MATCH_PLAYING = 3;
const MATCH_END = 4;

/**
 * gets all the matches in the database
 * 
 * @param int $limit number of matches to get
 * @return array array of sql objects
 */
function get_matches($limit = null) {
	$sql = new sql('matches');
	$matches = $sql->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
	return $matches;
}

function get_avail_matches($limit = null) {
	$sql = new sql('matches');
	$matches = $sql->select(array(
		'column' => 'match_status', 'value' => MATCH_WAITING_FOR_OPPONENT, 'limit' => $limit
	), sql::SELECT_MULTIPLE);
	return $matches;
}

function get_match_by_name($match_name) {
	$match = new sql('matches');
	$match->select(array(
		'column' => 'match_name', 
		'value' => $match_name
	));
	return $match;
}

function get_match_by_id($id) {
	$match = new sql('matches');
	$match->select(array(
		'column' => 'match_id', 
		'value' => $id
	));
	return $match;
}

function add_match($user_id, $board_id, $color) {
	$match = sql::insert('matches', array(
		'board_id' => $board_id, 'match_status' => MATCH_WAITING_FOR_OPPONENT
	), true);
	if ($color == 'white') {
		sql::insert('match_users', array(
			'match_white_user_id' => $user_id, 'match_id' => $match['match_id']
		));
	} else if ($color == 'black') {
		sql::insert('match_users', array(
			'match_black_user_id' => $user_id, 'match_id' => $match['match_id']
		));
	}
}

function edit_match($id, $matchname, $password, $email) {
	$match = new sql('matches');
	$match->select(array('match_id', $id));
	$match['match_name'] = $matchname;
	$match['match_password'] = sha1($matchname . $password);
	$match['match_email'] = $email;
	$match->update();
}

function delete_match($id) {
	
	/*global $db;
	
	$sql = 'DELETE FROM matchs 
			WHERE match_id = ?';
	
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $match_id);
	$stmt->execute();
	$stmt->closeCursor();*/
    
    $match = new sql('matches');
    $match->select(array('match_id', $id));
    $match->delete();
	
}

?>