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
        
        $login_check = [
            'logged_in' => !empty($_SESSION),
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'null'
        ];
        
        //echo empty($_SESSION) ? '{"logged_in": false}' : json_encode($_SESSION);
        echo json_encode($login_check);
        header('HTTP/1.1 200');
        break;
    
}