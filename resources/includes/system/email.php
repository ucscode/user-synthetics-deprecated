<?php defined("ROOT_PATH") or die;

call_user_func(function() use($backend) {
	
	global $universal;
	
	if( $universal->plugin->REQUEST['content'] != 'email' ) return;
	
	$errorType = call_user_func(function() use(&$universal) {
		
		global $usermeta, $helper;
		
		$universal->plugin->title = "Error";
		$universal->plugin->message = "Sorry! The verification process failed <br> New email was not updated";
		
		if( $universal->plugin->NAME != $universal->temp->system_page ) return '-1';
		
		$REQ = $universal->plugin->REQUEST;
		
		foreach( ['content', 'uid', 'code'] as $key ) {
			if( !isset($REQ[$key]) ) return 'A';
		};
		
		$user = $helper->_data( DB_PREFIX . "users", $universal->plugin->REQUEST['uid'] );
		if( !$user ) return 'B';
		else if( $universal->plugin->REQUEST['code'] != $user['activation_key'] ) return 'C';
		
		global $ucsqli;
		
		$email = $usermeta->get("user.email", $user['id']);
		
		if( !$email ) {
			$universal->plugin->message = "Something is missing!";
			return 'D';
		} else {
			$updated = $ucsqli->update( 
				DB_PREFIX . "users",
				array("email" => $email),
				"id={$user['id']}"
			);
			if( $updated ) {
				$universal->plugin->message = "Great! Your new email has been confirmed";
				$universal->plugin->title = "Success!";
				$usermeta->remove("user.email", $user['id']);
				return;
			};
			return 'E';
		};

		return 1;
		
	});
	
	$backend->liteDOM->billboard( 
		"<sup>{$errorType}.</sup> " . $universal->plugin->title, 
		null, 
		function() use($universal) {
			echo "<div class='mb-4'>{$universal->plugin->message}</div>" . $universal->plugin->button;
		}
	);
	
});

