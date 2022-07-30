<?php 

( defined("AJAX_MODE") && !empty($_POST['plugin']) && !empty($_POST['request']) ) OR DIE;


$plugin_key = $_POST['plugin'];


# -- [ authenticate ] --

(new potty())->auth("uss-plugin-{$plugin_key}", $_POST['otp'] ?? '', true, true);


# -- [ get all available ] -- 
$avail_plugins = array_keys( plugins::overview() );


# -- [ check if plugin is recognized ] --

$recognized = in_array($plugin_key, $avail_plugins);


# -- [ set up a pre-defined title ] --

if( $recognized ) {
	$title = plugins::overview()[$plugin_key]['title'];
	$universal->ajax->message = "<h6>{$title}</h6> <div class='pluglist'>";
} else $universal->ajax->message = '<div>';


# -- [ process the request ] --

switch( $_POST['request'] ) {

	case "state":
	
			# -- [ if plugin is not active, activate it ] --

			if( empty($_POST['status']) ):
				
				# -- [ check if plugin exists ] --
				if( !$recognized ) $universal->ajax->message .= "<p>The plugin is not recognized</p>";
				
				# -- [ now! activate it ] --
				else if( plugins::is_active($plugin_key) ) 
					$universal->ajax->message .= "<p>The plugin is already activated</p>";
				
				else {
					
					$status = $universal->ajax->status = plugins::activate($plugin_key, true);
						
					if( $status )
						$universal->ajax->message .= "<p class='success'>The plugin has been activated</p>";
					
					else $universal->ajax->message .= "<p class='failed'>The plugin was not activated</p>";
				
				};
				
			# -- [ else: deactivate it ] --

			else:
				
				if( !plugins::is_active($plugin_key) )
					$universal->ajax->message .= "<p>The plugin is currently not active</p>";
				
				else {
					
					$status = $universal->ajax->status = plugins::activate($plugin_key, false);
					
					if( $status )
						$universal->ajax->message .= "<p class='success'>The plugin has been deactivated</p>";
					
					else $universal->ajax->message .= "<p class='failed'>The plugin was not deactivated</p>";

				};
				
			endif;
			
		break;
		
	
	case "trash":
	
		if( plugins::is_active($plugin_key) )
			$universal->ajax->message .= "<p>Please deactivate the plugin before deleting it</p>";
		
		else {
			
			$plugin = plugins::overview()[$plugin_key];
			$universal->ajax->status = $helper->deldir( $plugin['plugin_dir'] );
			
			if( $universal->ajax->status ) {
				$universal->ajax->message .= "<p class='success'>The plugin has been deleted</p>";
				plugins::activate($plugin_key, false);
			} else $universal->ajax->message .= "<p class='failed'>The plugin was not deleted</p>";
			
		}
	
		break;

};


if( $universal->ajax->status ) 
	$universal->ajax->message .= "<p>This page will be reloaded to implement changes</p>";

else if( empty($universal->ajax->message) )
	$universal->ajax->message = "<p>No command was executed</p>";


$universal->ajax->message .= "</div>";

