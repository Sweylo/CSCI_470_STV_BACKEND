<?php

/**
 *	controller for user operations
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

require_once('../model/sql.php');
require_once('../model/input.php');
require_once('../model/user_db.php');

$page = 'user';
$referer = input(INPUT_SERVER, 'HTTP_REFERER');
$action = input(INPUT_GET, 'action', true) === null 
	? input(INPUT_POST, 'action', true) 
	: input(INPUT_GET, 'action', true);

switch ($action) {

    case 'register':
        include('register.php');
        break;
    
    case 'list_users':
		$users = get_users();
        include('list_users.php');
        break;
    
    case 'login': 
        
        $username = input(INPUT_POST, 'username');
        $password = input(INPUT_POST, 'password');
		$login_error = validate_user($username, $password);
		
		switch ($login_error) {
			
			case USER_NOT_FOUND: case WRONG_PASSWORD:
				//$get_operator = (strpos($referer, '?') != false) ? '&' : '?';
				//header("Location: $referer${get_operator}login_error=$login_error");
				echo $login_error;
				break;
			
			case USER_VALIDATED: 
				$_SESSION['user'] = $username;
				echo $_SESSION['user'];
				header("Location: $referer");
			
		}
		
		break;
		
	case 'logout':
		
		session_unset();
		session_destroy();
        
        header("Location: $referer");
		break;
	
	case 'add_user':
		
		$username = input(INPUT_POST, 'username', true);
		$email = input(INPUT_POST, 'email', false, FILTER_VALIDATE_EMAIL);
		$password = input(INPUT_POST, 'password');
		$confirm = input(INPUT_POST, 'confirm');
		
		if ($password != $confirm) {
			$message = 'Passwords do not match.';
            $is_error_message = true;
			include('register.php');
            break;
		}
		
		try {
			add_user($username, $password, $email);
		} catch(mysqli_sql_exception $e) {
			//$error_message = $e->getMessage();
            //echo $e->getMessage();
			//include('admin_setup_form.php');
            $message = "Unable to add user to database: {$e->getMessage()})";
            $is_error_message = true;
            include('register.php');
            break;
		}
		
		//header('Location: ../setup/?action=admin_setup');
        //header("Location: $referer");
        $message = 'user successfully added';
        $is_error_message = false;
        include('register.php');
		break;
	
}

?>