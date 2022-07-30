<?php

/**

	* Author: UCSCODE
	* Author Name: Uchenna Ajah
	* Author URI: https://ucscode.com
	* Github URI: https://github.com/ucscode
	
	* Description: Dedicated to User Synthetics, this class allows you to add custom page or plugin.
	
**/

class plugins {
	
	private static $pages = array();
	private static $plugins = array();
	private static $config;
	private static $errors = array();
	private static $lastdir;

	const plugin_file = "/default.php";
	
	# -- [ create a new page ] --
	
	public static function create_page( string $name, array $attrs ) {
		if( empty($attrs['output']) || !is_callable($attrs['output']) )
			throw new Exception("New page `{$name}` requires a callable output");
		$attrs += array( "role" => "client", "blank" => false, "bodyclass" => "pt-0", "sidebar" => true );
		if( !self::get_page($name) ) {
			$attrs['ignored'] = 0;
			$attrs['blank'] = !!$attrs['blank'];
			self::$pages[ $name ] = $attrs;
		} else self::$pages[ $name ]['ignored']++;
	}
	
	# -- [ remove a created page ] --
	
	public static function remove_page( string $name ) {
		if( self::get_page($name) ) unset(self::$pages[$name]);
		return true;
	}
	
	# -- [ get a page content ] --
	# -- [ also used to check if page exists ] --
	
	public static function get_page( string $name ) {
		return array_key_exists($name, self::$pages) ? self::$pages[$name] : false;
	}
	
	# -- [ return an array containing all the existing pages ] --
	
	public static function pages() {
		return self::$pages;
	}

	# -- [ configure the plugin once ] --
	
	public static function config($func) {

		if( self::$config ) return;
		self::$config = !self::$config;	

		$plugs = new directoryIterator( PLUG_PATH );

		foreach( $plugs as $iter ) {
			if( $iter->isDot() || $iter->isFile() ) continue;
			self::$lastdir = $iter->getPathname();
			if( !is_file( self::$lastdir . self::plugin_file ) ) continue;
			$func( $iter->getPathname() );
			self::$lastdir = null;
		};

	}

	# -- [ register a new plugin ] --
	
	public static function register( string $unique_name, array $info ) {
		$error = false;
		if( !preg_match("/^[a-z0-9_\-]+$/i", trim($unique_name)) )
			$error = !!self::$errors[] = "{$unique_name} is not a valid plugin name";
		if( array_key_exists($unique_name, self::$plugins) ) 
			$error = !!self::$errors[] = "{$unique_name} is already registered as a plugin name";
		foreach( ['title', 'plugin_file'] as $key ) {
			if( empty($info[ $key ]) ) 
				$error = !!self::$errors[] = "{$key} for {$unique_name} is not given in the information array";
		};
		if( $error ) return;
		$info['plugin_dir'] = self::$lastdir;
		self::$plugins[ $unique_name ] = array_map('trim', array_map("strip_tags", $info) );
	}

	# -- [ return an array containing all registered pluging ] --
	
	public static function overview() {
		return self::$plugins;
	}
	
	# -- [ public change state ] --
	
	public static function activate(string $plugin_name, bool $activate = true) {
		global $uss_options;
		$active_plugins = $uss_options->get("active_plugins") ?? [];
		$all_plugins = array_keys(self::overview());
		if( $activate ) {
			if( !in_array($plugin_name, $all_plugins) ) return false;
			else if( !self::is_active($plugin_name) ) {
				$active_plugins[] = $plugin_name;
				$active_plugins = array_filter($active_plugins);
				return $uss_options->set('active_plugins', array_values($active_plugins));
			};
		} else {
			$index = array_search($plugin_name, $active_plugins);
			if( $index !== false ) {
				unset($active_plugins[$index]);
				$active_plugins = array_filter($active_plugins);
				return $uss_options->set('active_plugins', array_values($active_plugins));
			};
		};
		return true;
	}
	
	# -- [ check if plugin is active ] --

	public static function is_active(string $plugin_name) {
		global $uss_options;
		$active_plugins = $uss_options->get("active_plugins") ?? [];
		$is_active = in_array($plugin_name, $active_plugins);
		return $is_active;
	}
	
}


