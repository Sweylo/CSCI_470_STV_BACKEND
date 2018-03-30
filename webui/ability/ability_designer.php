<?php require('../view/default/header.php'); ?>

<h2>Ability Designer</h2>

<?php echo isset($message) ? "<p>$message</p>" : ''; ?>

<form method="post" action="./?action=add_ability">
	
	<table>
		
		<?php
		
		for ($x = 2; $x >= -2; $x--) {
			
			echo '<tr>';
			
			for ($y = -2; $y <= 2; $y++) {
				
				echo '<td style="border: solid 1px #000; padding: 10px">';
				
				if ($x != 0 || $y != 0) {
					
					echo "defend: <input name='$x,$y-defend' type='checkbox'><br />";
					echo "attack: <input name='$x,$y-attack' type='checkbox'><br />";
					echo "move: <input name='$x,$y-move' type='checkbox'><br />";
					
					if (abs($x) > 1 || abs($y) > 1) {
						
					} else {
						echo "range: <input name='$x,$y-range' type='number' value='1'>";
					}
					
				} else { ?>
				<div>piece:</div>
				<select name="piece_class_id">
					<option value="6">pawn</option>
					<option value="5">knight</option>
					<option value="4">bishop</option>
					<option value="3">rook</option>
					<option value="2">queen</option>
					<option value="1">king</option>
				</select>
				<?php }
				
				echo '</td>';
				
			}
			
			echo '</tr>';
			
		}
		
		?>
		
	</table>
	
	level: <input type="number" name="level" value="1"><br />
	
	<input type="submit" value="Save Ability">
	
</form>