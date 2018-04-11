<?php

require_once($dir_depth . 'model/sql.php');
require_once($dir_depth . 'model/match_db.php');


function get_match_move_log_by_id($id) {
	
	

}

function get_match_move_log_by_match_id($match_id) {
	
}

function log_move($match, $moving_piece, $captured_piece, $space) {
	
	//print_r($captured_piece['piece_id']);
	
	$captured_piece_id = is_null($captured_piece['piece_id']) 
		? 0
		: (int) $captured_piece['piece_id'];
	
	//echo $captured_piece_id;
	
	sql::insert('match_move_log', [
		'match_move_match_id' => $match['match_id'],
		'match_move_piece_id' => $moving_piece['piece_id'],
		'match_move_captured_piece_id' => $captured_piece_id,
		'match_move_new_space_id' => $space['space_id'],
		'match_move_timestamp' => time(),
		'match_move_turn_count' => $match['match_turn_count']
	]);
	
}

?>