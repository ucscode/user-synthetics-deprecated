<?php

/**
	* Name: BACKEND
	
	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode
	
	* Description: This class controls the User Interface & Experience.
	
**/

require_once __DIR__ . "/abst.templates.php";

class backend extends templates {
	
	public $liteDOM;
	
	public function __construct() {
		global $uss_user;
		require TEMP_PATH . "/litedom.php";
	}
	
	public function output( callable $contents ) {
		
		global $universal, $helper, $uss_user;
		
		# ensure that user is logged in;
		
		if( !$uss_user && !$this->blank ) $this->force_login();
		else if( $this->panel(1) == ADMIN_PATH && $uss_user && $uss_user['role'] != 'admin' ) {
			$contents = function() use($universal) {
				$this->blank(true);
				$this->liteDOM->billboard(
					"<span class='text-danger'>Denied!</span>", 
					'Please exit right away', 
					"<p>You are not permitted into this section </p> 
					<p><a href='{$universal->src->root_url}' class='btn app-btn-primary'>Go to dashboard</a></p>"
				);
			};
		};
		
		try {
			
			# -- do something before buffer
			
			events::exec("output:init");
			
			ob_start();
				# buffer the html content;
				$contents( $this );
			$universal->STDOUT = ob_get_clean();
			
			# -- do something before printing 
			
			events::exec("output:before");
			
			# get header;
			require_once TEMP_PATH . "/the-header.php";
			
			# output the html content;
			echo $universal->STDOUT;
			
			# get footer;
			require_once TEMP_PATH . "/the-footer.php";
			
			# -- do something after printing
			
			events::exec("output:after");
			
		} catch( Exception $e ) {
			fatality( $e->getMessage() );
		};
		
		return $this;
		
	}
	
	public function force_login() {
		global $uss_user;
		if( headers_sent() ) throw new Exception("Login cannot be forced after header has been sent");
		else if( !$uss_user && !defined("_uss_authorizing") ) {
			define("_uss_authorizing", true);
			parent::get_content_file( "login.php", null, true );
			die;
		};
	}

}