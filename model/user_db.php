<?php

require_once($dir_depth . 'model/sql.php');

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
	echo $username;
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

function add_friend($user_1_id, $user_2_id) {
    sql::insert('friends', [
        'friend_user_1_id' => $user_1_id,
        'friend_user_2_id' => $user_2_id
    ]);
}

function get_friends($user_id) {
    
    /*$sql = 'SELECT * FROM friends 
			WHERE (friend_user_1_id = ? OR friend_user_2_id = ?) 
                AND friend_accepted = 1';
    
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('ii', $user_id, $user_id);
	$stmt->execute();
    //$friends = $stmt->fetch_all(MYSQL_ASSOC);
    
    do {
        if ($res = sql::$db->store_result()) {
            var_dump($res->fetch_all(MYSQLI_ASSOC));
            $res->free();
        }
    } while (sql::$db->more_results() && sql::$db->next_result());
    //$friends = $result->fetch_array(MYSQLI_ASSOC);*/
    
    $sql = new sql('friends');
    $friends = $sql->select([
        'column' => "(friend_user_1_id = $user_id OR friend_user_2_id = $user_id) AND friend_accepted",
        'value' => 1    
    ], sql::SELECT_MULTIPLE);
    
    return $friends;
    
}

function check_friendship($user_1_id, $user_2_id) {
    
    $sql = 'SELECT * FROM friends 
			WHERE (friend_user_1_id = ? AND friend_user_2_id = ?)
                OR (friend_user_1_id = ? AND friend_user_2_id = ?)';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('iiii', $user_1_id, $user_2_id, $user_2_id, $user_1_id);
	$stmt->execute();
    $result = $stmt->get_result();
    $friendship = new sql('friends', $result->fetch_array(MYSQLI_ASSOC));
    
    return $friendship;
    
}

function accept_friend($user_from_id, $user_to_id) {
    
}

function get_friend_requests($user_id) {
    
    $sql = 'SELECT * FROM friends 
			WHERE friend_user_2_id = ? 
                AND friend_accepted = \'0\'';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('i', $user_id);
	$stmt->execute();
    $result = $stmt->get_result();
    $friendship = $result->fetch_array(MYSQLI_BOTH);
    
    return $friendship;
    
}

function remove_friend($user_you_id, $user_them_id) {
    
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
    $is_mod = $me['user_account_type_id'] == USER_TYPE_MOD;
    $is_admin = $me['user_account_type_id'] == USER_TYPE_ADMIN;
} else {
	//header('Location: ' . $dir_depth . 'webui/login/');
}

?>