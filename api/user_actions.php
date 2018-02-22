<?php

require_once('../model/user_db.php');

switch ($action) {
    
    case 'register':
        
        // forbid users that are logged in to register new users
        if ($me) {
            send_to_client(403);
        }
        
        $username = filter_var($input['user_name']);
		$email = filter_var($input['user_email'], FILTER_VALIDATE_EMAIL);
		$password = filter_var($input['user_password']);
		
		try {
			add_user($username, $password, $email);
		} catch(mysqli_sql_exception $e) {
			send_to_client(500);
		}
        
        // remove the break to log the user in after successful registration
        //send_to_client(202);
        //break;
    
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
        send_to_client(200, [
            'logged_in' => !empty($_SESSION),
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null',
            'user_name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'null'
        ]);
        break;
    
    case 'add_friend': 
        
        if (!$me) {
            send_to_client(401);
        }
        
        $friend_user_id = filter_var($input['friend_user_id'], FILTER_VALIDATE_INT);
        $friend = get_user_by_id($friend_user_id);
        
        if ($friend) {
            send_to_client(400);
        }
        
        // check if friend to add is same user
        if ($me['user_id'] == $friend_user_id) {
            send_to_client(400);
        }
        
        // check to see if the users are friends already
        $friendship = check_friendship($me['user_id'], $friend_user_id);
        if (is_array($friendship->data)) {
            send_to_client(409);
        }
        
        // try to add friend request to database
        try {
            //add_friend($me['user_id'], $friend_user_id);
        } catch (Exception $e) {
            send_to_client(500, null, $e);
        }
        
        // send success code
        send_to_client(201);
        
        break;
    
    case 'accept_friend':
        
        if (!$me) {
            send_to_client(401);
        }
        
        $friend_user_id = filter_var($input['friend_user_id'], FILTER_VALIDATE_INT);
        
        $friendship = check_friendship($me['user_id'], $friend_user_id);
        if (!$friendship->data) {
            send_to_client(400);
        }
        
        $friendship['friend_accepted'] = 1;
        $friendship->update();
        
        send_to_client(202);
        
        break;
        
    case 'list_friends':

        if (!$me) {
            send_to_client(401);
        }
        
        $friends = get_friends($me['user_id']);
        $friends_array = [];
        
        //print_r($friends);
        
        /*if (!is_array($friends[0]->data)) {
            $friends = [0 => $friends];
        }*/
        
        /*print_r($friends);
        
        print_r($friends[0]->data);*/
        
        foreach ($friends as $friend) {
            $friend_user_id = $friend['friend_user_1_id'] != $me['user_id']
                ? $friend['friend_user_1_id'] : $friend['friend_user_2_id'];
            $friend_user = get_user_by_id($friend_user_id);
            array_push($friends_array, [
                'friend_user_id' => $friend_user['user_id'],
                'friend_user_name' => $friend_user['user_name']
            ]);
        }
        
        //print_r($friends_array);
        
        send_to_client(200, ['friends_list' => $friends_array]);
        
        break;
    
    case 'get_friend_requests':
        
        if (!$me) {
            send_to_client(401);
        }
        
        $friend_requests = get_friend_requests($me['user_id']);
        
        if (is_array($friend_requests)) {
            
            $friend_requests_array = [];
            
            if (is_array($friend_requests[0])) {
            
                foreach ($friend_requests as $request) {
                    $this_user = get_user_by_id($request['friend_user_1_id']);
                    array_push($friend_requests_array, [
                        'request_user_id' => $this_user['user_id'],
                        'request_user_name' => $this_user['user_name']
                    ]);
                }

            } else {
                $this_user = get_user_by_id($friend_requests['friend_user_1_id']);
                array_push($friend_requests_array, [
                    'request_user_id' => $this_user['user_id'],
                    'request_user_name' => $this_user['user_name']
                ]);
            }
            
            send_to_client(200, ['friend_requests' => $friend_requests_array]);
            
        } else {
            send_to_client(204);
        }
        
        break;
    
    case 'get_token': 
        if ($me) {
            send_to_client(200, ['user_token' => $me['user_token']]);
        } else {
            send_to_client(401);
        }
        break;
        
    case 'get_toke':
        send_to_client(420);
        break;
    
}