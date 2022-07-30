<?php 

defined( "ROOT_PATH" ) or DIE;

# -- [ indicate environment ] --

if( !defined("TERRITORY") ) define("TERRITORY", NULL);


# -- [ plugin class ] --

require_once INCL_PATH . "/class.plugins.php";

plugins::config(function($defaultFile) {
	require_once( $defaultFile . plugins::plugin_file );
});


# -- include PHPMailer --

foreach( ["Exception.php", "PHPMailer.php", "SMTP.php"] as $PHPMailer_file ) {
	require_once INCL_PATH . "/PHPMailer/src/" . $PHPMailer_file;
};


