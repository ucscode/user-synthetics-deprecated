<?php

defined( "ROOT_PATH" ) OR DIE;

require_once INCL_PATH . "/universal.methods.php";
require_once INCL_PATH . "/system/index.php";

/*
	This config file does not output anything and should not.
	Else, it will generate error or unusual results when running ajax query
*/


# -- [ execute extensions ] --

$extensionIterator = new directoryIterator( EXT_PATH );

foreach( $extensionIterator as $iterable ):
	if( $iterable->isDot() || !$iterable->isDir() ) continue;
	$dirname = $iterable->getPathname();
	$pluginFile = $dirname . "/index.php";
	if( is_file($pluginFile) ) require_once $pluginFile;
endforeach;



# -- [ execute active plugins ] --;


$active_plugins = $uss_options->get("active_plugins") ?? [];
$all_plugins = plugins::overview();

foreach( $active_plugins as $plugin_key ):
	if( array_key_exists( $plugin_key, $all_plugins ) ) {
		$the_plugin = $all_plugins[ $plugin_key ];
		$plugin_filepath = $the_plugin['plugin_dir'] . "/" . $the_plugin['plugin_file'];
		if( is_file($plugin_filepath) ) require_once $plugin_filepath;
	}
endforeach;

