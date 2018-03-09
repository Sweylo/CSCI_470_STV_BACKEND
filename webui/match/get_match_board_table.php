<table id="board">
	
	<?php
	
	for ($x = $board['board_row_count']; $x >= 1; $x--) {
		
		echo '<tr>';
		
		for ($y = 1; $y <= $board['board_col_count']; $y++) {
			
			$is_space_black = $x % 2 == 0 && $y % 2 == 0 || $x % 2 != 0 && $y % 2 != 0;
			echo '<td class="normal ' . ($is_space_black ? 'black' : 'white') . '">';
			
			$space = get_space_by_coords($match, $x, $y);
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