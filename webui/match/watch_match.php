<?php require('../view/default/header.php'); ?>

<script type="text/javascript">
	
</script>

<h2>Watch match</h2>

<?php

echo html('h3', [], 
	'name: ' . ($match['match_name'] 
		? $match['match_status'] 
		: "Match #{$match['match_id']}")
);
		
foreach ($match_users as $match_user) {
	$this_user = get_user_by_id($match_user['match_user_user_id']);
	echo html('p', [],
		"{$match_user['match_user_color']}: {$this_user['user_name']} (#{$this_user['user_id']})"
	);
}

?>

<table id="board">
	
	<?php
	
	for ($x = $board['board_row_count']; $x >= 1; $x--) {
		
		echo '<tr>';
		
		for ($y = 1; $y <= $board['board_col_count']; $y++) {
			
			$is_space_black = $x % 2 == 0 && $y % 2 == 0 || $x % 2 != 0 && $y % 2 != 0;
			echo '<td class="normal ' 
				. ($x % 2 == 0 && $y % 2 == 0 || $x % 2 != 0 && $y % 2 != 0 
					? 'black' 
					: 'white'
				  ) 
				. '">';
			
			$space = get_space_by_coords($match, $y, $x);
			$piece = get_piece_by_space($space['space_id']);
			
			echo html('div', [], "space_id: {$space['space_id']}");
			
			if ($piece->data) {
				
				$piece_html_code = 9811 + $piece['piece_class_id'] + 
					($piece['piece_user_id'] == $white_match_user['match_user_user_id'] ? 0 : 6);

				echo html('div', ['class' => 'piece'], "&#$piece_html_code;");
					
			}
			
			echo '</td>';
			
		}
		
		echo '</tr>';
		
	}
	
	?>
	
</table>