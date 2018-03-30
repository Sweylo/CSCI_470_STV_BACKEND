<?php

require_once($dir_depth . 'model/sql.php');

/**
 * gets all the abilities in the database
 * 
 * @param int $limit number of abilities to get
 * @return array array of sql objects`
 */
function get_abilities($limit = null) {
	$sql = new sql('abilities');
	$abilities = $sql->select(array('limit' => $limit), sql::SELECT_MULTIPLE);
	return $abilities;
}

function get_ability_by_id($id) {
	$ability = new sql('abilities');
	$ability->select(array(
		'column' => 'ability_id', 
		'value' => $id
	));
	return $ability;
}

function add_ability($data, $class_id, $level) {
	sql::insert('abilities', array(
		'ability_data' => $data,
		'ability_class_id' => $class_id,
        'ability_level' => $level
	));
}

function edit_ability($id, $abilityname, $password, $email) {
	$ability = new sql('abilities');
	$ability->select(array('ability_id', $id));
	$ability['ability_name'] = $abilityname;
	$ability['ability_password'] = password_hash($abilityname . $password, PASSWORD_DEFAULT);
	$ability['ability_email'] = $email;
	$ability->update();
}

function delete_ability($id) {
	
	/*global $db;
	
	$sql = 'DELETE FROM abilities 
			WHERE ability_id = ?';
	
	$stmt = $db->prepare($sql);
	$stmt->bind_param('i', $ability_id);
	$stmt->execute();
	$stmt->closeCursor();*/
    
    $ability = new sql('abilities');
    $ability->select(array('ability_id', $id));
    $ability->delete();
	
}

?>