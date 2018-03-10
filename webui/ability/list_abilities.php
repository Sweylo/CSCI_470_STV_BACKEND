<?php require('../view/default/header.php'); ?>

<h2>List Abilities</h2>

<p><a href="./?action=ability_designer">Make a new ability</a></p>

<table>

<?php

foreach ($abilities as $ability) {
	
	echo '<tr>';
	
	echo html('td', [], $ability['ability_id']);
	
	echo '</tr>';
	
}

?>

</table>