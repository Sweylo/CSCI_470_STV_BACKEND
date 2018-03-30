<?php require('../view/default/header.php'); ?>

<script type="text/javascript" src="../view/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="../view/js/jquery.cardDesigner.js"></script>

<h2>Card Designer</h2>

<?php echo isset($message) ? "<p>$message</p>" : ''; ?>

<form method="post" action="./?action=add_card">
	
	<div>Card name <input type="text" name="card_name"></div>
	<div>Card description <input type="text" name="card_desc"></div>
	
	<div id="type">
		<span>Card type</span>
		<select name="card_type">
			<option value="power">power</option>
			<option value="trap">trap</option>
		</select>
	</div>
	
	<div id="power" class="link">
		<span>Power card ability id</span>
		<select name="power_card_ability_id">
		<?php
		
			foreach ($abilities as $ability) {
				echo "<option value='{$ability['ability_id']}'>{$ability['ability_id']}</option>";
			}
		
		?>
		</select>
	</div>
	
	<div id="trap" class="link">
		<span>Trap card trap id</span>
		<select name="trap_card_trap_id">
		</select>
	</div>
	
	<input type="submit" value="Save card">
	
</form>