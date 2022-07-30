<?php 

defined("ROOT_PATH") or die("DIRECT ENTRY RESTRICTED"); 

if( !isset($this) || !($this instanceof backend) ) exit("Limited Access");

# This is a BACK-END DOM compontent

$this->liteDOM = (new class() {

	public function alert(?string $title = null, $content, ?string $dismissible = null) {
		
		global $helper;
		
		$attrs = array(
			"class" => "app-card alert shadow-sm mb-4 border-left-decoration",
			"role" => "alert"
		);
		
		if( $dismissible ) {
			$attrs['class'] .= " alert-dismissible d-none";
			$attrs['data-alert-dismiss'] = '_' . md5($dismissible);
		}
		
?>
	
		<div <?php echo $helper->array_to_html_attrs($attrs); ?>>
			<div class="inner">
				<div class="app-card-body p-3 p-lg-4">
					<?php if( $title ): ?>
					<h3 class="mb-3"><?php echo $title; ?></h3>
					<?php endif; ?>
					<div class="row gx-5 gy-3">
						<div class="col-12">
							<?php
								if( is_callable($content) ) $content( $this );
								else if( is_string($content) ) echo $content;
								else {
									$type = getType($content);
									throw new Exception( "backend.liteDOM.alert: {$type} to string conversion. Cannot alert {$type}" );
								}
							?>
						</div><!--//col-->
					</div><!--//row-->
					<?php if( $dismissible ): ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" data-alert-close="<?php echo $attrs['data-alert-dismiss']; ?>"></button>
					<?php endif; ?>
				</div><!--//app-card-body-->
				
			</div><!--//inner-->
		</div><!--//app-card-->
	
	<?php }
	
	public function billboard( ?string $title, ?string $subtitle, $content = null) { 
		global $universal;
	?>
	
		<div class="container mb-5">
			<div class="row">
				<div class="col-12 col-md-11 col-lg-7 col-xl-6 mx-auto"> 
				
					<div class="app-branding text-center mb-5">
						<a class="app-logo" href="index.html">
							<img class="logo-icon me-2" src="<?php echo $universal->site->logo; ?>" alt="logo">
							<div class='mb-1'></div>
							<span class="logo-text text-uppercase"><?php echo $universal->site->name; ?></span>
						</a>
					</div><!--//app-branding--> 
					
					<div class="app-card p-5 text-center shadow-sm">
						<div class="mb-4">
							<h1 class="page-title"><?php echo $title; ?></h1>
							<h4><span class="font-weight-light">
									<?php echo $subtitle; ?>
								</span>
							</h4>
						</div>
						<?php 
							if( is_callable($content) ) $content( $this );
							else if( is_string($content) ) echo $content;
						?>
					</div>
					
				</div><!--//col-->
			</div><!--//row-->
		</div><!--//container-->
	
	<?php }

});

