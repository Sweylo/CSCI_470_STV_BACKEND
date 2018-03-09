<?php

require_once($dir_depth . 'model/sql.php');

// space type constants
const SPACE_TYPE_NORMAL = 1;
const SPACE_TYPE_VOID = 2;
const SPACE_TYPE_WATER = 3;
const SPACE_TYPE_MOUNTAIN = 4;

// space error constants
const SPACE_ERROR_OBSTACLE = 1;
const SPACE_ERROR_NO_PIECE_TO_MOVE = 2;
const SPACE_ERROR_INVALID_MOVE = 3;

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

function get_space_by_coords($match, $coord_x, $coord_y) {
    
    $sql = 'SELECT * FROM spaces 
            WHERE space_match_id = ? 
                AND space_coord_x = ? 
                AND space_coord_y = ?';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('iii', $match['match_id'], $coord_x, $coord_y);
	$stmt->execute();
	$result = $stmt->get_result();
    $space = $result->fetch_array(MYSQLI_ASSOC);
	
    return $space;
    
}

function get_spaces_by_match($match_id) {
    $spaces = new sql('spaces');
    $spaces->select([
        'column' => 'space_match_id',
        'value' => $match_id
    ], sql::SELECT_MULTIPLE);
    return $spaces;
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