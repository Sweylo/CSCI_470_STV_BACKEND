<?php

require_once('../model/sql.php');

// constants

/**
 * gets all the boards in the database
 * 
 * @param int $limit number of boards to get
 * @return array array of sql objects
 */
function get_boards($limit = null) {
	$sql = new sql('boards');
	$boards = $sql->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
	return $boards;
}

function get_board_by_name($board_name) {
	$board = new sql('boards');
	$board->select(array(
		'column' => 'board_name', 
		'value' => $board_name
	));
	return $board;
}

function get_board_by_id($id) {
	$board = new sql('boards');
	$board->select(array(
		'column' => 'board_id', 
		'value' => $id
	));
	return $board;
}

function add_board($board_name, $board_data) {
	sql::insert('boards', array(
		'board_name' => $board_name, 
		'board_data' => $board_data
	));
}

function edit_board($id, $boardname, $password, $email) {
	$board = new sql('boards');
	$board->select(array('board_id', $id));
	$board['board_name'] = $boardname;
	$board['board_password'] = sha1($boardname . $password);
	$board['board_email'] = $email;
	$board->update();
}

function delete_board($id) {
	
	/*global $db;
	
	$sql = 'DELETE FROM boards 
			WHERE board_id = ?';
	
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $board_id);
	$stmt->execute();
	$stmt->closeCursor();*/
    
    $board = new sql('boards');
    $board->select(array('board_id', $id));
    $board->delete();
	
}

?>