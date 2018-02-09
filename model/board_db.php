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

function validate_board($boardname, $password) {
	
	$board = get_board_by_name($boardname);
	
	print_r($board);
	echo '<br /><br />' . sha1($boardname . $password);
	
	if (!$board) {
		return USER_NOT_FOUND;
	} else if (sha1($boardname . $password) != $board['board_password']) {
		return WRONG_PASSWORD;
	} else if (sha1($boardname . $password) == $board['board_password']){
		return USER_VALIDATED;
	}
	
}

/**
 * simplified gravatar implementation from https://en.gravatar.com/site/implement/images/php/
 * 
 * @param string $email email address of the board
 * @param int $s size of the image
 * @param string $d default image
 * @param string $r rating
 * @return string $url the url of the image
 */
function get_gravatar($email, $s = 16, $d = 'mm', $r = 'g') {
	
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
	
    return $url;
	
}

session_start();

if (sql::is_connected()) {

	/*// check to see if the admin account has been setup
	$admin = get_board_by_name('admin');
	$admin_needs_pw = $admin['board_password'] == 'password';
	
    if ($admin_needs_pw && !$is_admin_setup_page) {
        header('Location: ../setup/?action=admin_setup');
    }
	
	unset($admin);*/

    // check to see a board is logged in
    $me = (isset($_SESSION['board'])) ? get_board_by_name($_SESSION['board']) : false;

}

?>