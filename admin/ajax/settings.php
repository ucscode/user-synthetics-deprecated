<?php defined('ROOT_PATH') OR DIE;

# [ authentication ] 

(new potty())->auth( 'admin-settings', $_POST['as-auth'] ?? '', true, true );


# [ begin ]

$type = array_keys($_POST)[0];

if( !is_array($_POST[$type]) ) {

	$universal->ajax->message = "
		Changes were not saved! <br/> 
		Could not define settings group!
	";

} else {

	$input = $helper->sanitize($_POST[$type]);

	$universal->ajax->message = "Changes successfully saved!";

	if( $type == 'basic' && !empty($_FILES['site_icon']['tmp_name']) ) {
		
		$icon_data = $_FILES['site_icon'];
		$icon_info = getimagesize( $icon_data['tmp_name'] );
		
		if( !$icon_info || !preg_match("/image\/(?:jpg|jpeg|png|gif)/i", $icon_info['mime']) ):
			
			$universal->ajax->error = !!( $univesal->ajax->message = "The file you uploaded is not supported" );
		
		else:
			
			$filepath = ROOT_PATH . "/assets/images/icon." . pathinfo( $icon_data['name'], PATHINFO_EXTENSION );
			$moved = move_uploaded_file( $icon_data['tmp_name'], $filepath );
			
			if( !$moved ) $universal->ajax->message .= "<br/>However, the icon could not be saved";
			else $input['icon'] = $helper->server_to_url( $filepath );
		
		endif;
		
	};

	if( !$universal->ajax->error ) {
	
		$results = [];

		foreach( $input as $key => $value )
			$results[] = $uss_options->set( $key, $value );

		$universal->ajax->status = !in_array(false, $results);	

		if( !$universal->ajax->status ) $universal->ajax->message = "Something went wrong";
	
	};

};


$helper->jsonify( $universal->ajax->status, $universal->ajax->message, null, true );