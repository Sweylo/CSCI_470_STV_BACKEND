<?php

/**
 *	controller for user operations
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 1);
date_default_timezone_set('UTC');

$dir_depth = '../../';

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/input.php');
require_once($dir_depth . 'model/user_db.php');

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
        
        try {
            $username = input(INPUT_POST, 'username');
            $password = input(INPUT_POST, 'password');
            $login_error = validate_user($username, $password);
        } catch (Exception $e) {
            //echo $e;
        }
        
		switch ($login_error) {
			
			case USER_NOT_FOUND: 
				echo "user not found: '$username'";
				break;
				
			case WRONG_PASSWORD:
				echo 'wrong password';
				break;
			
			case USER_VALIDATED: 
				$_SESSION['user_name'] = $username;
				//echo $_SESSION['user'];
				header("Location: $referer");
                break;
			
		}
		
		//echo "error logging in '$username'";
        echo "<br /><a href='$referer'>back</a>";
		
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