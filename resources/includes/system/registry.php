<?php defined("ROOT_PATH") or die;

call_user_func(function() use($backend) {
	
	global $universal;
	
	$backend->liteDOM->billboard( 
		"Sorry!", 
		null, 
		function() use($universal) {
			echo "<div class='mb-4'>Registration is closed!</div>" . $universal->plugin->button;
		}
	);
	
});