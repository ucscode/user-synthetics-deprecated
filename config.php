<?php 

/*

	Developed by ucscode
	email: uche23mail@gmail.com
	https://github.com/ucscode
	
*/

# - start a global session;

if( empty(session_id()) ) session_start();


# - Make a constant to identify the installation directory 

define( "INST_PATH", __DIR__ ); // install path


# - define other important directories;

define( "ROOT_PATH", INST_PATH . "/dashboard" );
define( "ADMIN_PATH", INST_PATH . "/admin" );
define( "RES_PATH", INST_PATH . "/resources" );

define( "CLASS_PATH", RES_PATH . "/classes" );
define( "TEMP_PATH", RES_PATH . "/templates" );
define( "PLUG_PATH", RES_PATH . "/plugins" );
define( "EXT_PATH", RES_PATH . "/extensions" );
define( "INCL_PATH", RES_PATH . "/includes" );
define( "MEMO_PATH", RES_PATH . "/memo" );


/*
	- declear a universal variable for storing temporary data;
	- The universal variable contains properties and methods;
	- The property and methods are stored in the universal variable for simplicity of overriding.
	
	# For we know that PHP will never allow you override a function.
	# However, be careful not to reset the instance of the universal variable else the whole site will be dangling with errors.
*/

$GLOBALS['universal'] = (new class() {
	public function __get($key) {
		return (property_exists($this, $key)) ? $this->{$key} : null;
	}
});


# - include third party libraries

require_once INCL_PATH . "/init.php";


# - Get all required classes for this project

$classlist = array(
	"project.php",
	"backend.php",
	"helper.php",
	"events.php",
	"menufy.php",
	"potty.php",
	"ucsqli.php",
	"datamemo.php",
	"meta.php",
	"tabledata.php"
);

foreach( $classlist as $classfile ) {
	/* - If class does not exist, this will throw an error */
	require_once CLASS_PATH . "/" . $classfile;
};


# -- init --

project::init();


# -- auto processor -- 

require_once INCL_PATH . "/core.php";


/*
	--- [ Help for developers ]: --
	
	The 6 main global variables used by the system are:
	
		- $universal
		- $helper
		- $ucsqli
		- $uss_options
		- $uss_user
		- $usermeta
		
	Most of what you need to do can be empowered by properties or methods stored in those variables;
	
*/