<?php 

	require_once  __DIR__ . "/config.php";
	
	$backend = new backend();
	$backend->blank(true);
	$backend->bodyclass[] = "app-reset-password p-0";
	
	$backend->output(function() {
		
		global $universal, $helper;
		
		$approved = call_user_func(function() use($helper) {;
			foreach( $_GET as $key => $value ) $_GET[ $key ] = $helper->sanitize($value);
			if( !isset($_GET['u']) || !isset($_GET['verify']) ) return;
			$user = $helper->_data( DB_PREFIX . "users", $_GET['u'] );
			if( !$user ) return;
			global $usermeta;
			$reset_data = $usermeta->get("reset.password", $user['id'], true);
			if( $reset_data['meta_value'] == $_GET['verify'] ) {
				$period = (int)$reset_data['period'];
				$not_expired = ($period + 3600) >  time();
				if( !$not_expired ) {
					$usermeta->remove("reset.password", $user['id']);
					return 0;
				} else return $user;
			} return 0;
		});
		
		if( $approved ) $_SESSION['nonce'] = array(
			"key" => $helper->keygen(),
			"usercode" => $approved['usercode']
		);
		
?>
 	
    <div class="row g-0 app-auth-wrapper">
	    <div class="col-12 col-md-7 col-lg-6 auth-main-col text-center p-5">
		    <div class="d-flex flex-column align-content-end">
			
			    <div class="app-auth-body mx-auto">
				
				    <div class="app-auth-branding mb-4">
						<a class="app-logo" href="<?php echo $universal->src->root_url; ?>">
							<img class="logo-icon me-2" src="<?php echo $universal->site->logo; ?>" alt="logo">
						</a>
					</div>
					
					<h2 class="auth-heading text-center mb-4">
						Password Reset
					</h2>
	
					<?php if( !$approved ): ?>
					
						<div class="auth-intro mb-4 text-center">
							Enter your email address below. <br/>
							You'll receive an email to confirm the process.
						</div>
		
						<div class="auth-form-container text-left">
							
							<form class="auth-form resetpass-form" onsubmit="return false" id="bkend-reset">                
								<div class="email mb-3">
									<label class="sr-only" for="reg-email">Your Email</label>
									<input id="reg-email" name="email" type="email" class="form-control login-email" placeholder="Your Email" required="required">
								</div><!--//form-group-->
								<div class="text-center">
									<button type="submit" class="btn app-btn-primary btn-block theme-btn mx-auto">Reset Password</button>
								</div>
								<div data-v-auth='auth'><?php echo (new potty())->assign("reset-password"); ?></div>
							</form>
							
							<div class="auth-option text-center pt-5">
								<a class="app-link" href="<?php echo $universal->src->login_url; ?>" >Log in</a> 
								<span class="px-2">|</span> 
								<a class="app-link" href="<?php echo $universal->src->signup_url; ?>" >Sign up</a>
							</div>
						</div><!--//auth-form-container-->
						
					<?php else: ?>
						
						<div class="auth-intro mb-4 text-center">
							Please enter your new password <br/>
							You will be logged out from any active session after the change.
						</div>
		
						<div class="auth-form-container text-left">
							
							<form class="auth-form resetpass-form" onsubmit="return false" id="bkend-changepass">                
								<div class="password mb-3">
									<label class="sr-only" for="reset-password">New Password</label>
									<input id="reset-password" name="password" type="password" class="form-control login-password" placeholder="New Password" required="required">
								</div><!--//form-group-->
								
								<div class="password mb-3">
									<label class="sr-only" for="reset-password">Confirm Password</label>
									<input id="confirm-password" type="password" class="form-control login-password" placeholder="Confirm Password" required="required">
								</div><!--//form-group-->
								
								<input type="hidden" name="nonce" value="<?php echo sha1($_SESSION['nonce']['key']); ?>">
								
								<div class="text-center">
									<button type="submit" class="btn app-btn-primary btn-block theme-btn mx-auto">Update Password</button>
								</div>
								
								<div data-v-potty="auth"><?php echo (new potty())->assign("change-password"); ?></div>
								
							</form>
							
						</div><!--//auth-form-container-->

					<?php endif; ?>

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
	<?php }); // end listener; ?>
	
<?php });

