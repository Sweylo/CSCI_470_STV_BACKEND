<?php require('../view/default/header.php'); ?>

<h2>List Abilities</h2>

<p><a href="./?action=ability_designer">Make a new ability</a></p>

<table>
	
	<tr>
		<th>ability_id</th>
		<th>ability_data</th>
		<th>ability_class</th>
		<th>ability_level</th>
	</tr>

<?php

$piece_classes = [
	1 => 'king',
	2 => 'queen',
	3 => 'rook',
	4 => 'bishop',
	5 => 'knight',
	6 => 'pawn'
];

foreach ($abilities as $ability) {
	
	$data = json_decode($ability['ability_data'], true);
	
	echo '<tr>';
	echo html('td', [], $ability['ability_id']);
	echo '<td><table>';
	
	for ($y = 2; $y >= -2; $y--) {
		
		echo '<tr>';
		
		for ($x = -2; $x <= 2; $x++) {
			
			echo '<td style="border: solid 1px #000">';
			
			echo "<div>code: <strong>{$data[$y][$x]['move_code']}</strong></div>";
			echo "<div>range: <strong>{$data[$y][$x]['move_range']}</strong></div>";
			
			echo '</td>';
			
		}
		
		echo '</tr>';
		
	}
	
	echo '</table></td>';
	
	echo "<td>{$piece_classes[$ability['ability_class_id']]}</td>";
	echo "<td>{$ability['ability_level']}</td>";
	
	echo '</tr>';
	
}

?>

</table>