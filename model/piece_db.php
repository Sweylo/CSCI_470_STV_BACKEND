<?php

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/board_db.php');
require_once($dir_depth . 'model/match_db.php');
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
const INVALID_MOVE_OUT_OF_RANGE = -4;

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

function get_my_pieces($user_id) {
	
	$sql = 'SELECT * FROM pieces
			WHERE piece_user_id = ? 
				AND piece_space_id IS NOT NULL';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$result = $stmt->get_result();
    $pieces = $result->fetch_all(MYSQLI_ASSOC);
	
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

function get_piece_by_user_and_class($user_id, $piece_class_id) {
	
	$sql = 'SELECT * FROM pieces
			WHERE piece_user_id = ? 
				AND piece_class_id = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('ii', $user_id, $piece_class_id);
	$stmt->execute();
	$result = $stmt->get_result();
    $piece = new sql('pieces', $result->fetch_array(MYSQLI_ASSOC));
	
	return $piece;
	
}

function get_piece_by_relative_id($user_id, $relative_id) {
	
	$sql = 'SELECT * FROM pieces p 
			JOIN spaces s 
				ON p.piece_space_id = s.space_id
			JOIN matches m
				ON s.space_match_id = m.match_id
			WHERE piece_user_id = ? 
				AND piece_relative_id = ?
				AND match_status < 4';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('ii', $user_id, $relative_id);
	$stmt->execute();
	$result = $stmt->get_result();
    //$piece = new sql('pieces', $result->fetch_array(MYSQLI_ASSOC));
	$record = $result->fetch_array(MYSQLI_ASSOC);
	
	return get_piece_by_id($record['piece_id']);
	
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

function get_piece_move_code($board, $piece, $new_x, $new_y) {
	
	// get the ability record
	$ability = get_ability_by_class_and_kill_count($piece['piece_class_id'], 
		$piece['piece_kill_count']);
	
	//print_r($piece);
	
	// get the board and space records
	$space = get_space_by_id($piece['piece_space_id']);
	
	//print_r($space);
	
	// extract values from database records
	$max_x = $board['board_col_count'];
	$max_y = $board['board_row_count'];
	$current_x = $space['space_coord_x'];
	$current_y = $space['space_coord_y'];
	
	//echo "max_x: $max_x | max_y: $max_y<br />";
	//echo "current_x: $current_x | current_y: $current_y<br />";
	//echo "new_x: $new_x | new_y: $new_y<br />";
	
	// check that the move is in the bounds of the board
	if (($new_x > $max_x || $new_x < 1) && ($new_y > $max_y || $new_y < 1)) {
		return INVALID_MOVE_OFF_BOARD;
	}
	
	$piece_color = get_piece_color($piece);

	// get coordinates relative to current space coordinate
	$rel_x = $new_x - $current_x;
	$rel_y = ($new_y - $current_y) * ($piece_color == 'black' ? -1 : 1);
	
	//echo "rel_x: $rel_x | rel_y: $rel_y<br />";
	
	// decode the json data from the database record
	$ability_data = json_decode($ability['ability_data'], true);
	
	//print_r($ability_data);
	//echo $ability_data[$rel_y][$rel_x]['move_code'];
	
	// apply king upgrade
	if ($piece['piece_class_id'] == PIECE_CLASS_KING) {
		$ability_data[$y_dir][$x_dir]['move_range'] *= $piece['piece_kill_count'] + 1;
	}
	
	$piece_is_on_home_row = $piece_color == 'white' 
		? $current_y - 1 == 1
		: $max_y - $current_y == 1;
	
	// allow home row pawns to move 2 spaces
	if ($piece['piece_class_id'] == PIECE_CLASS_PAWN && $piece_is_on_home_row) {
		$ability_data[1][0]['move_range'] = 2;
	}
	
	//print_r($ability_data);
	
	$move_is_in_matrix = !is_null($ability_data[$rel_y][$rel_x]);
	
	// move is in ability matrix with a move code above 0
	if ($move_is_in_matrix && $ability_data[$rel_y][$rel_x]['move_code'] != 0) {

		return $ability_data[$rel_y][$rel_x]['move_code'];
		
	// move is outside ability matrix or is in the matrix but has a move code of 0
	} else if (
		!$move_is_in_matrix
		||
		($move_is_in_matrix && $ability_data[$rel_y][$rel_x]['move_code'] == 0)
	) {
		
		// diagonal moves
		if (abs($rel_x) == abs($rel_y)) {
		
			$x_dir = $rel_x > 0 ? 1 : -1;
			$y_dir = $rel_y > 0 ? 1 : -1;
			$magnitude = abs($rel_x);
			
		// up/down moves
		} else if ($rel_x == 0 && abs($rel_y) > 0) {
			
			$x_dir = 0;
			$y_dir = $rel_y > 0 ? 1 : -1;
			$magnitude = abs($rel_y);
			
		// left/right moves
		} else if ($rel_y == 0 && abs($rel_x) > 0) {
			
			$x_dir = $rel_x > 0 ? 1 : -1;
			$y_dir = 0;
			$magnitude = abs($rel_x);
			
		// invalid moves
		} else {
			return INVALID_MOVE;
		}
		
		if (
			$ability_data[$y_dir][$x_dir]['move_range'] >= abs($magnitude)
			||
			$ability_data[$y_dir][$x_dir]['move_range'] == -1
		) {
			return $ability_data[$y_dir][$x_dir]['move_code'];
		} else {
			return INVALID_MOVE_OUT_OF_RANGE;
		}
		
	}
	
	//throw new Exception();
	return false;
	
}

function get_piece_move_code_matrix($board, $piece) {
	
	$max_x = $board['board_col_count'];
	$max_y = $board['board_row_count'];
	
	$move_code_matrix = [];
	
	for ($y = 1; $y <= $max_y; $y++) {
		
		$move_code_matrix[$y] = [];
		
		for ($x = 1; $x <= $max_x; $x++) {
			
			$move_code_matrix[$y][$x] = get_piece_move_code($board, $piece, $x, $y);
			
		}
		
	}
	
	return $move_code_matrix;
	
}

function piece_is_threatened($this_user_id, $opp_piece, $board) {
	
	// get the opponent's piece's coordinates
	$opp_piece_space = get_space_by_id($opp_piece['piece_space_id']);
	$opp_piece_x = $opp_piece_space['space_coord_x'];
	$opp_piece_y = $opp_piece_space['space_coord_y'];
	
	// get array of this player's pieces
	$my_pieces = get_my_pieces($this_user_id);
	
	//print_r($my_pieces);
	
	foreach ($my_pieces as $piece) {
		
		//print_r($piece);
		
		$move_code = get_piece_move_code($board, $piece, $opp_piece_x, $opp_piece_y);
		
		//echo "move code: $move_code";
		
		if (piece_can_attack($move_code)) {
			return true;
		}
		
	}
	
	return false;
	
}

function get_check_status($user_id, $match) {
	
	// get the match user record of the opponent
	$opp_match_user = get_opponent_match_user($match['match_id'], $user_id);
	
	// get the piece record of the opponent's king
	$opp_king_piece = get_piece_by_user_and_class(
		$opp_match_user['match_user_user_id'], 
		PIECE_CLASS_KING
	);
	
	$board = get_board_by_id($match['match_board_id']);
	
	return piece_is_threatened($user_id, $opp_king_piece, $board);
	
}

function get_checkmate_status($user_id, $match, $threatening_piece) {
	
	// get the match user record of the opponent
	$opp_match_user = get_opponent_match_user($match['match_id'], $user_id);
	
	// get the piece record of the opponent's king
	$opp_king_piece = get_piece_by_user_and_class(
		$opp_match_user['match_user_user_id'], 
		PIECE_CLASS_KING
	);
	
	$board = get_board_by_id($match['match_board_id']);

	$king_move_code_matrix = get_piece_move_code_matrix($board, $opp_king_piece);
	
	// get array of this player's pieces
	$my_pieces = get_my_pieces($user_id);
	
	//print_r($my_pieces);
	
	$my_pieces_move_code_matrix = [];
	$matricies = [];
	$i = 0;
	
	foreach ($my_pieces as $piece) {
		
		$this_matrix = get_piece_move_code_matrix($board, $piece);
		//echo "piece_relative_id: {$piece['piece_relative_id']}<br />";
		//print_r($this_matrix);
		
		$matricies[$i] = $this_matrix;
		$i++;
		
		//$my_pieces_move_code_matrix = array_merge($my_pieces_move_code_matrix, $this_matrix);
		
	}
	
	$check_space_count = 0;
	$king_move_space_count = 0;
	
	for ($y = 1; $y <= count($matricies[0]); $y++) {
		
		for ($x = 1; $x <= count($matricies[0][1]); $x++) {
			
			for ($j = 0; $j < count($matricies); $j++) {
				
				if (piece_can_attack($matricies[$j][$y][$x])) {
					$my_pieces_move_code_matrix[$y][$x] = $matricies[$j][$y][$x];
					break;
				}
				
			}
			
			$king_can_move_here = piece_can_move($king_move_code_matrix[$y][$x]);
			
			if (piece_can_move($king_move_code_matrix[$y][$x])) {
				$king_move_space_count++;
			}
			
			if (piece_can_attack($my_pieces_move_code_matrix[$y][$x]) && $king_can_move_here) {
				$check_space_count++;
			}
			
		}
		
	}
	
	//print_r($my_pieces_move_code_matrix);
	
	//echo "king_move_space_count: $king_move_space_count";
	//echo "check_space_count: $check_space_count";
	
	return 
		$king_move_space_count == $check_space_count 
		&& 
		!piece_is_threatened($opp_match_user['match_user_user_id'], $threatening_piece, $board);
	
}

function piece_can_move($move_code) {
	return $move_code == 1 || $move_code == 3 || $move_code == 5 || $move_code == 7;
}

function piece_can_attack($move_code) {
	return $move_code == 2 || $move_code == 3 || $move_code == 6 || $move_code == 7;
}

function piece_can_defend($move_code) {
	return $move_code == 4 || $move_code == 5 || $move_code == 6 || $move_code == 7;
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