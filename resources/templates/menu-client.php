<?php 

defined("ROOT_PATH") or die("DIRECT ENTRY RESTRICTED"); 

global $backend_menu, $helper;

$backend_menu = new menufy();

$backend_menu->title = "Dashboard";
$backend_menu->role = "member";
$backend_menu->url = $universal->src->root_url;


# --- [ check for active menu ] ----

$backend_menu->is_active = function($data) {
	$links = array( $data['link'], $_SERVER['REQUEST_URI'] );
	foreach( $links as $key => $uri ) {
		if( substr($uri, -1) == '/' ) $links[ $key ] = substr($uri, 0, -1);
		else if( substr($uri, -4) == '.php' ) $links[ $key ] = substr($uri, 0, -4);
	};
	// count length of request uri
	$reqlen = strlen($links[1]);
	// trim the user defined href
	$href = substr($links[0], -$reqlen);
	// check if the trimmed link matches the request uri
	return ( $href == $links[1] );
};


# ----------------------------------- !
# ----------- LIST MENU ------------- !
# ----------------------------------- !

# --- [ overview ] ---

$backend_menu->add("dashboard", array(
	"label" => "dashboard", 
	"link" => $helper->server_to_url( ROOT_PATH ),
	"icon" => "fas fa-dashboard"
));


# --- [ account ] ---

$backend_menu->add("account", array(
	"label" => "Account", 
	"link" => 'javascript:void(0)',
	"icon" => "fas fa-user"
));

	$backend_menu->add_submenu("account", null, array(
		"label" => "Profile",
		"link" => $helper->server_to_url( ROOT_PATH ) . "/account"
	));



