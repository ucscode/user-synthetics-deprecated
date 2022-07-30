<?php

/**
	* Name: DATAMEMO ~ Date Memory
	
	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode
	
	* Title: MANAGE JSON FILES AND DATA!
	
	* Description: [GET] data as array, [SET] data as array, [SAVE] data as string.
	
	* Requires PHP: 5.6

	[ ----------------------------- ]
	
	version 2.0.0
	----------------------
	changed name from data_storage to storage
	referenced the __get() as &__get()
	added __debuginfo() for debug visuality
	added new exception handler
	temporarily stored data in $memo property
	added __destruct() to finalized storage action
	converted delete_file() method to remove()
	added clear() method to erase storage memory
	converted json_pretty_print() method to pretty_print();
	removed readonly() and writable() method;

	v 2.0.1
	-------------------------
	added data() method to access $memo property [ in case of foreach ]
	
	v 2.0.2
	-------------------------
	removed auto-saver from __destruct() function
	added save() function to store $memo data to file
	
	v 2.0.3
	------------------------
	updated the __construct() function
	fixed clearing of data when json syntax is not valid - throws exception instead
	removed isFileType() function
	removed debug() function - used exception instead
	
	v 2.0.4
	------------------------
	Added value_exist() method to check if $memo value exists
	Added push() method to add new value - auto increment index
	
	v 2.0.5
	------------------------
	Updated save() method to check if json is valid before saving
	Added lastError() method to get the last occuring error while  trying to save json data
	converted private properties to protected 
		* ( now the class can be extended by another class )
		* e.g class datamemo extends storage {}
		
	v 2.0.6
	------------------------
	Fixed error in push() method when generating new key
	renamed from class storage to datamemo
	
**/

class datamemo {

	protected $filename;
	
	protected $filepath;
	
	protected $readonly = false;
	
	protected $memo;
	
	protected $__NULL = null;
	
	protected $pretty;
	
	protected $remove;
	
	protected $last_error;
	
	//-----------------------------------------------------------------
	
	public function __construct( $filename, $filepath = __DIR__ . "/datamemo" ) {
			
		if( empty($filename) ) throw new Exception( "Missing file name in argument 1"  );
		
		$this->filename = $this->stripe($filename);
		$this->filepath = $this->stripe($filepath);
		
		$absolutePath = preg_match( "~^" . $this->stripe($_SERVER['DOCUMENT_ROOT']) . "~", $this->filepath );
		if( !$absolutePath ) throw new Exception( "Invalid absolute file path in argument 2" );
		
		$file = $this->filepath . "/" . $this->filename;
		
		if( !is_file($file) ) $this->memo = array();
		else {
			$contents = trim(file_get_contents($file));
			if( empty($contents) ) $this->memo = array();
			else {
				$contents = json_decode( $contents, true );
				$reference = __class__ . "::" . __function__ . "('{$this->filename}', ?); ";
				if( json_last_error() ) {
					throw new Exception( $reference . " Json " . strtolower(json_last_error_msg()) );
				} else {
					if( !is_array($contents) ) throw new Exception( $reference . "Json data is not properly formatted" );
					else $this->memo = $contents;
				}
			};
		};
		
	}
	
	//-----------------------------------------------------------------

	protected function stripe( $path ) {
		$path = str_replace( "\\", "/", $path );
		if( substr($path, -1) == '/' ) $path = substr($path, 0, -1);
		return $path;
	}
	
	
	
	/* ------------------ [ magic functions ] ------------------ */
	
		public function __debuginfo() {
			return (array)$this->memo;
		}
		
	// --------------------------------------------------------------------
	
		public function &__get($key) {
			if( !array_key_exists( $key, $this->memo ) ) {
				throw new exception( "Undefined index $key" );
			};
			return $this->memo[$key];
		}
		
	// --------------------------------------------------------------------
	
		public function __set($key, $value) {
			$this->memo[$key] =& $value;
		}
		
	// --------------------------------------------------------------------

		
		public function __isset($key) {
			return array_key_exists( $key, $this->memo );
		}
		
	// -------------------------------------------------------------------
		
		public function __unset($key) {
			if( array_key_exists( $key, $this->memo ) ) unset( $this->memo[$key] );
		}
	
	/* ------------- /end magic functions ------------ */
	
	
	
	/* ----- [ preset functions ] ------- */
	
	public function remove( $default = true ) {
		$this->remove = $default;
	}
	
	public function pretty_print( $default = true ) {
		$this->pretty = ( $default ) ? JSON_PRETTY_PRINT : NULL;
	}
	
	public function clear() {
		$this->memo = new ArrayObject();
	}
	
	/* ------- preset function ends -------- */
	
	
	
	/* ------ [ executive functions ] -------- */
	
	protected function getFile() {
		if( !is_dir( $this->filepath ) ) mkdir( $this->filepath );
		$file = $this->filepath . "/" . $this->filename;
		return $file;
	}
	
	public function save() {
		
		$data = json_encode( $this->memo, $this->pretty );
		
		// if encoding fails;
		
		if( !$data ) {
			
			// save error and return false;
			$this->last_error = json_last_error_msg();
			return false;
			
		} else {
			
			// try to re-decode it;
			
			$valid = is_array( json_decode($data, true) );
			
			/*
				Why is it being re-decoded ?
				
				For an unknown reason, an error was returned when an array data was decoded into json with an additional closing brace;
				
				like `{data: 1, data: 2}}`
				
				Re-decoding is used to test and ensure that valid json file is about to be saved!
			*/
			
			if( json_last_error() === JSON_ERROR_NONE && $valid ) {
				// save to json file
				return !!file_put_contents( $this->getFile(), $data );
			} else {
				// save error and return false;
				$this->last_error = json_last_error_msg();
				return false;
			};
			
		};
		
	}
	
	public function __destruct() {
		$file = $this->getFile();
		if( $this->remove && file_exists( $file ) ) unlink( $file );
	}
	
	public function data() {
		return $this->memo;
	}
	
	public function value_exists( $value ) {
		return in_array( $value, $this->memo );
	}
	
	public function push( $value ) {
		$index = empty($this->memo) ? 0 : max(array_keys($this->memo));
		if( !is_numeric($index) ) $index = count($this->memo);
		else $index = (int)$index + 1;
		$this->memo[ $index ] = $value;
	}
	
	public function lastError() {
		return $this->last_error;
	}
	
}

