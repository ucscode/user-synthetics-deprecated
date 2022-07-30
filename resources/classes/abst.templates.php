<?php 

/**
	* Name: TEMPLATES

	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode
	
	* Description: This extendable class provides basic method needed to build an class for user interface. You can use it to build a totally new interface for the User Synthetics Project.
	
**/

abstract class templates {

	protected $title;
	protected $blank = false;
	protected $sidebar = true;
	public array $bodyclass = array("app");
	
	public function title( ?string $title ) {
		$this->title = ucwords($title);
		return $this;
	}
	
	public function blank( ?bool $blank = null ) {
		if( is_null($blank) ) return $this->blank;
		else $this->blank = !!$blank;
		return $this;
	}
	
	public function sidebar( ?bool $sidebar = null ) {
		if( is_null($sidebar) ) return $this->sidebar;
		else $this->sidebar = !!$sidebar;
		return $this;
	}
	
	public function panel( ?bool $path = false ) {
		
		if( is_null(TERRITORY) ) {
			if( defined("SUB_TERRITORY") && is_string(SUB_TERRITORY) && !empty(SUB_TERRITORY) ) {
				$TERRITORY = SUB_TERRITORY;
			} else $TERRITORY = NULL;
		} else $TERRITORY = TERRITORY;
		
		if( !$path ) return $TERRITORY;
		else {
			switch( $TERRITORY ) {
				case 'admin':
					return ADMIN_PATH;
				case 'client':
					return ROOT_PATH;
				default:
					return INST_PATH;
			}
		};
		
	}
	
	public function get_template( $_filename ) {
		global $helper, $ucsqli, $universal, $uss_user, $uss_options;
		$the_file = TEMP_PATH . "/{$_filename}";
		$is_file = is_file($the_file);
		if( $is_file ) require_once $the_file;
		return $is_file;
	}
	
	public function get_content_file( string $filename, ?string $PATH = null, bool $require = false ) {
		if( is_null($PATH) ) $PATH = ROOT_PATH;
		if( !is_file($file = $PATH . "/" . $filename) ) {
			if( !is_file($file = $PATH . '/contents/' . $filename) ) $file = '';
		}
		if( $require && !empty($file) ) require_once $file;
		return $file;
	}
	
}