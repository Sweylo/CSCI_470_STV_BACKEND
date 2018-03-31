<table id="board">
	
	<tr>
		<th colspan="<?php echo $board['board_col_count']; ?>">
			<p>turn count: <?php echo $match['match_turn_count'];?></p>
		</th>
	</tr>
	
	<?php
	
	for ($y = $board['board_row_count']; $y >= 1; $y--) {
		
		echo '<tr>';
		
		for ($x = 1; $x <= $board['board_col_count']; $x++) {
			
			$is_space_black = $x % 2 == 0 && $y % 2 == 0 || $x % 2 != 0 && $y % 2 != 0;
			echo '<td class="normal ' . ($is_space_black ? 'black' : 'white') . '">';
			
			$space = get_space_by_coords($match, $x, $y);
			$piece = get_piece_by_space($space['space_id']);
			
			echo html('div', [], "space_id: {$space['space_id']}");
			
			if ($piece['piece_id']) {
				
				echo html('div', [], "piece_rel_id: {$piece['piece_relative_id']}");
				
				$piece_html_code = 9811 + $piece['piece_class_id'] + 
					($piece['piece_user_id'] == $white_match_user['match_user_user_id'] ? 0 : 6);

				echo html('div', ['class' => 'piece'], "&#$piece_html_code;");
				
				echo html('div', ['class' => 'kills'], "kills: {$piece['piece_kill_count']}");
					
			}
			
			echo html('div', [], "x: $x y: $y");
			
			echo '</td>';
			
		}
		
		echo '</tr>';
		
	}
	
	?>
	
	<tr>
		<td class="normal" colspan="<?php echo $board['board_col_count']; ?>">
			<h4>captures</h4>
			<ul id="captured">
				
				<?php
				
				foreach ($captured_pieces as $piece) {
				
					$piece_html_code = 9811 + $piece['piece_class_id'] + 
						($piece['piece_user_id'] == $white_match_user['match_user_user_id'] 
							? 0 : 6);
					
					echo html('li', [], [
						html('div', [], "piece_id: {$piece['piece_id']}"),
						html('div', ['class' => 'piece'], "&#$piece_html_code;")
					]);
				
				}
					
				?>
				
			</ul>
		</td>
	</tr>
	
</table>