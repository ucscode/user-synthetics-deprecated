<?php 

/**

	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode
	
	* Description: Contains Initial Configurations for User Synthetics Platform.
	
**/

class project {
	
	private static $init = false;
	
	
	// process everything that the project needs - only once;
	
	public static function init() {
		
		# if processed, ignore;
		if( self::$init ) return;
		
		# - start processing 
		self::connect();
		self::universal_variables();
		self::authenticate_user();
		self::autoload();
		self::filters();
		self::others();
		
		# - end processing 
		self::$init = true;
		
	}
	
	
	// - get all components that should be autoloaded before HTML output;
	
	private static function autoload() {
		
		global $universal, $helper;
		
		// prepare the front end menu;
		require_once TEMP_PATH . "/menu-client.php";
		require_once TEMP_PATH . "/menu-admin.php";
		require_once TEMP_PATH . "/menu-grid.php";
		require_once TEMP_PATH . "/html.extended.php";
		
	}
	
	
	// - Establish a database connection ;
	
	private static function connect() {
		
		global $ucsqli, $usermeta, $uss_options;
		
		require_once TEMP_PATH . "/fatality.php";
		require_once RES_PATH . "/conn.php";
		
		try {
			
			$ucsqli = @new ucsqli( array( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME ) );
			
		} catch( exception $e ) {
			
			$respath = str_replace(["\\",$_SERVER['DOCUMENT_ROOT']], ["/",NULL], RES_PATH);
			
			$error_message = "
				<h5 class='mb-4'><u>What to do?</u></h5>
				<p class='text-danger'>This might have happened because the MYSQL server has not yet started. Otherwise:</p>
				<ul class='mb-4'>
					<li class='mb-3'>Open the connection file @ $respath/conn.php</li>
					<li class='mb-3'>Setup the database information correctly</li>
					<li class='mb-3'>Ensure that the database user have permission to create new tables</li>
					<li class='mb-3'>If the database does not exist, create it (you can use PHPMyAdmin if available)</li>
				</ul>";
				
			fatality( "DATABASE CONNECTION FAILED", $error_message );
			
		}
		
		require_once INCL_PATH . "/dbase.create.php";
		
		if( empty($error) ) {
			$usermeta = new meta( DB_PREFIX . 'usermeta' );
			$uss_options = new meta( DB_PREFIX . 'options' );
			$uss_options->_constant(1);
		};
		
	}
	
	
	/*
		Define universal properties that will be used across the entire platform
		This is significantly imitating $GLOBALS variable.
		Except that `$universal` variable is an object and it is dedicated to this platform only
	*/
	
	private static function universal_variables() {
		
		global $universal, $helper, $uss_options;
		
		$helper = new helper(); // The first instance of helper;
		
		# -- [ list elementary universal objects ] --
		
		$universal->js_var = new stdClass(); # For Javascript Only;
		
		/* 
			Use ``js_var`` only when your need to pass information to javascript.
			
			EXAMPLE: 
			
				$universal->js_var->custom_value = 5;
				
			CAN BE ACCESSED IN JAVASCRIPT AS:
			
				(uss.srv).custom_value; // 5
		*/
		
		$universal->src = new stdClass(); # Store URLs
		$universal->user = new stdClass(); # Store logged user info
		$universal->methods = new stdClass(); # Store anonymous functions
		$universal->messages = new stdClass(); # Store text messages
		$universal->site = new stdClass(); # Store site info
		$universal->plugin = new stdClass(); # Store info of plugin page
		$universal->temp = new stdClass(); # Only used in few case to store one-time use data
		$universal->html = new stdClass(); # Store HTML data that can be used for dynamic output;
		
		
		// -- [ installation url ] --
		
		$universal->src->inst_url = $universal->js_var->ins = $helper->server_to_url();
		$universal->src->admin_url = $helper->server_to_url( ADMIN_PATH );
		$universal->src->root_url = $universal->js_var->root = $helper->server_to_url( ROOT_PATH );
		
		$default_urls = array(
			"login_url" => $universal->src->root_url . "/login",
			"signup_url" => $universal->src->root_url . "/signup",
			"logout_url" => $universal->src->root_url . "/logout",
			"reset_password_url" => $universal->src->root_url . "/reset-password",
			"tos_url" => '#',
			"privacy_url" => '#'
		);
		
		foreach( $default_urls as $key => $value ) $universal->src->{$key} = $value;
		
		
		# -- [ user information ] --
		
		$universal->user->general_avatar = $helper->server_to_url( ROOT_PATH . "/assets/images/user.png" );
		
		
		# -- [ site information ] --
		
		$universal->site->logo = $uss_options->get("site_icon") ?? $universal->src->root_url . "/assets/images/logo.png";
		
		$universal->site->name = $uss_options->get("site_name") ?? "User Synthetics";
		
		$universal->site->description = $uss_options->get("site_description") ?? "User synthetics is a powerful open source user management system developed in PHP &amp; MYSQL. It provides lots of features making easy for developers to take control of the control panel without need to edit the original system code";
		
		$universal->site->headline = $uss_options->get("site_headline") ?? "The last user management system you'll ever need";
		
		$universal->site->email = $uss_options->get("site_email") ?? "no-reply@{$_SERVER['SERVER_NAME']}";
		
		$universal->site->roles = ['admin', 'member'];
		
		$universal->site->default_user_role = $uss_options->get('default_user_role') ?? 'member';
		
		$universal->site->confirm_user_reg_email = $uss_options->get("confirm_user_reg_email") ?? 1;
		
		$universal->site->country = $uss_options->get("site_country") ?? "NG";
		
		$universal->site->disable_signup = !!$uss_options->get('disable_signup');
		
		$universal->site->datetime_format = $uss_options->get('datetime_format') ?? "Y-M-jS";
		
		$universal->site->info = self::siteInfo();
		
		
		# -- [ plugin information ] --
		
		$universal->plugin->NAME = null;
		
		
		# -- [ temp information ] --
		
		$universal->temp->last_seen_minute = 4;
		
	}
	
	
	# - Restrict ajax call that seems insecure or suspicious
	
	public static function enable_ajax_mode( $directory ) {
		
		global $helper, $universal;
		
		# -- [ allow only post request ] --
		if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) exit("UNACCEPTED REQUEST TYPE");
		
		# -- [ get the action string ] --
		if( !isset($_GET['action']) || empty($_GET['action']) ) exit("NO ACTION QUERY SPECIFIED");
		
		# -- [ check if action is trying to access parent directory ] --
		$super_path = preg_match( "/^(?:\.|\/|\\\\)/", $_GET['action'] );
		
		if( $super_path ) {
			$error_msg = "
				Access to `Parent Directory` or `Document Root` is not permitted from here. <br />\nIf you are the developer, send a direct request to the action page and then include the configuration file. <br /> \nThis activity is considered unsave and suspicious.
			";
			die( trim($error_msg) );
		};
		
		
		# -- [ get the action file ] --
		$filepath = $file = str_replace("\\", "/", $directory . "/" . $_GET['action'] );
		
		# -- [ if it is not a file, trying testing it with .php suffix ] -- 
		if( !is_file($file) ) {
			$file = $filepath . '.php';
			# -- [ if it's still not a file, try testing with /index suffix ] --
			if( !is_file($file) ) $file = $filepath . "/index.php";
		};
		
		if( !defined("AJAX_MODE") ) define("AJAX_MODE", TRUE);
		
		# [ $universal->ajax becomes available ]
		$universal->ajax = new stdClass();
		$universal->ajax->script = ( is_file($file) ) ? $file : false;
		$universal->ajax->admin = (new backend())->panel() == 'admin';
		
		# [ jsonify ];
		$universal->ajax->status = null;
		$universal->ajax->message = "";
		$universal->ajax->data = [];
		$universal->ajax->error = null;
		
		if( !$universal->ajax->script ) $helper->jsonify( false, "The action file could not be found", null, true );
		
	}
	
	# --- [ ignore! used for testing ] ---
	
	private static function others() {
		global $universal, $helper, $uss_user;
	}
	
	# --- [ get current logged in user ] ---
	
	private function authenticate_user() {
		
		global $uss_user, $ucsqli, $usermeta, $universal, $helper;
		
		if( isset($_SESSION['login']) && !empty($_SESSION['login']['token']) ) {
			
			// current logged in user;
			$uss_user = $helper->_data( DB_PREFIX . 'users', $_SESSION['login']['token'], 'logintoken' );
			
		} else if( isset($_COOKIE['_ussl']) ) {
			
			$hash = $_COOKIE['_ussl'];
			$SQL = "SELECT * FROM " . DB_PREFIX . "users WHERE SHA1(SHA1(logintoken)) = '{$hash}'";
			$uss_user = $ucsqli->query( $SQL )->fetch_assoc();
			
			if( $uss_user ) {
				$_SESSION['login'] = array(
					"token" => $uss_user['logintoken'],
					"time" => time()
				);
			};
			
		};
		
		if( $uss_user ) { 
		
			# -- indicate user avatar --
			
			$universal->user->avatar = $usermeta->get("user.avatar", $uss_user['id']);
			
			if( empty($universal->user->avatar) ) $universal->user->avatar = $universal->user->general_avatar;
			
			# -- update last seen --
			
			$ucsqli->update( DB_PREFIX . 'users', ['last_seen' => time()], "id = {$uss_user['id']}" );
			
		};
		
		$universal->js_var->userAvail = !!$uss_user;
		
	}
	
	private function filters() {
		global $helper;
		$_POST = $helper->sanitize($_POST);
		$_GET = $helper->sanitize($_GET);
	}
	
	private function siteInfo() {
		global $helper, $universal, $ucsqli;
		$auth_url = 'https://github.com/ucscode';
		$siteInfo = array(
			"installation_path" => INST_PATH,
			"domain_name" => $_SERVER['SERVER_NAME'],
			"HTTPS_enabled" => $_SERVER['SERVER_PORT'] == 80 ? 'No' : 'Yes',
			"IP_address" => $_SERVER['SERVER_ADDR'],
			"site_URL" => $helper->server_to_url( INST_PATH ),
			"site_name" => $universal->site->name,
			"site_email" => $universal->site->email,
			"site_country" => $universal->site->country . ' - ' . $helper->get_countries($universal->site->country, "name"),
			"site_timezone" => $helper->get_timezone_by_country( $universal->site->country ),
			"server_software" => $_SERVER['SERVER_SOFTWARE'],
			"PHP_OS" => PHP_OS,
			"PHP_version" => PHP_VERSION,
			"MYSQL_version" => $ucsqli->raw()->server_info,
			"database_host" => DB_HOST,
			"database_user" => '?',
			"database_password" => '****',
			"database_name" => '?',
			"database_prefix" => DB_PREFIX,
			"platform_name" => "User Synthetics",
			"platform_version" => "1.0.0",
			"platform_abbr" => "USS",
			"platform_language" => "en-GB",
			"platform_coding" => "PHP & MYSQL",
			"platform_author" => "Uchenna Ajah",
			"author_profile" => "<a href='{$auth_url}' target='_blank'>{$auth_url}</a>",
			"powered_by" => "UCSCODE"
		);
		return $siteInfo;
	}
	
}