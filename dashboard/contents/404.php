<?php 

	require_once __DIR__ . "/config.php";
	
	$backend = new backend();
	$backend->blank(true);
	$backend->bodyclass[] = 'app-404-page';
	
	$backend->output(function($backend) {
		
		$backend->liteDOM->billboard(404, 'Page Not Found', function() {
			global $universal;
			$helper = new helper();
			if( 
				isset($universal->plugin->REQUEST_DIR) && 
				in_array($universal->plugin->REQUEST_DIR, [ADMIN_PATH, ROOT_PATH]) 
			) {
				$home = $helper->server_to_url( $universal->plugin->REQUEST_DIR );
			} else $home = $universal->src->inst_url;
?> 

		<div class="mb-4">Sorry, we can't find the page you're looking for. </div>
		<a class="btn app-btn-primary" href="<?php echo $home; ?>">
			Go to home page
		</a>
   
	<?php }); // close illoard
	
}); // close output