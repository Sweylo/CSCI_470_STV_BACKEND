<?php

require_once('../model/user_db.php');

switch ($input['action']) {
    
    // attempts to log a user in
    case 'login':
        
        try {
            $login_status = validate_user($input['user_name'], $input['user_password']);
        } catch (Exception $e) {
            echo $e;
            header('HTTP/1.1 500');
        }
        
        switch ($login_status) {
            
            case USER_NOT_FOUND: case WRONG_PASSWORD:
                echo json_encode(['login_error_code' => $login_status]);
                header('HTTP/1.1 401');
                break;
                
            case USER_VALIDATED:
                $user = get_user_by_id($input['user_id']);
                echo json_encode(['user_token' => $user['user_token']]);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                header('HTTP/1.1 200');
                break;
                
        }
        
        break;
    
    case 'logout':
        session_unset();
		session_destroy();
        header('HTTP/1.1 200');
        break;
    
    case 'check_login':
        print_r($_SESSION);
        header('HTTP/1.1 200');
        break;
    
}