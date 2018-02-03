<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
 * SQL helper class to make moving data between the pages and db easier
 * Extends the ArrayObject class allowing the object to be treated like an array
 * 
 * Objects of this class represent rows/tuples of a given table in a database
 * 
 * Assumes the database has been setup with the following constraints:
 *	- The primary key of every table is the first column/field for said table.
 */
class sql extends ArrayObject {

	// fields
	public $table;
	public $data;
	public static $error_message;
	protected $primary_key_column;
	protected $primary_key_value;
	protected static $db;
	
	// constants
	const SELECT_SINGLE = 0;
	const SELECT_MULTIPLE = 1;
	
	/**
	 * Creates new sql helper object
	 * 
	 * @param string $table name of the table in the database
	 * @param array $data the row of the table
	 */
	public function __construct($table, $data = array()) {
		
		parent::__construct();
		
		// check to make sure the database has been connected to
		if (!sql::$db) {
			throw new Exception('You forgot to run the connect() function.');
		}
		
		$this->table = $table;
		
		// allow only 1d arrays to be stored in the object
		if (!is_array(data[0])) {
			$this->data = $data;
			$this->primary_key_column = key($data);
			$this->primary_key_value = reset($data);
		} else {
			throw Exception('Cannot handle 2d arrays.');
		}
		
	}
	
	/**
	 * allows use of index operators ([]) for data access
	 * 
	 * @param mixed $index index to be accessed
	 * @return mixed the value contained at the given index
	 */
	public function &offsetGet($index) {
		return $this->data[$index];
    }
	
	/**
	 * allows use of index operators ([]) for data mutation
	 * 
	 * @param mixed $index index for the data to be stored
	 * @param mixed $value data to be stored
	 * @throws Exception if index is not found
	 */
	public function &offsetSet($index, $value) {
		if ($this->data[$index]) {
			$this->data[$index] = $value;
		} else {
			throw new Exception("index is not found: '$index'");
		}
    }
	
	public function &append($value) {
		
    }
	
	/**
	 * connects to the database (must be called before instantiating any objects)
	 * 
	 * @global boolean $is_db_setup
	 * @param array $config: array of config parameters
	 * @throws mysqli_sql_exception 
	 * @throws Exception
	 */
    public static function connect($config) {
		
		global $is_db_setup_page;
		
		if ($config) {
			
			try {
				
				sql::$db = new mysqli(
					$config['mysql_host'],
					$config['mysql_user'],
					$config['mysql_password'],
					$config['mysql_db'],
					$config['mysql_port']
				);
				
			} catch (mysqli_sql_exception $e) {
				
				if (isset($is_db_setup_page) && $is_db_setup_page) {
					sql::$error_message = $e->getMessage();
				} else {
					header("Location: ../setup/?action=db_setup");
				}
				
				throw $e;
				
			}
			
		} else {
			throw new Exception('no config');
		}
		
    }
	
	/**
	 * returns a flag of whether or not the database has been connected to
	 * 
	 * @return boolean if the database has been connected to
	 */
	public static function is_connected() {
		return sql::$db != null;
	}
	
	/**
	 * Retrieves data from the sql table using a SELECT clause. Use the column/value parameters as 
	 * the 'WHERE' part of the SQL statement. If no parameters are passed, all rows will be 
	 * returned.
	 * 
	 * @param array $args array with the following fields: 'column', 'value', 'limit', and 'tables'
	 * @param int $type either SELECT_SINGLE for 1d array or SELECT_MULTIPLE for 2d array
	 * @throws Exception
	 * @return array an array containing all rows of a SELECT
	 */
	public function select($args = array(), $type = null) {
		
		// get all rows
		if (!isset($args['column']) && !isset($args['value']) && !isset($args['limit'])) {
			
			$sql = "SELECT * 
					FROM $this->table";
		
		// get specified rows using WHERE clause
		} else if (isset($args['column']) && isset($args['value']) && !isset($args['limit'])) {
			
			$sql = "SELECT * 
					FROM $this->table 
					WHERE $args[column] = '$args[value]'";
		
		// get all rows using a limit
		} else if (!isset($args['column']) && !isset($args['value']) && isset($args['limit'])) {
			
			$sql = "SELECT * 
					FROM $this->table 
					LIMIT $args[limit]";
		
		// get specified rows using WHERE clause and a limit	
		} else if (isset($args['column']) && isset($args['value']) && isset($args['limit'])) {
			
			$sql = "SELECT * 
					FROM $this->table 
					WHERE $args[column] = '$args[value]' 
					LIMIT $args[limit]";
		
		} else {
			throw new Exception('incorrect parameters in args array');
		}
		
		//echo "$sql<br />";
		
		// prepare the statement
		$stmt = sql::$db->prepare($sql);
		
		// execute the query and store the result
		$stmt->execute();
		$result = $stmt->get_result();
		if ($type == sql::SELECT_SINGLE || (sql::$db->num_rows == 1 && $type == null)) {
			$data = $result->fetch_array(MYSQLI_ASSOC);
		} else if ($type == sql::SELECT_MULTIPLE || $db->num_rows > 1) {
			
			$data = array();

			// push each row of the result as a new sql object to the local array
			while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
				array_push($data, new sql($this->table, $row));
			}
			
		} 
		
		//var_dump($data);
		//echo '<br /><br />';
		
		// close the prepared statement
		$stmt->close();
		
		// assign only the first row of data to the global variable
		$this->data = $data;
		//$this->data = $data;
		
		// store the primary key column name and its value
		if (!is_array($data[0])) {
			$this->primary_key_column = key($data);
			$this->primary_key_value = reset($data);
		}
		
		// return the whole array of objects for multi-row SELECTs
		return $data;
		
	}
	
	/**
	 * inserts a new row
	 * 
	 * @param string $table the table to insert into
	 * @param array $data array of column-indexed data
	 * @return object new sql object for the inserted row
	 */
	public static function insert($table, $data, $return_new = false) {
		
		if (is_array($data)) {
			foreach ($data as $i => $datum) {
				$columns .= "$i, ";
				$values .= "'$datum', ";
			}
		} else {
			$columns = key(reset($data));
			$values = reset($data);
		}
		
		// trim the last comma
		$columns = substr($columns, 0, strlen($columns) - 2);
		$values = substr($values, 0, strlen($values) - 2);
		
		$sql = "INSERT INTO $table ($columns) 
				VALUES ($values)";
		
		// prepare the statement and execute the statement
		$stmt = sql::$db->prepare($sql);
		$stmt->execute();
		$stmt->close();
		
		if ($return_new) {
		
			// retrieve the newly inserted row
			$new_id = sql::$db->insert_id;
			$inserted = new sql($table);
			$inserted->select('user_id', $new_id);

			return $inserted;
		
		}
		
	}
	
	/**
	 * updates the row based on the current value of the data
	 */
	public function update() {
		
		$fields = '';
		
		//var_dump($this);
		//echo '<br /><br />';
		
		if ($this->data == null) {
			throw new Exception('select needs to be done before update');
		}
		
		if (is_array($this->data)) {

			foreach ($this->data as $column => $value) {
				if (!is_int($column) && $column != $this->primary_key_column) {
					$fields .= "$column = '$value', ";
				}
			}

			// trim the last comma
			$fields = substr($fields, 0, strlen($fields) - 2);

		} else {
			$fields = "$column = $value";
		}
		
		$sql = "UPDATE $this->table 
				SET $fields 
				WHERE $this->primary_key_column = '$this->primary_key_value'";
		
		//echo "$sql<br />";
		
		// prepare the statement and execute the statement
		$stmt = sql::$db->prepare($sql);
		$stmt->execute();
		$stmt->close();
		
	}
	
	public function delete() {
		
	}
	
	/*private static function get_db_var_type($var) {
		
		$type = gettype($var);
		
		switch ($type) {
			
			case 'integer': case 'boolean':
				return 'i';
			
			case 'double':
				return 'd';
				
			case 'string': default:
				return 's';
			
		}
		
	}*/
	
};

// read db connection info from the config file
$config = (file_exists('../config/db_config.json'))
	? json_decode(file_get_contents('../config/db_config.json'), true)
	: null;

// try to connect to the database
try {
	sql::connect($config);
} catch (Exception $e) {
	//sql::$error_message = 'Unable to connect to the database';
	//include('../setup/db_setup_form.php');
	die('Unable to connect to the database.');
}

?>