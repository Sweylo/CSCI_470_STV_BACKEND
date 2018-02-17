<?php

require_once('../model/user_db.php');

switch ($action) {
    
    // attempts to log a user in
    case 'login':
        
        $username = filter_var($input['user_name']);
        $password = filter_var($input['user_password']);
        
        try {
            $login_status = validate_user($username, $password);
        } catch (Exception $e) {
            send_to_client(500, null, $e);
        }
        
        switch ($login_status) {
            
            case USER_NOT_FOUND: case WRONG_PASSWORD:
                send_to_client(401, ['login_error_code' => $login_status]);
                break;
                
            case USER_VALIDATED:
                $user = get_user_by_name($username);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                send_to_client(202, ['user_token' => $user['user_token']]);
                break;
                
        }
        
        die();
        
        break;
    
    case 'logout':
        session_unset();
		session_destroy();
        send_to_client(204);
        break;
    
    case 'check_login':
        
        $login_check = [
            'logged_in' => !empty($_SESSION),
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'null'
        ];
        
        echo send_to_client(200, $login_check);
        
        //die();
        
        break;
    
}