<?php

require_once('../model/sql.php');

// constants
const USER_NOT_FOUND = 0;
const WRONG_PASSWORD = 1;
const USER_VALIDATED = 2;

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
		'user_password' => password_hash($username . $password, PASSWORD_ARGON2I),
		'user_email' => $email
	));
}

function edit_user($id, $username, $password, $email) {
	$user = new sql('users');
	$user->select(array('user_id', $id));
	$user['user_name'] = $username;
	$user['user_password'] = password_hash($username . $password, PASSWORD_ARGON2I);
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
	$pw_match = password_verify($username . $password, $user['user_password']);
	
	if (!$user) {
		return USER_NOT_FOUND;
	} else if (!$pw_match) {
		return WRONG_PASSWORD;
	} else if ($pw_match) {
		
		$user['user_token'] = bin2hex(random_bytes(16));
		$user->update();
		
		return USER_VALIDATED;
		
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
function get_gravatar($email, $s = 16, $d = 'mm', $r = 'g') {
	
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$s&d=$d&r=$r";
	
    return $url;
	
}

session_start();

if (sql::is_connected()) {

	/*// check to see if the admin account has been setup
	$admin = get_user_by_name('admin');
	$admin_needs_pw = $admin['user_password'] == 'password';
	
    if ($admin_needs_pw && !$is_admin_setup_page) {
        header('Location: ../setup/?action=admin_setup');
    }
	
	unset($admin);*/

    // check to see a user is logged in
    $me = (isset($_SESSION['user'])) ? get_user_by_name($_SESSION['user']) : false;

}

?>