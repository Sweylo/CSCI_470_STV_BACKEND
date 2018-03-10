<?php

require_once($dir_depth . 'model/sql.php');

// piece class constants
const PIECE_CLASS_KING = 1;
const PIECE_CLASS_QUEEN = 2;
const PIECE_CLASS_ROOK = 3;
const PIECE_CLASS_BISHOP = 4;
const PIECE_CLASS_KNIGHT = 5;
const PIECE_CLASS_PAWN = 6;

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

function get_captured_pieces($match_id) {
	
	$match_users = get_match_users($match_id);
	$user_1_id = $match_users[0]['match_user_user_id'];
	$user_2_id = $match_users[1]['match_user_user_id'];
	
	$sql = "SELECT * FROM pieces
			WHERE piece_space_id = 0
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

function add_piece($space_id, $class_id, $user_id) {
	sql::insert('pieces', array(
		'piece_space_id' => $space_id,
		'piece_class_id' => $class_id,
        'piece_user_id' => $user_id
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