<?php

require_once('../model/sql.php');

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

?>