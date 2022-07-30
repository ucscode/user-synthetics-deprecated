<?php 
	
	require __DIR__ . "/config.php";
	
	$backend = new backend();
	$backend->output(function( $backend ) { 
	
		events::exec("client:home", ['backend' => $backend]);
	
	});
	
