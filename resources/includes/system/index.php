<?php defined("ROOT_PATH") OR DIE;

## --- Activate core ---;

$universal->temp->system_page = "uss-core";

plugins::create_page($universal->temp->system_page, array(

	"output" => function($backend) {
		
		global $universal;
		
		$universal->plugin->button = "
			<a class='btn app-btn-primary' href='{$universal->src->root_url}'>
				Go to account
			</a>
		";
		
		$cores = new directoryIterator( __DIR__ );		
		
		foreach( $cores as $iter ) {
			if( !$iter->isFile() || $iter->getFilename() == 'index.php' ) continue;
			$content = substr(basename($iter->getPathname()), 0, -4);
			if( $content != ($universal->plugin->REQUEST['content'] ?? false) ) continue;
			require_once ( $iter->getPathname() );
		}
		
	},
	
	"blank" => true,
	
	"bodyclass" => "app"
	
));
