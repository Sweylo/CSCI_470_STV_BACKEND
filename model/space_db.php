<?php

require_once('../model/sql.php');

/**
 * gets all the spaces in the database
 * 
 * @param int $limit number of spaces to get
 * @return array array of sql objects`
 */
function get_spaces($limit = null) {
	$sql = new sql('spaces');
	$spaces = $sql->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
	return $spaces;
}

function get_space_by_name($spacename) {
	$space = new sql('spaces');
	$space->select(array(
		'column' => 'space_name', 
		'value' => $spacename
	));
	return $space;
}

function get_space_by_id($id) {
	$space = new sql('spaces');
	$space->select(array(
		'column' => 'space_id', 
		'value' => $id
	));
	return $space;
}

function add_space($match_id, $coord_x, $coord_y) {
	return sql::insert('spaces', array(
		'space_match_id' => $match_id, 
		'space_coord_x' => $coord_x,
		'space_coord_y' => $coord_y
	), true);
}

function edit_space($id, $spacename, $password, $email) {
	$space = new sql('spaces');
	$space->select(array('space_id', $id));
	$space['space_name'] = $spacename;
	$space['space_password'] = password_hash($spacename . $password, PASSWORD_DEFAULT);
	$space['space_email'] = $email;
	$space->update();
}

function delete_space($id) {
	
	/*global $db;
	
	$sql = 'DELETE FROM spaces 
			WHERE space_id = ?';
	
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $space_id);
	$stmt->execute();
	$stmt->closeCursor();*/
    
    $space = new sql('spaces');
    $space->select(array('space_id', $id));
    $space->delete();
	
}

?>