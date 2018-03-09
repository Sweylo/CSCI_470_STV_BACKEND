<?php require('../view/default/header.php'); ?>

<h2>List matches</h2>

<table>
    
    <tr>
        <th>id</th>
        <th>name</th>
        <th>board_id</th>
        <th>white_user_id</th>
        <th>black_user_id</th>
        <th>status</th>
        <th>-</th>
		<th>-</th>
    </tr>
    
    <?php
    
    //print_r($matches);
    
    foreach ($matches as $match) {
        
		echo html('tr', [], [
			
			html('td', [], $match['match_id']),
			html('td', [], $match['match_name']),
			html('td', [], $match['board_id']),
			html('td', [], $match['match_white_user_id']),
			html('td', [], $match['match_black_user_id']),
			html('td', [], $match_status_enum[$match['match_status']]),
			
			html('td', [], [
				html('a', ['href' => "./?action=watch_match&match_id={$match['match_id']}"], 
					'watch')
			]),
						
			html('td', [], [
				html('a', ['href' => "./?action=delete_match&match_id={$match['match_id']}"], 
				'delete')
			])
						
		]);
        
    }
    
    ?>
    
</table>