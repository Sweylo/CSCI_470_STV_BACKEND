<?php require('../view/default/header.php'); ?>

<h2>List Cards</h2>

<p><a href="./?action=card_designer">Make a new card</a></p>

<table>

	<tr>
		<th>card_id</th>
		<th>card name</th>
		<th>card description</th>
		<th>card type</th>
	</tr>
	
<?php

foreach ($cards as $card) {
	
	$this_card = get_card_by_id($card['card_id']);
	
	echo '<tr>';
	
	echo html('td', [], $this_card['card_id']);
	echo html('td', [], $this_card['card_name']);
	echo html('td', [], $this_card['card_description']);
	
	/*if ($this_card['card_type'] == 'power') {
		echo html('td', [], "power (#{$this_card['power_card_ability_id']})");
	} else if ($this_card['card_type'] == 'trap') {
		echo html('td', [], "trap (#{$this_card['trap_card_trap_id']})");
	}*/
	
	echo html('td', [], "{$this_card['card_type']} (#{$this_card['attr_id']})");
	
	echo '</tr>';
	
}

?>

</table>