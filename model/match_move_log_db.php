<?php

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/match_db.php');


function get_match_move_log_by_id($id) {
	
	

}

function get_last_move_log_by_match_id($match_id) {
	
	$sql = 'SELECT * FROM match_move_log
			WHERE match_move_match_id = ? 
			ORDER BY match_move_timestamp DESC
			LIMIT 1';
	
	$stmt = sql::$db->prepare($sql);
	$stmt->bind_param('i', $match_id);
	$stmt->execute();
	$result = $stmt->get_result();
    $log = $result->fetch_array(MYSQLI_ASSOC);
	
	return $log;
	
}

function log_move($match, $moving_piece, $captured_piece, $space) {
	
	//print_r($captured_piece['piece_id']);
	
	$captured_piece_id = is_null($captured_piece['piece_id']) 
		? 0
		: $captured_piece['piece_relative_id'];
	
	//echo $captured_piece_id;
	
	sql::insert('match_move_log', [
		'match_move_match_id' => $match['match_id'],
		'match_move_relative_piece_id' => $moving_piece['piece_relative_id'],
		'match_move_captured_relative_piece_id' => $captured_piece_id,
		//'match_move_new_space_id' => $space['space_id'],
		'match_move_coord_x' => $space['space_coord_x'],
		'match_move_coord_y' => $space['space_coord_y'],
		'match_move_timestamp' => time(),
		'match_move_turn_count' => $match['match_turn_count']
	]);
	
}

?>