<?php

if( !function_exists('fatality') ) {
	/* This is not mortal kombat */
	function fatality( ?string $title = null, $content ) {
		if( headers_sent() ) return false;
		$helper = new helper();
		$icon = isset($uss_option) ? $uss_option->get("site_icon") : null;
		if( !$icon ) $icon = $helper->server_to_url( ROOT_PATH . "/assets/images/logo.png" );
?>
<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel='icon' href='<?php echo $icon; ?>'>
		<link rel="stylesheet" href="<?php echo $helper->server_to_url( ROOT_PATH . "/assets/plugins/bootstrap-5.2.0/css/bootstrap.min.css" ); ?>">
		<link rel="stylesheet" href="<?php echo $helper->server_to_url( ROOT_PATH . "/assets/plugins/fontawesome-6.1.1/css/all.min.css" ); ?>">
		<style>.icon { width: 56px; }</style>
	</head>
	<body>
		<div class='container my-4 br-0'>
			<div class='card'>
				<?php if( $title ): ?>
					<h4 class='card-header p-3' style='line-height: 1.6;'>
						<?php echo $title; ?>
					</h4>
				<?php endif; ?>
				<div class='card-body'>
					<?php is_callable($content) ? $content() : print_r($content); ?>
					<hr>
					<div class='row'>
						<div class="col-8 col-sm-10 col-lg-11">
						<h5>Get help!</h5>
						<p>Contact the developer for assistance:</p>
						<p><a href='mailTo:uche23mail@gmail.com' class='btn btn-primary'>Send email <i class='fas fa-envelope ms-1'></i> </a></p>
						</div>
						<div class="col-4 col-sm-2 col-lg-1 text-right">
							<a href='https://ucscode.com' target='_blank'>
								<img src='<?php echo $icon; ?>' class='icon img-fluid'>
							</a>
						</div>
					</div>
				</div>
				<div class='card-footer text-center'>
					&copy; 2020 - <a href='https://ucscode.com' target='_blank'>UCSCODE</a>
				</div>
			</div>
		</div>
	</body>
</html>
<?php	
		exit;
	};
}
