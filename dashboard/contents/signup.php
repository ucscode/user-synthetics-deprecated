<?php 
	
	require_once __DIR__ . "/config.php";
	
	if( $universal->site->disable_signup ) {
		$__url = $helper->server_to_url( 
			ROOT_PATH . "/{$universal->temp->system_page}?content=registry&status=closed" 
		);
		if( !headers_sent() ) header("location: $__url");
		else echo "<script>window.location.href = '{$src}';</script>";
		exit();
	};
	
	$backend = new backend();
	$backend->blank(true);
	$backend->bodyclass[] = 'app-signup p-0';
	
	$backend->output(function() {
		
		global $universal;
		$helper = new helper();
		
?>
  	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			    <div class="app-auth-body mx-auto">	
				
				    <div class="app-auth-branding mb-4">
						<a class="app-logo" href="<?php echo $universal->src->inst_url; ?>">
							<img class="logo-icon me-2" src="<?php echo $universal->site->logo; ?>" alt="logo">
						</a>
					</div>
					
					<h2 class="auth-heading text-center mb-4">
						Sign Up
					</h2>					
	
					<div class="auth-form-container text-start mx-auto">
					
						<form class="auth-form auth-signup-form" onsubmit="return false" id="bkend-signup">  
							
							<?php events::exec("signup:form-input:start"); ?>
							
							<div class="email mb-3">
								<label class="sr-only" for="signup-email">Your Email</label>
								<input id="signup-email" name="email" type="email" class="form-control signup-email" placeholder="Email" required="required">
							</div>
							
							<div class="password mb-3">
								<label class="sr-only" for="signup-password">Password</label>
								<input id="signup-password" name="password" type="password" class="form-control signup-password" placeholder="Create a password" required="required">
							</div>
							
							<div class="password mb-3">
								<label class="sr-only" for="confirm-password">Password</label>
								<input id="confirm-password" type="password" class="form-control signup-password" placeholder="Confirm password" required="required">
							</div>
							
							<?php events::exec("signup:form-input:end"); ?>
							
							<div class="extra mb-3">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="" id="agreement" required>
									<label class="form-check-label" for="agreement">
										I agree to the 
										<a href="<?php echo $universal->src->tos_url; ?>" class="app-link" target="_blank">Terms of Service</a> and 
										<a href="<?php echo $universal->src->privacy_url; ?>" class="app-link" target="_blank">Privacy Policy</a>.
									</label>
								</div>
							</div><!--//extra-->
							
							<div class="text-center">
								<button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">Sign Up</button>
							</div>
							
							<div data-v-potty="auth"><?php echo (new potty())->assign("signup"); ?></div>
							
						</form><!--//auth-form-->
						
						<div class="auth-option text-center pt-5">
							Already have an account? <a class="text-link" href="<?php echo $universal->src->login_url; ?>" >Log in</a>
						</div>
					</div><!--//auth-form-container-->	

			    </div><!--//auth-body-->
		    
				<footer class="app-auth-footer">
					<div class="container text-center py-3">
						<small class="copyright">
							Copyright &copy; <?php echo (new datetime())->format("Y"); ?> <span class='mx-2'>&mdash;</span> 
							<a class="app-link" href="<?php echo $universal->src->inst_url; ?>">
								<?php echo $universal->site->name; ?>
							</a>
						</small>
					</div>
				</footer><!--//app-auth-footer-->	
			
		    </div><!--//flex-column-->   
	    </div><!--//auth-main-col-->
	    <div class="col-12 col-md-5 col-lg-6 h-100 auth-background-col">
		    <div class="auth-background-holder"></div>
		    <div class="auth-background-mask"></div>
		    <div class="auth-background-overlay p-3 p-lg-5"></div><!--//auth-background-overlay-->
	    </div><!--//auth-background-col-->
    
    </div><!--//row-->
	
	<?php events::listener("backend-foot", function() use($helper) { ?>
		<script type="text/javascript" src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/js/userlogin.js"; ?>"></script>
	<?php }, 'userlogin.js'); // end listener; ?>
		
<?php }); // end output;
