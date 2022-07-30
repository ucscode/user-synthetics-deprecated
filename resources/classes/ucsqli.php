<?php

/**
	
	* Name: UCSQLI
	
	* Version: 1.2.1
	
	* Author: UCSCODE
	
	* Author Name: Uchenna Ajah
	
	* Author URI: https://ucscode.com
	
	* Github URI: https://github.com/ucscode
	
	* Description: This class makes it very easy to SELECT, UPDATE or INSERT MYSQL data into database.
	
	* Requires PHP: 5.6+
	
**/

class ucsqli {

	private $connect;
	
	protected $class = __class__;
	
	protected $last_call;
	
	protected $info;
	
	public function __construct($array) {
		
		$mysqli = @new mysqli($array[0], $array[1], $array[2], $array[3]);
		
		if( $mysqli ):
			if( $mysqli->connect_errno ) throw new Exception($mysqli->connect_error);
			else 
				$this->connect = $mysqli;
				$this->info = $array;
		endif;
		
	}
	
	public function raw() {
		return $this->connect;
	}
	
	private function purify($field_name, $array2json = false) { 
		if(is_null($field_name)) $field_name = 'NULL';
		else if(!is_numeric($field_name)) {
			if( is_array($field_name) && $array2json ) $field_name = json_encode( $field_name );
			$field_name = "'$field_name'";
		};
		return $field_name;
	}
	
	private function aggregate($rows, $seperator = ", ", $transform = false, $array2json = false) {
		$fields = '';
		$array_keys = array_keys($rows);
		$end = end($array_keys);
		foreach($rows as $key => $field_name) {
			$comma = ($key == end($array_keys)) ? NULL : $seperator;
			if($transform) $field_name = $this->purify($field_name, $array2json);
			$field_name = trim($field_name);
			$fields .= $field_name . $comma;
		};
		$fields = "$fields";
		return $fields;
	}
	
	private function is_assoc_array($array) {
		foreach($array as $key => $value) {
			if(is_numeric($key)) return false;
		};
		return true;
	}
	
	private function aggregate_assoc($array, $seperator = ", ") {
		$field_set = '';
		foreach($array as $key => $value):
			$array_keys = array_keys($array);
			$comma = ($key == end($array_keys)) ? NULL : $seperator;
			$field_set .= "$key = " . $this->purify($value) . $comma;
		endforeach;
		return $field_set;
	}
	
	public function select($table_name, $rows = '*', $where = NULL) {
		if(is_array($rows)) $fields = $this->aggregate($rows);
		else $fields = $rows;
		$sql = "SELECT $fields FROM `$table_name`";
		if($where) $sql .= " WHERE " . $where;
		$this->last_call = $sql;
		$selection = $this->connect->query($sql);
		return $selection;
	}
	
	public function insert($tablename, $array, $array_to_json = false) {
		$func = __function__;
		if(is_object($array)) $array = (array)$array;
		if(!$this->is_assoc_array($array)) return trigger_error("[method] {$this->class}::$func array type is not associate");
		$keys = $this->aggregate(array_keys($array));
		$fields = "($keys)";
		$values = $this->aggregate(array_values($array), ",", true, $array_to_json);
		$data = "($values)";
		$sql = "INSERT INTO `$tablename` $fields VALUES $data";
		$this->last_call = $sql;
		$insertion = $this->connect->query($sql);
		return $insertion;
	}
	
	public function update($tablename, $array, $where = NULL) {
		$func = __function__;
		if(is_object($array)) $array = (array)$array;
		if(!$this->is_assoc_array($array)) return trigger_error("[method] {$this->class}::$func array type is not associate");
		$field_set = $this->aggregate_assoc($array);
		$sql = "UPDATE `$tablename` SET $field_set";
		if($where) $sql .= " WHERE " . $where; 
		$this->last_call = $sql;
		$updated = $this->connect->query($sql);
		return $updated;
	}
	
	public function getError() {
		return "[errno::{$this->connect->errno}] " . $this->connect->error;
	}
	
	public function last_call() {
		return $this->last_call;
	}
	
	public function __destruct() {
		if( !$this->connect->connect_errno ) $this->connect->close();
	}
	
	public function table_exists($tablename) {
		$sql = "SHOW TABLES LIKE '$tablename'";
		$result = $this->connect->query($sql);
		return $result->num_rows ? TRUE : FALSE;
	}
	
	public function query($sql) {
		$this->last_call = $sql;
		return $this->connect->query($sql);
	}
	
	public function get_table_fields( $tablename ) {
		$sql = "DESCRIBE $tablename";
		$result = $this->connect->query($sql);
		if( !$result ) return false;
		$rows = array();
		while( $data = $result->fetch_assoc() ) $rows[] = $data['Field'];
		return $rows;
	}
	
	public function tables() {
		$tables = array();
		$result = $this->query( "SHOW TABLES" );
		if( $result->num_rows ) {
			while( $table = $result->fetch_assoc() )
				$tables[] = $table['Tables_in_'.$this->info[3]];
		};
		return $tables;
	}
	
}


