<?php 

/**
	* Name: POTTY
	
	* Version: 2.0.0
	
	* Author: UCSCODE
	
	* Author Name: Uchenna Ajah
	
	* Author URI: https://ucscode.com
	
	* Github URI: https://github.com/ucscode
	
	* Title: The most efficient honey pot system
	
	* Description: This class uses a very logical concept to secure form input, prevent spam and deny permission to unauthorized access. It requires calling of `session_start()` by a user / developer in order to work!
	
**/

class potty {
	
	
	protected $sessid;
	
	
	public function __construct() {
		//session_destroy();
		if( !empty(session_id()) ):
		
			$this->sessid = session_id();
			
			global $helper, $uss_user;
			
			# -- [ set potty ] --
			$_SESSION['potty'] = $_SESSION['potty'] ?? $this->pottify();
			
			if( strtotime("-25 minutes") > $_SESSION['potty']['time'] ):
				$_SESSION['potty'] = $this->pottify();
			endif;
			
		endif;
		
	}
	
	
	private function pottify() {
		global $helper;
		$pid = $this->sha_($helper->keygen(), 6);
		$userToken = $uss_user['logintoken'] ?? $helper->keygen();
		$potty = array( 
			'id' => $pid,
			'userToken' => $this->sha_(  $userToken . $pid ),
			'time' => time()
		);
		return $potty;
	}
	
	
	private function sha_( $value, $len = 15 ) {
		return substr( sha1($value), 10, $len );
	}
	
	
	protected function potty_value( string $name ) {
		$x = '';
		$alignment = array( ['id', $name, 'userToken'], ['userToken', $name, 'id'] );
		foreach( $alignment as $array ) {
			$x .= $_SESSION['potty'][($array[0])];
			$x .= "-{$array[1]}-";
			$x .= $_SESSION['potty'][($array[2])] . ';';
		};
		return sha1( $x );
	}
	
	
	# [create potty]
	
	public function assign( string $name ) {
		if( !$this->sessid ) return false;
		return $this->potty_value( $name );
	}
	
	
	# [authenticate potty];
	
	public function auth( string $name, ?string $value, ?bool $user_must_be_logged_in = true, ?bool $auto_message = false ) {
		global $uss_user, $helper;
		if( $user_must_be_logged_in && !$uss_user ) return false;
		$auth_result = ( $this->potty_value($name) === $value );
		if( $auto_message && !$auth_result )
			$helper->jsonify( $auth_result, "Forbidden - The request was aborted!", null, true );
		// false - not from a reliable source;
		return $auth_result;
	}
	
}