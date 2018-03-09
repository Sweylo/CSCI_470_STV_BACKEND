<?php require('../view/default/header.php'); ?>

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

<div id="board-wrapper" class="refresh"></div>

<script type="text/javascript" src="../view/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript">
	
$(document).ready(function() {

	function refresh() {

		var url = "./?action=get_match_board_table&match_id=<?php echo $match['match_id']; ?>";

		$('#board-wrapper.refresh').load(url + " #board");

	}

	autorefresh = setInterval(refresh, 2000);
	
	refresh();

});
	
</script>