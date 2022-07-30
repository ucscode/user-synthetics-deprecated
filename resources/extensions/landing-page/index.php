<?php 

$universal->temp->no_user = !$ucsqli->select( DB_PREFIX . 'users' )->num_rows;
$universal->temp->message = null;

if( $_SERVER['REQUEST_METHOD'] == 'POST' && $universal->temp->no_user && !defined("AJAX_MODE") ):
	
	if( empty($_POST['username']) ) unset($_POST['username']);
	
	if( !empty($_POST['username']) && !preg_match($helper->regex('word'), $_POST['username']) )
		$universal->temp->message = "The username is not valid";
	
	else if( !preg_match($helper->regex('email'), $_POST['email']) )
		$universal->temp->message = "The email is not valid";
	
	else {
		
		$_POST['password'] = $helper->passify($_POST['password']);
		$_POST['role'] = 'admin';
		$_POST['status'] = 'verified';
		$_POST['register_time'] = time();
		$_POST['usercode'] = ($universal->methods->new_usercode)();
		$_POST['id'] = 0;
		
		$universal->temp->status = $ucsqli->insert( DB_PREFIX . 'users', $_POST );
		
		if( !$universal->temp->status ) {
			$universal->temp->message = "
				New account could not be created! <br>
				<div class='fw-400 mt-2 fs-14'>{$ucsqli->getError()}</div>
			";
		} else {
			$universal->temp->message = "
				Your account has been created! <br>
			";
		};
		
		$universal->temp->no_user = !$ucsqli->select( DB_PREFIX . 'users' )->num_rows;
		
		plugins::activate('copyright');
		
	};
	
endif;

# - Wrap content around header and footer 

events::addListener("uss:index", function() {
	global $uss_user, $ucsqli, $universal, $helper;
?>
	
	<?php 
		function pasteMessage() {
			global $universal;
			if( $universal->temp->message ): ?>
				<div class='text-center font-weight-500 alert alert-<?php echo ($universal->temp->status ?? false) ? 'info' : 'danger'; ?>'>
					<?php echo $universal->temp->message; ?>
				</div>
	<?php endif; } ?>
	
	<div class="container pb-5">
		<div class="row">
		
			<div class="col-lg-9 col-xl-8 m-auto">
				<div class='card'>
					<div class='card-body'>
					
						<div class='d-flex align-items-center'>
							<img src='<?php echo $universal->site->logo; ?>' class='img-fluid --icon me-3'>
							<h5><?php echo $universal->site->name; ?></h5>
						</div>
						
						<hr>
						
					<?php 
						if( $universal->temp->no_user ): # -- [ START NO USER ] --
					?>
					
						<h5>Hi dear,</h5>
						<p class='fw-600'>
							Thank you for downloading user synthetics software.</p>
						<p>
							No user is available in the system. Probably, you should be the administrator. <br>Please proceed by filling up the information below to create your first account as an admin.
						</p>
						
						<hr>
						
						<div class="row">
							<div class='col-md-10 col-lg-7 m-auto p-2'>
							
								<?php pasteMessage(); ?>
								
								<form class='p-2' method="post">
									<fieldset>
										<div class="mb-4">
											<label class="form-label">
												Username (optional)
												<span class='ms-2' data-bs-toggle='popover' data-bs-container='body' data-bs-trigger='hover' data-bs-position='right' data-bs-content='Should contain only text, number and underscore'><i class='fas fa-info-circle'></i> </span>
											</label>
											<input type="text" class='form-control' name="username" placeholder='e.g user_247' value="<?php echo $_POST['username'] ?? null; ?>">
										</div>
										<div class="mb-4">
											<label class="form-label">
												Email (required)
												<span class='ms-2' data-bs-toggle='popover' data-bs-container='body' data-bs-trigger='hover' data-bs-position='right' data-bs-content='You will need this to login'><i class='fas fa-info-circle'></i> </span>
											</label>
											<input type="text" class='form-control' name="email" required value="<?php echo $_POST['email'] ?? null; ?>">
										</div>
										<div class="mb-4">
											<label class="form-label">
												Password (required)
												<span class='ms-2' data-bs-toggle='popover' data-bs-container='body' data-bs-trigger='hover' data-bs-position='right' data-bs-content='Use a very strong password to prevent cyber attacks'><i class='fas fa-info-circle'></i> </span>
											</label>
											<div class='input-group mb-2'>
												<input type="password" class='form-control' name="password" required>
												<button class='btn btn-secondary' type='button'>
													<i class='fas fa-eye'></i>
												</button>
											</div>
											<div class="progress">
											  <div class="progress-bar" id='password-security' role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">25%</div>
											</div>
										</div>
										<div class='mb-4'>
											<label class='form-label'>
												Role
												<span class='ms-2' data-bs-toggle='popover' data-bs-container='body' data-bs-trigger='hover' data-bs-position='right' data-bs-content='You will be registered as an admin by default'><i class='fas fa-info-circle'></i> </span>
											</label>
											<input type='text' disabled value='admin' class='form-control'>
										</div>
										<div class='mb-4'>
											<button class='btn app-btn-primary' type='submit'>
												Create My Account
											</button>
										</div>
									</fieldset>
								</form>
							</div>
						</div>
						
					<?php events::addListener("backend-foot", function() { ?>
					
						<script>
						
							let preg = {
								match: [/[a-z]+/, /[A-Z]+/, /[0-9]+/, /\W+/, /.{8,}/],
								status: ['very weak', 'weak', 'fair', 'strong', 'very strong'],
								color: ['bg-danger', 'bg-warning', 'bg-info', 'bg-primary', 'bg-success']
							};
							
							let progress = $("#password-security");
							
							let input = $("form [name='password']").on('input', function() {
								let value = this.value, secure = 0;
								let unit = 100 / preg.match.length;
								for( let regex of preg.match ) {
									if( value.match(regex) ) secure += unit;
								};
								progress.css("width", secure + '%');
								let key = ( secure / unit ) - 1;
								for( let x = 0; x < preg.match.length; x++ )
									progress.removeClass(preg.color[x]);
								progress.text('');
								if( key > -1 ) {
									progress.text(preg.status[key]).addClass(preg.color[key]);
								}
							});
							
							input.next().click(function() {
								if( input.attr('type') == 'password' ) {
									input.attr('type', 'text');
									$(this).find('.fas').removeClass('fa-eye').addClass('fa-eye-slash');
								} else {
									input.attr('type', 'password');
									$(this).find('.fas').removeClass('fa-eye-slash').addClass('fa-eye');
								}
							});
							
						</script>
						
					<?php	});
					
						else: # -- [ END NO USER ] -- 
								
							events::addListener("backend-head", function() {
								echo '<style>
									.coding {
										border: 1px solid var(--bs-gray-400);
										background-color: var(-bs-gray-100);
										border-radius: 5px;
										font-size: 14px;
									}
									h4 { text-align: center; }
									.coded {
										font-family: monospace;
										font-size: 14px;
										display: inline-block;
										padding: 0 6px;
										margin: auto 2px;
										background-color: var(--bs-gray-200);
										border-radius: 3px;
									}
								</style>';
							});
						?>
				
						<div class="py-3">
						
							<?php pasteMessage(); ?>
							
							<div class='border p-3 mb-3'>
							
								<h4 class='text-center'><u>What Next?</u></h4>
								
								<p>Please be informed of the following:</p> 
								
								<ul>
									<li class='mb-2'>This page was intended to help you create your first account</li>
									<li class='mb-2'>Now you must edit the content of this page to prevent other users from seeing this message</li>
									<li class='mb-2'>The content of this page was created by an extension called <span class='text-danger'>landing-page</span></li>
									<li>The extension is located at - <span class='text-danger'><?php echo EXT_PATH . "/landing-page"; ?></span></li>
								</ul>
							
							</div>
							
							<div class="border p-3 mb-3">
							
								<h4 class='text-center'><u>What should be in this page?</u></h4>
								
								<p>Anything can be programmed into this page. <br> Technically, it serves as a good environment to create a landing page. That is to say:</p>
								
								<ul>
									<li class='mb-2'>New users will access their dashboard by the URL - <a href='<?php echo $universal->src->root_url; ?>'><?php echo $universal->src->root_url; ?></a></li>
									<li class='mb-2'>Admins will access their panel by the URL - <a href='<?php echo $universal->src->admin_url; ?>'><?php echo $universal->src->admin_url; ?></a></li>
									<li class='mb-2'>Therefore, this can be used as a landing page to connect users to their account</li>
								</ul>
							
							</div>
							
							<div class='border p-3 mb-3'>
							
							<h4 class='text-center'><u>How do I program a landing page?</u></h4>
							
								<p>There are three ways you can do that:</p>
								
								<ol>
									<li>Edit the root <span class='text-primary'>index.php</span> file <span class='text-danger'>(Not recommended)</span></li>
									<li>Create an Extension</li>
									<li>Create a Plugin</li>
								</ol>
							
							</div>
							
							<div class='border p-3 mb-3'>
								
								<h6>Editing the root <span class='text-primary'>index.php</span> file</h6>
								
								<p class='text-center'>Here's a simple code to get you started</p>
								
								<div class='p-4 mb-3 coding'>
									<?php
										highlight_string(
'<?php 
	require_once "config.php"; // require the config file;
	
	$backend = new backend(); 
	
	$backend->blank(true); // prepare a blank page
	
	# add custom classes to <body> tag
	$backend->bodyclass[] = \'p-0\';
	$backend->bodyclass[] = \'my-custom-class\';
	
	$backend->output(function() {
		echo "<div class=\'alert alert-success\'> Your bootstrap 5 langing page output </div>";
	});
	'
									);
								?>
								</div>
								
								<p>The above code will create a blank page that contains a bootstrap alert box.</p>
								
								<p>I would also like to say that it is much easier and readable to output code as direct HTML rather than echo it in PHP. For example:</p>
								
								<div class='coding p-4 mb-3'>
									<?php
									highlight_string('<?php $backend->output(function() { ?>
	<div class=\'alert alert-danger\'> Your bootstrap 5 landing page output </div>
<?php });
');
									?>
								</div>
								
								<h6 class='text-info'>Why is it not recommended to edit the main index.php file?</h6>
								
								<p>Because it contains an event executor <span class='text-danger coded'>events::exec("uss:index")</span> which other extensions and plugins will listen to. So if you install or create an extension or plugin that is meant to alter the index page (landing page), it will not work in absence of the executor.</p>
							
							</div>
							
							<div class='border p-3 mb-3'>
							
								<h6>Creating an <span class='text-primary'>Extension</span> or <span class='text-primary'>Plugin</span></h6>
								
								<p class=''>
									Extensions and plugins are literally the same thing. The only difference is that: <br> Extension runs automatically and requires an <span class='text-primary'>index.php</span> file. <br> While Plugins must be activated in the admin panel and requires a file named <span class='text-primary'>default.php</span>
								</p>
								
								<p class='text-muted'><i>You can check the extension directory or plugin directory to see examples</i></p>
								
								<p>Whichever the case, you can update this <span class='text-primary'>index.php</span> page after removing the <span class='text-danger'>landing-page</span> extension or editing it instead.</p>
								
								<p class='text-center'>Here's a simple code to get you started</p>
								
								<div class='coding mb-4 p-3'>
									<?php highlight_string('<?php
	events::addListener("uss:index", function() {
		// your output code here
	});
'); ?>
								</div>
							
							</div>
							
							<div class='border p-3 mb-3'>
							
								<h4><u>But I am not a programmer?</u></h4>
								
								<p>I'm so sorry to hear that, but this project was developed to facilitate jobs for programmers. Therefore, you should hire one. </p>
								<p>Nevertheless, you can paste the following code into the root <span class='text-primary'>index.php</span></p>
								
								<div class='coding p-4 mb-3'>
								<?php highlight_string('<?php
	require_once "config.php";
	(new backend())->get_content_file( "login.php", null, true );
	exit;
'); ?>
								</div>
								
								<p>The above code will convert this page into the user login page. You can also use <span class='text-danger'>"signup.php"</span> instead to convert into a signup page. However, we do know that neither of the above page is a good alternative to a proper landing page</p>
								
							</div>
							
						</div>
						
						<hr>
						
						<div class='py-2'>
						
							<h2 class='text-center'>Best Of Luck!</h2>
							
							<a href='<?php echo $universal->src->admin_url; ?>' class='btn btn-success w-100 py-3 fs-18'>
								Login Now
							</a>
						
						</div>
						
					<?php endif; ?>
					
					</div>
					
					<div class='card-footer text-center pt-4'>
						<div class='fw-500 d-block'>
							&copy; 2020 <br>
							Developed by <a href='https://ucscode.com' target="_blank">UCSCODE</a>
						</div>
						<hr>
						<ul class="list-unstyled d-flex justify-content-center fs-32">
							<li class='me-3'>
								<a href='https://github.com/ucscode' class='text-dark' target='_blank'><i class='fab fa-github'></i></a>
							</li>
							<li class='me-3'>
								<a href='https://facebook.com/ucscode' class='text-primary' target='_blank'><i class='fab fa-facebook'></i></a>
							</li>
							<li class='me-3'>
								<a href='https://www.youtube.com/channel/UCPlGBkdI0ydlgAZWoLdmOFg' class='text-danger' target='_blank'><i class='fab fa-youtube'></i></a>
							</li>
						</ul>
					</div>
					
				</div>
			</div>
			
		</div>
	</div>
	
<?php }); 
	
