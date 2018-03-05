<?php

/**
 *	controller for login page
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$dir_depth = '../../';
$is_login_page = true;

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/input.php');
require_once($dir_depth . 'model/user_db.php');

$action = input(INPUT_GET, 'action', true);

switch ($action) {

	case 'login': default: 
		include('login.php');
		break;
	
	case 'register':
		include('register.php');
		break;
	
	case 'logout':
		
		session_unset();
		session_destroy();
        
        header('Location: ./?action=login');
		break;
	
	case 'add_user':
		
		$username = input(INPUT_POST, 'username', true);
		$email = input(INPUT_POST, 'email', false, FILTER_VALIDATE_EMAIL);
		$password = input(INPUT_POST, 'password');
		$confirm = input(INPUT_POST, 'confirm');
		$secret = input(INPUT_POST, 'secret');
		
		$webui_config = json_decode(file_get_contents($dir_depth . 'config/webui_config.json'), 
			true);
		
		// check registration secret
		if ($secret != $webui_config['registration_secret']) {
			$message = 'incorrect secret, talk to the admin';
            $is_error_message = true;
            include('register.php');
			die();
		}
		
		if ($password != $confirm) {
			$message = 'Passwords do not match.';
            $is_error_message = true;
			include('register.php');
            die();
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
            die();
		}
		
		// no break to login the user after successful registration
		
	case 'validate_user': 
        
        try {
            $username = input(INPUT_POST, 'username');
            $password = input(INPUT_POST, 'password');
            $login_error = validate_user($username, $password);
        } catch (Exception $e) {
            //echo $e;
        }
        
		switch ($login_error) {
			
			case USER_NOT_FOUND: case WRONG_PASSWORD:
				echo "invalid user or password";
				die();
				break;
			
			case USER_VALIDATED: 
				$_SESSION['user_name'] = $username;
				//echo $_SESSION['user'];
				header('Location: ../../');
                break;
			
		}
		
		break;
	
}

?>