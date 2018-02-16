<?php

require_once('../model/sql.php');
require_once('../model/board_db.php');
require_once('../model/space_db.php');
require_once('../model/piece_db.php');

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
    $join = $sql->join(['match_users'], [['match_id', 'match_user_match_id']]);
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

function get_match_by_user($user_id) {
    $match = new sql('matches');
    $join = $match->join(['match_users'], [['match_id', 'match_user_match_id']]);
	$user_match = $join->select(array(
		'column' => 'match_white_user_id', 
		'value' => $user_id
	));
    //print_r($user_match);
    if ($user_match) {
        return $user_match;
    } else {
        return $join->select(array(
            'column' => 'match_black_user_id', 
            'value' => $user_id
        ));
    }
}

function add_match($user_id, $board_id, $color) {
    
	$match = sql::insert('matches', array(
		'board_id' => $board_id, 'match_status' => MATCH_WAITING
	), true);
    
	if ($color == 'white') {
		sql::insert('match_users', array(
			'match_white_user_id' => $user_id, 'match_user_match_id' => $match['match_id']
		));
	} else if ($color == 'black') {
		sql::insert('match_users', array(
			'match_black_user_id' => $user_id, 'match_user_match_id' => $match['match_id']
		));
	}
    
    return $match['match_id'];
    
}

function join_match($match_id, $user_id) {
    
    //echo "join_match($match_id, $user_id);";
    
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
    
    echo "--{$match['match_id']}--";
    
    $match_users = new sql('match_users');
	$match_users->select(array('column' => 'match_user_match_id', 'value' => $match['match_id']));
    
    //print_r($match_users);
    
    if ($match_users['match_white_user_id']) {
        $match_users['match_black_user_id'] = $user_id;
    } else {
        $match_users['match_white_user_id'] = $user_id;
    }
    
	$match_users->update();
    
}

// creates records in the spaces table and creates records for the pieces
function init_match($match_id) {
    
    $match = get_match_by_id($match_id);
    
    // verify match has the right status
    if ($match['match_status'] != MATCH_PREGAME) {
        throw new Exception('match has the wrong status');
    } 
    
    $board = get_board_by_id($match['board_id']);
    $board_data = json_decode($board['board_data'], true);
    
    //print_r($board_data);
    
    foreach ($board_data['coords'] as $coord) {
        
        try {
            $space = add_space($match_id, $coord['col'], $coord['row']);
        } catch (Exception $e) {
            throw $e;
        }
        
        //print_r($space);
        //echo '<br />';
        //print_r($coord);
        //echo '<br />';
        
        // add a piece if there's a piece id, that's not null to the associated color
        if ($coord['piece_class_id'] && $coord['piece_color'] == 'white') {
            add_piece($space['space_id'], $coord['piece_class_id'], $match['match_white_user_id']);
        } else if ($coord['piece_class_id'] && $space && $coord['piece_color'] == 'black') {
            add_piece($space['space_id'], $coord['piece_class_id'], $match['match_black_user_id']);
        }
        
    }
    
}

function delete_match($id) {
    $match = new sql('matches');
    $match->select(array('match_id', $id));
    $match->delete();
}

?>