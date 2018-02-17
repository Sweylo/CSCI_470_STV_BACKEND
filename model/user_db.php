<?php

require_once('../model/sql.php');

// login constants
const USER_NOT_FOUND = 0;
const WRONG_PASSWORD = 1;
const USER_VALIDATED = 2;

// user type constants
const USER_TYPE_GUEST = 1;
const USER_TYPE_USER = 2;
const USER_TYPE_MOD = 3;
const USER_TYPE_ADMIN = 4;

/**
 * gets all the users in the database
 * 
 * @param int $limit number of users to get
 * @return array array of sql objects
 */
function get_users($limit = null) {
	$sql = new sql('users');
	$users = $sql->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
	return $users;
}

function get_user_by_name($username) {
	$user = new sql('users');
	$user->select(array(
		'column' => 'user_name', 
		'value' => $username
	));
	return $user;
}

function get_user_by_id($id) {
	$user = new sql('users');
	$user->select(array(
		'column' => 'user_id', 
		'value' => $id
	));
	return $user;
}

function add_user($username, $password, $email) {
	sql::insert('users', array(
		'user_name' => $username, 
		//'user_password' => sha1($username . $password),
		'user_password' => password_hash($username . $password, PASSWORD_DEFAULT),
		'user_email' => $email,
        'user_token' => bin2hex(random_bytes(16))
	));
}

function edit_user($id, $username, $password, $email) {
	$user = new sql('users');
	$user->select(array('user_id', $id));
	$user['user_name'] = $username;
	$user['user_password'] = password_hash($username . $password, PASSWORD_DEFAULT);
	$user['user_email'] = $email;
	$user->update();
}

function delete_user($id) {
	
	/*global $db;
	
	$sql = 'DELETE FROM users 
			WHERE user_id = ?';
	
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
	$stmt->closeCursor();*/
    
    $user = new sql('users');
    $user->select(array('user_id', $id));
    $user->delete();
	
}

function validate_user($username, $password) {
	
	$user = get_user_by_name($username);
    
    if ($user->data === null) {
        return USER_NOT_FOUND;
    }
    
	$pw_match = password_verify($username . $password, $user['user_password']);
    
	if ($pw_match) {
		
		$user['user_token'] = bin2hex(random_bytes(16));
		$user->update();
		
		return USER_VALIDATED;
		
	} else {
		return WRONG_PASSWORD;
	}
	
}

/**
 * simplified gravatar implementation from https://en.gravatar.com/site/implement/images/php/
 * 
 * @param string $email email address of the user
 * @param int $s size of the image
 * @param string $d default image
 * @param string $r rating
 * @return string $url the url of the image
 */
function get_gravatar_url($email, $s = 16, $d = 'mm', $r = 'g') {
	
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
	
    return $url;
	
}

// set session length
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();

// get info of logged in user if connected to the database and the user is logged in
if (sql::is_connected() && isset($_SESSION['user_name'])) {
    $me = get_user_by_name($_SESSION['user_name']);
}

?>