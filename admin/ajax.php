<?php require __DIR__ . "/config.php";

# [ enable ajax mode ];
project::enable_ajax_mode( __DIR__ );


# [start dev]
events::exec("ajax_mode:start");


# [verify user]
if( $uss_user && $uss_user['role'] == 'admin' ):

	# [get ajax file]
	require_once ( $universal->ajax->script );

	# [end dev]
	events::exec("ajax_mode:end");

else:
	
	# [deny permission] 
	$universal->ajax->message = "Insufficient access permission";
	
endif;


# -- return output --;

$helper->jsonify( !!$universal->ajax->status, trim($universal->ajax->message), $universal->ajax->data, TRUE );

