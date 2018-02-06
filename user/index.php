<?php

/**
 *	controller for user operations
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$page = 'user';

require_once('../model/sql.php');
require_once('../model/input.php');
require_once('../model/user_db.php');

$action = (input(INPUT_GET, 'action', true) === null) 
	? input(INPUT_POST, 'action', true) 
	: input(INPUT_GET, 'action', true);

switch ($action) {

    case 'register':
        include('register.php');
        break;
    
    case 'list_users':
        include('list_users.php');
        break;
    
    case 'login': 
        
        $username = input(INPUT_POST, 'username');
        $password = input(INPUT_POST, 'password');
		$referer = input(INPUT_SERVER, 'HTTP_REFERER');
		$login_error = validate_user($username, $password);
		
		switch ($login_error) {
			
			case USER_NOT_FOUND: case WRONG_PASSWORD:
				$get_operator = (strpos($referer, '?') != false) ? '&' : '?';
				//header("Location: $referer${get_operator}login_error=$login_error");
				break;
			
			case USER_VALIDATED: 
				$_SESSION['user'] = $username;
				header("Location: $referer");
			
		}
		
		break;
		
	case 'logout':
		
		session_unset();
		session_destroy();
		
		$referer = input(INPUT_SERVER, 'HTTP_REFERER');
        header("Location: $referer");
		break;
	
	case 'add_user':
		
		$username = input(INPUT_POST, 'username', true);
		$username = ($username) ? $username : 'admin';
		$email = input(INPUT_POST, 'email', false, FILTER_VALIDATE_EMAIL);
		$password = input(INPUT_POST, 'password');
		$confirm = input(INPUT_POST, 'confirm');
		
		if ($password != $confirm) {
			$error_message = 'Passwords do not match.';
			//include('admin_setup_form.php');
		}
		
		try {
			add_user($username, $password, $email);
		} catch(mysqli_sql_exception $e) {
			//$error_message = $e->getMessage();
            echo $e->getMessage();
			//include('admin_setup_form.php');
		}
		
		//header('Location: ../setup/?action=admin_setup');
		break;
	
}

?>