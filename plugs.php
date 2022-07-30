<?php 

require_once __DIR__ . "/config.php"; // -- TERRITORY is already defined as NULL;

# -- [ get page ] --

$THE_PAGE = call_user_func(function() {
	
	global $universal, $helper, $uss_user, $usermeta, $uss_options;
	
	foreach( $_GET as $key => $value ) {
		$_GET[$key] = $helper->sanitize($value);
	};
	
	$REQ = explode("/", $_GET['request']);
	$REQ_DIR = INST_PATH . "/" . $REQ[0];
	$page = isset($REQ[1]) ? plugins::get_page($REQ[1]) : false;
	
	$universal->plugin->REQUEST_DIR = $REQ_DIR;
	
	if( !in_array($REQ_DIR, [ROOT_PATH, ADMIN_PATH]) ):

		$__end = (new backend())->get_content_file( "404.php" );
			
	else:
		
		$altfile = implode("/", array_slice($REQ,1)) . ".php";
		$altpath = (new backend())->get_content_file( $altfile, $REQ_DIR );
		
		if( is_file($altpath) ) $__end = $altpath;
		else {
			$failed_1 = !$page || ( $page['role'] == 'admin' && $REQ_DIR != ADMIN_PATH );
			$failed_2 = !$page || ( $page['role'] != 'admin' && $REQ_DIR != ROOT_PATH );
			if( $failed_1 || $failed_2 ) $__end = (new backend())->get_content_file( "404.php" );
		}
	
	endif;
	
	if( isset($__end) ) {
		if( !defined('SUB_TERRITORY') )
			define('SUB_TERRITORY', ($REQ_DIR == ADMIN_PATH) ? 'admin' : 'client');
		require_once $__end;
		die();
	};
	
	$universal->plugin->NAME = $REQ[1];
	
	unset($_GET['request']);
	unset($REQ[0]);
	unset($REQ[1]);
	
	$universal->plugin->REQUEST = array_merge( $REQ, $_GET );
	$universal->plugin->IGNORED = $page['ignored'];
	$universal->plugin->ROLE = $page['role'];
	$universal->plugin->BLANK = $page['blank'];
	$universal->plugin->SIDEBAR = $page['sidebar'];
	
	if( !defined('SUB_TERRITORY') )
		define( "SUB_TERRITORY", ($page['role'] != 'admin') ? "client" : "admin" );
	
	return $page;
	
});


# --- [ plugin content ] ---

if( $THE_PAGE ):

	# --- [ getting ready ] ---
	
	$backend = new backend();
	$backend->blank( $THE_PAGE['blank'] );
	$backend->sidebar( $THE_PAGE['sidebar'] );
	$backend->bodyclass[] = $THE_PAGE['bodyclass'];

	# --- [ output! ] ---

	$backend->output($THE_PAGE['output']);

endif;
