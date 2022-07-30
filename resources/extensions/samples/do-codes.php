<?php

if( $_SESSION['menu'] ?? false ) {
		
	foreach( $_SESSION['menu'] as $menu_data ):
		
		$menu = $menu_data['menu_placement'];
		$menu = $$menu;
				
		$data = array(
			"label" => $menu_data['menu_label'],
			"icon" => $menu_data['menu_icon']
		);
				
		try {
			
			if( empty($menu_data['menu_parent']) ) $menu->add($menu_data['menu_name'], $data);
			else $menu->add_submenu($menu_data['menu_parent'], $menu_data['menu_name'], $data);
			
		} catch( exception $e ) {}

	endforeach;

};


# -- [ add pages ] --

if( $_SESSION['page'] ?? false ) {
	
	foreach( $_SESSION['page'] as $page_data ):

		plugins::create_page($page_data['page_name'], array(
			"role" => $page_data['page_panel'],
			"sidebar" => $page_data['page_sider'],
			"blank" => $page_data['page_blank'],
			"bodyclass" => $page_data['page_class'],
			"output" => function() use($page_data) {
				echo htmlentities($page_data['page_content']);
			}
		));

	endforeach;

}

# -- [ tags ] --

if( $_SESSION['HF'] ?? false ) {
	
	events::addListener("backend-body:start", function() {
		echo "<h2>{$_SESSION['HF']['h1_head']}</h2>";
	});
	
	events::addListener("backend-body:end", function() {
		echo "<h2>{$_SESSION['HF']['h1_foot']}</h2>";
	});
	
}

