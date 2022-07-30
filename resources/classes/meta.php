<?php

/**
	* Name: META
	
	* Version: 1.0.7
	
	* Author: UCSCODE
	
	* Author Name: Uchenna Ajah
	
	* Author URI: https://ucscode.com
	
	* Github URI: https://github.com/ucscode
	
	* Title: A medium to collect extra information
	
	* Description: This class builds a `key => value` database structure to store additional of users, system or anything alike.
	
	* Dependency: UCSQLI
	
**/

class meta {
	
	protected $tablename;
	protected $const_meta_id;
	
	public function __construct( $tablename ) {
		global $ucsqli;
		$this->tablename = $tablename;
		$table_exists = $ucsqli->table_exists($tablename);
		if( !$table_exists ) $this->create_meta_table();
	}
	
	protected function create_meta_table() {
		global $ucsqli;
		$SQL = "
			CREATE TABLE IF NOT EXISTS {$this->tablename} (
				id int not null primary key auto_increment,
				meta_id int not null,
				meta_key varchar(500) not null,
				meta_value text,
				period varchar(20) not null
			)
		";
		$create = $ucsqli->query( $SQL );
	}
	
	public function _constant($meta_id) {
		$meta_id = $this->linear_value($meta_id);
		if( !is_null($this->const_meta_id) ) {
			throw new exception( "({$this->tablename}) " . __class__ . "::" . __function__ . "({$this->const_meta_id}) already defined" );
		} else if( !is_numeric($meta_id) ) {
			throw new exception( "({$this->tablename}) " . __class__ . "::" . __function__ . "() requires number as argument" );
		};
		$this->const_meta_id = (int)$meta_id;
	}
	
	protected function set_meta_id($meta_id, $func_name) {
		if( !is_null($this->const_meta_id) ) return $this->const_meta_id;
		else {
			if( is_null($meta_id) )
				throw new Exception( "Meta ID is missing in " . __class__ . "::$func_name()" );
			else if( !is_numeric($meta_id) )
				throw new Exception( "Meta ID is not a number in " . __class__ . "::$func_name()" );
			else return (int)$meta_id;
		}
	}
	
	private function sanitize( $value ) {
		$value = trim(htmlspecialchars($value));
		return $value;
	}
	
	public function set( $key, $value, ?int $meta_id = null ) {
		
		global $ucsqli;

		$meta_id = $this->set_meta_id( $meta_id, __function__ );
		
		$meta_id = $this->sanitize($meta_id);
		$key = $this->sanitize($key);
		$value = ( is_array($value) || is_object($value) ) ? json_encode($value) : $this->sanitize($value);
		
		$meta_exists = $ucsqli->select( $this->tablename, "*", "meta_id = '$meta_id' AND meta_key = '$key'" );
		
		if( !$meta_exists->num_rows ) {
			$meta_data = array(
				"id" => 0,
				"meta_id" => $meta_id,
				"meta_key" => $key,
				"meta_value" => $value,
				"period" => time()
			);
			$result = $ucsqli->insert( $this->tablename, $meta_data );
		} else {
			$result = $ucsqli->update( $this->tablename, array( "meta_value" => $value ), "meta_key = '$key' AND meta_id = $meta_id" );
		}
		
		return $result;
		
	}
	
	public function dispose( ?int $meta_id = null ) {
		global $ucsqli;
		$meta_id = $this->set_meta_id( $meta_id, __function__ );
		$meta_id = trim($meta_id);
		$datas = $ucsqli->select( $this->tablename, '*', "meta_id = $meta_id" );
		$result = array();
		while( $data = $datas->fetch_assoc() ):
			$key = $data['meta_key'];
			$value = $data['meta_value'];
			$result[$key] = $this->linear_value($value);
		endwhile;
		return $result;
	}
	
	public function get( $key, ?int $meta_id = null, $all = false ) {
		
		global $ucsqli;
		
		$meta_id = $this->set_meta_id( $meta_id, __function__ );
		
		$meta_id = trim($meta_id);
		$key = trim($key);
		
		$datas = $ucsqli->select( $this->tablename, "*", "meta_id = $meta_id AND meta_key = '$key'" );
		
		if( $datas->num_rows ) {
			$data = $datas->fetch_assoc();
			return $all ? $data : $this->linear_value($data['meta_value']);
		};
		
		return;
		
	}
	
	protected function linear_value($value) {
		if( getType($value) == 'string' ) {
			if( is_numeric($value) ) {
				$int = "/^\d+$/";
				$float = "/^\d+\.\d+$/";
				if( preg_match( $int, trim($value) ) ) $value = (int)$value;
				else if( preg_match( $float, ($value) ) ) $value = (float)$value;
			} else {
				$prevalue = @json_decode($value, true);
				if( !json_last_error() ) $value = $prevalue;
			}
		}
		return $value;
	}
	
	public function remove( $key, ?int $meta_id = null) {
		global $ucsqli;
		$meta_id = $this->set_meta_id( $meta_id, __function__ );
		$DEL_SQL = "DELETE FROM {$this->tablename} WHERE meta_id = $meta_id AND meta_key = '$key'";
		return $ucsqli->query( $DEL_SQL );
	}
	
}















