<?php

require_once('../model/sql.php');

// status constants
const MATCH_WAITING = 1;
const MATCH_PREGAME = 2;
const MATCH_PLAYING = 3;
const MATCH_END = 4;

// text to associate with the error code
$match_status_enum = [
    MATCH_WAITING => 'waiting for an opponent', 
    MATCH_PREGAME => 'setting up the match',
    MATCH_PLAYING => 'match in being played',
    MATCH_END => 'match has ended'
];

/**
 * gets all the matches in the database
 * 
 * @param int $limit number of matches to get
 * @return array array of sql objects
 */
function get_matches($limit = null) {
	$sql = new sql('matches');
    $join = $sql->join(['match_users'], [['match_id', 'match_id']]);
	$matches = $join->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
	return $matches;
}

function get_avail_matches($limit = null) {
	$sql = new sql('matches');
	$matches = $sql->select(array(
		'column' => 'match_status', 'value' => MATCH_WAITING, 'limit' => $limit
	), sql::SELECT_MULTIPLE);
	return $matches;
}

function get_match_by_id($match_id) {
	$match = new sql('matches');
    $join = $match->join(['match_users'], [['match_id', 'match_user_match_id']]);
	$match = $join->select(array(
		'column' => 'match_id', 
		'value' => $match_id
	));
	return $join;
}

function add_match($user_id, $board_id, $color) {
    
	$match = sql::insert('matches', array(
		'board_id' => $board_id, 'match_status' => MATCH_WAITING
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

function join_match($match_id, $user_id) {
    
    $match = new sql('matches');
    $match->select(array(
		'column' => 'match_id', 
		'value' => $match_id
	));
    
    //echo $match['match_status'] == MATCH_WAITING ? 'true' : 'false';
    
    if ($match['match_status'] == MATCH_WAITING) {
        $match['match_status'] = MATCH_PREGAME;
        print_r($match);
        $match->update();
    } else {
        throw new Exception ("match is not available to join");
    }
    
    $match_users = new sql('match_users');
	$match_users->select(array('match_user_match_id', $match['match_id']));
    
    //print_r($match_users);
    
    if ($match_users['match_white_user_id']) {
        $match_users['match_black_user_id'] = $user_id;
    } else {
        $match_users['match_white_user_id'] = $user_id;
    }
    
	$match_users->update();
    
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