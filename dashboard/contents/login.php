<?php 
	
	require_once __DIR__ . "/config.php";
	
	$backend = new backend();
	$backend->blank(true);
	$backend->bodyclass[] = 'app-login p-0';
	
	$backend->output(function() {
		
		global $universal;
		
		$verified = ($universal->methods->confirm_verification)('verified');
		
		$helper = new helper();
		
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
					
					<h2 class="auth-heading text-center mb-5">
						Log in
					</h2>
					
					<div class="auth-form-container text-start">
					
						<form class="auth-form login-form mb-3" onsubmit="return false" id="bkend-login"> 
						
							<div class="email mb-3">
								<label class="sr-only" for="signin-login">Email</label>
								<input id="signin-login" name="login" type="text" class="form-control signin-email" placeholder="Email address" required="required">
							</div><!--//form-group-->
							
							<div class="password mb-3">
								<label class="sr-only" for="signin-password">Password</label>
								<input id="signin-password" name="password" type="password" class="form-control signin-password" placeholder="Password" required="required">
								<div class="extra mt-3 row justify-content-between">
									<div class="col-6">
										<div class="form-check">
											<input class="form-check-input" name="remember" type="checkbox" value="1" id="RememberPassword">
											<label class="form-check-label" for="RememberPassword">
												Remember me
											</label>
										</div>
									</div><!--//col-6-->
									<div class="col-6">
										<div class="forgot-password text-end">
											<a href="<?php echo $universal->src->reset_password_url; ?>">Forgot password?</a>
										</div>
									</div><!--//col-6-->
								</div><!--//extra-->
							</div><!--//form-group-->
							
							<div class="text-center">
								<button type="submit" class="btn app-btn-primary w-100 theme-btn mx-auto">
									Log In
								</button>
							</div>
							
							<div data-v-auth='auth'><?php echo (new potty())->assign('login'); ?></div>
							
						</form>
						
						<div class='text-center auth-option mb-2'>
							<a href='javascript:void(0)' id='re-mail'>
								<i class='fas fa-parachute-box me-1'></i> Resend Confirmation Email
							</a>
							<form data-ref="re-mail" class='d-none' onsubmit='return false'>
								<label class='form-label'>Please enter your account email address</label>
								<input type="email" name="email" class='form-control mb-2' placeholder='Email address' required>
								<button class='btn app-btn-primary w-100' type='submit'>Resend Email</button>
								<div data-v-potty="re-mail"><?php echo (new potty())->assign('re-mail'); ?></div>
							</form>
						</div>
						
						<div class="auth-option text-center pt-3">
							No account? Sign up <a class="text-link" href="<?php echo $universal->src->signup_url; ?>" >here</a>.
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
			<div class="auth-background-overlay p-3 p-lg-5">

			</div><!--//auth-background-overlay-->
		</div><!--//auth-background-col-->

	</div><!--//row-->

	<?php 
	
		events::listener("backend-foot", function() use($helper, $verified) { 
		
			echo "<script>uss.authorizing=" . ( defined("_uss_authorizing") ? 1 : 0 ) . "</script>";
			
	?>
	
		<script type="text/javascript" src="<?php echo $helper->server_to_url( ROOT_PATH ) . "/assets/js/userlogin.js"; ?>"></script>
		
		<?php 
			if( $verified !== null ): 
				if( $verified === true ) $message = "<i class='fas fa-check-circle text-success'></i> - Email verification successful";
				else if( $verified === false ) $message = "<i class='fas fa-ban text-danger'></i> - Email verification failed";
				else $message = "<i class='fas fa-exclamation-triangle text-warning'></i> - Invalid verification link";
		?>
			<script>$(() => { uss.meth.modal(<?php echo "\"{$message}\""; ?>); })</script>
		<?php endif; ?>
		
	<?php }); // end listener; ?>

<?php }); ?>


