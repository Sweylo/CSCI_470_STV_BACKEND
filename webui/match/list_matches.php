<?php require('../view/default/header.php'); ?>

<h2>List matches</h2>

<table>
    
    <tr>
        <th>id</th>
        <th>name</th>
        <th>board_id</th>
        <th>white_user</th>
        <th>black_user</th>
        <th>status</th>
        <th>-</th>
		<th>-</th>
    </tr>
    
    <?php
    
    //print_r($matches);
    
    foreach ($matches as $match) {
        
		$match_users = get_match_users($match['match_id'])->data;
		
		if ($match_users[0]['match_user_color'] == 'white') {
			$white_match_user = $match_users[0];
			$black_match_user = $match_users[1];
		} else if ($match_users[0]['match_user_color'] == 'black') {
			$black_match_user = $match_users[0];
			$white_match_user = $match_users[1];
		}
		
		$white_user = $white_match_user 
						? get_user_by_id($white_match_user['match_user_user_id']) 
						: null;
		$black_user = $black_match_user 
						? get_user_by_id($black_match_user['match_user_user_id']) 
						: null;
		
		echo '<tr>';
			
		echo html('td', [], $match['match_id']);
		echo html('td', [], $match['match_name']);
		echo html('td', [], $match['match_board_id']);
		echo html('td', [], $white_user['user_name']);
		echo html('td', [], $black_user['user_name']);
		echo html('td', [], $match_status_enum[$match['match_status']]);
		
		if ($match['match_status'] == MATCH_PLAYING) {
			echo html('td', [], [
				html('a', ['href' => "./?action=watch_match&match_id={$match['match_id']}"], 
					'watch')
			]);
		} else {
			echo '<td></td>';
		}
		 			
		echo html('td', [], [
			html('a', ['href' => "./?action=delete_match&match_id={$match['match_id']}"], 'delete')
		]);
							
		echo '</tr>';
        
    }
    
    ?>
    
</table>