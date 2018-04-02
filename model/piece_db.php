<?php

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/board_db.php');
require_once($dir_depth . 'model/space_db.php');
require_once($dir_depth . 'model/ability_db.php');

// piece class constants
const PIECE_CLASS_KING = 1;
const PIECE_CLASS_QUEEN = 2;
const PIECE_CLASS_ROOK = 3;
const PIECE_CLASS_BISHOP = 4;
const PIECE_CLASS_KNIGHT = 5;
const PIECE_CLASS_PAWN = 6;

// move constants
const INVALID_MOVE = 0;
const INVALID_MOVE_OFF_BOARD = -1;
const INVALID_MOVE_BLOCKED = -2;
const INVALID_MOVE_KING_JEOPARDY = -3;

/**
 * gets all the pieces in the database
 * 
 * @param int $limit number of pieces to get
 * @return array array of sql objects`
 */
function get_pieces($limit = null) {
	$sql = new sql('pieces');
	$pieces = $sql->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
	return $pieces;
}

function get_piece_by_name($piecename) {
	$piece = new sql('pieces');
	$piece->select(array(
		'column' => 'piece_name', 
		'value' => $piecename
	));
	return $piece;
}

function get_piece_by_id($id) {
	$piece = new sql('pieces');
	$piece->select(array(
		'column' => 'piece_id', 
		'value' => $id
	));
	return $piece;
}

function get_piece_by_space($space_id) {
    $piece = new sql('pieces');
    $piece->select([
        'column' => 'piece_space_id',
        'value' => $space_id
    ]);
    return $piece;
}

function get_piece_by_relative_id($user_id, $relative_id) {
	
	$sql = 'SELECT * FROM pieces
			WHERE piece_user_id = ? 
				AND piece_relative_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('ii', $user_id, $relative_id);
	$stmt->execute();
	$result = $stmt->get_result();
    $piece = new sql('pieces', $result->fetch_array(MYSQLI_ASSOC));
	
	return $piece;
	
}

function get_piece_color($piece) {
	return get_match_user($piece['piece_user_id'])['match_user_color'];
}

function get_captured_pieces($match_id) {
	
	$match_users = get_match_users($match_id);
	$user_1_id = $match_users[0]['match_user_user_id'];
	$user_2_id = $match_users[1]['match_user_user_id'];
	
	$sql = "SELECT * FROM pieces
			WHERE piece_space_id IS NULL
                AND (piece_user_id = $user_1_id OR piece_user_id = $user_2_id)";
	
	$stmt = sql::$db->prepare($sql);
	//$stmt->bind_param('iii', 0, $user_1_id, $user_2_id);
	$stmt->execute();
    $result = $stmt->get_result();
    $pieces = [];
	
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		array_push($pieces, $row);
	}
    
	return $pieces;
	
}

function get_piece_move_code($match, $piece, $new_x, $new_y) {
	
	$ability = get_ability_by_class_and_kill_count($piece['piece_class_id'], 
		$piece['piece_kill_count']);
	$space = get_space_by_id($piece['piece_space_id']);
	$board = get_board_by_id($match['match_board_id']);
	
	$max_x = $board['board_col_count'];
	$max_y = $board['board_row_count'];
	$current_x = $space['space_coord_x'];
	$current_y = $space['space_coord_y'];
	
	// check that the move is in the bounds of the board
	if (($new_x > $max_x || $new_x < 1) && ($new_y > $max_y || $new_y < 1)) {
		return INVALID_MOVE_OFF_BOARD;
	}
	
	$rel_x = $new_x - $current_x;
	$rel_y = ($new_y - $current_y) * (get_piece_color($piece) == 'black' ? -1 : 1);
	
	//echo "rel_x: $rel_x | rel_y: $rel_y<br />";
	
	$ability_data = json_decode($ability['ability_data'], true);
	
	//print_r($ability_data);
	//echo $ability_data[$rel_y][$rel_x]['move_code'];
	
	if ($piece['piece_class_id'] == PIECE_CLASS_KING) {
		$ability_data[$y_dir][$x_dir]['move_range'] *= $piece['piece_kill_count'] + 1;
	}
	
	$move_is_in_matrix = !is_null($ability_data[$rel_y][$rel_x]);
	
	// move is in ability matrix with a move code above 0
	if ($move_is_in_matrix && $ability_data[$rel_y][$rel_x]['move_code'] != 0) {
		
		return $ability_data[$rel_y][$rel_x]['move_code'];
		
	// move is outside ability matrix
	} else if (
		!$move_is_in_matrix
		||
		(
			$move_is_in_matrix
			&& 
			$ability_data[$rel_y][$rel_x]['move_code'] == 0
		)
	) {
		
		// diagonal move
		if (abs($rel_x) == abs($rel_y)) {
		
			$x_dir = $rel_x > 0 ? 1 : -1;
			$y_dir = $rel_y > 0 ? 1 : -1;
			$magnitude = $rel_x;
			
		// up/down moves
		} else if ($rel_x == 0 && abs($rel_y) > 0) {
			
			$x_dir = 0;
			$y_dir = $rel_y > 0 ? 1 : -1;
			$magnitude = $rel_y;
			
		// left/right moves
		} else if ($rel_y == 0 && abs($rel_x) > 0) {
			
			$x_dir = $rel_x > 0 ? 1 : -1;
			$y_dir = 0;
			$magnitude = $rel_x;
			
		// invalid moves
		} else {
			echo '1';
			return false;
		}
		
		if (
			$ability_data[$y_dir][$x_dir]['move_range'] >= abs($magnitude)
			||
			$ability_data[$y_dir][$x_dir]['move_range'] < 0
		) {
			return $ability_data[$y_dir][$x_dir]['move_code'];
		}
		
	} else {
		echo '2';
		return false;
	}
	
	echo '3';
	return false;
	
}

function add_piece($space_id, $class_id, $user_id, $relative_id = null) {
	sql::insert('pieces', array(
		'piece_space_id' => $space_id,
		'piece_class_id' => $class_id,
        'piece_user_id' => $user_id,
		'piece_relative_id' => $relative_id
	));
}

function edit_piece($id, $piecename, $password, $email) {
	$piece = new sql('pieces');
	$piece->select(array('piece_id', $id));
	$piece['piece_name'] = $piecename;
	$piece['piece_password'] = password_hash($piecename . $password, PASSWORD_DEFAULT);
	$piece['piece_email'] = $email;
	$piece->update();
}

function delete_piece($id) {
	
	/*global $db;
	
	$sql = 'DELETE FROM pieces 
			WHERE piece_id = ?';
	
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $piece_id);
	$stmt->execute();
	$stmt->closeCursor();*/
    
    $piece = new sql('pieces');
    $piece->select(array('piece_id', $id));
    $piece->delete();
	
}

function delete_piece_by_space($space_id) {
    /*($piece = new sql('pieces');
    $piece->select([
        'column' => 'piece_space_id',
        'value' => $space_id
    ]);
    $piece->delete();*/
	
	$sql = 'DELETE FROM pieces 
			WHERE piece_space_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('i', $space_id);
	$stmt->execute();
	
}

?>