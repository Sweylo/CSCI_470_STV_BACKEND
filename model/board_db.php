<?php

require_once($dir_depth . 'model/sql.php');

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
	$board->select([
		'column' => 'board_id', 
		'value' => $id
	]);
	return $board;
}

function get_board_init_space_array($board_id) {
    
    $board_init_spaces = get_board_init_spaces($board_id);
    $board_coords = [];

    //print_r($board);
    //print_r($board_init_spaces);

    foreach ($board_init_spaces as $space) {

        $this_coord = [
            'col' => $space['board_init_coord_x'],
            'row' => $space['board_init_coord_y'],
            'piece_class_id' => $space['board_init_class_id']
        ];

        if ($space['board_init_class_id'] > 0) {
            $this_coord['piece_color'] = $space['board_init_piece_color'];
        }

        array_push($board_coords, $this_coord);
    }
    
    return $board_coords;
    
}

function add_board($board_name, $row_count, $col_count, $home_col) {
	return sql::insert('boards', [
        'board_name' => $board_name,
        'board_row_count' => $row_count,
        'board_col_count' => $col_count,
        'board_home_col' => $home_col
    ], true);
}

function add_board_init_space($board_id, $coord_x, $coord_y, $class_id, $piece_color) {
    sql::insert('board_init_spaces', [
        'board_init_board_id' => $board_id,
        'board_init_coord_x' => $coord_x,
        'board_init_coord_y' => $coord_y,
        'board_init_class_id' => $class_id,
        'board_init_piece_color' => $piece_color
    ]);
}

function get_board_init_spaces($board_id) {
    $sql = new sql('board_init_spaces');
    return $sql->select([
        'board_init_board_id' => $board_id
    ], sql::SELECT_MULTIPLE);
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