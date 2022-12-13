<?php

/**

	* Name: PAGINATE
	
	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode
	
	* Description: This extendable class allows you to control MYSQL data and iterate through limited result based on conditions such as [ Max Rows Per Page ] - & - [ Current Page ]
	
**/

abstract class paginate {
	
	protected $mysqli_result;
	protected $array_data;
	
	public $current_page;
	public $rows_per_page = 10;
	
	protected $max_rows;
	protected $max_pages;
	private $page_indexes = array();
	
	protected $error;
	
	public function use_mysqli_result( object $mysqli_result ) {
		$this->mysqli_result = $mysqli_result;
	}
	
	public function use_array( array $array ) {
		$this->array_data = $array;
	}
	
	protected function ready() {
		
		# -- [ check if mysqli_result is valid ] --
		if( !($this->mysqli_result instanceof mysqli_result) ) 
			$this->error = ":mysqli result to use is not an instance of mysqli_result class";
		
		# -- [ check if rows_per_page is a number ] --
		else if( !is_numeric($this->rows_per_page) ) $this->error = ":rows_per_page is not numeric";
		
		# -- [ check if current page is a number ] --
		else if( !is_numeric($this->current_page) || (int)$this->current_page < 1 ) $this->current_page = 1;
		else $this->current_page = (int)$this->current_page;
		
		# -- [ if any of the above is not valid, return false ] --
		if( $this->error ) return !$this->error;
		
		# -- [ else process the data for iteration ] --
		
		$this->max_rows = $this->mysqli_result->num_rows;
		$this->max_pages = (int)ceil( $this->max_rows / $this->rows_per_page );
		
		for( $x = 0; $x < $this->max_rows; $x += $this->rows_per_page ) $this->page_indexes[] = $x;
		
		if( $this->current_page > count($this->page_indexes) ) $this->current_page = count($this->page_indexes);
		
		return true;
		
	}
	
	public function iterate( callable $func ) {
		
		$iterated_data_results = array();
			
		if( !empty( $this->mysqli_result ) ) {
			
			if( !$this->ready() ) return false;
			
			if( !empty($this->page_indexes) ):
			
				# -- [ get the starting point of mysql result ] --
				$index_start = $this->page_indexes[ $this->current_page - 1 ];
				
				# -- [ get the ending point of mysqli result ] --
				$index_end = $this->page_indexes[ $this->current_page ] ?? $this->max_rows;
				
				# -- [ loop through the two points ] --
				
				for( $x = $index_start; $x < $index_end; $x++ ) {
					# -- [ fetch the data of the result ] --
					$result = $this->mysqli_result->data_seek( $x );
					# -- [ pass it as an argument ] --
					if( $result ) $iterated_data_results[] = $func( $this->mysqli_result->fetch_assoc() );
				};
				
				# -- [ reset the mysqli result key ] --
				$this->mysqli_result->data_seek(0);
				
			endif;
			
		} else {
			
			$this->current_page = (int)$this->current_page;
			$this->max_rows = count($this->array_data);
			$this->max_pages = (int)ceil( $this->max_rows / $this->rows_per_page );
			$start = ($this->current_page - 1) * $this->rows_per_page;
			//$end = ($this->current_page * $this->rows_per_page) - 1;
			$rows = array_slice( $this->array_data, $start, $this->rows_per_page, true );
			foreach( $rows as $array ) $iterated_data_results[] = $func( $array );
			
		}
		
		return $iterated_data_results;
			
	}
	
}