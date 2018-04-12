<?php

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/board_db.php');
require_once($dir_depth . 'model/space_db.php');
require_once($dir_depth . 'model/piece_db.php');

// status constants
const MATCH_WAITING = 1;
const MATCH_PREGAME = 2;
const MATCH_PLAYING = 3;
const MATCH_STALEMATE = 4;
const MATCH_WHITE_WIN = 5;
const MATCH_BLACK_WIN = 6;

// text to associate with the error code
$match_status_enum = [
    MATCH_WAITING => 'waiting for an opponent', 
    MATCH_PREGAME => 'setting up the match',
    MATCH_PLAYING => 'match is being played',
    MATCH_STALEMATE => 'match is over (stalemate)',
    MATCH_WHITE_WIN => 'match is over (white win)',
    MATCH_BLACK_WIN => 'match is over (black win)'
];

// error constants
const MATCH_ERROR_NOT_LOGGED_IN = 1;
const MATCH_ERROR_NOT_IN_A_MATCH = 2;
const MATCH_ERROR_ALREADY_IN_A_MATCH = 3;

// message constants
const MATCH_MESSAGE_WAITING_FOR_OPPONENT_READY = 1;
const MATCH_MESSAGE_MATCH_IS_STARTING = 2;

/**
 * gets all the matches in the database
 * 
 * @param int $limit number of matches to get
 * @return array array of sql objects
 */
function get_matches($limit = null) {
	$sql = new sql('matches');
    //$join = $sql->join(['match_users'], [['match_id', 'match_user_match_id']]);
	$matches = $sql->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
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
    $match->select([
        'column' => 'match_id',
        'value' => $match_id
    ]);
    return $match;
}

function get_match_user($user_id) {
	
    /*$match_user = new sql('match_users');
    $match_user->select([
        'column' => 'match_user_user_id', 
        'value' => $user_id
    ], sql::SELECT_SINGLE);
    return $match_user;*/
	
	$match = get_user_current_match($user_id);
	
	$sql = 'SELECT * FROM match_users 
			WHERE match_user_match_id = ? 
				AND match_user_user_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('ii', $match['match_id'], $user_id);
	$stmt->execute();
	$result = $stmt->get_result();
    $match_user = new sql('match_users', $result->fetch_array(MYSQLI_ASSOC));
	
	return $match_user;
}

function get_match_users($match_id) {
    $match_users = new sql('match_users');
    $match_users->select([
        'column' => 'match_user_match_id', 
        'value' => $match_id
    ], sql::SELECT_MULTIPLE);
    return $match_users;
}

function get_opponent_match_user($match_id, $user_id) {
	
    $sql = 'SELECT * FROM match_users 
			WHERE match_user_match_id = ? 
				AND match_user_user_id != ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('ii', $match_id, $user_id);
	$stmt->execute();
	$result = $stmt->get_result();
    $opponent = new sql('match_users', $result->fetch_array(MYSQLI_ASSOC));
	
	return $opponent;
	
}

function get_match_by_user($user_id) {
    
    // get the match_user row for this user_id
    $match_user = get_match_user($user_id);
    
    //print_r($match_user);
    
    // if there is a user associated with this match, return the match data
    if ($match_user->data) {
        $match = new sql('matches');
        $match->select([
            'column' => 'match_id', 
            'value' => $match_user['match_user_match_id']
        ]); 
        return $match;
    }
    
}

function get_user_current_match($user_id, $return_obj = false) {
	
	$sql = 'SELECT * FROM match_users mu JOIN matches m 
				ON mu.match_user_match_id = m.match_id 
			WHERE match_user_user_id = ? 
				AND match_status < 4';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	$match = $result->fetch_array(MYSQLI_ASSOC);
	
	if ($match && !$return_obj) {
		return $match;
	} else if ($match && $return_obj) {
		return get_match_by_id($match['match_id']);
	} else {
		return false;
	}
	
}

function is_user_playing_match($user_id) {
	$match = get_user_current_match($user_id);
	return (bool) $match;
}

function add_match($user_id, $board_id) {
    
    // insert record to match table
	$match = sql::insert('matches', array(
		'match_board_id' => $board_id, 'match_status' => MATCH_WAITING
	), true);
    
    // insert record to match_users to link the user and match
    sql::insert('match_users', [
        'match_user_user_id' => $user_id, 
        'match_user_match_id' => $match['match_id']
        //'match_user_color' => $color
    ]);
    
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
        //print_r($match);
        $match->update();
    } else {
        throw new Exception ("match is not available to join");
    }
    
    sql::insert('match_users', [
        'match_user_user_id' => $user_id, 
        'match_user_match_id' => $match['match_id']
    ]);
    
}

// creates records in the spaces table and creates records for the pieces, and randomly assigns 
//  both users to a color
function init_match($match_id) {
    
    $match = get_match_by_id($match_id);
    $match_users = new sql('match_users');
    $match_users->select(
        ['column' => 'match_user_match_id', 'value' => $match_id], 
        sql::SELECT_MULTIPLE
    );
    
    // verify match has the right status
    if ($match['match_status'] != MATCH_PREGAME) {
        throw new Exception('match has the wrong status');
    } 
    
    // verify there are the right number of users
    if (count($match_users->data) != 2) {
        throw new Exception('invalid number of users');
    }
    
    // simulate the flip of a coin to decide who goes first
    $coin_is_heads = (int) rand(0, 1);
    
    // associate the colors with the coin toss value
    $colors = [$coin_is_heads => 'white', !$coin_is_heads => 'black'];
    
    // change the index to the color
    $match_users[$colors[0]] = $match_users[0];
    $match_users[$colors[1]] = $match_users[1];
    
    // set the color of the field match_user_color
    $match_users[$colors[0]]['match_user_color'] = $colors[0];
    $match_users[$colors[1]]['match_user_color'] = $colors[1];
    
    // update the rows
    $match_users[$colors[0]]->update();
    $match_users[$colors[1]]->update();
    
    // this doesn't work (Uncaught Error: Call to a member function update() on array in...)
    /*for ($i = 0; $i < count($match_users->data); $i++) {
        $match_users[$colors[$i]] = $match_users[$i];
        unset($match_users[$i]);
        $match_users[$colors[$i]]['match_user_color'] = $colors[$i];
        $match_users[$colors[$i]]->update();
    }*/
    
    //print_r($match_users);
	
    // get and decode the specified board's data
    $board = get_board_by_id($match['match_board_id']);
    //$board_data = json_decode($board['board_data'], true);
    
    $board_coords = get_board_init_space_array($board['board_id']);
    
    //print_r($board_data);
	
	$relative_id = 1;
    
    // loop through every coordinate and add a row to the database for each one and for any pieces
    //  that start out on that space
    foreach ($board_coords as $coord) {
        
        try {
            $space = add_space($match_id, $coord['col'], $coord['row']);
        } catch (Exception $e) {
            throw $e;
        }
        
        /*print_r($space);
        echo '<br />';
        print_r($coord);
        echo '<br />';*/
      
        if ($coord['piece_class_id'] && $coord['piece_color']) {
            add_piece($space['space_id'], $coord['piece_class_id'], 
                $match_users[$coord['piece_color']]['match_user_user_id'], $relative_id);
			$relative_id++;
        }
        
    }
    
}

function delete_match($match_id) {
	
    $match = new sql('matches');
    $match->select(array('match_id', $match_id));
	
	$sql = 'DELETE FROM pieces 
			WHERE piece_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('i', $piece_id);
	$stmt->execute();
	$stmt->closeCursor();
	
    $match->delete();
	
}

?>