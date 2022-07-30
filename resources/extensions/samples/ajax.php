<?php 

require "simple.config.php";

(isset($_POST['area']) && isset($_POST['otp'])) or $helper->jsonify(false, "Hmm... Suspecious", null, 1);

(new potty())->auth($_POST['area'], $_POST['otp'], true, true);


foreach( $_POST as $key => $value ) {
	if( $key == 'page_content' ) continue;
	preg_match("/[a-z0-9_\-\s.]+/i", $value, $match);
	$_POST[$key] = empty($match) ? null : $match[0];
};

$area = $_POST['area'];

unset($_POST['area']);
unset($_POST['otp']);

$universal->temp->status = false;

switch( $area ) {
	
	case "menu":
			
			$_SESSION['menu'] = $_SESSION['menu'] ?? [];
			
			if( !in_array($_POST['menu_placement'], ['backend_menu', 'admin_menu', 'grid_menu']) ) 
				$helper->jsonify( false, "Invalid placement destination", null, true );
			
			$menu = $_POST['menu_placement'];
			$menu = $$menu;
			
			if( !empty($menu->search($_POST['menu_name'])[0]) )
				$universal->temp->message = "<p>The menu name already exists</p>";
			
			else {
				
				$_SESSION['menu'][] = $_POST;
				
				$universal->temp->status = true;
				$universal->temp->message = "
					<p>The menu has been added </p>
					<p>Once change has been implemented, do check for the new item in the " . str_replace("_", " ", $_POST['menu_placement']) . "</p>";
					
			};

		break;
		
	
	case "page":
		
		$_SESSION['page'] = $_SESSION['page'] ?? [];
		
		foreach(['page_sider', 'page_blank'] as $key) 
			$_POST[$key] = ( $_POST[$key] == 'true' ) ? true : false;
		
		$pagename = $_POST['page_name'];
		
		if( !array_key_exists($pagename, $_SESSION['page']) ) {
			$_SESSION['page'][$pagename] = $_POST;
			$universal->temp->status = !!($universal->temp->message = "
				<p>The page has been created!</p>
				<p>You should click on the link under <strong>`Execute Code`</strong> to access the page</p>
			");
		} else 
			$universal->temp->message = "<p>Please choose a different page name</p>";
		
		break;
		
	case "head_foot":
			
			$_SESSION['HF'] = $_POST;
			$universal->temp->status = !!($universal->temp->message = "
				<p>The tags has been added</p>
			");
			
		break;
};


if( $universal->temp->status )
	$universal->temp->message .= "<p>This page will be reloaded to implement change</p>";
else {
	if( !isset($universal->temp->message) )
		$universal->temp->message = "<p>No command was executed</p>";
};

$universal->temp->message = "<div class='pluglist'>" . trim($universal->temp->message) . "</div>";

$helper->jsonify( $universal->temp->status, $universal->temp->message, null, true );