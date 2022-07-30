<?php 

defined("ROOT_PATH") or die("DIRECT ENTRY RESTRICTED"); 

global $admin_menu, $helper;

$admin_menu = new menufy();

$admin_menu->title = "Admin";
$admin_menu->role = "admin";
$admin_menu->url = $universal->src->admin_url;

$admin_menu->is_active = $backend_menu->is_active;

# ----------------------------------- !
# ----------- LIST MENU ------------- !
# ----------------------------------- !

# ---- [ dashboard ] ----

$admin_menu->add("overview", array(
	"icon" => "fas fa-chart-pie",
	"label" => "overview",
	"link" => $helper->server_to_url( ADMIN_PATH )
));


# ---- [ users ] ----

$admin_menu->add("users", array(
	"icon" => "fas fa-users",
	"label" => "users",
	"link" => 'javascript:void(0)'
));
	
	$admin_menu->add_submenu("users", null, array(
		"label" => "List users",
		"link" => $helper->server_to_url( ADMIN_PATH . "/users_list" )
	));
	$admin_menu->add_submenu("users", null, array(
		"label" => "Add new",
		"link" => $helper->server_to_url( ADMIN_PATH . "/users_add" )
	));
	

# ---- [ settings ] ----

$admin_menu->add("settings", array(
	"icon" => "fas fa-cog",
	"label" => "settings",
	"link" => $helper->server_to_url( ADMIN_PATH . "/settings" )
));


# ---- [ Tools ] ----

$admin_menu->add("tools", array(
	"icon" => "fas fa-wrench",
	"label" => "tools"
));

	$admin_menu->add_submenu("tools", null, array(
		"label" => "Info",
		"link" => $helper->server_to_url( ADMIN_PATH . "/tools_info" )
	));
	$admin_menu->add_submenu("tools", null, array(
		"label" => "Plugins",
		"link" => $helper->server_to_url( ADMIN_PATH . "/plugins" )
	));


# ---- [ supports ] ----

/*
$admin_menu->add("supports", array(
	"icon" => "fas fa-exclamation-circle",
	"label" => "supports"
));

	$admin_menu->add_submenu("supports", null, array(
		"label" => "tickets"
	));
	$admin_menu->add_submenu("supports", null, array(
		"label" => "Announcements"
	));
	
*/